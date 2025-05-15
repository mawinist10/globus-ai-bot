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

function generateRoomGPT($input_url, $replicate_token) {
    $postData = [
        "version" => "a9758cb5c2c54c8f88dfc426ccdc0c305701629c8a60c907a0789cce34a454b8",
        "input" => [
            "image" => $input_url,
            "prompt" => "interior room with modern furniture"
        ]
    ];

    $ch = curl_init("https://api.replicate.com/v1/predictions");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Token $replicate_token",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($postData));
    $response = json_decode(curl_exec($ch), true);
    curl_close($ch);

    $prediction_url = $response['urls']['get'] ?? null;
    if (!$prediction_url) return null;

    // Ожидаем завершения
    for ($i = 0; $i < 10; $i++) {
        sleep(5);
        $check = curl_init($prediction_url);
        curl_setopt($check, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($check, CURLOPT_HTTPHEADER, [
            "Authorization: Token $replicate_token"
        ]);
        $status = json_decode(curl_exec($check), true);
        curl_close($check);

        if (!empty($status['output'])) {
            return $status['output'][0];
        }
    }
    return null;
}

if ($text === "/start") {
    $_SESSION['photos'] = [];
    $_SESSION['room'] = null;
    sendMessage($chat_id, "Привет! Чтобы я сделал визуализацию твоей комнаты, тебе нужно:\n\n1️⃣ Загрузить до 5 фото мебели (можно скриншоты из каталога)\n📎 Каталог: https://globus.world/store?utm_source=telegram&utm_medium=bot&utm_campaign=globus_ai_catalog\n\n2️⃣ Затем — одно фото своей комнаты (желательно пустой)");
} elseif ($photo) {
    $file_id = end($photo)['file_id'];
    $info = json_decode(file_get_contents("https://api.telegram.org/bot$token/getFile?file_id=$file_id"), true);
    $path = $info['result']['file_path'];
    $url = "https://api.telegram.org/file/bot$token/$path";

    if (count($_SESSION['photos']) < 5 && !$_SESSION['room']) {
        $_SESSION['photos'][] = $url;
        sendMessage($chat_id, "✅ Фото мебели " . count($_SESSION['photos']) . " из 5 добавлено.");
        if (count($_SESSION['photos']) == 5) {
            sendMessage($chat_id, "Теперь пришли фото своей комнаты (желательно пустой).");
        }
    } elseif (!$_SESSION['room']) {
        $_SESSION['room'] = $url;
        sendMessage($chat_id, "✅ Фото комнаты получено. Генерирую визуализацию...");

        $generated = generateRoomGPT($_SESSION['room'], $replicate_token);
        if ($generated) {
            sendPhoto($chat_id, $generated);
            sendMessage($chat_id, "🖼 Визуализация готова! Спасибо за использование Globus AI Room Bot.");
        } else {
            sendMessage($chat_id, "⚠️ Ошибка генерации. Попробуй позже.");
        }

        session_destroy();
    } else {
        sendMessage($chat_id, "📌 Я уже получил все необходимые фото. Если хочешь начать заново — напиши /start.");
    }
} else {
    sendMessage($chat_id, "Пожалуйста, отправь фото или используй команду /start.");
}
?>
