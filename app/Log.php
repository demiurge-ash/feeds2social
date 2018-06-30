<?php

namespace Feeds2Social;

class Log
{
    public $file;

    public function __construct($file)
    {
        $this->file = $file;
    }

    public function update($txt)
    {
        $row  = date("d F Y, G:i") . ' - ' . $txt . PHP_EOL;
        file_put_contents($this->file, $row, FILE_APPEND);
    }

}