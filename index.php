<?php

$botToken = getenv('TELEGRAM_BOT_TOKEN');
$input = json_decode(file_get_contents('php://input'), true);

$chat_id = $input['message']['chat']['id'] ?? '';
$text = $input['message']['text'] ?? '';
$photo = $input['message']['photo'] ?? null;
$user_state_file = __DIR__ . '/sessions/' . $chat_id . '.json';

// === Утилита для отправки сообщений
function sendMessage($chat_id, $text, $buttons = null) {
    $url = "https://api.telegram.org/bot" . getenv('TELEGRAM_BOT_TOKEN') . "/sendMessage";
    $data = [
        'chat_id' => $chat_id,
        'text' => $text,
        'parse_mode' => 'HTML'
    ];
    if ($buttons) {
        $data['reply_markup'] = json_encode([
            'inline_keyboard' => $buttons
        ]);
    }
    file_get_contents($url . '?' . http_build_query($data));
}

// === Сохраняем состояние пользователя
function setUserState($chat_id, $state) {
    global $user_state_file;
    file_put_contents($user_state_file, json_encode(['state' => $state]));
}

// === Получаем текущее состояние
function getUserState($chat_id) {
    global $user_state_file;
    if (file_exists($user_state_file)) {
        $data = json_decode(file_get_contents($user_state_file), true);
        return $data['state'] ?? 'start';
    }
    return 'start';
}

// === Команда /start
if ($text === '/start') {
    sendMessage($chat_id, "👋 Привет! Я помогу тебе визуализировать комнату с мебелью Globus.\n\nПожалуйста, отправь до 5 фото мебели, которую ты хочешь добавить в комнату.\n\nКогда будешь готов — просто отправь фото своей комнаты.");
    setUserState($chat_id, 'wait_furniture');
    exit;
}

// === Фото мебели
if ($photo && getUserState($chat_id) === 'wait_furniture') {
    // Здесь можно сохранять ссылки или file_id в сессию
    sendMessage($chat_id, "📸 Мебель добавлена. Теперь пришли фото комнаты (желательно пустой).");
    setUserState($chat_id, 'wait_room');
    exit;
}

// === Фото комнаты
if ($photo && getUserState($chat_id) === 'wait_room') {
    // Здесь можно отправить на генерацию
    sendMessage($chat_id, "✅ Визуализация готова!\n\nПосмотреть PDF и другие опции:", [
        [['text' => '🖼 Получить PDF', 'callback_data' => 'get_pdf']],
        [['text' => '📞 Связаться с менеджером', 'url' => 'https://t.me/globus_furniture']],
        [['text' => '🔁 Сгенерировать заново', 'callback_data' => 'restart']]
    ]);
    setUserState($chat_id, 'done');
    exit;
}

// === Ответ по умолчанию
if ($text !== '' && getUserState($chat_id) === 'start') {
    sendMessage($chat_id, "Напиши /start для начала.");
}
?>
