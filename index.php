<?php
$token = getenv('TELEGRAM_BOT_TOKEN');
$input = file_get_contents('php://input');
$update = json_decode($input, true);

$chat_id = $update['message']['chat']['id'] ?? null;
$text = $update['message']['text'] ?? '';

if ($text === '/start' && $chat_id) {
    $message = "Привет! Я помогу тебе визуализировать комнату с мебелью Globus.";
    file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($message));
}
?>
