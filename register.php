<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'root', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeatPassword = $_POST['repeat_password'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? OR email = ?");
    $stmt->execute([$phone, $email]);
    $user = $stmt->fetch();

    if ($user) {
        echo "Телефон или почта уже заняты.";
    } else {
        if ($password != $repeatPassword) {
            echo "Пароли не совпадают.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, $password]);
            echo "Регистрация успешна!";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Регистрация</title>
</head>
<body>

<form method="POST" action="">
    Имя: <input type="text" name="name" required><br>
    Телефон: <input type="text" name="phone" required><br>
    Почта: <input type="email" name="email" required><br>
    Пароль: <input type="password" name="password" required><br>
    Повторите пароль: <input type="password" name="repeat_password" required><br>
    <input type="submit" value="Зарегистрироваться">
</form>

<a href="index.php">Вернуться на главную страницу</a>

</body>
</html>