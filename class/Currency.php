<?php
require_once "DB.php";

class Currency
{
    private $db;
    public function __construct()
    {
        $this->db = new DB();
    }

    /**
     * Получение все типов валют
     * @return array
     */
    public function getTypes(){
        return $this->db->getAllData("SELECT id, name FROM currencies_directory");
    }

    /**
     * Получение курса валют
     * @param null $dateStart - Дата начала периода
     * @param null $dateEnd - Дата окончания периода
     * @param null $type - тип валюты
     * @return array
     */
    public function getRates($dateStart = null, $dateEnd = null, $type = null){
        $filter = "";
        if(isset($type)&&$type!=0&&$type!=null)
            $filter.=" WHERE cd.id=".$type;
        if($dateStart!=""&&$dateStart!=null&&$dateEnd!=""&&$dateEnd!=null){
            $filter.=$filter==""?" WHERE ":" AND";
            $filter.=" er.date>=".strtotime($dateStart)." AND er.date<=".strtotime($dateEnd);
        }
        $sql = "SELECT er.id, cd.name, er.nominal, from_unixtime(er.date, '%d.%m.%Y') date, er.value FROM 
            exchange_rates er LEFT JOIN currencies_directory cd ON cd.id=er.id_currency". $filter;
        return $this->db->getAllData($sql);
    }

    /**
     * Проверка даты на наличие в таблице
     * @param $date - Проверяемая дата
     * @return mixed
     */
    public function checkDate($date){
        $sql = "SELECT COUNT(*) cn FROM exchange_rates where date=".$date;
        return $this->db->getOneData($sql)['cn'];
    }

    /**
     * Ручная загрузка
     * @param $date - Загружаемая дата
     */
    public function update($date){
        $day = date("d", $date);
        $month = date("m", $date);
        $year = date("Y", $date);

        $data = file_get_contents("http://www.cbr.ru/scripts/XML_daily.asp?date_req=".$day."/".$month."/".$year);
        $xml = new SimpleXMLElement($data);
        foreach ($xml->children() as $item){
            $id = $this->db->getOneData("SELECT id FROM currencies_directory WHERE id_currency
                LIKE '%".$item->attributes()['ID']->__toString()."%'")['id'];
            foreach ($item as $element){
                if($element->getName()=="Nominal")
                    $nominal = $element->__toString();
                if($element->getName()=="Value")
                    $value = $element->__toString();

            }
            $this->db->query(sprintf("INSERT INTO `exchange_rates`(`id_currency`, `nominal`, `date`, `value`) VALUES
                (".$id.",". $nominal.",".  strtotime($year."-".$month."-".$day).",".
                (float)str_replace(',','.',$value).")"));
        }
    }
}