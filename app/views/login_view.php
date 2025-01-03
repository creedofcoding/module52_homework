<div class="row justify-content-center mt-4">
    <div class="col-4">
        <form id="login-form" action="/login" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input type="text" class="form-control" id="login" name="login" placeholder="Введите логин">
                <div class="form-control-feedback" id="login-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Пароль">
                <div class="form-control-feedback" id="password-feedback"></div>
            </div>
            <button type="submit" class="btn btn-primary w-100">Войти</button>
        </form>

        <div class="separator mt-3 mb-3">
            <hr class="line">
            <span class="text">или</span>
            <hr class="line">
        </div>
        <div class="mb-3">
            <form method="POST" action="/auth_vk">
                <div class="d-grid gap-2 mt-3">
                    <button type="submit" id="vk_auth_button" name="vk_auth_button" class="btn btn-primary w-100">
                        <img src="assets/img/vk_icon.png" alt="VK Icon" class="vk-icon">
                        Авторизоваться через VK
                    </button>
                </div>
            </form>
        </div>
        <div class="col-12 text-center">
            <p class="small mb-0">Нет аккаунта? <a href="/register">Зарегистрироваться</a></p>
        </div>
    </div>
</div>

<script>
    function logErrorToServer(message, login = null, password = null) {
        // Проверяем существование log_error.php
        fetch('/log_error', {
                method: 'HEAD'
            })
            .then((response) => {
                if (response.ok) {
                    //console.log('Путь /log_error найден.');

                    // Если файл существует, отправляем сообщение об ошибке
                    return fetch('/log_error', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            error: message,
                            login: login,
                            password: password,
                        }),
                    }).then((response) => {
                        //console.log('Статус ответа:', response.status);
                        return response.text(); // Используйте text(), чтобы увидеть сырой ответ
                    }).then((text) => {
                        //console.log('Ответ сервера:', text);
                        try {
                            const json = JSON.parse(text); // Попытка преобразовать в JSON
                            //console.log('JSON:', json);
                        } catch (e) {
                            //console.error('Ошибка парсинга JSON:', e.message);
                        }
                    }).catch((err) => console.error('Ошибка:', err));
                } else {
                    console.error('Путь /log_error не найден.');
                }
            })
            .catch((err) => {
                console.error('Ошибка при проверке существования log_error.php:', err);
            });
    }

    $(document).ready(function() {
        $('#login-form').on('submit', function(e) {
            e.preventDefault();

            // Сбрасываем ошибки
            $('.form-control-feedback').text('');
            $('input').removeClass('is-invalid is-valid');

            // Получаем значения из полей
            var login = $('#login').val().trim();
            var password = $('#password').val().trim();

            // Проверка
            if (!login || !password) {
                if (!login) {
                    var errorMsg = 'Логин не может быть пустым';
                    $('#login-feedback').text(errorMsg);
                    $('#login').addClass('is-invalid');

                    logErrorToServer(errorMsg, login, password);
                } else {
                    $('#login').addClass('is-valid');
                }

                if (!password) {
                    var errorMsg = 'Пароль не может быть пустым! Пробелы не допускаются';
                    $('#password-feedback').text(errorMsg);
                    $('#password').addClass('is-invalid');

                    logErrorToServer(errorMsg, login, password);
                } else {
                    $('#password').addClass('is-valid');
                }

                return;
            }

            var formData = $(this).serialize();
            formData = decodeURIComponent(formData);
            //console.log(formData);

            $.ajax({
                url: '/login',
                method: 'POST',
                data: formData,
                success: function(response) {
                    const parsedResponse = JSON.parse(response);
                    //console.log(parsedResponse);

                    if (!parsedResponse.success) {
                        if (parsedResponse.message === 'Пользователь с таким логином не найден') {
                            $('#login-feedback').text(parsedResponse.message);
                            $('#login').addClass('is-invalid');
                            $('#password').addClass('is-invalid');

                            logErrorToServer(parsedResponse.message, login, password);
                        } else if (parsedResponse.message === 'Неверный пароль') {
                            $('#password-feedback').text(parsedResponse.message);
                            $('#password').addClass('is-invalid');

                            logErrorToServer(parsedResponse.message, login, password);
                        }
                        return;
                    }

                    $('#login').addClass('is-valid');
                    $('#password').addClass('is-valid');

                    // Перенаправляем пользователя на главную страницу
                    window.location.href = '/';
                },
                error: function(xhr, status, error) {
                    // Сбрасываем старые ошибки и классы
                    $('.form-control-feedback').text('');
                    $('input').removeClass('is-invalid is-valid');

                    $('#login').addClass('is-invalid');
                    $('#password').addClass('is-invalid');

                    logErrorToServer(`Ошибка сервера: ${xhr.status}.`, login, password);

                    // Если ошибка на сервере, выводим ее через SweetAlert
                    Swal.fire({
                        icon: 'error',
                        title: 'Произошла ошибка',
                        text: `Ошибка сервера: ${xhr.status}.`,
                    });

                    // Или можно использовать стандартный alert
                    //console.log('Произошла ошибка при отправке данных: ' + xhr.status + ': ' + xhr.statusText);
                }
            });
        });
    });
</script>