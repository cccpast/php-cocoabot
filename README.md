# php-cocoabot

## 次のメッセージを送るとオウム返し以外の返答が来ます（絶対一致）

- 「確認」 確認ダイアログ表示（特別な返答を実行）
- 「検索」 検索ページを開く（google or yahoo）
- 「天気」 今日と明日の天気を返す（金沢 or 東京）

## Notes

- Herokuを無課金で利用しているので突然利用できなくなるかもしれません
- 送ることのできるメッセージの量が決まっているみたいです。大きなサイズ画像や、連続してメッセージを送ることは避けてください

## To developers

### Setting

 - [LINE developers](https://developers.line.me/ja/)で登録
 - 上記サイトで入手したアクセストークンを```constinfo.php```の```ACCESS_TOKEN```にセット
