<?php

namespace Feeds2Social\lib;

use Feeds2Social\Config;

class MySQL
{
    private $link;

    public function __construct()
    {
        $this->connection();
    }

    public function __destruct(){
        $this->link->close();
    }

    public function connection()
    {
        $this->link = new \mysqli(
            Config::$item->host,
            Config::$item->user,
            Config::$item->password,
            Config::$item->database
        );

        if ($this->link->connect_errno) {
            throw new Exception("Connection Error: (" . $this->link->connect_errno . ") " . $this->link->connect_error);
        }
    }

    public function query($queryString)
    {
        return $this->link->query($queryString);
    }

    public function insert($table, $params=array())
    {
        $params = $this->sanitize($params);
        $queryString = "INSERT INTO " . $table
            . " (".implode(", ", array_keys($params))
            . ") VALUES ('".implode("', '", $params)
            ."')";
        $this->query($queryString);
    }

    public function get($table, $rows = '*', $where = array(), $order = null, $limit = null)
    {
        $where = $this->sanitize($where);
        $argsWhere = $this->formatArrayToSQL($where);
        if ($where) $where = " WHERE ".$argsWhere;
        if ($order) $order = " ORDER BY ".$order;
        if ($limit) $limit = " LIMIT ".$limit;
        $queryString = "SELECT ".$rows." FROM ".$table.$where.$order.$limit;

        $rawResult = $this->query($queryString);

        if ($rawResult) {
            $resultRows = $rawResult->num_rows;

            for ($i = 0; $i < $resultRows; $i++) {
                $resultArray = $rawResult->fetch_array();

                $key = array_keys($resultArray);

                for ($x = 0; $x < count($key); $x++) {
                    if (!is_int($key[$x])) {
                        if ($rawResult->num_rows >= 1) {
                            $result[$i][$key[$x]] = $resultArray[$key[$x]];
                        } else {
                            $result[$i][$key[$x]] = null;
                        }
                    }
                }
            }
            return $result;
        }
        return false;
    }

    public function getSingle($table, $rows, $where = array())
    {
           $arrayResult = $this->get($table, $rows, $where);
           $result = $arrayResult[0][$rows];
           return $result;
    }

    public function update($table, $set=array(), $where=array())
    {
        $argsSet = $this->formatArrayToSQL($set);
        $argsWhere = $this->formatArrayToSQL($where);
        $queryString = "UPDATE ".$table." SET ".$argsSet." WHERE ".$argsWhere;
        $this->query($queryString);
    }

    public function sanitize($data)
    {
        if (!is_array($data)) {
            $data = $this->link->real_escape_string($data);
            $data = trim(htmlentities($data, ENT_QUOTES, 'UTF-8', false));
        } else {
            $data = array_map(array($this, 'sanitize'), $data);
        }
        return $data;
    }

    public function formatArrayToSQL($params=array())
    {
        $args=array();
        foreach($params as $field=>$value){
            $args[]=$field.'="'.$value.'"';
        }
        $stringSQL = implode(',',$args);
        return $stringSQL;
    }
}