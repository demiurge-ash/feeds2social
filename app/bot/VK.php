<?php

namespace Feeds2Social\bot;

use Feeds2Social\Config;
use Feeds2Social\Parser;
use Feeds2Social\lib\Curl;
use Feeds2Social\lib\Download;

class VK
{
    const PHOTOS_GET_SERVER = 'photos.getWallUploadServer';
    const PHOTOS_SAVE ='photos.saveWallPhoto';
    const WALL_POST = 'wall.post';

    public $params = array();
    public $response;

    public function __construct(Parser $parser)
    {
        $this->config();

        $this->postImages($parser);

        $this->postMessage($parser);

    }

    public function config()
    {
        $this->params = array(
            'owner_id' => Config::$item->vk,
            'from_group' => '1',
            'access_token' 	=> Config::$item->vk_token,
            'v' => Config::$item->vk_api_version,
            'group_id' => (int)preg_replace('/([^\d]+)/', '',  Config::$item->vk)
        );
    }

    public function getServer()
    {
        $url = $this->makeURL(self::PHOTOS_GET_SERVER);

        $curl = new Curl($url);
        $result = $curl->execute('json');
        return $result;
    }

    public function uploadImage($url, $image)
    {
        $params = array(
            'photo' => new \CURLFile($image),
            'v' => Config::$item->vk_api_version
        );

        $curl = new Curl($url, $params);
        $result = $curl->execute('json');
        return $result;
    }

    public function saveImage($response)
    {
        $url = $this->makeURL(self::PHOTOS_SAVE);

        $params = array(
            'server' => $response->server,
            'photo' => $response->photo,
            'hash' => $response->hash,
            'v' => Config::$item->vk_api_version,
        );

        $curl = new Curl($url, $params);
        $result = $curl->execute('json');
        return $result;
    }

    public function postMessage($parser)
    {
        $this->params['message'] = "";

        if (Config::$item->title)
            $this->params['message'] .= $parser->title . PHP_EOL;
        if (Config::$item->message)
            $this->params['message'] .= $parser->message . PHP_EOL;
        if (Config::$item->link)
            $this->params['message'] .= $parser->link;

        $url = $this->makeURL(self::WALL_POST);

        $curl = new Curl($url, $this->params);
        $result = $curl->execute('json');
        $this->response = $result;
    }

    public function postImages($parser)
    {
        if(empty($parser->image)) return;

        $i = 1;
        $photoUrl = "";
        $comma = "";
        foreach ($parser->image as $currentImage){

            $response = $this->getServer();

            $download = new Download();
            $image = $download->image($currentImage);
            if (!$image) break;

            $response = $this->uploadImage($response->response->upload_url, $image);

            $response = $this->saveImage($response);

            $photoUrl .= $comma.'photo'.$response->response['0']->owner_id.'_'.$response->response['0']->id;
            $comma = ",";
            $i++;
            if ($i > Config::$item->vk_image_per_post) break;
        }
        $this->params['attachments'] = $photoUrl;
    }

    public function makeURL($method, $argsOpt=array())
    {
        $args = array(
            'access_token' => $this->params['access_token'],
            'group_id' => $this->params['group_id'],
            'v' => Config::$item->vk_api_version
        );
        $args = array_merge( $args, $argsOpt);

        $apiURL = 'https://api.vk.com/method/';
        $apiURL .= $method . '?';

        foreach($args as $key=>$value) {
            $apiURL .= $key . '=' . $value . '&';
        }

        return $apiURL;
    }

}