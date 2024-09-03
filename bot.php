<?php
require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
use Telegram\Bot\Api;
use Telegram\Bot\FileUpload\InputFile;

$dotenv = Dotenv::createImmutable(__DIR__);
$dotenv->load();

$telegram = new Api($_ENV['TELEGRAM_BOT_TOKEN']);

while (true) {
    $updates = $telegram->getUpdates(['timeout' => 30]);

    foreach ($updates as $update) {
        $message = $update->getMessage();
        $chatId = $message->getChat()->getId();
        $text = $message->getText();

        switch ($text) {
            case '/buy_tits':
                $imageUrl = getRandomImageUrl("http://api.oboobs.ru/boobs/1/1/random", "http://media.oboobs.ru/boobs/");
                break;
            case '/buy_butts':
                $imageUrl = getRandomImageUrl("http://api.obutts.ru/butts/1/1/random", "http://media.obutts.ru/butts/");
                break;
            default:
                $imageUrl = null;
        }

        if ($imageUrl !== null) {
            $photo = InputFile::create(fopen($imageUrl, 'r'), basename($imageUrl));
            $telegram->sendPhoto([
                'chat_id' => $chatId,
                'photo' => $photo
            ]);
        }
    }

    $lastUpdate = end($updates);
    if ($lastUpdate) {
        $telegram->getUpdates(['offset' => $lastUpdate->getUpdateId() + 1]);
    }
}

function getRandomImageUrl($apiUrl, $mediaUrl) {
    $retries = 3;
    $imageUrl = null;

    while ($retries > 0) {
        $retries--;

        $response = json_decode(file_get_contents($apiUrl), true);
        $imageUrl = $mediaUrl . sprintf("%05d", $response[0]['id']) . ".jpg";

        if (@fopen($imageUrl, "r") !== false) {
            break;
        }
    }

    return $imageUrl;
}