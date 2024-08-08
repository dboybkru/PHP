<?php
session_start();
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Главная страница</title>
</head>
<body>

<h1>Добро пожаловать!</h1>

<p>Пожалуйста, выберите действие:</p>

<a href="register.php">Регистрация</a><br>
<a href="login.php">Авторизация</a><br>

<?php
if (isset($_SESSION['user_id'])) {
    echo '<a href="profile.php">Перейти в профиль</a><br>';
    echo '<form method="POST" action=""><button type="submit" name="logout">Выйти</button></form>';
}
?>

<a href="index.php">Вернуться на главную страницу</a>

<?php
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: index.php");
    exit;
}
?>

</body>
</html>