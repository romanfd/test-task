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
$login = $_POST['login'];  // Логин может быть email или телефон
$password = $_POST['password'];
$captchaToken = $_POST['smart-token']; // Получаем токен капчи

// Проверяем капчу на сервере Yandex SmartCaptcha
$secret = 'server-key';
$captchaUrl = 'https://smartcaptcha.yandexcloud.net/validate';
$response = file_get_contents($captchaUrl, false, stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/x-www-form-urlencoded",
        'content' => http_build_query(['secret' => $secret, 'token' => $captchaToken])
    ]
]));

$responseData = json_decode($response, true);

if ($responseData['status'] !== 'ok') {
    $_SESSION['errors']['captcha'] = 'Ошибка верификации капчи.';
    header('Location: index.php');
    exit();
}

// Проверяем, существует ли пользователь с таким email или телефоном
$stmt = $pdo->prepare("SELECT * FROM users WHERE email = :login OR phone = :login");
$stmt->execute(['login' => $login]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Проверка пароля и наличие пользователя
if ($user && password_verify($password, $user['password'])) {
    // Если данные верны, создаем сессию и перенаправляем на страницу профиля
    $_SESSION['user'] = [
        'name' => $user['name'],
        'phone' => $user['phone'],
        'email' => $user['email']
    ];

    header('Location: profile.php');
    exit();
} else {
    // Если данные неверны, добавляем ошибку в сессию и перенаправляем на главную страницу
    $_SESSION['errors']['login'] = 'Неправильный email/телефон или пароль.';
    header('Location: index.php');
    exit();
}
