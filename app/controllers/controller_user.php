<?php
class Controller_User extends Controller
{
    public function action_register()
    {
        // Стартуем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Вы уже вошли в аккаунт.',
            ];
            header("Location: /");
            exit;
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Устанавливаем заголовок страницы
        $this->view->pageTitle = 'Регистрация';

        // Проверяем, что запрос POST
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if ($csrfToken !== $_SESSION['csrf_token']) {
                http_response_code(403);
                exit();
            }

            $login = trim($_POST['login'] ?? '');
            $name = trim($_POST['name'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $role = 'user'; // Роль по умолчанию

            // Обращаемся к модели для регистрации
            $userModel = new Model_User();

            // Проверка существования таблицы users
            if ($userModel->checkUsersTableExists() === false) {
                http_response_code(500); // Устанавливаем статус ответа 500
                exit();
            }

            if ($userModel->checkUserExists($login)) {
                echo json_encode(
                    [
                        'success' => false,
                        'message' => 'Этот login уже зарегистрирован',
                        'errors' => [
                            'login' => 'Этот login уже зарегистрирован'
                        ]
                    ],
                    JSON_UNESCAPED_UNICODE
                );
                exit();
            }

            $userId = $userModel->registerUser($login, $name, $password, $role);

            echo json_encode(
                [
                    'success' => true,
                    'message' => 'Вы успешно зарегистрировались'
                ],
                JSON_UNESCAPED_UNICODE
            );

            // В контроллере при успешной регистрации
            $_SESSION['notification'] = [
                'type' => 'success', // Тип уведомления ('success', 'error', 'info', 'warning')
                'message' => 'Вы успешно зарегистрировались!' // Сообщение уведомления
            ];

            // Записываем в сессию с ролью
            $this->storeUserInSession($userId, $login, $name, $role);

            exit();
        }

        // Генерируем представление с переданными данными
        $this->view->render('register_view.php', 'default_layout.php');
    }

