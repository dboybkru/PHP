<?php
session_start();
$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'root', '');
//  - Начинается сессия и подключаемся к базе данных, как и раньше.

if (!isset($_SESSION['user_id'])) { // - Проверяем, вошел ли пользователь в систему. Если нет, перенаправляем его на главную страницу.
    header("Location: index.php");
    exit();
}

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?"); //- Ищем данные пользователя по его ID и сохраняем их в переменной.
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //- Проверяем, была ли отправлена форма (метод POST).
    $name = $_POST['name']; //- Извлекаем значения из формы для обновления данных пользователя.
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    $stmt = $pdo->prepare("UPDATE users SET name = ?, phone = ?, email = ? WHERE id = ?"); //- Обновляем имя, телефон и почту пользователя в таблице `users`.
    $stmt->execute([$name, $phone, $email, $_SESSION['user_id']]);
    
    if (!empty($password)) { //- Если пользователь ввел новый пароль, мы обновляем его тоже. Затем выводим сообщение о том, что данные обновлены.
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
    <!-- - Создаем форму для ввода новых данных пользователя. Поля имени, телефона и почты уже заполнены значениями из базы данных.-->

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