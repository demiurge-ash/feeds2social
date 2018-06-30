<?php

namespace Feeds2Social\lib;

use Feeds2Social\Config;
use Feeds2Social\lib\Curl;

class Download
{
    public $url;

    public function image($url)
    {
        $this->url = $url;

        if (!$this->checkUrl()) return false;

        $raw = $this->downloadImage();
        if (!$raw) return false;

        $fileName = $this->saveImage($raw);

        return $fileName;
    }

    public function downloadImage()
    {
        $curl = new Curl($this->url);
        $options = array(
            CURLOPT_TIMEOUT => 60,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_NOPROGRESS => false,
            CURLOPT_BUFFERSIZE => 1024,
        );
        $curl->setOptions($options);

        $raw = $curl->execute();

        if ($curl->errorCode === CURLE_OPERATION_TIMEDOUT) return false;
        if ($curl->errorCode === CURLE_ABORTED_BY_CALLBACK) return false;
        if ($curl->info['http_code'] !== 200) return false;

        return $raw;
    }

    public function checkUrl()
    {
        if (!preg_match("/^https?:/i", $this->url) && filter_var($this->url, FILTER_VALIDATE_URL)) {
            return false;
        }
        return true;
    }

    public function saveImage($raw)
    {
        $file = finfo_open(FILEINFO_MIME_TYPE);
        $mime = (string) finfo_buffer($file, $raw);
        finfo_close($file);

        if (strpos($mime, 'image') === false) return false;

        $image = getimagesizefromstring($raw);
        $name = Config::$item->upload_dir.'tmp';
        $extension = image_type_to_extension($image[2]);

        file_put_contents( $name.$extension, $raw );

        return $name.$extension;
    }

}