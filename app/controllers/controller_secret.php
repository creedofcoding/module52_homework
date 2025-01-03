<?php
class Controller_Secret extends Controller
{
    function action_index()
    {
        // Стартуем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        // Устанавливаем заголовок страницы
        $this->view->pageTitle = 'Секретная страница';
        
        if (!isset($_SESSION['user'])) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Секретная страница доступна только авторизованным пользователям.',
            ];
            header("Location: /");
            exit;
        }

        // Генерируем представление с переданными данными
        $this->view->render('secret_view.php', 'default_layout.php');
    }
}
