<?php

namespace Feeds2Social;

use Feeds2Social\lib\MySQL;

class Install
{
    public $db;

    public function __construct()
    {
        $this->db = new Mysql();

        $this->makeCountTable();

        foreach (Config::$rawConfig as $feedConfig){
            if (isset($feedConfig['db'])) {
                $this->makeCounter($feedConfig['db']);
                $this->makeIDsTable($feedConfig['db']);
            }
        }
    }

    public function makeCountTable()
    {
        $this->db->query("CREATE TABLE counter (
								feed varchar(48) NOT NULL,
								count int(10) UNSIGNED NOT NULL DEFAULT '0')
								");
        $this->db->query("ALTER TABLE counter ADD UNIQUE KEY feed (feed)");
    }

    public function makeCounter($name)
    {
        $this->db->query("INSERT INTO counter (feed, count) VALUES ('" . $name . "', 0) ");
    }

    public function makeIDsTable($name)
    {
        $this->db->query("CREATE TABLE " . $name . "_feed (
								id int(10) UNSIGNED NOT NULL AUTO_INCREMENT PRIMARY KEY ,
								hash CHAR(32) NOT NULL,
								date timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP)
								");
    }
}