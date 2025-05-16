<?php

$input = json_decode(file_get_contents('php://input'), true);

$chat_id = $input['message']['chat']['id'] ?? '';
$text    = $input['message']['text'] ?? '';

$token = getenv('TELEGRAM_BOT_TOKEN');

function sendMessage($chat_id, $message) {
    $url = "https://api.telegram.org/bot" . getenv('TELEGRAM_BOT_TOKEN') . "/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $message,
        'parse_mode' => 'HTML'
    ];
    file_get_contents($url . '?' . http_build_query($data));
}

// логика обработки команд
if ($text === '/start') {
    $msg = "👋 Привет! Отправь фото своей комнаты, а потом фото мебели для визуализации.\n\n";
    $msg .= "Ты можешь отправить до <b>5 фото мебели</b>, а затем фото комнаты (желательно пустой).";
    sendMessage($chat_id, $msg);
    exit;
}

// дальше — можно подключать остальную логику: загрузку, генерацию и т.д.

?>
