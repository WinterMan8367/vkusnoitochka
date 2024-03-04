<? 
    session_start();
    require_once('functions.php');

    if (empty($_SESSION))
    {
        header('Location: index.php');
        exit;
    }

    if (!empty($_GET['logout']))
    {
        unset($_SESSION['user_info']);
        $_SESSION = [];
        session_destroy();
        header("Location: /");
        exit;
    }
    
    $user = $_SESSION['user'];
    $coupons = get_user_coupon($user['id']);

    if (!empty($_POST['edit_profile']))
    {
        if (empty($_POST['surname'])) $error[] = "Фамилия не должна быть пустой.";
        if (empty($_POST['name'])) $error[] = "Имя не должно быть пустым.";
        if (empty($_POST['phone'])) $error[] = "Номер телефона не должен быть пустым.";

        $other_phone = get_user_info($_POST['phone']);
        {
            if (!empty($other_phone))
            {
                if ($other_phone['phone'] != $user['phone']) $error[] = "Данный номер телефона уже занят.";
            }
        }

        if (!empty($_POST['old_password']) and !empty($_POST['new_password']) and !empty($_POST['repeat_new_password']))
        {
            if (password_verify($_POST['old_password'], $user['password_hash']))
            {
                if ($_POST['new_password'] == $_POST['repeat_new_password'])
                {
                    if (empty($error))
                    {
                        edit_profile($user['id'], $_POST['surname'], $_POST['name'], $_POST['phone'], password_hash($_POST['new_password'], PASSWORD_BCRYPT));
                        $_SESSION['user'] = get_user_info($user['id']);
                        
                        $_POST = [];
                        header('Location: personal-profile.php');
                        exit;
                    }
                }
                else
                {
                    $error[] = "Пароли не совпадают.";
                }
            }
            else
            {
                $error[] = "Неправильный пароль.";
            }
        }

        if (empty($error))
        {
            edit_profile($user['id'], $_POST['surname'], $_POST['name'], $_POST['phone']);
            $_SESSION['user'] = get_user_info($user['id']);

            $_POST = [];
            header('Location: personal-profile.php');
            exit;
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- База -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Иконка с названием -->
    <link rel="shortcut icon" href="images/icons/company_icon.png">
    <title>Личный профиль гостя кафе «Вкусно – и точка!»</title>

    <!-- Стили страницы -->
    <link rel='stylesheet' type='text/css' href='fonts/sansation/stylesheet.css'>
    <link rel='stylesheet' type='text/css' href='styles/reset_style.css'>
    <link rel='stylesheet' type='text/css' href='styles/basic.css'>
    <link rel='stylesheet' type='text/css' href='styles/personal_profile.css'>
</head>

<body>
    <!-- Шапка страницы -->
    <? require_once('header.php') ?>

    <? if (isset($_GET['edit'])): ?>
        <style>
            body { overflow-y: hidden }
        </style>

            <? if (isset($error)): ?>
                <div id="error">
                    <span class="error-note">Кликните, чтобы убрать ошибку.</span>
                    <?
                    foreach ($error as $elem)
                    {
                        echo $elem . "<br>";
                    }
                    ?>
                </div>
            <? endif ?>

        <!-- Редактирование профиля -->
        <div id="dark-blur-profile"></div>

        <form action="" method="POST" id="edit-profile">
            <input type="hidden" name="edit-profile" value="true">
            <div>
                <h1>Редактирование профиля</h1>
                <a href="/personal-profile.php" class="close"></a>
            </div>
            <p>✦ Фамилия</p>
            <input type="text" name="surname" pattern="[А-ЯЁа-яё]*" value="<?= $user['surname'] ?>">
            <p>✦ Имя</p>
            <input type="text" name="name" pattern="[А-ЯЁа-яё]*" value="<?= $user['name'] ?>">
            <p>✦ Номер телефона</p>
            <input type="tel" name="phone" pattern="[0-9]{11}" value="<?= $user['phone'] ?>">
            <hr>
            <p>Не трогайте поля для ввода пароля, если не хотите его менять.</p>
            <hr>
            <p>✦ Введите старый пароль</p>
            <input type="password" name="old_password">
            <p>✦ Введите новый пароль</p>
            <input type="password" name="new_password">
            <p>✦ Повторите новый пароль</p>
            <input type="password" name="repeat_new_password">
            <input type="submit" value="Сохранить">
        </form>
    <? endif ?>

    <!-- Основное тело -->
    <main style="margin-top: 90px">

    <div class="gradient-line"></div> <!-- Линия с градиентом -->

    <!-- Блок с личной информацией пользователя -->
    <div class="user-personal-information-block">
        <div class="container">
            <div class="user-personal-information-mockup">

                <!-- ФИО пользователя -->
                <h1 class="full-name-style"><?=$user['surname'] . " " . $user['name']?></h1>

                <!-- Фото пользователя -->
                <div class="user-photo"></div>

                <!-- Номер пользователя -->
                <div class="information">
                    <img src="images\icons\icon-phone.png" alt="Иконка телефона">
                    <span>Номер телефона: <?=$user['phone']?></span>
                </div>

                <!-- Бонусы -->
                <div class="information">
                    <img src="images\icons\icon-ruble.png" alt="Иконка рубля">
                    <span id="orange">Ваши бонусы: <?= round($user['point'], 2)?> ₽</span>
                </div>

                <? if ($user['role'] == 1): ?>
                <div class="information">
                    <img src="images\icons\icon-admin.png" alt="Иконка рубля">
                    <span id="orange">Ваш статус: администратор</span>
                </div>
                <? endif ?>

                <!-- Кнопки -->
                <div class="flex">
                    <a href="?edit" class="profile-button" id="white-button">Редактировать</a>
                    <a href="?logout=1" class="profile-button" id="green-button">Выйти</a>
                </div>

                <? if ($user['role'] == 1): ?>
                <div>
                    <a href="admin.php" class="profile-button" id="orange-button">Кабинет администратора</a>
                </div>
                <? endif ?>

            </div> <!-- /.user-personal-information-mockup -->
        </div> <!-- /.container -->
    </div> <!-- /.user-personal-information-block -->

    <div class="gradient-line"></div> <!-- Линия с градиентом -->

    <!-- Блок с купонами пользователя -->
    <div class="block-with-coupons">
        <div class="container">
            
            <!-- Заголовок -->
            <div class="title-block-coupons">
                <h1 class="full-name-style" id="orange">Ваши купоны</h1>
                <span>Скидка предоставляется только на одну товарную позицию. Возможно использование только одного купона в чеке. Количество товара ограничено.</span>
            </div>
            
            <? if (!empty($coupons)): ?>
            <!-- Разметка купонов -->
            <div class="block-with-coupons-mockup">
                <?
                    $categories = [1 => 'burgers', 2 => 'drinks', 3 => 'snacks', 4 => 'desserts', 5 => 'sauces', 6 => 'salads-and-rolls'];

                    foreach ($coupons as $elem):
                    $product = get_product($elem['product_id']);
                    $picture = $product['picture'];
                    $category = $categories[$product['category_id']];
                ?>
                <!-- Купон -->
                <div class="coupon">
                    
                    <!-- Скидка -->
                    <div class="discount">-<?=$elem['discount']?>%</div>
                    
                    <!-- Фото товара -->
                    <a href="<?= "$_SERVER[SCRIPT_NAME]?id=$product[id]"?>"><img src="/images/<?= (!empty($picture)) ? "menu/" . $category . "/" . $picture : "icons/not_found.png" ?>" class="photo-discounted-product" alt="Фото товара"></a>

                    <!-- Текст на купоне -->
                    <div class="coupon-mockup">
                        <!-- Заголовок -->
                        <h1>Купон на покупку <?=$product['name']?></h1>
                        
                        <!-- Скидка -->
                        <span>Скидка: -<?=$elem['discount']?>%</span>
                        
                        <!-- Промокод -->
                        <div class="flex">
                            <span>Ваш уникальный код купона:&nbsp;</span>
                            <h1><?=$elem['code']?></h1>
                        </div> <!-- /.flex -->
                    </div> <!-- /.coupon-mockup -->
                </div> <!-- /.coupon -->
                <? endforeach ?>

            </div> <!-- /.block-with-coupons-mockup -->
            <? else: ?>
            <div class="non-coupon">
                Сейчас у вас купонов нет. Их можно получить случайно, заказывая нашу продукцию!
            </div>
            <? endif ?>
        </div> <!-- /.container -->
    </div> <!-- /.block-with-coupons -->

    <div class="gradient-line"></div> <!-- Линия с градиентом -->

    </main> <!-- /.main -->
    
    <!-- Подвал страницы -->
    <? require_once('footer.php') ?>

    <script>
        function $(element) {
            elem = document.querySelector(element);
            return elem;
        }

        function $all(element) {
            elem = document.querySelectorAll(element);
            return elem;
        }

        if ($('#error') != null && $('#edit-profile') != null) {
            // Получить координаты модального окна оформления заказа и установить модальное окно ошибки на 100 пикселей выше первого.

            let coordsModal = $('#edit-profile').getBoundingClientRect();
            let coordsError = $('#error').getBoundingClientRect();

            $('#error').style.top = (coordsModal.top - 15 - coordsError.height) + "px";

            // Убрать уведомление об ошибке.
    
            $('#error').addEventListener('click', function () {
                $('#error').style.opacity = 0;
                $('#error').style.cursor = "default";
                setTimeout(() => {
                    $('#error').remove();
                }, 1000);
            });
        }

        if ($('#edit-profile') != null) {
            let coordsEdit = $('#edit-profile').getBoundingClientRect();

            $('#edit-profile').style.top = "calc(50% - " + (coordsEdit.height / 2) + "px)";
            $('#edit-profile').style.left = "calc(50% - " + (coordsEdit.width / 2) + "px)";
        }
    </script>
</body>
<html>