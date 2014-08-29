github2chatwork
===============

chatworkにPullRequestを通知します。

簡易版なので、認証やコメント等には対応していません。

#使い方

##サーバー側の設定
外部からリクエスト可能な場所にgithub2cw.phpを設置します。  
CHATWORK_APIKEY にchatworkのAPIキーを設定します。  

##GitHubの設定
GitHubの「Webhooks & Services」から、Payload URLを設定します。
http://example.com/github2cw.php?rid={RoomID}

Which events would you like to trigger this webhook?の項目で
Let me select individual events.を選択し、Pull Request にチェックをいれます。

WebhooksのRedeliver機能など適宜利用していただき、動作を確認してください。

###chatwork API
APIの利用には申請が必要となります。  
下記URLからAPIの利用申請を行ってください。  
http://developer.chatwork.com/ja/

