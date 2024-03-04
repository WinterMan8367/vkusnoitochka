<?
    session_start();
    require_once('functions.php');

    if (empty($_GET))
    {
        header('Location: /cafe.php?drinks');
        exit;
    }

    if (isset($_GET['drinks']))
    {
        $drinks = get_all_products_in_category(2);
    }
    else
    {
        $desserts = get_all_products_in_category(4);
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
    <title>Кофе и десерты из кафе «Вкусно – и точка!»</title>

    <!-- Стили страницы -->
    <link rel='stylesheet' type='text/css' href='fonts/sansation/stylesheet.css'>
    <link rel='stylesheet' type='text/css' href='styles/reset_style.css'>
    <link rel='stylesheet' type='text/css' href='styles/basic.css'>
    <link rel='stylesheet' type='text/css' href='styles/cafe.css'>
</head>

<body>
    <!-- Шапка страницы -->
    <? require_once('header.php') ?>

    <!-- Основное тело -->
    <main style="margin-top: 60px">

        <!-- Приветственный блок с кофе -->
        <div class="block-cafe" id="coffee">
            <h1>Открой новое измерение вкуса!</h1>
            <h1>Готовим так, как вы любите!</h1>
            <? if (isset($_GET['drinks'])): ?>
            <a href="#catering-building" class="button-block-cafe">Хочу вкусный кофе !</a>
            <? else: ?>
            <a href="#catering-building" class="button-block-cafe">Хочу вкусный десерт !</a>
            <? endif ?>
        </div> <!-- /.block-cafe -->

        <div class="gradient-line"></div> <!-- Линия с градиентом -->

        <!-- Меню [Напитки/Десерты] -->
        <div class="menu-desserts-and-drinks">
            <div class="container">
                <div class="desserts-and-drinks">

                    <!-- Выбор меню -->
                    <a href="?drinks">Напитки</a>
                    <img src="images/logos/full_logo.png" alt="Вкусно - и точка!">
                    <a href="?desserts">Десерты</a>

                </div> <!-- /.desserts-and-drinks -->
            </div> <!-- /.container -->
        </div> <!-- /.menu-desserts-and-drinks -->

        <div class="gradient-line"></div> <!-- Линия с градиентом -->

        <!-- Блок со скролом карточек -->
        <div class="block-cafe" id="catering-building">
            <h1>Выбери свой любимый вкус!</h1>
            <div class="container">
                <!-- Скрол карточек -->
                <div class="layer">

                    <? if (isset($_GET['drinks'])): ?>
                        <? foreach ($drinks as $elem): ?>

                        <!-- Карточка -->
                        <a href="<?= "$_SERVER[REQUEST_URI]&id=$elem[id]" ?>" class="square-with-product">
                            <img src="images/menu/drinks/<?= $elem['picture'] ?>" alt="<?= $elem['name'] ?>">
                        </a>

                        <? endforeach ?>
                    <? else: ?>
                        <? foreach ($desserts as $elem): ?>

                    <!-- Карточка -->
                    <a href="<?= "$_SERVER[REQUEST_URI]&id=$elem[id]" ?>" class="square-with-product">
                        <img src="images/menu/desserts/<?= $elem['picture'] ?>" alt="<?= $elem['name'] ?>">
                    </a>

                        <? endforeach ?>
                    <? endif ?>

                </div> <!-- /.layer -->
            </div> <!-- /.container -->
        </div> <!-- /.block-cafe -->

        <div class="gradient-line"></div> <!-- Линия с градиентом -->

    </main> <!-- /.main -->

    <? require_once('footer.php') ?>

    <div class="follow-cursor"></div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const followCursor = () => { // объявляем функцию followCursor
                const el = document.querySelector('.follow-cursor'); // ищем элемент, который будет следовать за курсором

                window.addEventListener('mousemove', e => { // при движении курсора
                    const target = e.target; // определяем, где находится курсор
                    if (!target) return;

                    if (target.closest('a.square-with-product > img')) { // если курсор наведён на ссылку
                        el.classList.add('follow-cursor_active'); // элементу добавляем активный класс
                        el.innerHTML = target.closest('img').getAttribute('alt');
                    }
                    else { // иначе
                        el.classList.remove('follow-cursor_active'); // удаляем активный класс
                        el.innerHTML = "";
                    }

                    el.style.left = e.pageX + 'px'; // задаём элементу позиционирование слева
                    el.style.top = e.pageY + 'px'; // задаём элементу позиционирование сверху
                });
            }
            followCursor() // вызываем функцию followCursor
        })
    </script>
</body>