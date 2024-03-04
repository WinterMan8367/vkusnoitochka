<?
    session_start();
    require_once('functions.php');

    if (!empty($_SESSION))
    {
        header("Location: /");
        exit;
    }

    if (!empty($_POST['phone']) and !empty($_POST['password']))
    {
        $phone = $_POST['phone'];
        $password = $_POST['password'];
    
        $arr = authorization($phone, $password);
        if (!empty($arr))
        {
            $_SESSION['user'] = get_user_info($arr['password_hash']);
            $_SESSION['basket'] = [];
            header("Location: /");
            exit;
        }
        else
        {
            $error = "Неправильный логин или пароль";
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/icons/company_icon.png">
    <title>Авторизация в сети «Вкусно - и точка!»</title>

    <!-- Стили страницы -->
    <link rel='stylesheet' type='text/css' href='fonts/sansation/stylesheet.css'>
    <link rel='stylesheet' type='text/css' href='styles/reset_style.css'>
    <link rel='stylesheet' type='text/css' href='styles/basic.css'>
    <link rel='stylesheet' type='text/css' href='styles/login_and_registration.css'>
</head>

<body>
    <? if (isset($error)): ?>
        <div id="error">
            <span class="error-note">Кликните, чтобы убрать ошибку.</span>
            <?= $error ?>
        </div>
    <? endif ?>

    <form id="login" action="" method="POST" hidden></form>
    <!-- Окно авторизации -->
    <div class="window">

        <!-- Логотип компании -->
        <div class="company-logo-window-markup">
            <!-- Вкусно и точка -->
            <a href="index.php" >
                <img src="images/logos/full_logo.png" class="logo" alt="Вкусно - и точка!">
            </a>
            <!-- Бешеный бургер -->
            <a href="index.php#menu">
                <img src="images/logos/hamburger_logo.png" id="hamburger-logo" alt="Бургер">
            </a>
        </div> <!-- /.company-logo-window-markup -->

        <!-- Поля для ввода -->
        <input type="tel" name="phone" form="login" class="input-field" placeholder="Номер телефона" pattern="[0-9]{11}">
        <input type="password" name="password" form="login" class="input-field" placeholder="Пароль">

        <!-- Кнопка входа -->
        <input type="submit" value="Вход" form="login" class="window-button">

        <!-- Забыли пароль? -->
        <a href="#" class="window-link">✦ Забыли пароль?</a>

    </div> <!-- /.window -->

    <!-- Переход на регистрацию -->
    <div class="window">
        <span class="window-text text-indent">Ещё нет учётной записи?</span>

        <!-- Ссылка -->
        <a href="registration.php" class="link-transition">
            <input type="submit" value="Зарегистрироваться" class="window-button">
        </a>

        <span class="window-text">После регистрации вы получите возможность зайти в личный профиль для оформления заказа.</span>
    </div> <!-- /.window -->

    <!-- Копирайт -->
    <span>Попов Александр 2ПР-21 @ 2023</span>

    <script>
        function $(element) {
            elem = document.querySelector(element);
            return elem;
        }

        function $all(element) {
            elem = document.querySelectorAll(element);
            return elem;
        }

        if ($('#error') != null) {
            // Получить координаты модального окна оформления заказа и установить модальное окно ошибки на 100 пикселей выше первого.

            let coordsModal = $('.window').getBoundingClientRect();
            let coordsError = $('#error').getBoundingClientRect();

            $('#error').style.top = (coordsModal.top - 15 - coordsError.height) + "px";

            // Убрать уведомление об ошибке.
    
            $('#error').addEventListener('click', function () {
                $('#error').style.opacity = 0;
                $('#error').style.cursor = "default";
                setTimeout(() => {
                    $('#error').remove();
                }, 1000);
            })
        }
    </script>
</body>