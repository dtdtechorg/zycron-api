<?php

require_once __DIR__ . '/Config.php';

/**
 * TelegramErrorLogger - Telegram API uchun xatoliklarni log qiladi.
 */
class TelegramErrorLogger
{
    private static $logFile;

    /**
     * Fayl nomini va joylashuvini o‘rnatish
     */
    public static function init()
    {
        $config = require __DIR__ . '/Config.php';
        self::$logFile = $config['log_file'] ?? __DIR__ . '/../logs/errors.log';
    }

    /**
     * Xatoliklarni logga yozish
     * @param array $result Telegram API natijasi
     * @param array $content Jo‘natilgan ma’lumot
     */
    public static function log($result, $content)
    {
        if (!isset($result['ok']) || $result['ok'] !== true) {
            self::init();
            $errorText = self::formatLog($result, $content);
            file_put_contents(self::$logFile, $errorText, FILE_APPEND);
        }
    }

    /**
     * Log uchun format yaratish
     * @param array $result Telegram javobi
     * @param array $content Jo‘natilgan ma’lumot
     * @return string
     */
    private static function formatLog($result, $content)
    {
        $date = date("Y-m-d H:i:s");
        $log  = "============[Date]============\n";
        $log .= "[ $date ]\n";

        $log .= "==========[Response]==========\n";
        foreach ($result as $key => $value) {
            $log .= "$key: " . json_encode($value, JSON_PRETTY_PRINT) . "\n";
        }

        $log .= "=========[Sent Data]==========\n";
        foreach ($content as $key => $value) {
            $log .= "$key: " . json_encode($value, JSON_PRETTY_PRINT) . "\n";
        }

        $log .= "================================\n\n";
        return $log;
    }
}
