<?php
$token = getenv('TELEGRAM_BOT_TOKEN');
$replicate_token = getenv('REPLICATE_API_TOKEN');
$data = json_decode(file_get_contents("php://input"), true);

$chat_id = $data['message']['chat']['id'] ?? null;
$text = $data['message']['text'] ?? '';
$photo = $data['message']['photo'] ?? null;

session_id($chat_id);
session_start();

function sendMessage($chat_id, $text) {
    global $token;
    file_get_contents("https://api.telegram.org/bot$token/sendMessage?chat_id=$chat_id&text=" . urlencode($text));
}

function sendPhoto($chat_id, $photo_url) {
    global $token;
    file_get_contents("https://api.telegram.org/bot$token/sendPhoto?chat_id=$chat_id&photo=" . urlencode($photo_url));
}

if ($text === "/start") {
    $_SESSION['photos'] = [];
    $_SESSION['room'] = null;
    sendMessage($chat_id, "Привет! Чтобы я сделал визуализацию твоей комнаты, тебе нужно:\n\n1️⃣ Загрузить до 5 фото мебели (можно скриншоты из каталога)\n📎 Каталог: https://globus.world/store?utm_source=telegram&utm_medium=bot&utm_campaign=globus_ai_catalog\n\n2️⃣ Затем — одно фото своей комнаты (желательно пустой)");
} elseif ($photo) {
    $file_id = end($photo)['file_id'];
    $file_info = json_decode(file_get_contents("https://api.telegram.org/bot$token/getFile?file_id=$file_id"), true);
    $file_path = $file_info['result']['file_path'];
    $file_url = "https://api.telegram.org/file/bot$token/$file_path";

    if (count($_SESSION['photos']) < 5 && !$_SESSION['room']) {
        $_SESSION['photos'][] = $file_url;
        sendMessage($chat_id, "✅ Фото мебели " . count($_SESSION['photos']) . " из 5 добавлено.");
        if (count($_SESSION['photos']) == 5) {
            sendMessage($chat_id, "Теперь пришли фото своей комнаты (желательно пустой).");
        }
    } elseif (!$_SESSION['room']) {
        $_SESSION['room'] = $file_url;
        sendMessage($chat_id, "✅ Фото комнаты получено. Генерирую визуализацию...");

        // Вызов RoomGPT (заглушка)
        $output_image = "https://via.placeholder.com/800x600.png?text=RoomGPT+Generated";

        sendPhoto($chat_id, $output_image);
        sendMessage($chat_id, "🖼 Визуализация готова! Спасибо за использование Globus AI Room Bot.");

        session_destroy();
    } else {
        sendMessage($chat_id, "📌 Я уже получил все необходимые фото. Если хочешь начать заново — напиши /start.");
    }
} else {
    sendMessage($chat_id, "Пожалуйста, отправь фото или используй команду /start.");
}
?>
