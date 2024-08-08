<?php
session_start();
ob_start(); 

$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'root', '');

define('SMARTCAPTCHA_SERVER_KEY', 'ysc2_py1MHYLoBY1TQFWoEUUzIHR6R4UC7uFvjrQxeRcO26046078');

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

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $phone_or_email = $_POST['phone_or_email'];
    $password = $_POST['password'];
    $captcha_token = $_POST['smart-token']; 

    if (check_captcha($captcha_token)) {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? OR email = ?");
        $stmt->execute([$phone_or_email, $phone_or_email]);
        $user = $stmt->fetch();
        
        if ($user && $user['password'] == $password) {
            $_SESSION['user_id'] = $user['id'];
            header("Location: profile.php");
            exit;
        } else {
            echo "Неверный телефон/почта или пароль.";
        }
    } else {
        echo "Невозможно пройти валидацию капчи.";
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
      sitekey: 'ysc1_py1MHYLoBY1TQFWoEUUzQufHUqYDbmZZgGBcrpfm2b97fd48', // Клиентский ключ
      callback: callback,
    });
  }
</script>

<a href="index.php">Вернуться на главную страницу</a>

</body>
</html>