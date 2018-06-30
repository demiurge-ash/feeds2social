<?php

namespace Feeds2Social;

use Feeds2Social\bot\Telegram;
use Feeds2Social\bot\VK;
use Feeds2Social\lib\MySQL;

class Stream
{
    public $config;
    public $db;
    public $url;

    public function __construct()
    {
        // connect to the database
        $this->db = new Mysql();

        // select the next feed for the parser
        $feedNumber = $this->getFeedNumber();
        $this->url = Config::$item->feeds[$feedNumber];
        // parsing feed
        $feed = new Parser($this);

        // no new items in feed?
        if (!$feed->found) return false;

        // remember the ID in the database
        $this->db->insert(
            Config::$item->db.'_feed',
            array('hash' => $feed->itemID)
        );

        // create log entry
        $log = new Log(Config::$item->log_file);
        $log->update("Starting...");
        $log->update("Item: ".$feed->link);

        // publish in Vkontakte
        if (Config::$item->vk) {
            $vk = new VK($feed);
            // update log
            if ($vk->response->error->error_code)
                $log->update('VK Post: ' . $vk->response->error->error_msg);
            if ($vk->response->response->post_id)
                $log->update('VK Post: https://vk.com/wall'.Config::$item->vk.'_'.$vk->response->response->post_id);
        }

        // publish in Telegram
        if (Config::$item->tm) {
            $tm = new Telegram($feed);
            // update log
            if ($tm->response->error_code)
                $log->update('Telegram Post: ' . $tm->response->description);
            if ($tm->response->ok)
                $log->update('Telegram Post: ' . Config::$item->tm . ':' . $tm->response->result->message_id);
        }

        return true;
    }

    public function getFeedNumber()
    {
        $feedsCount = count(Config::$item->feeds);
        $feedNumber = ($feedsCount > 1) ? $this->nextFeed($feedsCount) : 0;
        return $feedNumber;
    }

    public function nextFeed($feedsCount)
    {
        $feedNumber = $this->db->getSingle(
            'counter',
            'count',
            array('feed' => Config::$item->db)
        );

        if ((!$feedNumber) OR ($feedNumber > ($feedsCount-1))) $feedNumber = 0;

        $this->db->update(
            'counter',
            array('count' => $feedNumber+1),
            array('feed' => Config::$item->db)
        );

        return  $feedNumber;
    }

    public function checkPublished($itemID)
    {
        return $this->db->getSingle(
            Config::$item->db.'_feed',
            'id',
            array('hash' => $itemID)
        );
    }
}