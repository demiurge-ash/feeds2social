<?php

namespace Feeds2Social;

class Config
{
    public static $item;
    public static $feeds;
    public static $numberFeeds;
    public static $rawConfig;

    public function __construct()
    {
        // load config from file
        Config::$rawConfig = include('./config.php');

        // remove main config from counter
        Config::$numberFeeds = count(Config::$rawConfig) - 1;

        // merge main config and chosen one feed config
        $chosenOne = intval($_GET['feed']);
        if (($chosenOne <= 0) OR ($chosenOne > Config::$numberFeeds)) $chosenOne = 1;
        $mainConfig = Config::$rawConfig[0];
        $feedConfig = Config::$rawConfig[$chosenOne];
        Config::$item = (object) array_merge( $mainConfig, $feedConfig);
    }
}