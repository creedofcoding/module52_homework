<div class="row justify-content-center mt-4">
    <div class="col-4">
        <form id="registration-form" action="/register" method="POST">
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token']; ?>">

            <div class="mb-3">
                <label for="login" class="form-label">Логин</label>
                <input type="text" class="form-control" id="login" name="login" placeholder="Введите логин">
                <div class="form-control-feedback" id="login-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="name" class="form-label">Имя</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Введите имя">
                <div class="form-control-feedback" id="name-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Пароль</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Пароль">
                <div class="form-control-feedback" id="password-feedback"></div>
            </div>
            <div class="mb-3">
                <label for="repeat-password" class="form-label">Повторите пароль</label>
                <input type="password" class="form-control" id="repeat-password" name="repeat-password" placeholder="Повторите пароль">
                <div class="form-control-feedback" id="repeat-password-feedback"></div>
            </div>
            <button type="submit" class="btn btn-primary w-100 mb-0">Зарегистрироваться</button>
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
            <p class="small mb-0">Уже есть аккаунт? <a href="/login">Войти</a></p>
        </div>
    </div>
</div>

<script>
    $(document).ready(function() {
        $('#registration-form').on('submit', function(e) {
            e.preventDefault(); // Предотвращаем стандартную отправку формы

            // Сбрасываем старые ошибки и классы
            $('.form-control-feedback').text('');
            $('input').removeClass('is-invalid is-valid');

            // Получаем значения из полей
            var login = $('#login').val().trim();
            var name = $('#name').val().trim();
            var password = $('#password').val().trim();
            var repeatPassword = $('#repeat-password').val().trim();

            // Регулярное выражение для проверки логина
            var loginRegex = /^[a-zA-Z0-9_]{3,50}$/; // Логин должен быть от 3 до 50 символов, содержать только буквы, цифры и подчеркивания
            var loginValid = loginRegex.test(login);

            // Проверка
            if (!login || !name || !password || !repeatPassword || !loginValid) {
                if (!login) {
                    $('#login-feedback').text('Логин не может быть пустым');
                    $('#login').addClass('is-invalid');
                } else if (!loginValid) {
                    $('#login-feedback').text('Логин должен быть от 3 до 50 символов, содержать только буквы, цифры и подчеркивания');
                    $('#login').addClass('is-invalid');
                } else {
                    $('#login').addClass('is-valid');
                }

                if (!name) {
                    $('#name-feedback').text('Имя не может быть пустым');
                    $('#name').addClass('is-invalid');
                } else {
                    $('#name').addClass('is-valid');
                }

                if (!password) {
                    $('#password-feedback').text('Пароль не может быть пустым! Пробелы не допускаются');
                    $('#password').addClass('is-invalid');
                } else {
                    $('#password').addClass('is-valid');
                }

                if (!repeatPassword) {
                    $('#repeat-password-feedback').text('Это поле не может быть пустым');
                    $('#repeat-password').addClass('is-invalid');
                }

                return;
            }

            // Проверка на совпадение паролей
            if (password !== repeatPassword) {
                $('#password-feedback').text('Пароли не совпадают');
                $('#password').addClass('is-invalid');
                $('#repeat-password').addClass('is-invalid');
                return;
            } else {
                $('#password').addClass('is-valid');
                $('#repeat-password').addClass('is-valid');
            }

            var formData = $(this).serialize();
            formData = decodeURIComponent(formData);
            //console.log(formData);

            $.ajax({
                url: '/register',
                method: 'POST',
                data: formData,
                success: function(response) {
                    const parsedResponse = JSON.parse(response);
                    //console.log(parsedResponse);

                    // Сбрасываем старые ошибки и классы
                    $('.form-control-feedback').text('');
                    $('input').removeClass('is-invalid is-valid');

                    if (!parsedResponse.success) {
                        // Отображаем новые ошибки и добавляем классы
                        if (parsedResponse.errors.login) {
                            $('#login-feedback').text(parsedResponse.errors.login);
                            $('#login').addClass('is-invalid');
                        } else {
                            $('#login').addClass('is-valid');
                        }
                        if (parsedResponse.errors.name) {
                            $('#name-feedback').text(parsedResponse.errors.name);
                            $('#name').addClass('is-invalid');
                        } else {
                            $('#name').addClass('is-valid');
                        }
                        if (parsedResponse.errors.password) {
                            $('#password-feedback').text(parsedResponse.errors.password);
                            $('#password').addClass('is-invalid');
                        } else {
                            $('#password').addClass('is-valid');
                        }
                        if (parsedResponse.errors.repeat_password) {
                            $('#repeat-password-feedback').text(parsedResponse.errors.repeat_password);
                            $('#repeat-password').addClass('is-invalid');
                        } else {
                            $('#repeat-password').addClass('is-valid');
                        }
                    } else {
                        // Обработка успешной регистрации
                        //console.log(parsedResponse);
                        $('#login').addClass('is-valid');
                        $('#name').addClass('is-valid');
                        $('#password').addClass('is-valid');
                        $('#repeat-password').addClass('is-valid');

                        window.location.href = '/';
                    }
                },
                error: function(xhr, status, error) {
                    // Сбрасываем старые ошибки и классы
                    $('.form-control-feedback').text('');
                    $('input').removeClass('is-invalid is-valid');

                    $('#login').addClass('is-invalid');
                    $('#name').addClass('is-invalid');
                    $('#password').addClass('is-invalid');
                    $('#repeat-password').addClass('is-invalid');

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