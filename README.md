# PHP Репозиторий

Добро пожаловать в репозиторий PHP проектов! Здесь вы найдете примеры создания базовой системы регистрации, авторизации и управления профилем пользователя с использованием только PHP.

## Содержимое Репозитория

В этом репозитории представлены четыре основных файла:

1. **index.php** - Главная страница сайта, предоставляющая пользователю выбор между регистрацией и авторизацией.
2. **login.php** - Страница для входа в систему с использованием капчи Яндекса.
3. **profile.php** - Страница профиля пользователя, где он может просматривать и редактировать свои данные.
4. **register.php** - Страница для регистрации нового пользователя.

## Как запустить проект

1. Клонируйте репозиторий на ваш локальный компьютер:
   ```bash
   git clone https://github.com/dboybkru/PHP.git


Установите необходимые зависимости, такие как подключение к базе данных. Для этого откройте каждый файл и обновите пути к базе данных и ключи API:

В login.php убедитесь, что SMARTCAPTCHA_SERVER_KEY установлен правильно.

В остальных файлах убедитесь, что строка подключения к базе данных указана верно.



Запустите локальный сервер (например, Apache) и перейдите в папку с проектом:
cd path/to/PHP
php -S localhost:8000


Откройте браузер и перейдите по адресу http://localhost:8000/index.php для просмотра главной страницы.


Структура Проекта

Copy Code
PHP/
│
├── index.php          # Главная страница
├── login.php          # Страница авторизации
├── profile.php        # Страница профиля пользователя
└── register.php       # Страница регистрации
Особенности Проекта


Регистрация и Авторизация: Пользователи могут зарегистрироваться, войти в систему и управлять своим профилем.

Капча Яндекса: Для защиты от ботов используется капча Яндекса.

Сессии: Интегрирована работа с сессиями для хранения информации о пользователе.


Технологии


PHP

MySQL (или аналог)

Капча Яндекса

Сессии
