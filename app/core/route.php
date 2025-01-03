<?php
class Route
{
    public static function start()
    {
        function ErrorPage404()
        {
            header("HTTP/1.0 404 Not Found");
            $pageTitle = '404 - Страница не найдена';
            $content_view = __DIR__ . '/../views/error404_view.php'; // Путь до шаблона 404
            include __DIR__ . '/../views/default_layout.php'; // Подключаем главный шаблон
            exit();
        }

        // Очищаем кеш
        //clearstatcache();

        // контроллер и действие по умолчанию
        $controller_name = 'Main';
        $action_name = 'index';
        $routes = isset($_GET['url']) ? $_GET['url'] : '';

        if (in_array($routes, ['register', 'login', 'logout', 'auth_vk', 'log_error'])) {
            $controller_name = 'Controller_user';
            $action_name = 'action_' . $routes; // Преобразуем в нужное действие
            $model_name = 'model_user';
        } else {
            // получаем имя контроллера
            if (!empty($routes)) {
                $controller_name = $routes;
            }

            // добавляем префиксы
            $model_name = 'model_' . $controller_name;
            $controller_name = 'Controller_' . $controller_name;
            $action_name = 'action_' . $action_name;
        }

        // подцепляем файл с классом модели (файла модели может и не быть)
        $model_file = strtolower($model_name) . '.php';
        $model_path = __DIR__ . "/../models/" . $model_file;

        if (file_exists($model_path)) {
            //echo "Файл модели $model_name найден<br/>";
            include $model_path;
        } else {
            //echo "Файл модели $model_name не найден<br/>";
        }

        $controller_name = trim($controller_name); // Убираем пробелы
        $controller_file = strtolower($controller_name) . '.php';

        $controller_path = __DIR__ . "/../controllers/" . $controller_file;

        if (file_exists($controller_path)) {
            //echo 'Файл ' . $controller_path . ' найден<br/>';
            include $controller_path;
        } else {
            echo 'Файл контроллера не найден<br/>';
            ErrorPage404();
        }

        // создаем контроллер
        $controller = new $controller_name;

        if (method_exists($controller, $action_name)) {
            //echo 'Действие контроллера существует: ' . $action_name . '<br/>';
            // вызываем действие контроллера
            $controller->$action_name();
        } else {
            //echo 'Действие контроллера не найдено<br/>';
            ErrorPage404();
        }
    }
}
