<?php

namespace Feeds2Social;

use Feeds2Social\lib\FeedParser\FeedParser;

class Parser
{
    public $image;
    public $message;
    public $title;
    public $link;
    public $itemID;
    public $found;

    public function __construct(Stream $stream)
    {
        $feed = new FeedParser($stream->url);

        $items = $feed->getItems();

        $this->getNewItem($items, $stream);

    }

    public function getNewItem($items, $stream)
    {
        foreach($items as $item) {
            $rssNode = $item->getContent();
            if($rssNode) {
                $this->itemID = $this->getItemHash($item);
                if (!$stream->checkPublished($this->itemID)) {
                    $this->image = $this->getImages($rssNode);
                    $this->title = $this->getTitle($item);
                    $this->filterMessage($rssNode);
                    $this->found = true;
                    break;
                }
            }
        }

    }

    public function getItemHash($item)
    {
        $itemUrl = $item->getLink();
        $this->link = $itemUrl;
        // clean up url for DB
        $itemUrl = preg_replace( "/https?:\/\//", "", $itemUrl );
        // hashed link for char(32) DB field
        $itemUrl = md5($itemUrl);
        return $itemUrl;
    }

    public function getImages($rssNode)
    {
        $xpath = new \DOMXPath(\DOMDocument::loadHTML($rssNode));
        $srcRaw = $xpath->query("//img[@src]");
        for( $i=0; $i < $srcRaw->length; $i++ ){
            $src[$i] = $srcRaw->item($i)->getAttribute("src");
            $src[$i] = strtok($src[$i], '?');
            $src[$i] = $this->imageFilter($src[$i]);
        }
        return $src;
    }

    public function imageFilter($src)
    {
        // for example Tumblr filter for big images
        // $src = str_replace('_500.','_1280.', $src);

        // remove GIF images
        // if (preg_match("/.gif/", $src, $match)) return null;

        return $src;
    }

    public function getTitle($item)
    {
        $title = $item->getTitle();
        $title = $this->titleFilter($title);
        return $title;

    }

    public function titleFilter($title)
    {
        $title = htmlspecialchars_decode($title);
        return $title;
    }

    public function filterMessage($message)
    {
        $message = htmlspecialchars_decode($message);
        $message = strip_tags($message);
        $message = preg_replace('/([\r\n\t])/',' ', $message);
        $message = preg_replace("/ {2,}/"," ",$message);
        $message = trim($message);
        $this->message = $message;
    }
}