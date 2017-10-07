<?php
require_once __DIR__ . '/weather.php';

$weather = new Weather();
$info = $weather->getWeather("金沢");
// echo $info[0]["weather"];
// var_dump($info);
