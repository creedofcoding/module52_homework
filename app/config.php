<?php
function isUrlAvailable($url)
{
    $ch = curl_init($url);

    curl_setopt($ch, CURLOPT_NOBODY, true); // Не загружать тело ответа
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Тайм-аут в секундах
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1); // Тайм-аут соединения
    curl_setopt($ch, CURLOPT_FAILONERROR, true); // Считать ошибки HTTP как сбой

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    return $httpCode === 200;
}

function getNgrokUrl()
{
    $url = 'http://127.0.0.1:4040/api/tunnels';

    if (!isUrlAvailable($url)) {
        return null;
    }

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1); // Тайм-аут в секундах

    $response = curl_exec($ch);
    curl_close($ch);

    if ($response === false) {
        return null;
    }

    $data = json_decode($response, true);
    if (!isset($data['tunnels'][0]['public_url'])) {
        return null;
    }

    return $data['tunnels'][0]['public_url'];
}

define('DATABASE', 'sqlite:' . realpath(__DIR__ . '/database/database.db')); //путь к БД
define('LOGS_DIR', __DIR__ . '/logs/logs.log');

//для авторизации при помощи ВК
define('VK_CLIENT_ID', свой_VK_CLIENT_ID);
define('VK_CLIENT_SECRET', 'свой_VK_CLIENT_SECRET');

//автоматически генерируется ссылка для редиректа через ngrok
$ngrokUrl = getNgrokUrl();
define('REDIRECT_URL', $ngrokUrl);