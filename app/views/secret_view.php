<?php

// Получение роли пользователя
$userRole = $_SESSION['user']['role'] ?? 'user'; // 'user' по умолчанию

?>

<div class="container mt-4">
    <div class="card shadow-sm">
        <div class="card-body">
            <h1 class="card-title text-center fw-bold pt-0">Секретная страница</h1>
            <p class="card-text text-center text-success fw-bold">Этот текст доступен всем авторизованным пользователям.</p>

            <?php if ($userRole === 'vk_user'): ?>
                <div class="text-center mt-3">
                    <img src="/assets/img/secret_image.jpg" alt="Секретное изображение" class="img-fluid rounded shadow">
                </div>
            <?php else: ?>
                <p class="card-text text-center text-danger fw-bold">Изображение доступно только пользователям VK.</p>
            <?php endif; ?>
        </div>
    </div>
</div>