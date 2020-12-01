<?php
require_once "class/Tpl.php";
require_once "class/DB.php";
require_once "class/Currency.php";

if(!isset($db)) $db = new DB();
if(!isset($currency)) $currency = new Currency();

$data = $_POST;

// Стартовая страница
if(!isset($data['type'])){
    $select = Tpl::generateOptions($currency->getTypes());

    $rows = Tpl::generateTable($currency->getRates());
    $form = Tpl::render('table', [ 'rows' => $rows]);

    $header = Tpl::render('header');
    $footer = Tpl::render('footer');

    $chart = Tpl::generateChart();

    echo Tpl::render('main', ['select' => $select, 'header' => $header, 'body' => $form, 'footer' => $footer, 'chart' => $chart]);
}elseif($data['type']=="filter"){
    // Применение фильтров
    $select = Tpl::generateOptions($currency->getTypes());

    $items = $currency->getRates($data['date_start'], $data['date_end'], $data['currency_type']);

    $rows = Tpl::generateTable($items);

    $chart = Tpl::generateChart($data['date_start'], $data['date_end'], $data['currency_type']);

    echo json_encode(['content' => Tpl::render('table', [ 'rows' => $rows]), 'chart'=> $chart]);
}elseif($data['type']=="update"){
    // Ручная загрузка
    $date = strtotime($data['date']);
    $count = $currency->checkDate($date);

    if($count==0){
        $currency->update($date);
        echo json_encode(['code' => 0, 'message' => 'Данные успешно загружены']);
    }else
        echo json_encode(['code' => 1, 'message' => 'Данные уже загружены']);
}
