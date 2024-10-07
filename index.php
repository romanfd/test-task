<?php
session_start();
$errors = $_SESSION['errors'] ?? [];
$success = $_SESSION['success'] ?? null;

// Очистка ошибок после отображения
unset($_SESSION['errors']);
unset($_SESSION['success']);
?>


<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Главная страница</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"
          integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
    <script src="https://smartcaptcha.yandexcloud.net/captcha.js" defer></script>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background-color: #f8f9fa;
        }

        .form-container {
            width: 900px;
            margin: 0 auto;
        }

        .card {
            padding: 20px;
        }
    </style>
</head>
<body>
<div class="form-container row g-4">
    <!-- Левая колонка (Авторизация) -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <h4 class="text-center mb-4">Авторизация</h4>
            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="login" class="form-label">Email или телефон</label>
                    <input type="text" class="form-control" id="login" name="login"
                           placeholder="Введите email или телефон" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Введите пароль" required>
                </div>
                <!-- Вывод ошибки, если данные логина или пароля неверны -->
                <?php if (isset($errors['login'])): ?>
                    <div class="text-danger mb-3"><?= $errors['login'] ?></div>
                <?php endif; ?>
                <!-- Вывод ошибки верификации капчи -->
                <?php if (isset($errors['captcha'])): ?>
                    <div class="text-danger mb-3"><?= $errors['captcha'] ?></div>
                <?php endif; ?>
                <div id="captcha-container" class="smart-captcha mb-3"
                     data-sitekey="client-key"></div>
                <button type="submit" class="btn btn-primary w-100">Войти</button>
            </form>
        </div>
    </div>

    <!-- Правая колонка (Регистрация) -->
    <div class="col-md-6">
        <div class="card shadow-sm">
            <h4 class="text-center mb-4">Регистрация</h4>
            <form action="register.php" method="POST">
                <div class="mb-3">
                    <label for="name" class="form-label">Имя</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Введите ваше имя"
                           required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Телефон</label>
                    <input type="tel" class="form-control" id="phone" name="phone" placeholder="Введите телефон"
                           required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="Введите email"
                           required>
                    <?php if (isset($errors['email_phone'])): ?>
                        <div class="text-danger"><?= $errors['email_phone'] ?></div>
                    <?php endif; ?>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Пароль</label>
                    <input type="password" class="form-control" id="password" name="password"
                           placeholder="Введите пароль" required>
                </div>
                <div class="mb-3">
                    <label for="password_confirmation" class="form-label">Повтор пароля</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation"
                           placeholder="Повторите пароль" required>
                    <?php if (isset($errors['password_confirmation'])): ?>
                        <div class="text-danger"><?= $errors['password_confirmation'] ?></div>
                    <?php endif; ?>
                </div>
                <button type="submit" class="btn btn-success w-100">Зарегистрироваться</button>
            </form>
        </div>
    </div>
</div>


<!-- Подключаем Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz"
        crossorigin="anonymous"></script>
</body>
</html>
