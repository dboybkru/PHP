<?php
session_start();
ob_start(); 

require 'vendor/autoload.php'; // Подключаем автозагрузчик Composer, для сокрытия ключей капчи

// Загружаем переменные из .env файла
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'root', ''); // Укажите свои значения для подключения к БД

/*  - **`session_start();`**: Запускает сессию, как и в `index.php`.
    - ob_start();  - Буферизует вывод, чтобы можно было изменить или отменить его до отправки клиенту. Капча без этого не захотела работать.
    - **`$pdo = new PDO(...)`**: Создается объект PDO для подключения к базе данных.
    (Объект PDO (PHP Data Objects) — это облегчённое средство доступа к базам данных, ... далее то же что и в index.php
    Капча сделана на основе сервиса Яндекс.Капча. (код из документации Яндекс.Капчи, сильно не разбирался, пока сложно)
*/

// Замените на использование переменной окружения для SERVER_KEY
define('SMARTCAPTCHA_SERVER_KEY', $_ENV['SMARTCAPTCHA_SERVER_KEY']);

function check_captcha($token) {
    $ch = curl_init();
    $args = http_build_query([
        "secret" => SMARTCAPTCHA_SERVER_KEY,
        "token" => $token,
        "ip" => $_SERVER['REMOTE_ADDR'], // Передаем IP-адрес пользователя
    ]);
    
    curl_setopt($ch, CURLOPT_URL, "https://smartcaptcha.yandexcloud.net/captcha/api/validate?$args");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 1);

    $server_output = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpcode !== 200) {
        echo "Allow access due to an error: code=$httpcode; message=$server_output\n";
        return true;
    }
    
    $resp = json_decode($server_output);
    return isset($resp->status) && $resp->status === "ok"; 
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') { // Проверяем, была ли отправлена форма методом POST
    $phone_or_email = $_POST['phone_or_email']; // Получаем телефон или email пользователя из формы
    $password = $_POST['password']; // Получаем пароль пользователя из формы
    $captcha_token = $_POST['smart-token']; // Получаем токен капчи из формы

    if (check_captcha($captcha_token)) { // Проверяем капчу
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? OR email = ?"); // Подготавливаем SQL запрос для выборки пользователя по телефону или email
        $stmt->execute([$phone_or_email, $phone_or_email]); // Выполняем подготовленный запрос
        $user = $stmt->fetch(); // Получаем результат запроса
        
        if ($user && $user['password'] == $password) { // Проверяем, существует ли пользователь и совпадает ли пароль
            $_SESSION['user_id'] = $user['id']; // Сохраняем ID пользователя в сессию
            header("Location: profile.php"); // Перенаправляем на страницу профиля пользователя
            exit;
        } else {
            echo "Неверный телефон/почта или пароль."; // Выводим сообщение об ошибке, если данные неверны
        }
    } else {
        echo "Невозможно пройти валидацию капчи."; // Выводим сообщение об ошибке, если капча не прошла проверку
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Авторизация</title>
</head>
<body>

<form method="POST" action="">
    Телефон или Email: <input type="text" name="phone_or_email" required><br>
    Пароль: <input type="password" name="password" required><br>
    <div id="captcha-container"></div>
    <input type="hidden" name="smart-token" id="smart-token"> <!-- Скрытое поле для хранения токена капчи -->
    <button id="smartcaptcha-demo-submit" disabled="1" type="submit">Войти</button>
</form>

<script
  src="https://smartcaptcha.yandexcloud.net/captcha.js?render=onload&onload=smartCaptchaInit"
  defer
></script>

<script>
  function callback(token) {
    document.getElementById('smart-token').value = token; // Сохраняем токен в скрытое поле
    if (token) {
      document.getElementById('smartcaptcha-demo-submit').removeAttribute('disabled');
    } else {
      document.getElementById('smartcaptcha-demo-submit').setAttribute('disabled', '1');
    }
  }

  function smartCaptchaInit() {
    if (!window.smartCaptcha) {
      return;
    }

    window.smartCaptcha.render('captcha-container', {
      sitekey: '<?php echo $_ENV['SMARTCAPTCHA_SITE_KEY']; ?>', // Клиентский ключ, загружается из .env
      callback: callback,
    });
  }
</script>

<a href="index.php">Вернуться на главную страницу</a>

</body>
</html>