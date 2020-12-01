<?php
require_once "class/Currency.php";

if(!isset($currency)) $currency = new Currency();

$file = $_SERVER['DOCUMENT_ROOT']."/exchange_rates/files/".time().".json";
$handle=fopen($file, "w");
$items = $currency->getRates();
fwrite($handle, json_encode($items));
fclose($handle);
if (file_exists($file)) {
    // сбрасываем буфер вывода PHP, чтобы избежать переполнения памяти выделенной под скрипт
    if (ob_get_level()) {
        ob_end_clean();
    }
    // окно сохранения файла
    header('Content-Description: File Transfer');
    header('Content-Type: application/octet-stream');
    header('Content-Disposition: attachment; filename=' . basename($file));
    header('Content-Transfer-Encoding: binary');
    header('Expires: 0');
    header('Cache-Control: must-revalidate');
    header('Pragma: public');
    header('Content-Length: ' . filesize($file));
    readfile($file);
    exit;
} else {
    die($file);
}