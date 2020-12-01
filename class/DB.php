<?php

class DB
{
    private $host = 'db';
    private $log = 'root';
    private $pass = 'example';
    private $db;

    public function __construct($dbname='currency')
    {
        try {
            if($dbname!=null)
                $this->db = new mysqli($this->host, $this->log, $this->pass, $dbname);
            else
                $this->db = new mysqli($this->host, $this->log, $this->pass);
            $this->db->query("SET NAMES 'utf8' ");
        } catch (Exception $ex) {
            var_dump($ex);
        }
    }

    public function changeConnection($dbname){
        $this->db->close();
        $this->db = new mysqli($this->host, $this->log, $this->pass, $dbname);
        $this->db->query("SET NAMES 'utf8' ");
    }

    /**
     * Запрос к БД
     */
    public function query(){
        $args = func_get_args();
        call_user_func_array(array($this->db, "query"), $args);
    }

    /**
     * Получение всех данных запроса
     * @param $sql - Текст запроса
     * @return array
     */
    public function getAllData($sql){
        $result = $this->db->query($sql) or die("Ошибка " . $this->db->error);
        $array = [];
        while($row = $result->fetch_assoc())
            array_push($array,$row);
        return $array;
    }

    /**
     * Получение одной записи из БД
     * @param $sql - Текст запроса
     * @return array|null
     */
    public function getOneData($sql){
        $result = $this->db->query($sql) or die("Ошибка " . $this->db->error);
        $res=$result->fetch_assoc();
        return $res;
    }

    /**
     * Получение ИД последней добавленной записи
     * @return mixed
     */
    public function getLastInserted(){
        $sql="SELECT LAST_INSERT_ID() id";
        return $this->getOneData($sql)['id'];
    }

}