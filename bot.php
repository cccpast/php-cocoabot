<?php
require_once __DIR__ . '/constinfo.php';
require_once __DIR__ . '/weather.php';

class Bot {

    private $accessToken;
    private $message;
    private $replayToken;
    private $response;
    private $weather;
    private $hitWords;

    function __construct()
    {
        $this->accessToken = ConstInfo::ACCESS_TOKEN;
        $this->weather = new Weather();
        $this->hitWords = ConstInfo::HIT_WORDS;
    }

    /**
     * メッセージが送られてきたら、内容を確認し返す
     */
    public function reply()
    {
        $this->getMessage();
        $this->checkMessage();
        return $this->replyMessage();
    }

    /**
     * 送られてきたメッセージをJSON形式からPHPの適切な型へ変換、クラス変数へセット
     */
    private function getMessage()
    {
        try {
            $jsonString = file_get_contents('php://input');
            $jsonObj = json_decode($jsonString);
            $this->message = $jsonObj->{"events"}[0]->{"message"};
            $this->replyToken = $jsonObj->{"events"}[0]->{"replyToken"};
        }
        catch(Exception $e) {
            throw new Exception("function getMessage: " . $e);
        }
    }

    /**
     * 送られてきたメッセージを選別し、レスポンス形式を決める
     */
    private function checkMessage()
    {
        try {
            $messageData;
            $keyTemp = -1;
            foreach ($this->hitWords as $key => $val) {
                if ($this->message->{"text"} == $val) {
                    $messageData = $this->getMessageData($key + 1);
                    $keyTemp = $key;
                    break;
                }
            }
            if ($keyTemp === -1) {
                $messageData = $this->getMessageData(99);
            }
            error_log(print_r($messageData, true));
            $this->response = [
                'replyToken' => $this->replyToken,
                'messages' => [$messageData]
            ];
            json_encode($this->response);
        }
        catch (Exception $e) {
            throw new Exception("function checkMessage: " . $e);
        }
    }

    /**
     * cURLでメッセージを返す
     * @return array $result
     */
    private function replyMessage()
    {
        try {
            $ch = curl_init(ConstInfo::REPLY_API); // 初期化
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->response));
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json; charser=UTF-8',
                'Authorization: Bearer ' . $this->accessToken
            ));
            $result = curl_exec($ch); // 実行
            curl_close($ch);
            return $result;
        }
        catch (Exception $e) {
            throw new Exception("function replyMessage: " . $e);
        }
    }

    /**
     * メッセージデータを返す
     * @param int $msgNo 返すデータを振り分ける番号
     * @return array $messageData 返すメッセージとその形式
     */
    private function getMessageData($msgNo)
    {
        try {
            $messageData = array();
            switch($msgNo) {
                case 1:
                    $messageData = [
                        'type' => 'template',
                        'altText' => '確認ダイアログ',
                        'template' => [
                            'type' => 'confirm',
                            'text' => '元気ですか？',
                            'actions' => [
                                [
                                    'type' => 'message',
                                    'label' => '元気',
                                    'text' => '元気だよ'
                                ],
                                [
                                    'type' => 'message',
                                    'label' => 'まあまあ',
                                    'text' => 'まあまあかな'
                                ],
                            ]
                        ]
                    ];
                    return $messageData;
                case 2:
                    $messageData = [
                        'type' => 'template',
                        'altText' => '検索',
                        'template' => [
                            'type' => 'buttons',
                            'title' => 'Search',
                            'text' => '検索ページを開きます',
                            'actions' => [
                                [
                                    'type' => 'uri',
                                    'label' => 'Google',
                                    'uri' => 'https://google.com'
                                ],
                                [
                                    'type' => 'uri',
                                    'label' => 'Yahoo',
                                    'uri' => 'http://yahoo.co.jp'
                                ]
                            ]
                        ]
                    ];
                    return $messageData;
                case 3:
                    $messageData = [
                        'type' => 'text',
                        'text' => '私も元気だよっ！'
                    ];
                    return $messageData;
                case 4:
                    $messageData = [
                        'type' => 'text',
                        'text' => '「ノーポイッ」を聴くと元気になれるよ！'
                    ];
                    return $messageData;
                case 5:
                    $messageData = [
                        'type' => 'template',
                        'altText' => '天気',
                        'template' => [
                            'type' => 'buttons',
                            'title' => 'Weather',
                            'text' => '今日と明日の天候をチェック',
                            'actions' => [
                                [
                                    'type' => 'message',
                                    'label' => '金沢',
                                    'text' => '金沢の天気は？'
                                ],
                                [
                                    'type' => 'message',
                                    'label' => '東京',
                                    'text' => '東京の天気は？'
                                ]
                            ]
                        ]
                    ];
                    return $messageData;
                case 6:
                    $info = $this->weather->getWeather("金沢");
                    $messageData = [
                        'type' => 'text',
                        'text' => $info[0]["datelabel"] . 'の' .
                        $info["city"] . 'の天気は「' .
                        $info[0]["weather"] . '」だね！  ' .
                        $info[1]["datelabel"] . 'の天気は「' .
                        $info[1]["weather"] . '」だって！'
                    ];
                    return $messageData;
                case 7:
                    $info = $this->weather->getWeather("東京");
                    $messageData = [
                        'type' => 'text',
                        'text' => $info[0]["datelabel"] . 'の' .
                        $info["city"] . 'の天気は「' .
                        $info[0]["weather"] . '」だね！  ' .
                        $info[1]["datelabel"] . 'の天気は「' .
                        $info[1]["weather"] . '」だって！'
                    ];
                    return $messageData;
                case 99:
                    $messageData = [
                        'type' => 'text',
                        'text' => '「' . $this->message->{"text"} . '」だねっ！'
                    ];
                    return $messageData;
                default:
                    return $messageData;
            }
            return $messageData;
        }
        catch(Exception $e) {
            throw new Exception("function getMessageData: " . $e);
        }
    }
}
