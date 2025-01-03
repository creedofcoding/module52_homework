<?php
    date_default_timezone_set('Europe/Moscow');

    require_once __DIR__ . '/../vendor/autoload.php';

    use Monolog\Level;
    use Monolog\Logger;
    use Monolog\Handler\StreamHandler;
    use Monolog\Formatter\LineFormatter;

    // Получаем путь к папке из LOGS_DIR
    $logDir = dirname(LOGS_DIR);

    // Проверяем наличие папки и создаём её при необходимости
    if (!is_dir($logDir)) {
        mkdir($logDir, 0755, true); // Создаём папку с правами доступа
        //echo "Создана папка $logDir <br/>";
    }

    // Проверяем существование файла и создаём его при необходимости
    if (!file_exists(LOGS_DIR)) {
        touch(LOGS_DIR); // Создаём файл
        //echo "Создан файл " . LOGS_DIR . " <br/>";
    }

    // Настраиваем формат времени
    $dateFormat = "Y-m-d H:i:s";
    $outputFormat = "[%datetime%] %channel%.%level_name%: %message% %context% %extra%\n";

    // Создаём форматтер
    $formatter = new LineFormatter($outputFormat, $dateFormat, true, true);

    // Указываем московский часовой пояс
    $formatter->includeStacktraces(false);
    $formatter->setDateFormat($dateFormat);

    // Настраиваем обработчик
    $stream = new StreamHandler(LOGS_DIR, Level::Warning);
    $stream->setFormatter($formatter);

    // Конфигурация Monolog
    $log = new Logger('module52_homework');
    $log->pushHandler($stream);

    // Делаем логгер глобально доступным
    $GLOBALS['logger'] = $log;

    /* echo "<pre>";
    var_dump($GLOBALS['logger']);
    echo "</pre>"; */

    // Подключение основных компонентов приложения
    require_once 'core/model.php';
    require_once 'core/view.php';
    require_once 'core/controller.php';
    require_once 'core/route.php';

    // Запуск маршрутизатора
    Route::start(); // запускаем маршрутизатор