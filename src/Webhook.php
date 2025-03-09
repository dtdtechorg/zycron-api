<?php

require_once __DIR__ . '/src/Config.php';
require_once __DIR__ . '/src/Telegram.php';
require_once __DIR__ . '/src/TelegramErrorLogger.php';

// Telegram webhookdan kelgan JSON so‘rovni olish
$update = json_decode(file_get_contents("php://input"), true);

// Agar bo‘sh kelgan bo‘lsa, chiqib ketamiz
if (!$update) {
    TelegramErrorLogger::log(['ok' => false, 'error' => 'Empty request'], []);
    exit;
}

// Telegram obyektini yaratamiz
$config = require __DIR__ . '/src/Config.php';
$telegram = new Telegram($config['token']);

// Webhookdan kelgan xabarni qayta ishlash
try {
    if (isset($update['message'])) {
        $message = $update['message'];
        $chatId = $message['chat']['id'] ?? null;
        $text = $message['text'] ?? '';

        if ($chatId) {
            if ($text === "/start") {
                $response = $telegram->sendMessage($chatId, "Salom! Xush kelibsiz.");
            } else {
                $response = $telegram->sendMessage($chatId, "Siz yozdingiz: $text");
            }
            TelegramErrorLogger::log($response, $update);
        }
    }
} catch (Exception $e) {
    TelegramErrorLogger::log(['ok' => false, 'error' => $e->getMessage()], $update);
}

