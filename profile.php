<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'root', '');

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?");
    $stmt->execute([$name, $phone, $email, $_SESSION['user_id']]);
    
    if (!empty($password)) {
        $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
        $stmt->execute([$password, $_SESSION['user_id']]);
    }

    echo "Данные обновлены.";
}
?>

<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <title>Профиль</title>
</head>
<body>

<form method="POST" action="">
    Имя: <input type="text" name="name" value="<?php echo $user['name']; ?>" required><br>
    Телефон: <input type="text" name="phone" value="<?php echo $user['phone']; ?>" required><br>
    Почта: <input type="email" name="email" value="<?php echo $user['email']; ?>" required><br>
    Новый пароль: <input type="password" name="password"><br>
    <input type="submit" value="Сохранить">
</form>

<a href="index.php">Вернуться на главную страницу</a>

</body>
</html>