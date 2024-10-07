<?php
session_start();

// Подключение к базе данных
$dsn = 'mysql:host=localhost;dbname=dbname';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: ' . $e->getMessage());
}

$_SESSION['errors'] = [];

// Получаем данные из формы
$name = $_POST['name'];
$phone = $_POST['phone'];
$email = $_POST['email'];
$password = $_POST['password'];
$password_confirmation = $_POST['password_confirmation'];

// Проверка на совпадение паролей
if ($password !== $password_confirmation) {
    $_SESSION['errors']['password_confirmation'] = 'Пароли не совпадают.';
    header('Location: index.php');
    exit();
}

// Проверка существования пользователя с таким email или телефоном
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email OR phone = :phone");
$stmt->execute(['email' => $email, 'phone' => $phone]);
$user = $stmt->fetch();

if ($user) {
    $_SESSION['errors']['email_phone'] = 'Пользователь с таким email или телефоном уже существует.';
    header('Location: index.php');
    exit();
}

// Добавляем пользователя в базу данных
$stmt = $pdo->prepare("INSERT INTO users (name, phone, email, password) VALUES (:name, :phone, :email, :password)");
$stmt->execute([
    'name' => $name,
    'phone' => $phone,
    'email' => $email,
    'password' => password_hash($password, PASSWORD_BCRYPT),
]);

// Устанавливаем сессию авторизации
$_SESSION['user'] = [
    'name' => $name,
    'phone' => $phone,
    'email' => $email
];

// Перенаправление на страницу профиля
header('Location: profile.php');
exit();