    public function action_login()
    {
        // Стартуем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Вы уже вошли в аккаунт.',
            ];
            header("Location: /");
            exit;
        }

        if (!isset($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        // Устанавливаем заголовок страницы
        $this->view->pageTitle = 'Вход';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $csrfToken = $_POST['csrf_token'] ?? '';
            if ($csrfToken !== $_SESSION['csrf_token']) {
                http_response_code(403);
                exit('CSRF token mismatch');
            }

            $login = trim($_POST['login'] ?? '');
            $password = trim($_POST['password'] ?? '');

            $userModel = new Model_User();

            // Проверка существования таблицы users
            if ($userModel->checkUsersTableExists() === false) {
                http_response_code(500); // Устанавливаем статус ответа 500
                exit();
            }

            $loginResult = $userModel->loginUser($login, $password);

            if (!$loginResult['success']) {
                //$GLOBALS['logger']->error($loginResult['message'], []);

                echo json_encode(
                    [
                        'success' => false,
                        'message' => $loginResult['message']
                    ],
                    JSON_UNESCAPED_UNICODE
                );
                exit();
            }

            // Сохраняем пользователя в сессию
            $this->storeUserInSession(
                $loginResult['user']['id'],
                $loginResult['user']['login'],
                $loginResult['user']['name'],
                $loginResult['user']['role'] // Передаём роль
            );

            echo json_encode(
                [
                    'success' => true,
                    'message' => 'Вы успешно вошли'
                ],
                JSON_UNESCAPED_UNICODE
            );

            $_SESSION['notification'] = [
                'type' => 'success', // Тип уведомления ('success', 'error', 'info', 'warning')
                'message' => 'Вы успешно вошли!' // Сообщение уведомления
            ];

            exit();
        }

        // Генерируем представление
        $this->view->render('login_view.php', 'default_layout.php');
    }

    public function action_auth_vk()
    {
        // Стартуем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (isset($_SESSION['user'])) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Вы уже вошли в аккаунт.',
            ];
            header("Location: /");
            exit;
        }

        $userModel = new Model_User();

        // Проверка существования таблицы users
        if ($userModel->checkUsersTableExists() === false) {
            http_response_code(500); // Устанавливаем статус ответа 500
            
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Ошибка авторизации через ВК: Internal Server Error',
            ];

            $GLOBALS['logger']->error("Ошибка авторизации через ВК: Internal Server Error", []);
            
            header("Location: /login");
            exit();
        }

        if (REDIRECT_URL === null) {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Ссылка REDIRECT_URL равна null. Пожалуйста, запустите ngrok.',
            ];

            $GLOBALS['logger']->error('Глобальная переменная REDIRECT_URL принимает значение null в action_auth_vk', [
                'redirect_url' => REDIRECT_URL,
            ]);

            header("Location: /login");
            exit;
        }

        // 1) Если нет code, отправляем пользователя на oauth.vk.com
        if (!isset($_GET['code'])) {
            $params = [
                'client_id'     => VK_CLIENT_ID, // константы или config
                'redirect_uri'  => REDIRECT_URL . '/auth_vk', //динамический адрес
                'display'       => 'page',
                'scope'         => 'email',
                'response_type' => 'code',
                'v'             => '5.131',
            ];
            $url = 'https://oauth.vk.com/authorize?' . http_build_query($params);
            header("Location: $url");
            exit();
        }

        // 2) Если code есть — это значит, что ВК вернул пользователя с кодом
        $code = $_GET['code'];

        // Обмен кода на access_token
        $params = [
            'client_id'     => VK_CLIENT_ID,
            'client_secret' => VK_CLIENT_SECRET,
            'redirect_uri'  => REDIRECT_URL . '/auth_vk', //динамический адрес
            'code'          => $code,
        ];

        $tokenResponse = file_get_contents('https://oauth.vk.com/access_token?' . http_build_query($params));
        $tokenData = json_decode($tokenResponse, true);

        if (!isset($tokenData['access_token'])) {
            $_SESSION['notification'] = [
                'type' => 'error', // Тип уведомления ('success', 'error', 'info', 'warning')
                'message' => 'Ошибка авторизации через ВК!' // Сообщение уведомления
            ];
            $GLOBALS['logger']->error('Ошибка авторизации через ВК!', [
                'access_token' => $tokenData['access_token'],
            ]);
            exit();
        }

        $accessToken = $tokenData['access_token'];
        $userId      = $tokenData['user_id'];
        $login       = isset($tokenData['email']) ? $tokenData['email'] : 'vkuser_' . $userId;

        // 3) Запрашиваем доп. данные (например, имя, аватар)
        $userInfoResponse = file_get_contents('https://api.vk.com/method/users.get?' . http_build_query([
            'user_ids'      => $userId,
            'access_token'  => $accessToken,
            'v'             => '5.131',
        ]));
        $userInfo = json_decode($userInfoResponse, true);

        // Предположим, что пришел массив userInfo['response'][0] с данными о пользователе
        $vkData = $userInfo['response'][0] ?? null;
        if (!$vkData) {
            $_SESSION['notification'] = [
                'type' => 'error', // Тип уведомления ('success', 'error', 'info', 'warning')
                'message' => 'Не удалось получить данные пользователя ВК!' // Сообщение уведомления
            ];
            $GLOBALS['logger']->error('Не удалось получить данные пользователя ВК!', [
                'vk_data' => $vkData,
            ]);
            exit();
        }

        // 4) Смотрим, есть ли пользователь в БД с таким логином (если логин вернулся от ВК)
        $existingUser = $userModel->getUserByLogin($login);

        // Если пользователя нет, регистрируем (роль = 'vk_user')
        if (!$existingUser) {
            $nameForDb = $vkData['first_name'] . ' ' . $vkData['last_name'];

            $newUserId = $userModel->registerUser(
                $login,
                $nameForDb,
                '',           // пустой пароль
                'vk_user'     // роль
            );

            $existingUser = $userModel->getUserById($newUserId);
        }

        // 5) Сохраняем в сессию
        $this->storeUserInSession(
            $existingUser['id'],
            $existingUser['login'],
            $existingUser['name'],
            $existingUser['role']
        );

        // ... плюс можно записывать аватарку/ссылку на профайл, если надо ...

        // 6) Редиректим
        $_SESSION['notification'] = [
            'type' => 'success',
            'message' => 'Вы успешно авторизовались через ВК!'
        ];
        header("Location: /");
        exit();
    }

    public function action_logout()
    {
        // Стартуем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        //проверка на существующую сессию
        if (!isset($_SESSION['user'])) {
            // Если пользовательской сессии нет, создаем уведомление об ошибке
            $_SESSION['notification'] = [
                'type' => 'error', // Тип уведомления ('success', 'error', 'info', 'warning')
                'message' => 'Вы не авторизованы!' // Сообщение уведомления
            ];
            header('Location: /');
            exit();
        }

        echo json_encode(
            [
                'success' => true,
                'message' => 'Вы успешно вышли!'
            ],
            JSON_UNESCAPED_UNICODE
        );

        $_SESSION['notification'] = [
            'type' => 'success', // Тип уведомления ('success', 'error', 'info', 'warning')
            'message' => 'Вы успешно вышли!' // Сообщение уведомления
        ];

        // Удаляем данные пользователя из сессии
        unset($_SESSION['user']);

        // Перенаправляем на главную страницу
        header("Location: /");
        exit();
    }

    public function action_log_error()
    {
        // Стартуем сессию
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json');
            //$GLOBALS['logger']->error('Тестовая ошибка для проверки логгера.');

            $rawData = file_get_contents('php://input');
            $data = json_decode($rawData, true);

            if (!empty($data['error'])) {
                $context = ['error' => $data['error']];
                if (!empty($data['login'])) {
                    $context['login'] = $data['login'];
                }
                if (!empty($data['password'])) {
                    $context['password'] = $data['password'];
                }

                $GLOBALS['logger']->error($data['error'], [
                    'login' => $context['login'],
                    'password' => $context['password'],
                ]);

                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Ошибка не передана.']);
            }

            exit();
        } else {
            $_SESSION['notification'] = [
                'type' => 'error',
                'message' => 'Страница логирования ошибок недоступна для прямого перехода.',
            ];
            header('Location: /');
            exit();
        }
    }

    private function storeUserInSession($id, $login, $name, $role)
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION['user'] = [
            'id' => $id,
            'login' => $login,
            'name' => $name,
            'role' => $role // Сохраняем роль
        ];
    }
}
