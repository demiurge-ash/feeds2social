<?php

namespace Feeds2Social\bot;

use Feeds2Social\Config;
use Feeds2Social\Parser;
use Feeds2Social\lib\Curl;

class Telegram
{
    public $message;
    public $markup;
    public $params = array();
    public $response;

    public function __construct(Parser $parser)
    {
        $this->makeMessage($parser);

        if ($parser->image){
            $this->message = $this->shortenMessage($this->message);
            $this->response = $this->postPhoto($parser);
        }else{
            $this->response = $this->postMessage();
        }
    }

    public function postMessage()
    {
        $url = $this->makeURL('sendMessage');

        $params = array(
            'chat_id' => Config::$item->tm,
            'text' => $this->message,
            'reply_markup' => $this->markup,
        );
        $params = $this->makeOptionUrl($params);

        $curl = new Curl($url, $params);
        $result = $curl->execute('json');
        return $result;
    }

    public function postPhoto($parser)
    {
        $url = $this->makeURL('sendPhoto');

        $params = array(
            'chat_id' => Config::$item->tm,
            'caption' => $this->message,
            'photo' => $parser->image[0],
            'reply_markup' => $this->markup,
        );
        $params = $this->makeOptionUrl($params);

        $curl = new Curl($url, $params);
        $result = $curl->execute('json');
        return $result;
    }

    public function makeOptionUrl($params)
    {
        $optionUrl = "";
        foreach($params as $key => $value) {
            if (!$value) continue;
            $optionUrl .= $key . '=' . $value . '&';
        }
        rtrim($optionUrl, '&');
        return $optionUrl;

    }
    public function makeURL($method)
    {
        $apiURL = 'https://api.telegram.org/bot';
        $apiURL .= Config::$item->tm_token . '/';
        $apiURL .= $method;
        return $apiURL;
    }

    public function makeMessage($parser)
    {
        $br = "";
        if (Config::$item->title AND Config::$item->link){
            if (!$parser->title) $parser->title = 'Link';
            $inlineButton = array(
                "text" => $parser->title,
                "url" => str_replace('&', '%26', $parser->link)
            );
            $inlineKeyboard = array(
                "inline_keyboard" => [[$inlineButton]]
            );
            $this->markup = json_encode($inlineKeyboard);

        }else if (Config::$item->title){
            $this->message = $parser->title;
            $br = PHP_EOL;
        }

        if (Config::$item->message) {
            $this->message = $this->message . $br . $parser->message;
        }
    }

    public function shortenMessage($message)
    {
        // take a first sentence
        $arr = explode(".", $message);
        $arr = array_slice($arr, 0, 1);
        $message = implode(".", $arr);

        // shorten the message for photo's caption
        $message = mb_substr($message,0,199,'UTF-8');
        return $message;
    }
}