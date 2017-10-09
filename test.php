<?php
require_once __DIR__ . '/weather.php';
/**
 * 単体テスト用ファイル
 * 後々PHPUnitに移行するかも？
 */
$weather = new Weather();
$info = $weather->getWeather("金沢");
// echo $info[0]["weather"];
// var_dump($info);
