<?php
/*  - **`session_start();`**: Запускает сессию, как и в `index.php`. 
    - **`$pdo = new PDO(...)`**: Создается объект PDO для подключения к базе данных.
    (Объект PDO (PHP Data Objects) — это облегченное средство доступа к базам данных, предоставляющее универсальный интерфейс для
    взаимодействия с различными СУБД через PHP. Он был создан для упрощения работы с базами данных и обеспечения безопасности при работе с ними, 
    предотвращая SQL-инъекции и другие распространенные ошибки.)
    `'dbname=test_db'` указывает, что мы подключаемся к базе данных с именем `test_db`. 
    `'root'` – это имя пользователя для подключения к базе данных.
    Базу данных создал в ручную в XAMPP (там же тестировал всё)
*/

session_start();
$pdo = new PDO('mysql:host=localhost;dbname=test_db', 'root', '');

if ($_SERVER['REQUEST_METHOD'] == 'POST') { //- Проверяет, был ли запрос отправлен методом POST (то есть через форму).
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $repeatPassword = $_POST['repeat_password']; 
    /* - Извлекаем данные из формы, которые отправил пользователь, и сохраняем их в переменные. 
    - **`$_POST`** – это суперглобальный массив в PHP, который содержит данные, отправленные через метод POST.
    */
    
    $stmt = $pdo->prepare("SELECT * FROM users WHERE phone = ? OR email = ?"); 
    //Подготавливает SQL-запрос. Знак вопроса (`?`) используется как плейсхолдер для данных, чтобы предотвратить SQL-инъекции.
    $stmt->execute([$phone, $email]); //Выполняет запрос и подставляет значения переменных `$phone` и `$email`.
    $user = $stmt->fetch(); //Получает первую строку результата запроса и сохраняет ее в переменной `$user`.
    
    if ($user) {
        //Если пользователь с таким телефоном или почтой уже существует (переменная `$user` не пустая), выводится сообщение об ошибке.
        echo "Телефон или почта уже заняты.";
    } else {
        if ($password != $repeatPassword) { //Если пароли не совпадают, выводится сообщение об ошибке.
            echo "Пароли не совпадают.";
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (name, phone, email, password) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $phone, $email, $password]);
            echo "Регистрация успешна!";//Если все проверки пройдены, выполняется SQL-запрос для вставки данных в таблицу `users`. Сообщение о успешной регистрации выводится на экран.
        }
    }
}
?>

<!DOCTYPE html>
    <!--    - Создается HTML-форма для ввода имени, телефона, почты и паролей. 
            - Атрибут `required` в каждом поле делает его обязательным для заполнения. -->

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