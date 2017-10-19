<?php
require_once __DIR__ . '/weather.php';
require_once __DIR__ . '/constinfo.php';

// $weather = new Weather();
// $info = $weather->getWeather("金沢");
// echo $info[0]["weather"];
// var_dump($info);

foreach (ConstInfo::HIT_WORDS as $key => $val) {
    echo ($key + 1) . " " . $val . PHP_EOL;
}
