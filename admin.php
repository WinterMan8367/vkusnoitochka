<? 
    session_start();
    $server_path = $_SERVER['DOCUMENT_ROOT'];
    require_once('functions.php');

    if (!empty($_SESSION))
    {
        if ($_SESSION['user']['role'] != 1)
        {
            header('Location: index.php');
            exit;
        }
    }
    else
    {
        header('Location: index.php');
        exit;
    }

    $categories = [1 => 'burgers', 2 => 'drinks', 3 => 'snacks', 4 => 'desserts', 5 => 'sauces', 6 => 'salads-and-rolls'];

    if (!empty($_GET))
    {
        if (!empty($_POST))
        {
            $error = [];
    
            if ($_GET['action'] == 'create_ingredient')
            {
                if (!empty($_POST['name']) and !empty($_POST['compound']))
                {
                    $result = create_ingredient($_POST['name'], $_POST['compound']);
        
                    if ($result != 'access')
                    {
                        $error[] = "Ошибка ввода ингредиента.";
                    }
        
                    $_POST = [];
                    header("Location: admin.php");
                    exit;
                }
            }

            if ($_GET['action'] == 'create_product')
            {
                if (
                    !empty($_POST['name']) and
                    !empty($_POST['description']) and
                    isset($_POST['proteins']) and
                    isset($_POST['fats']) and
                    isset($_POST['carbohydrates']) and
                    !empty($_POST['weight']) and
                    isset($_POST['ccal']) and
                    !empty($_POST['price']) and
                    !empty($_POST['category_id'])
                )
                {
                    $proteins = (empty($_POST['proteins']) or $_POST['proteins'] == 0) ? 0 : $_POST['proteins'];
                    $fats = (empty($_POST['fats']) or $_POST['fats'] == 0) ? 0 : $_POST['fats'];
                    $carbohydrates = (empty($_POST['carbohydrates']) or $_POST['carbohydrates'] == 0) ? 0 : $_POST['carbohydrates'];
                    $ccal = (empty($_POST['ccal']) or $_POST['ccal'] == 0) ? 0 : $_POST['ccal'];
                    $picture = false;

                    if (!empty($_FILES['picture']['tmp_name']))
                    {
                        if ($_FILES['picture']['type'] == 'image/png')
                        {
                            $category = $categories[$_POST['category_id']];
            
                            move_uploaded_file($_FILES['picture']['tmp_name'], $server_path . "/images/menu/" . $category . "/" . basename($_FILES['picture']['name']));
                            
                            $picture = $_FILES['picture']['name'];

                            $ingredients = [];
        
                            foreach ($_POST as $key => $elem)
                            {
                                if (preg_match('/(ingredient_id)/', $key))
                                {
                                    $ingredients[] = $elem;
                                }
                            }

                            create_product(
                                $_POST['name'],
                                $_POST['description'],
                                $proteins,
                                $fats,
                                $carbohydrates,
                                $_POST['weight'],
                                $ccal,
                                $_POST['price'],
                                $_POST['category_id'],
                                $picture,
                                $ingredients
                            );
                
                            $_POST = [];
                            header("Location: admin.php");
                            exit;
                        }
                        else
                        {
                            $error[] = "Загруженный файл должен быть формата '.png'.";
                        }
                    }
                    else
                    {
                        $error[] = "Вы не выбрали картинку для товара.";
                    }
                }
                else
                {
                    $error[] = "Один из параметров пустой.";
                }
            }

            if ($_GET['action'] == 'update_ingredient')
            {
                if (!empty($_POST['ingredient_id']))
                {
                    $ingredient = get_ingredient_by_id($_POST['ingredient_id']);
                }

                if (!empty($_POST['id']) and !empty($_POST['name']) and !empty($_POST['compound']))
                {
                    update_ingredient($_POST['id'], $_POST['name'], $_POST['compound']);

                    $_POST = [];
                    header("Location: admin.php");
                    exit;
                }
                else
                {
                    if (empty($_POST['verify']))
                    {
                        $error[] = "Один из параметров пустой.";
                    }
                }
            }
    
            if ($_GET['action'] == 'update_product')
            {
                if (!empty($_GET['category_id']))
                if (!empty($_POST['product_id']))
                {
                    $product = get_product($_POST['product_id']);
                }

                if (
                    !empty($_POST['id']) and
                    !empty($_POST['name']) and
                    !empty($_POST['description']) and
                    isset($_POST['proteins']) and
                    isset($_POST['fats']) and
                    isset($_POST['carbohydrates']) and
                    !empty($_POST['weight']) and
                    isset($_POST['ccal']) and
                    !empty($_POST['price']) and
                    isset($_POST['category_id']) and
                    isset($_POST['status'])
                )
                {
                    $product = get_product($_POST['id']);

                    $picture = $product['picture'];

                    if (!empty($_FILES['picture']['tmp_name']))
                    {
                        if ($_FILES['picture']['type'] == 'image/png')
                        {
                            $new_category = $categories[$_POST['category_id']];
            
                            move_uploaded_file($_FILES['picture']['tmp_name'], $server_path . "/images/menu/" . $new_category . "/" . basename($_FILES['picture']['name']));
                            
                            if ($_FILES['picture']['name'] != $product['picture'])
                            {
                                $old_category = $categories[$product['category_id']];
                                unlink($server_path . "/images/menu/" . $old_category . "/" . $product['picture']);
                            }

                            $picture = $_FILES['picture']['name'];
                        }
                        else
                        {
                            $error[] = "Загруженный файл должен быть формата '.png'.";
                        }
                    }
                    else
                    {
                        if ($product['category_id'] != $_POST['category_id'])
                        {
                            $old_category = $categories[$product['category_id']];
                            $new_category = $categories[$_POST['category_id']];
                            copy($server_path . "/images/menu/" . $old_category . "/" . $product['picture'], $server_path . "/images/menu/" . $new_category . "/" . $product['picture']);
                            unlink($server_path . "/images/menu/" . $old_category . "/" . $product['picture']);
                        }
                    }


                    $proteins = (empty($_POST['proteins']) or $_POST['proteins'] == 0) ? 0 : $_POST['proteins'];
                    $fats = (empty($_POST['fats']) or $_POST['fats'] == 0) ? 0 : $_POST['fats'];
                    $carbohydrates = (empty($_POST['carbohydrates']) or $_POST['carbohydrates'] == 0) ? 0 : $_POST['carbohydrates'];
                    $ccal = (empty($_POST['ccal']) or $_POST['ccal'] == 0) ? 0 : $_POST['ccal'];

                    $ingredients = [];
        
                    foreach ($_POST as $key => $elem)
                    {
                        if (preg_match('/(ingredient_id)/', $key))
                        {
                            $ingredients[] = $elem;
                        }
                    }
            
                    update_product(
                        $_POST['id'],
                        $_POST['name'],
                        $_POST['description'],
                        $proteins,
                        $fats,
                        $carbohydrates,
                        $_POST['weight'],
                        $ccal,
                        $_POST['price'],
                        $_POST['category_id'],
                        $_POST['status'],
                        $ingredients,
                        $picture
                    );
        
                    $_POST = [];
                    header("Location: admin.php");
                    exit;

                }
                else
                {
                    if (empty($_POST['verify']))
                    {
                        $error[] = "Один из параметров пустой.";
                    }
                }
            }

            if ($_GET['action'] == 'delete_ingredient')
            {
                if (!empty($_POST['ingredient_id']))
                {
                    $ingredient = get_ingredient_by_id($_POST['ingredient_id']);
                }
                
                if (!empty($_POST['verify']))
                {
                    if ($_POST['verify'] == 'delete')
                    {
                        if (!empty($_POST['id']))
                        {
                            delete_ingredient($_POST['id']);
        
                            $_POST = [];
                            header("Location: admin.php");
                            exit;
                        }
                    }
                    else
                    {
                        $error[] = "Неправильно введено ключевое слово.";
                    }
                }
            }

            if ($_GET['action'] == 'delete_product')
            {
                if (!empty($_POST['product_id']))
                {
                    $product = get_product($_POST['product_id']);
                }
                
                if (!empty($_POST['verify']))
                {
                    if ($_POST['verify'] == 'delete')
                    {
                        if (!empty($_POST['id']))
                        {
                            delete_product($_POST['id']);
        
                            $_POST = [];
                            header("Location: admin.php");
                            exit;
                        }
                    }
                    else
                    {
                        $error[] = "Неправильно введено ключевое слово.";
                    }
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="ru">
    <head>
        <!-- База -->
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">

        <!-- Иконка с названием -->
        <link rel="shortcut icon" href="images/icons/company_icon.png">
        <title>«Вкусно – и точка!» – Кабинет администратора</title>

        <!-- Стили страницы -->
        <link rel='stylesheet' type='text/css' href='fonts/sansation/stylesheet.css'>
        <link rel='stylesheet' type='text/css' href='styles/reset_style.css'>
        <link rel='stylesheet' type='text/css' href='styles/basic.css'>
        <link rel='stylesheet' type='text/css' href='styles/admin.css'>
    </head>
    <body>
        <?
        if (!empty($error))
        {
            foreach ($error as $elem)
            {
                echo "<p style='color: red'>$elem</p><br>";
            }
        }
        ?>

        <!-- Заголовок кабинета -->
        <h3 id="administrators-office">« Кабинет администратора »</h3>

        <!-- Выбор действия -->
        <form method="GET" action="" class="choice">
            <p>✦ Выберите действие.</p>
            <select name="action">
                <option selected>Выберите действие</option>
                <option value="create_ingredient" <?= (!empty($_GET['action']) and $_GET['action'] == "create_ingredient") ? "selected" : "" ?>>Добавить ингредиент</option>
                <option value="create_product" <?= (!empty($_GET['action']) and $_GET['action'] == "create_product") ? "selected" : "" ?>>Добавить товар</option>
                <option value="update_ingredient" <?= (!empty($_GET['action']) and $_GET['action'] == "update_ingredient") ? "selected" : "" ?>>Обновить ингредиент</option>
                <option value="update_product" <?= (!empty($_GET['action']) and $_GET['action'] == "update_product") ? "selected" : "" ?>>Обновить товар</option>
                <option value="delete_ingredient" <?= (!empty($_GET['action']) and $_GET['action'] == "delete_ingredient") ? "selected" : "" ?>>Удалить ингредиент</option>
                <option value="delete_product" <?= (!empty($_GET['action']) and $_GET['action'] == "delete_product") ? "selected" : "" ?>>Удалить товар</option>
            </select>
            <input type="submit">
        </form> <!-- /.choice-->

        <? if (!empty($_GET)): ?>

            <!-- Добавить ингредиент -->
            <? if ($_GET['action'] == 'create_ingredient'): ?>
                <form method="POST" action="" class="choice">
                    <p>✦ Добавить ингредиент</p>
                    <input name="name" maxlength="100" type="text" placeholder="Название ингредиента">
                    <textarea name="compound" maxlength="2048" placeholder="Состав ингредиента"></textarea>
                    <input type="submit">
                </form>

                <div class="choice">
                    <p>✦ Существующие ингредиенты:</p>
                    <hr>
                    <?
                        $ingredient = get_ingredient();
                        foreach ($ingredient as $elem):
                    ?>
                    <p><?= $elem['name'] ?></p>
                    <? endforeach ?>
                </div>
            <? endif ?>
            
            <!-- Добавить товар -->
            <? if ($_GET['action'] == 'create_product'):  ?>
                <form enctype="multipart/form-data" method="POST" action="" class="choice">
                    <p>✦ Добавить товар</p>
                    <input name="MAX_FILE_SIZE" type="hidden" value="10485760">
                    <p>Выберите картинку товара:</p>
                    <input name="picture" type="file">
                    <input name="name" type="text" placeholder="Название товара">
                    <textarea name="description" maxlength="255" placeholder="Описание товара"></textarea>
                    <input name="proteins" type="text" placeholder="Белки, граммы">
                    <input name="fats" type="text" placeholder="Жиры, граммы">
                    <input name="carbohydrates" type="text" placeholder="Углеводы, граммы">
                    <input name="weight" type="text" placeholder="Вес, граммы">
                    <input name="ccal" type="text" placeholder="Энергетическая ценность, ккал">
                    <input name="price" type="text" placeholder="Цена, рубли">
                    <select name="category_id">
                        <option value="1">Бургеры</option>
                        <option value="2">Напитки</option>
                        <option value="3">Картофель и стартеры</option>
                        <option value="4">Десерты</option>
                        <option value="5">Соусы</option>
                        <option value="6">Салаты и роллы</option>
                    </select>
                    <hr>
                    <p>✦ Выберите ингредиенты:</p>
                    <hr>
                    <? foreach (get_ingredient() as $elem): ?>
                        <label>
                            <input type="checkbox" name="ingredient_id_<?=$elem['id']?>" value="<?=$elem['id']?>">
                            <?=$elem['name']?>
                        </label>
                    <? endforeach ?>
                    <input type="submit">
                </form> <!-- /.choice-->
            <? endif ?>
            
            <!-- Изменить ингредиент -->
            <? if ($_GET['action'] =='update_ingredient'): ?>
                <? if (empty($ingredient)): ?>
                    <form method="POST" action="" class="choice">
                        <p>✦ Изменить ингредиент</p>
                        <input name="verify" type="hidden" value="1"> 
                        <div>    
                            <select name="ingredient_id">
                            <?
                                $all_ingredient = get_ingredient();
                                foreach ($all_ingredient as $elem):
                            ?>
                                <option value="<?=$elem['id']?>"><?=$elem['name']?></option>
                            <? endforeach ?>
                            </select>
                        </div>
                        <input type="submit">
                    </form>
                <? endif ?>

                <? if (!empty($ingredient)): ?>
                    <form method="POST" action="" class="choice">
                        <input name="id" type="hidden" value="<?=$ingredient['id']?>">
                        <p>✦ Название ✦</p> <input name="name" type="text" value="<?=$ingredient['name']?>">
                        <p>✦ Состав ✦</p> <textarea name="compound"><?=$ingredient['compound']?></textarea>
                        <input type="submit" value="Изменить">
                    </form>
                <? endif ?>
            <? endif ?>

            <!-- Изменить товар -->
            <? if ($_GET['action'] == 'update_product'): ?>
                <? if (empty($_GET['category_id'])): ?>
                    <div class="choice">
                        <p>✦ Выберите категорию товара:</p> 
                        <hr>
                        <a href="<?= $_SERVER['REQUEST_URI'] . "&category_id=1" ?>">Бургеры</a>
                        <a href="<?= $_SERVER['REQUEST_URI'] . "&category_id=2" ?>">Напитки</a>
                        <a href="<?= $_SERVER['REQUEST_URI'] . "&category_id=3" ?>">Картофель и стартеры</a>
                        <a href="<?= $_SERVER['REQUEST_URI'] . "&category_id=4" ?>">Десерты</a>
                        <a href="<?= $_SERVER['REQUEST_URI'] . "&category_id=5" ?>">Соусы</a>
                        <a href="<?= $_SERVER['REQUEST_URI'] . "&category_id=6" ?>">Салаты и роллы</a>
                    </div>
                <? else: ?>
                    <? if (empty($product)): ?>
                        <form method="POST" action="" class="choice">
                            <p>✦ Изменить товар</p>
                            <input name="verify" type="hidden" value="1">  
                            <select name="product_id">
                            <?
                                $all_product = get_all_products_in_category($_GET['category_id']);
                                foreach ($all_product as $elem):
                            ?>
                                <option value="<?=$elem['id']?>"><?=$elem['name']?></option>
                            <? endforeach ?>
                            </select>
                            <input type="submit">
                        </form>
                    <? else: ?>
                        <form enctype="multipart/form-data" method="POST" action="" class="choice">
                            <p style="font-weight: bold"><?=$product['name']?></p>
                            <hr>
                            <input name="MAX_FILE_SIZE" type="hidden" value="10485760">
                            <p>✦ Картинка товара</p>
                            <? $category = $categories[$product['category_id']]; ?>
                            <div class="picture-box">
                                <img src="/images/menu/<?= "$category/$product[picture]" ?>" alt="<?=$product['name']?>">
                            </div>
                            <input name="picture" type="file">
                            <input name="id" type="hidden" value="<?=$product['id']?>">
                            <p>✦ Название</p> <input name="name" type="text" value="<?=$product['name']?>">
                            <p>✦ Описание</p> <textarea name="description" maxlength="255" placeholder="Описание товара"><?=$product['description']?></textarea>
                            <p>✦ Белки</p> <input name="proteins" type="text" placeholder="Белки" value="<?=$product['proteins']?>">
                            <p>✦ Жиры</p> <input name="fats" type="text" placeholder="Жиры" value="<?=$product['fats']?>">
                            <p>✦ Углеводы</p> <input name="carbohydrates" type="text" placeholder="Углеводы" value="<?=$product['carbohydrates']?>">
                            <p>✦ Вес</p> <input name="weight" type="text" placeholder="Вес, граммы" value="<?=$product['weight']?>">
                            <p>✦ Калорийность</p> <input name="ccal" type="text" placeholder="Энергетическая ценность, ккал" value="<?=$product['ccal']?>">
                            <p>✦ Цена</p> <input name="price" type="text" placeholder="Цена, рубли" value="<?=$product['price']?>">
                            <p>✦ Категория товара</p>
                            <select name="category_id">
                                <option value="1" <?= ($product['category_id'] == 1) ? "selected" : ""?>>Бургеры</option>
                                <option value="2" <?= ($product['category_id'] == 2) ? "selected" : ""?>>Напитки</option>
                                <option value="3" <?= ($product['category_id'] == 3) ? "selected" : ""?>>Картофель и стартеры</option>
                                <option value="4" <?= ($product['category_id'] == 4) ? "selected" : ""?>>Десерты</option>
                                <option value="5" <?= ($product['category_id'] == 5) ? "selected" : ""?>>Соусы</option>
                                <option value="6" <?= ($product['category_id'] == 6) ? "selected" : ""?>>Салаты и роллы</option>
                            </select>
                            <p>✦ Статус товара</p>
                            <select name="status">
                                <option value="0" <?= ($product['status'] == 0) ? "selected" : ""?>>Обычный</option>
                                <option value="1" <?= ($product['status'] == 1) ? "selected" : ""?>>Новинка</option>
                                <option value="2" <?= ($product['status'] == 2) ? "selected" : ""?>>Популярное</option>
                            </select>
                            <p>✦ Ингредиенты:</p>
                            <? foreach (get_ingredient() as $elem): ?>
                                <?
                                    $i = false;
                                    foreach ($product['ingredient'] as $ingredient)
                                    {
                                        if ($ingredient['id'] == $elem['id'])
                                        {
                                            $i = true;
                                        }
                                    }
                                ?>
                                <label>
                                    <input type="checkbox" name="ingredient_id_<?=$elem['id']?>" value="<?=$elem['id']?>" <?= ($i) ? "checked" : "" ?>>
                                    <?=$elem['name']?>
                                </label>
                            <? endforeach ?>
                            <input type="submit" value="Изменить">
                        </form>
                    <? endif ?>
                <? endif ?>
            <? endif ?>

            <!-- Удалить ингредиент -->
            <? if ($_GET['action'] == 'delete_ingredient'): ?>
                <? if (empty($ingredient)): ?>
                    <form method="POST" action="" class="choice">
                        <p>✦ Удалить ингредиент</p>
                        <div>    
                            <select name="ingredient_id">
                            <?
                                $all_ingredient = get_ingredient();
                                foreach ($all_ingredient as $elem):
                            ?>
                                <option value="<?=$elem['id']?>"><?=$elem['name']?></option>
                            <? endforeach ?>
                            </select>
                        </div>
                        <input type="submit" value="Удалить">
                    </form>
                <? endif ?>
                <? if (!empty($ingredient)): ?>
                    <form method="POST" action="" class="choice">
                        <p>✦ Вы действительно хотите удалить ингредиент <span style="font-weight: bold"><?=$ingredient['name']?></span>?</p>
                        <p>Напишите слово <b>delete</b> с учётом регистра, если действительно хотите удалить ингредиент.</p>
                        <input name="id" type="hidden" value="<?=$ingredient['id']?>">
                        <input name="verify" type="text">
                        <input type="submit" value="Удалить">
                    </form>
                <? endif ?>
            <? endif ?>

            <!-- Удалить товар -->
            <? if ($_GET['action'] == 'delete_product'): ?>
                <? if (empty($product)): ?>
                    <form method="POST" action="" class="choice">
                        <p>✦ Удалить товар</p>
                        <select name="product_id">
                        <?
                            $all_product = get_all_product();
                            foreach ($all_product as $elem):
                        ?>
                            <option value="<?=$elem['id']?>"><?=$elem['name']?></option>
                        <? endforeach ?>
                        </select>
                        </div>
                        <input type="submit" value="Удалить">
                    </form>
                <? endif ?>
                <? if (!empty($product)): ?>
                    <form method="POST" action="" class="choice">
                        <p>✦ Вы действительно хотите удалить товар <span style="font-weight: bold"><?=$product['name']?></span>?</p>
                        <p>Напишите слово <b>delete</b> с учётом регистра, если действительно хотите удалить товар.</p>
                        <input name="id" type="hidden" value="<?=$product['id']?>">
                        <input name="verify" type="text">
                        <input type="submit" value="Удалить">
                    </form>
                <? endif ?>
            <? endif ?>
        <? endif ?>
        
        <!-- Кнопка возвращения в личный кабинет -->
        <div class="choice" style="margin-bottom: 20px;">
            <a href="personal-profile.php" id="come-back">Вернуться в личный кабинет</a>
        </div>
    </body>
</html>