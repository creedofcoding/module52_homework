# Система регистраций (HW-04)

## Практическое задание к модулю 52. Критерии оценивания

**+3 балла**
- Система регистраций должна позволять регистрироваться при помощи пары логин-пароль

**+3 балла**
- Сделать страницу авторизации, на которой пользователь будет вводить заранее созданные логин и пароль

**+10 баллов**
- Сделать авторизацию через VK (для этого на форме регистрации и авторизации нужно выводить отдельную кнопку «Авторизоваться через VK»). Страница авторизации должна быть создана с CSRF-токеном

**+5 баллов**
- Сделать систему с ролями «обычный пользователь» и «пользователь VK»

**+6 баллов**
- Сделать страницу, на которую нельзя попасть, пока пользователь не авторизован. На этой странице необходимо отобразить один абзац текста и одну картинку. Текст должен быть виден всем авторизованным пользователям, картинка — только пользователям с ролью  «пользователь VK»

**+3 балла**
- Сделать систему хранения логов, которая будет записывать все неудачные попытки авторизации через логин и пароль

## Используемое ПО
- OS: Windows
- IDE: [VSCode](https://code.visualstudio.com/)
- Локальный веб-сервер: [XAMPP](https://www.apachefriends.org/)
- Безопасное туннелирование от общедоступной конечной точки к локально запущенной сетевой службе: [ngrok](https://ngrok.com/)
- Программа для работы с БД SQLite: [DB Browser for SQLite](https://sqlitebrowser.org/dl/)
- Браузер: Яндекс

## <a name="гайд_без_авторизации">Гайд, чтобы работало локально на ПК (без авторизации ВК)</a>
- **Важно**, чтобы **все файлы** были в папке `module52_homework` по пути `C:\xampp\htdocs\module52_homework\`, иначе **ничего работать не будет!!!**
- Отредактировать файл `httpd-vhosts.conf` по пути `C:\xampp\apache\conf\extra`, вставив туда этот блок кода **(не забудьте сохранить файл!)**:
```
 <VirtualHost *:80>
    DocumentRoot "C:/xampp/htdocs/module52_homework/public"
    ServerName module52_homework.local
 </VirtualHost>
```
- Далее открыть файл `hosts` **от имени администратора** (например, в [Notepad++](https://notepad-plus-plus.org/downloads/)) по пути `C:\Windows\System32\drivers\etc` и **внизу всего** вставить эту строчку, **попутно не забыв сохранить файл**:
```
127.0.0.1   module52_homework.local
```
- **Находясь в папке** `module52_homework`, в Терминале VSCode **прописать** ``composer install``, чтобы установить **все** зависимости и чтобы система логирования Monolog **заработала**!!!
- **Если создалась** папка ``src``, то её **нужно удалить!**
- **Перезапускаем XAMPP!**
- **Вуаля! Всё работает!**

## Гайд, чтобы работало всё (и авторизация в том числе)
- **Сделать все шаги [прошлого гайда](#гайд_без_авторизации)**
- **Запускаем XAMPP (если не запущен), иначе запущенный сайт через ngrok не будет работать!!!**
- **Иметь приложение** на сервисе авторизации VK ID ([пошаговая инструкция](https://id.vk.com/about/business/go/docs/ru/vkid/latest/vk-id/connection/create-application))
- После создания приложения VK ID в файле [config.php](/app/config.php) отредактировать эти две строчки, вставив **свой VK_CLIENT_ID и VK_CLIENT_SECRET** (они имеются в созданном приложении **VK ID**):
```
define('VK_CLIENT_ID', свой_VK_CLIENT_ID);
define('VK_CLIENT_SECRET', 'свой_VK_CLIENT_SECRET');
```
- Зарегистрироваться в [ngrok](https://ngrok.com/)
- Получить **Authtoken** с сайта ngrok после успешной регистрации
- Скачать [ngrok](https://download.ngrok.com/windows?tab=download) **в любую папку** для своей ОС (у меня **Windows**)
- Разархивировать и запустить ``ngrok.exe``
- После запуска прописать ``ngrok config edit``
- Вставить данный блок кода в файл конфигурации ``ngrok.yml``:
```
version: "3"
agent:
    authtoken: authtoken                      #заменить на свой authtoken
tunnels:
  module52_homework:                          #название туннеля (лучше оставить таким, но можно и поменять)
    proto: http
    addr: 80
    host_header: "module52_homework.local"    #эту строчку не менять!!!
    basic_auth:                               #эта строчка включает авторизацию до перехода на сайт (можно удалить)
        - "логин:пароль"                      #(например, "admin:12345", если удалили предыдущую строчку - эту тоже удаляем)
```
- Когда всё заполнили и **сохранили** файл ``ngrok.yml`` - запускаете ngrok, затем вводите команду ``ngrok start module52_homework (или другое название, которое вы придумали)`` и нажимаете Enter
- Затем ищете строку с **Forwarding** и копируете ссылку (пример ссылки: https://af5e-46-138-159-3.ngrok-free.app)
- Вставляете полученную ссылку в адресную строку Браузера, нажимаете на **Visit Site** и вводите пару логин:пароль, которую записывали ранее в файл конфигурации ``ngrok.yml`` (если вы удалили эти две строчки ``basic_auth: - "логин:пароль"``, то вы сразу попадёте на сайт)
- В созданном приложении VK ID вставляете **сгенерированную ngrok'ом ссылку** (в моем случае: https://af5e-46-138-159-3.ngrok-free.app) в два места так, как на скриншоте, - **не забудьте нажать на Сохранить!!!**
![подключение_авторизации](/public/assets/img/подключение_авторизации.png)
- Вернитесь на свой сайт (в моем случае: https://af5e-46-138-159-3.ngrok-free.app) и попробуйте авторизоваться **при помощи ВК!**
- **Вуаля! Всё работает!**

## Дополнительно проделанная работа
- Используется **модифицированная** версия файла ``route.php`` [отсюда](https://github.com/creedofcoding/module50_homework/tree/master?tab=readme-ov-file#дополнительно-проделанная-работа).
- Используется **bootstrap** для стилизации сайта.
- Проверки у форм осуществляются через **AJAX и JQuery**.
- Используется **Sweetalert** для вывода ошибок.
- Используется **toastr** для отображения как успешных так и неудачных запросов.
- Используется **Monolog** для ведения логов неудачных попыток **обычной авторизации**, а также неудачных попыток **авторизации при помощи ВК**.
- Все запросы к БД можно посмотреть [тут](app/database/queries.sql). **Пароль ко всем пользователям единственный - 123**