<?php
/**
 * メール送信関数
 */
function sendOrderConfirmationEmail($userEmail, $userName, $foodName, $orderId = null) {
    $orderId = $orderId ?? rand(10000, 99999);
    
    $subject = "【FoodDelivery】ご注文確認";
    
    $message = "
{$userName} 様

この度は、FoodDeliveryをご利用いただき、誠にありがとうございます。

ご注文が正常に受け付けられました。

■ ご注文内容 ■
注文番号: {$orderId}
料理名: {$foodName}
注文日時: " . date('Y年m月d日 H:i') . "

■ 配達予定時間 ■
約30-40分後にお届け予定です。

配達状況につきましては、別途ご連絡いたします。

何かご不明な点がございましたら、お気軽にお問い合わせください。

────────────────────────
FoodDelivery カスタマーサポート
Email: support@fooddelivery.com
Tel: 03-1234-5678
────────────────────────
";

    $headers = array(
        'From' => 'noreply@fooddelivery.com',
        'Reply-To' => 'support@fooddelivery.com',
        'X-Mailer' => 'PHP/' . phpversion(),
        'Content-Type' => 'text/plain; charset=UTF-8'
    );

    // 開発環境では実際にメールを送信せず、ログファイルに記録
    $logFile = '/var/www/html/secure/mail_log.txt';
    $logContent = "
=== メール送信ログ ===
送信日時: " . date('Y-m-d H:i:s') . "
宛先: {$userEmail}
件名: {$subject}
本文:
{$message}
ヘッダー: " . json_encode($headers) . "
========================

";
    
    file_put_contents($logFile, $logContent, FILE_APPEND);
    
    // 実際の本番環境では以下のコードを使用してメールを送信
    return mail($userEmail, $subject, $message, $headers);
    
    // 開発環境では常にtrueを返す
    #return true;
}

/**
 * 新規登録確認メール送信
 */
function sendRegistrationEmail($userEmail, $userName) {
    $subject = "【FoodDelivery】会員登録完了のお知らせ";
    
    $message = "
{$userName} 様

FoodDeliveryへのご登録、誠にありがとうございます。

アカウントの作成が完了いたしました。
今後とも、FoodDeliveryをよろしくお願いいたします。

■ ご利用方法 ■
1. ログインページからログイン
2. お好みの料理を検索
3. 注文ボタンをクリック
4. 美味しい料理をお楽しみください！

何かご不明な点がございましたら、お気軽にお問い合わせください。

────────────────────────
FoodDelivery カスタマーサポート
Email: support@fooddelivery.com
Tel: 03-1234-5678
────────────────────────
";

    $headers = array(
        'From' => 'noreply@fooddelivery.com',
        'Reply-To' => 'support@fooddelivery.com',
        'X-Mailer' => 'PHP/' . phpversion(),
        'Content-Type' => 'text/plain; charset=UTF-8'
    );

    // 開発環境ではログファイルに記録
    $logFile = '/var/www/html/secure/mail_log.txt';
    $logContent = "
=== 登録確認メール送信ログ ===
送信日時: " . date('Y-m-d H:i:s') . "
宛先: {$userEmail}
件名: {$subject}
本文:
{$message}
ヘッダー: " . json_encode($headers) . "
============================

";
    
    file_put_contents($logFile, $logContent, FILE_APPEND);
    
    return true;
}
?>