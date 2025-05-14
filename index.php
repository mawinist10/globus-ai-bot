<?php
$token = getenv('TELEGRAM_BOT_TOKEN');
$replicate_token = getenv('REPLICATE_API_TOKEN');
$update = json_decode(file_get_contents('php://input'), true);

$chat_id = $update['message']['chat']['id'] ?? null;
$text = $update['message']['text'] ?? '';
$photo = $update['message']['photo'] ?? null;

function sendMessage($chat_id, $text) {
    global $token;
    file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($text));
}

if ($text === "/start") {
    sendMessage($chat_id, "Привет! Отправь фото комнаты, а затем фото мебели (до 5 штук).");
} elseif ($photo) {
    sendMessage($chat_id, "📸 Фото получено. Сейчас сгенерирую визуализацию...");
    sleep(3);
    sendMessage($chat_id, "🖼 Вот пример визуализации: https://via.placeholder.com/600x400.png?text=RoomGPT+Preview");
} else {
    sendMessage($chat_id, "Пожалуйста, отправь фото или команду /start.");
}
?>
