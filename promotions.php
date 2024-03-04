<?
    session_start();
    require_once('functions.php');
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <!-- База -->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <!-- Иконка с названием -->
    <link rel="shortcut icon" href="images/icons/company_icon.png">
    <title>Акции и скидки от сети предприятий кафе «Вкусно – и точка!»</title>

    <!-- Стили страницы -->
    <link rel='stylesheet' type='text/css' href='fonts/sansation/stylesheet.css'>
    <link rel='stylesheet' type='text/css' href='styles/reset_style.css'>
    <link rel='stylesheet' type='text/css' href='styles/basic.css'>
    <link rel='stylesheet' type='text/css' href='styles/promotions.css'>
</head>

<body>
    <!-- Шапка страницы -->
    <? require_once('header.php') ?>

    <!-- Основное тело -->
    <main style="margin-top: 90px">
        
        <div class="gradient-line"></div> <!-- Линия с градиентом -->

        <!-- Реклама акций -->
        <div class="marking-mockup">
            <div class="container">

                <!-- Постер с акцией -->
                <div class="poster-storage">
                    <img class="advertising-poster" src="images\page-elements\glasses-promotion.png" alt="Постер с акцией">
                    <!-- Перемотка -->
                    <a href="#coupon">↓</a>
                </div> <!-- /.poster-storage -->

            </div> <!-- /.container -->
        </div> <!-- /.marking-mockup -->

        <div class="gradient-line"></div> <!-- Линия с градиентом -->

        <!-- Акции и купоны -->
        <div class="marking-mockup">
            <div class="container" id="coupon">

                <!-- [Купон №1] -->
                <div class="promotion-card">

                    <!-- Информация о купоне -->
                    <div class="info-card">
                        <h1>✦ Стакан всего за 1 рубль !</h1>
                        <span>– При покупке от 449 рублей 💸</span>
                        <p>Предъяви QR-код в терминале самообслуживания, на кассе или используй в заказе и получи Стакан по специальной цене 1 рубль при покупке от 449 рублей. Акцией можно воспользоваться несколько раз во время срока действия. За докупленные продукты, не участвующие в акции, будут начислены Бонусы. В одном заказе нельзя использовать несколько одинаковых акций, разных акций не более 4. Акция действует до 12.12.2023.</p>
                    </div>
                    
                    <!-- Фото товара -->
                    <div class="promotion-card-image"><img src="images\page-elements\glasses-for-rubles.png" alt="Стаканы"></div>
                
                </div> <!-- /.promotion-card -->

                <!-- [Купон №2] -->
                <div class="promotion-card">

                    <!-- Информация о купоне -->
                    <div class="info-card">
                        <h1>✦ Кофе и выпечка !</h1>
                        <span>– При покупке от 2-х стаканов кофе ☕</span>
                        <p>При заказе 2-х стаканов кофе вы получаете скидку 50% на любой вид выпечки. Идеальное сочетание для сладкой паузы. Купон можно будет использовать на кассе или во время оформления заказа на сайте. Убедитесь что в вашей корзине находится не менее 2-х горячих напитков, иначе воспользоваться купоном не получится! Акция действует до 28.12.2023.</p>
                    </div>
                    
                    <!-- Фото товара -->
                    <div class="promotion-card-image"><img src="images\page-elements\glass-of-cappuccino.png" alt="Стаканы"></div>
                
                </div> <!-- /.promotion-card -->

                <!-- [Купон №3] -->
                <div class="promotion-card" style="margin: 0px;">

                    <!-- Информация о купоне -->
                    <div class="info-card">
                        <h1>✦ Ужин вдвоем !</h1>
                        <span>– При покупке 2-х бургеров «Биг Спешиал» 🍔</span>
                        <p>Закажите два бургера «Биг Спешиал» и получите скидку 40% на десерт. Отличный повод устроить романтический ужин с вашей второй половиной. Акцией можно воспользоваться только 1 раз. В одном заказе можно использовать только одну акцию. Акция действует до 01.01.2024.</p>
                    </div>
                    
                    <!-- Фото товара -->
                    <div class="promotion-card-image"><img src="images\page-elements\big-special-burger-promotion.png" alt="Стаканы"></div>
               
                </div> <!-- /.promotion-card -->

            </div> <!-- /.container -->
        </div> <!-- /.marking-mockup -->

        <div class="gradient-line"></div> <!-- Линия с градиентом -->

    </main> <!-- /.main -->

    <!-- Подвал страницы -->
    <? require_once('footer.php') ?>
</body>