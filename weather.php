<?php
require_once __DIR__ . '/constinfo.php';

class Weather {

    private $baseUrl;

    function __construct()
    {
        $this->baseUrl = ConstInfo::LIVEDOOR_WHEATHER_HACKS_API;
    }

    /**
     * 天気取得
     * @param string $area 地域名
     * @return array 天候データ
     */
    public function getWeather($area = "東京")
    {
        $url = $this->getUrl($area);
        $json = file_get_contents($url, true);
        $json = mb_convert_encoding($json, 'UTF8', 'ASCII,JIS,UTF-8,EUC-JP,SJIS-WIN');
        $weatherInfo = json_decode($json);
        return $this->formatInfo($weatherInfo);
    }

    /**
     * 地域名からIDを取得し、完成されたURLを返す
     * @param string $area 地域名
     * @return string リクエストURL
     */
    private function getUrl($area)
    {
        switch ($area) {
            case "東京":
                return $this->baseUrl . '130010';
            case "金沢":
                return $this->baseUrl . '170010';
            default:
                return $this->baseUrl . '130010';
        }
    }

    /**
     * 天候情報を理解しやすいように整形
     * @param object $weatherInfo 整形前情報
     * @return array $info 整形後情報
     */
    private function formatInfo($weatherInfo)
    {
        $info = array();
        $info["city"] = $weatherInfo->location->city;
        foreach(array($weatherInfo) as $val) {
            foreach ($val->forecasts as $key => $forecast) {
                $info[$key]["datelabel"] = $forecast->dateLabel;
                $info[$key]["weather"] = $forecast->telop;
            }
        }
        return $info;
    }
}
