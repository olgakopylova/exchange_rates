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
        $filter = "";
        $filter2 = "";
        if(isset($type)&&$type!=0){
            $filter.=" AND cd.id=".$type;
            $filter2.="WHERE cd.id=".$type;
        }
        if($dateStart!=""&&$dateEnd!=""){
            $filter.=" AND er.date>=".strtotime($dateStart)." AND er.date<=".strtotime($dateEnd);
            $filter2.=$filter;
        }
        $sql = "SELECT DISTINCT cd.id, cd.name FROM exchange_rates er 
        LEFT JOIN currencies_directory cd ON cd.id=er.id_currency ".$filter2;
        $chart = ['labels'=>[], 'datasets'=>[]];
        $items = $db->getAllData($sql);
        foreach ($items as $item){
            $sql = "SELECT from_unixtime(er.date, '%d.%m.%Y') date, er.value FROM exchange_rates er 
        LEFT JOIN currencies_directory cd ON cd.id=er.id_currency WHERE cd.id=".$item['id'].$filter." ORDER BY er.date ASC";
            $values = $db->getAllData($sql);
            $temp = ['labels'=>[],'datasets'=>['data'=>[]]];
            foreach ($values as $value){
                if(array_search($value['date'], $chart['labels'])===false)
                    array_push($chart['labels'], $value['date']);
                array_push($temp['datasets']['data'], (float)$value['value']);
            }
            $temp['datasets']['backgroundColor'] = ['rgba(0, 0, 0, 0)'];
            $temp['datasets']['borderColor'] = ['rgba('.rand(1,255).', '.rand(1,255).', '.rand(1,255).', .7)'];
            $temp['datasets']['borderWidth'] = 2;
            $temp['datasets']['label'] = $item['name'];

            array_push($chart['datasets'], $temp['datasets']);
        }
        return $chart;
    }

}