<?php

namespace Feeds2Social\lib;

use Feeds2Social\Config;

class Curl
{
    public $id;
    public $response;
    public $errorCode;
    public $errorMessage;
    public $info;

    public function __construct($url, $params="")
    {
        if (!extension_loaded('curl')) {
            throw new \Exception('cURL not found');
        }

        $this->id = curl_init();

        $this->setDefault();
        $this->setUrlField($url);
        $this->setPostField($params);
    }

    public function __destruct()
    {
        $this->close();
    }

    public function close()
    {
        if (is_resource($this->id)) {
            curl_close($this->id);
        }
    }
    public function setDefault()
    {
        curl_setopt($this->id, CURLOPT_HEADER, false );
        curl_setopt($this->id, CURLOPT_RETURNTRANSFER, true );
        curl_setopt($this->id, CURLOPT_SSL_VERIFYHOST, true );
        curl_setopt($this->id, CURLOPT_SSL_VERIFYPEER, true );
        curl_setopt($this->id, CURLOPT_SAFE_UPLOAD, true);
        curl_setopt($this->id, CURLOPT_PROGRESSFUNCTION, array($this, 'progress'));
    }

    private function progress($resource, $downloadSize, $downloaded, $uploadSize, $uploaded)
    {
        // no more then upload_max_filesize
        if ($downloaded > (Config::$item->upload_max_filesize * 1024 * 1024)) {
            return -1;
        }
    }

    public function setOptions($params = array())
    {
        curl_setopt_array($this->id, $params);
    }

    public function setPostField($params)
    {
        if($params) {
            $options = array(
                CURLOPT_POST => 1,
                CURLOPT_POSTFIELDS => $params,
            );
            $this->setOptions($options);
        }
    }

    public function setUrlField($url)
    {
        $this->setOptions( array(CURLOPT_URL => $url));
    }

    public function execute($mode="")
    {
        $this->response = curl_exec($this->id);

        $this->errorCode = curl_errno($this->id);
        $this->errorMessage = curl_error($this->id);
        $this->info = curl_getinfo($this->id);

        if ($mode == 'json')
            $this->response = json_decode($this->response);

        $this->close();
        return $this->response;
    }

}