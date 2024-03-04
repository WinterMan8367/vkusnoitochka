<?
    session_start();
    require_once('functions.php');

    if (!empty($_SESSION))
    {
        header("Location: /index.php");
        exit;
    }
    
    if (!empty($_POST))
    {
        $name = $_POST['name'];
        $surname = $_POST['surname'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];
        $repeat_password = $_POST['repeat_password'];

        if (!empty($name) and !empty($phone) and !empty($password) and !empty($repeat_password))
        {
            $result = registration($name, $surname, $phone, $password, $repeat_password);
        
            if (is_array($result))
            {
                $_SESSION['user'] = $result;
                $_SESSION['basket'] = [];
                header('Location: /');
                exit;
            }
            else
            {
                $error = $result;
            }
        }
        else
        {
            $error = "Заполните все поля.";
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="shortcut icon" href="images/icons/company_icon.png">
    <title>Регистрация в сети «Вкусно - и точка!»</title>

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

    <form id="registration" action="" method="POST" hidden></form>
    <!-- Окно регистрации -->
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

        <!----- Поля для ввода ----->

        <!-- ФИО -->
        <div class="split-fields">
            <input type="text" name="name" form="registration" class="input-field indentation-fields" placeholder="Имя">
            <input type="text" name="surname" form="registration" class="input-field" placeholder="Фамилия">
        </div>

        <!-- Номер телефона -->
        <input type="tel" name="phone" form="registration" class="input-field" placeholder="Номер телефона" pattern="[0-9]{11}">
        
        <!-- Пароль -->
        <div class="split-fields">
            <input type="password" name="password" form="registration" class="input-field indentation-fields" placeholder="Пароль">
            <input type="password" name="repeat_password" form="registration" class="input-field" placeholder="Повтор пароля">
        </div>

        <!-- Кнопка регистрации -->
        <input type="submit" form="registration" value="Зарегистрироваться" class="window-button">

        <!-- Предупреждение -->
        <span class="window-text">Пожалуйста, проверьте поля на корректность заполнения !</span>
    </div> <!-- /.window -->

    <!-- Переход на авторизацию -->
    <div class="window">
        <span class="window-text" style=" margin-bottom: 12px;">Уже есть учётная запись?</span>
        
        <!-- Ссылка -->
        <a href="login.php" class="link-transition">
            <input type="submit" value="Войти" class="window-button">
        </a>
        
        <span class="window-text">После авторизации вы сможете оформить заказ в вашем личном профиле.</span>
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