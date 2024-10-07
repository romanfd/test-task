<?php
session_start();

// Проверка авторизации
if (!isset($_SESSION['user'])) {
    header('Location: index.php'); // Перенаправление на главную страницу, если пользователь не авторизован
    exit();
}

$user = $_SESSION['user'];

// Подключение к базе данных
$dsn = 'mysql:host=localhost;dbname=dbname';
$username = 'root';
$password = '';

try {
    $pdo = new PDO($dsn, $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die('Ошибка подключения к базе данных: '.$e->getMessage());
}

// Обработка обновления данных пользователя
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Формируем запрос для обновления данных
    $update_query = "UPDATE users SET name = :name, phone = :phone, email = :email";

    // Если пароль введен, добавляем его в запрос
    if (!empty($password)) {
        $update_query .= ", password = :password";
    }

    $update_query .= " WHERE email = :old_email";

    // Готовим запрос
    $stmt = $pdo->prepare($update_query);

    // Подготавливаем данные для выполнения
    $update_data = [
        'name' => $name,
        'phone' => $phone,
        'email' => $email,
        'old_email' => $user['email']
    ];

    // Если пароль введен, добавляем его в массив данных
    if (!empty($password)) {
        $update_data['password'] = password_hash($password, PASSWORD_BCRYPT);
    }

    // Выполняем запрос
    $stmt->execute($update_data);

    // Обновляем информацию в сессии сразу после успешного обновления данных
    $_SESSION['user']['name'] = $name;
    $_SESSION['user']['phone'] = $phone;
    $_SESSION['user']['email'] = $email;

    header('Location: profile.php?success=1');
    exit();
}

// Отображаем сообщение об успешном обновлении, если присутствует GET-параметр success
$success = isset($_GET['success']) ? "Данные успешно обновлены!" : null;
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Профиль</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
<div class="container mt-5">
    <h2>Профиль пользователя</h2>
    <?php if (isset($success)): ?>
        <div class="alert alert-success"><?= $success ?></div>
    <?php endif; ?>
    <form action="profile.php" method="POST">
        <div class="mb-3">
            <label for="name" class="form-label">Имя</label>
            <input type="text" class="form-control" id="name" name="name" value="<?= htmlspecialchars($user['name']) ?>"
                   required>
        </div>
        <div class="mb-3">
            <label for="phone" class="form-label">Телефон</label>
            <input type="tel" class="form-control" id="phone" name="phone"
                   value="<?= htmlspecialchars($user['phone']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="email" class="form-label">Email</label>
            <input type="email" class="form-control" id="email" name="email"
                   value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>
        <div class="mb-3">
            <label for="password" class="form-label">Новый пароль (если хотите сменить)</label>
            <input type="password" class="form-control" id="password" name="password"
                   placeholder="Оставьте пустым, если не хотите менять пароль">
        </div>
        <button type="submit" class="btn btn-primary mb-3">Обновить данные</button>
    </form>
    <a class="btn btn-primary" href="/" role="button">На главную</a>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
