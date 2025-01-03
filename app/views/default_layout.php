<?php
// Стартуем сессию
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Проверяем, есть ли пользователь в сессии
$isLoggedIn = isset($_SESSION['user']);
$user = $isLoggedIn ? $_SESSION['user'] : null;
?>

<!DOCTYPE html>
<html lang="ru">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Главная'; ?></title>

    <!-- Иконки -->
    <link href="/assets/img/logo.png" rel="icon">
    <link href="/assets/img/logo.png" rel="apple-touch-icon">

    <!-- Vendor CSS Files -->
    <link href="/assets/vendor/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link href="/assets/vendor/bootstrap-icons/bootstrap-icons.css" rel="stylesheet">

    <link href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/css/toastr.min.css" rel="stylesheet">

    <!-- Template Main CSS File -->
    <link href="/assets/css/style.css" rel="stylesheet">
</head>

<body>
    <!-- ======= Header ======= -->
    <header id="header" class="header fixed-top d-flex align-items-center">
        <div class="d-flex align-items-center justify-content-between">
            <a href="/" class="logo d-flex align-items-center">
                <img src="assets/img/logo.png" alt="">
                <span class="d-none d-lg-block">AuthRegister</span>
            </a>
            <i class="bi bi-list toggle-sidebar-btn"></i>
        </div>

        <!-- ======= Navigation ======= -->
        <nav class="header-nav ms-auto">
            <ul class="d-flex align-items-center">
                <?php if ($isLoggedIn): ?>
                    <!-- ======= Profile Nav ======= -->
                    <li class="nav-item dropdown pe-3">
                        <a class="nav-link nav-profile d-flex align-items-center pe-0" href="#" data-bs-toggle="dropdown">
                            <img src="assets/img/profile_img.png" alt="Profile" class="rounded-circle">
                            <span class="d-none d-md-block dropdown-toggle ps-2"><?php echo htmlspecialchars($user['name']); ?></span>
                        </a><!-- End Profile Image Icon -->

                        <ul class="dropdown-menu dropdown-menu-end dropdown-menu-arrow profile">
                            <li class="dropdown-header">
                                <h6><?php echo htmlspecialchars($user['name']); ?></h6>
                                <span>Логин: <?php echo htmlspecialchars($user['login']); ?></span>
                                <hr class="mb-2 mt-2">
                                <span>Роль: <?php echo htmlspecialchars($user['role']); ?></span>
                            </li>
                            <li>
                                <hr class="dropdown-divider">
                            </li>

                            <li>
                                <a class="dropdown-item d-flex align-items-center" href="/logout">
                                    <i class="bi bi-box-arrow-right"></i>
                                    <span>Выйти</span>
                                </a>
                            </li>
                        </ul><!-- End Profile Dropdown Items -->
                    </li><!-- End Profile Nav -->
                <?php else: ?>
                    <li class="nav-item pe-3">
                        <a class="nav-link" href="/login">Вход</a>
                    </li>
                    <li class="nav-item pe-3">
                        <a class="nav-link" href="/register">Регистрация</a>
                    </li>
                <?php endif; ?>
            </ul>
        </nav><!-- End Navigation -->
    </header><!-- End Header -->

    <!-- ======= Sidebar ======= -->
    <aside id="sidebar" class="sidebar">
        <ul class="sidebar-nav" id="sidebar-nav">
            <li class="nav-item">
                <a class="nav-link collapsed" href="/">
                    <span>Главная</span>
                </a>
            </li>
            <?php if ($isLoggedIn): ?>
                <li class="nav-item">
                    <a class="nav-link collapsed" href="/secret">
                        <span>Секретная страница</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </aside><!-- End Sidebar-->

    <!-- JS Files for login and register -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Vendor JS Files -->
    <script src="assets/vendor/bootstrap/js/bootstrap.bundle.min.js"></script>

    <!-- Template Main JS File -->
    <script src="assets/js/main.js"></script>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/js/toastr.min.js"></script>

    <?php if (isset($_SESSION['notification'])): ?>
        <script>
            // Настройки уведомления
            toastr.options = {
                "closeButton": true, // Кнопка для закрытия уведомления
                "debug": false,
                "positionClass": "toast-top-right", // Позиция уведомления
                "showDuration": "300", // Продолжительность показа анимации
                "hideDuration": "1000", // Продолжительность скрытия
                "timeOut": "5000", // Время отображения уведомления
                "extendedTimeOut": "1000", // Время на закрытие при наведении
                "showEasing": "swing",
                "hideEasing": "linear",
                "showMethod": "fadeIn",
                "hideMethod": "fadeOut"
            };

            // Вывод уведомления на страницу
            toastr.<?= $_SESSION['notification']['type']; ?>(<?= json_encode($_SESSION['notification']['message']); ?>);
        </script>

        <?php unset($_SESSION['notification']); // Очистка уведомления после его отображения 
        ?>
    <?php endif; ?>

    <!-- ======= Main Content ======= -->
    <main id="main" class="main">
        <div class="container-fluid">
            <?php include $content_view; ?>
        </div>
    </main><!-- Main Content -->
</body>

</html>