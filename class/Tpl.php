<?php
require_once "DB.php";
class Tpl
{
    /**
     * Формирование страницы из шаблона
     * @param $tmp - Название шаблона
     * @param array $vars - Параметры для шаблона
     * @return false|string
     */
    public static function render($tmp, $vars = []) {
        if(file_exists('tpl/'.$tmp.'.tpl')) {
            ob_start();
            extract($vars);
            require 'tpl/'.$tmp.'.tpl';
            return ob_get_clean();
        }
    }

    /**
     * Формирование options
     * @param $params - Массив параметров для options
     * @return string
     */
    public static function generateOptions($params){
        $result="";
        foreach ($params as $key=>$param)
            $result.="<option value='".$param['id']."'>".$param['name']."</option>";
        return $result;
    }

    /**
     * Формирование таблицы
     * @param $params - Массив параметров для таблицы
     * @return string
     */
    public static function generateTable($params){
        $result="";
        foreach ($params as $key=>$param)
            $result.="<tr><td>".$param['id']."</td><td>".$param['name']."</td><td>".$param['nominal'].
                "</td><td>".$param['date']."</td><td>".$param['value']."</td></tr>";
        return $result;
    }

    /**
     * Формирование графика
     * @param null $dateStart - Дата начала периода
     * @param null $dateEnd - Дата окончания периода
     * @param null $type - тип валюты
     * @return array
     */
    public static function generateChart($dateStart = null, $dateEnd = null, $type = null){
        $db = new DB();
        $sql = "SELECT DISTINCT er.id, cd.name FROM exchange_rates er 
        LEFT JOIN currencies_directory cd ON cd.id=er.id_currency";
        $chart = [];
        $items = $db->getAllData($sql);
        $filter = "";
        if(isset($type)&&$type!=0)
            $filter.=" AND cd.id=".$type;
        if($dateStart!=""&&$dateEnd!=""){
            $filter.=" AND er.date>=".strtotime($dateStart)." AND er.date<=".strtotime($dateEnd);
        }
        foreach ($items as $item){
            $sql = "SELECT from_unixtime(er.date, '%d.%m.%Y') date, er.value FROM exchange_rates er 
        LEFT JOIN currencies_directory cd ON cd.id=er.id_currency WHERE er.id=".$item['id'].$filter;
            $values = $db->getAllData($sql);
            $temp = ['labels'=>[],'datasets'=>['data'=>[]]];
            foreach ($values as $value){
                array_push($temp['labels'], $value['date']);
                array_push($temp['datasets']['data'], (float)$value['value']);
            }
            $temp['datasets']['backgroundColor'] = ['rgba(105, 0, 132, .2)'];
            $temp['datasets']['borderColor'] = ['rgba(200, 99, 132, .7)'];
            $temp['datasets']['borderWidth'] = 2;
            $temp['datasets']['label'] = $item['name'];
            array_push($chart, $temp);
        }
        return $chart;
    }

}