<?php
class View
{
    public $pageTitle; // Динамический заголовок страницы

    function render($content_view, $template_view, $data = null)
    {
        // Делаем переменные объекта доступными в шаблоне
        extract(get_object_vars($this));

        // Если данные переданы, делаем их доступными в шаблоне
        if (is_array($data)) {
            extract($data);
        }

        $template_view_path = __DIR__ . '/../views/' . $template_view;

        if (file_exists($template_view_path)) {
            //echo 'Файл найден<br/>';
            include $template_view_path;
        } else {
            echo 'Файл представления не найден<br/>';
        }
    }
}
