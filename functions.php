<?
    require_once('db_model.php');

    function registration($name, $surname, $phone, $password, $repeat_password)
    {
        if ($password == $repeat_password)
        {
            $phone = preg_replace('/[^0-9]/', "", $phone);
            
            $find_phone = get_user_info($phone);

            if ($find_phone == null)
            {
                $password_hash = password_hash($password, PASSWORD_BCRYPT);

                $arr = [];
                $db = new MysqlModel();
                $arr = $db->query("
                    INSERT INTO user(
                        `id`,
                        `name`,
                        `surname`,
                        `phone`,
                        `password_hash`,
                        `role`,
                        `point`
                    )
                    VALUES(
                        NULL,
                        '$name',
                        '$surname',
                        '$phone',
                        '$password_hash',
                        0,
                        0
                    )
                ");

                $autoriz = get_user_info($password_hash);

                return $autoriz;
            }
            else
            {
                return "Данный номер телефона уже используется.";
            }
        }
        else
        {
            return "Пароли не совпадают.";
        } 
    }
    
    function authorization($phone, $password)
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->goResultOnce("
            SELECT 
                * 
            FROM
                user
            WHERE
                phone = '$phone' 
        ");

        if (!empty($arr))
        {
            $password_hash = $arr['password_hash'];
            $verify = password_verify($password, $password_hash);

            return $result = $verify == true ? $arr : false;
        }
        else
        {
            return false;
        }
    }

    function edit_profile($id, $surname, $name, $phone, $password_hash = false)
    {
        $arr = [];
        $db = new MysqlModel();

        if (!empty($password_hash))
        {
            $arr = $db->query("
                UPDATE
                    `user`
                SET
                    `name` = '$name',
                    `surname` = '$surname',
                    `phone` = '$phone',
                    `password_hash` = '$password_hash'
                WHERE
                    id = $id
            ");
        }
        else
        {
            $arr = $db->query("
            UPDATE
                `user`
            SET
                `name` = '$name',
                `surname` = '$surname',
                `phone` = '$phone'
            WHERE
                id = $id
        "); 
        }
    }

    function edit_point($user_id, $count)
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->query("
            UPDATE
                `user`
            SET
                `point` = $count
            WHERE
                id = $user_id
        ");
    }

    function delete_coupon($coupon_id)
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->query("
            DELETE
            FROM
                `coupon`
            WHERE
                id = $coupon_id
        ");
    }

    function get_user_info($value)
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->goResultOnce("
            SELECT
                *
            FROM
                user
            WHERE
                password_hash = '$value' OR id = '$value' OR phone = '$value'
        ");

        return $arr;
    }

    function get_user_coupon($user_id)
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->goResult("
            SELECT 
                *
            FROM
                coupon
            WHERE
                user_id = $user_id
        ");

        return $arr;
    }

    function get_coupon_by_id($coupon_id)
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->goResultOnce("
            SELECT 
                *
            FROM
                coupon
            WHERE
                id = $coupon_id
        ");

        return $arr;
    }

    function get_coupons_by_items_in_basket($user_id, $basket)
    {
        $user_coupon = get_user_coupon($user_id);

        $available_coupons = [];

        foreach ($basket as $key => $elem)
        {
            foreach ($user_coupon as $coupon)
            {
                if ($coupon['product_id'] == $key) $available_coupons[] = $coupon;
            }
        }

        return $available_coupons;
    }

    function get_product($id)
    {
        $product = [];
        $db = new MysqlModel();

        $product = $db->goResultOnce("
            SELECT
                *
            FROM
                product
            WHERE
                id = $id
        ");

        $ingredient = [];
        $ingredient = $db->goResult("
            SELECT
                i.id,
                i.name,
                i.compound
            FROM
                ingredient i,
                ingredient_for_product ifp,
                product p
            WHERE
                i.id = ifp.ingredient_id AND p.id = ifp.product_id AND p.id = $id
        ");

        $product['ingredient'] = $ingredient;

        return $product;
    }

    function get_all_products_in_category($category)
    {
        $all_products = [];
        $db = new MysqlModel();

        $all_products = $db->goResult("
            SELECT
                *
            FROM
                product
            WHERE
                category_id = $category
        ");

        return $all_products;
    }

    function get_all_products_in_status($status)
    {
        $all_products = [];
        $db = new MysqlModel();

        $all_products = $db->goResult("
            SELECT
                *
            FROM
                product
            WHERE
                status = $status
        ");

        return $all_products;
    }

    function get_ingredient()
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->goResult("
            SELECT
                *
            FROM
                ingredient
        ");

        return $arr;
    }

    function get_ingredient_by_id($id)
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->goResultOnce("
            SELECT
                *
            FROM
                ingredient
            WHERE
                id = $id
        ");

        return $arr;
    }

    function create_ingredient($name, $compound)
    {
        $arr = [];
        $db = new MysqlModel();

        $arr = $db->query("
            INSERT INTO ingredient(
                id,
                name,
                compound
            )
            VALUES(
                NULL,
                '$name',
                '$compound'
            )
        ");

        return 'access';
    }

    function create_product($name, $description, $proteins, $fats, $carbohydrates, $weight, $ccal, $price, $category_id, $picture, $ingredients)
    {
        $picture = ($picture != false) ? "'$picture'" : "NULL";

        $product = [];
        $db = new MysqlModel();

        $product = $db->query("
            INSERT INTO `product`(
                `id`,
                `name`,
                `description`,
                `proteins`,
                `fats`,
                `carbohydrates`,
                `weight`,
                `ccal`,
                `price`,
                `category_id`,
                `status`,
                `picture`
            )
            VALUES(
                NULL,
                '$name',
                '$description',
                $proteins,
                $fats,
                $carbohydrates,
                $weight,
                $ccal,
                $price,
                $category_id,
                1,
                $picture
            )
        ");

        $product = $db->goResultOnce("
            SELECT
                *
            FROM
                `product`
            WHERE
                name = '$name' AND
                description = '$description'
        ");

        $product_id = $product['id'];

        if (!empty($ingredients))
        {
            foreach ($ingredients as $ingredient_id)
            {
                $ingredient = [];
                $ingredient = $db->query("
                    INSERT INTO `ingredient_for_product`(
                        `id`,
                        `product_id`,
                        `ingredient_id`
                    )
                    VALUES(
                        NULL,
                        $product_id,
                        $ingredient_id
                    )
                ");
            }
        }
    }

    function get_all_product()
    {
        $product = [];
        $db = new MysqlModel();

        $product = $db->goResult("
            SELECT
                *
            FROM
                product
        ");
        
        return $product;
    }

    function update_ingredient($id, $name, $compound)
    {
        $ingredient = [];
        $db = new MysqlModel();

        $ingredient = $db->query("
            UPDATE
                `ingredient`
            SET
                `name` = '$name',
                `compound` = '$compound'
            WHERE
                id = $id
        ");
    }

    function update_product($id, $name, $description, $proteins, $fats, $carbohydrates, $weight, $ccal, $price, $category_id, $status, $ingredients, $picture)
    {
        $product = [];
        $db = new MysqlModel();

        $product = $db->query("
            UPDATE
                `product`
            SET
                `name` = '$name',
                `description` = '$description',
                `proteins` = $proteins,
                `fats` = $fats,
                `carbohydrates` = $carbohydrates,
                `weight` = $weight,
                `ccal` = $ccal,
                `price` = $price,
                `category_id` = $category_id,
                `status` = $status,
                `picture` = '$picture'
            WHERE
                id = $id
        ");

        $product = $db->query("
            DELETE
            FROM
                `ingredient_for_product`
            WHERE
                product_id = $id
        ");

        if (!empty($ingredients))
        {
            foreach ($ingredients as $ingredient_id)
            {
                $ingredient = [];
                $ingredient = $db->query("
                    INSERT INTO `ingredient_for_product`(
                        `id`,
                        `product_id`,
                        `ingredient_id`
                    )
                    VALUES(
                        NULL,
                        $id,
                        $ingredient_id
                    )
                ");
            }
        }

    }

    function delete_ingredient($id)
    {
        $ingredient = [];
        $db = new MysqlModel();

        $ingredient = $db->query("
            DELETE
            FROM
                `ingredient`
            WHERE
                id = $id
        ");
    }

    function delete_product($id)
    {
        $ingredient = [];
        $db = new MysqlModel();

        $ingredient = $db->query("
            DELETE
            FROM
                `product`
            WHERE
                id = $id
        ");
    }

    function generate_random_coupon($user_id)
    {
        $product = [];
        $db = new MysqlModel();

        $product = $db->goResult("
            SELECT
                *
            FROM
                `product`
        ");

        $product_id = $product[rand(0, count($product) - 1)]['id'];

        $discount = floor(rand(10, 50) / 5) * 5;

        $pattern_letter = [1 => 'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k', 'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v', 'w', 'x', 'y', 'z'];
        $pattern_num = [1 => 1, 2, 3, 4, 5, 6, 7, 8, 9];

        $code = "";
        $query = [];

        for ($i = 1; $i <= 10; $i++)
        {
            if ($i <= 6)
            {
                $code .= $pattern_letter[rand(1, 26)];
            }
            else
            {
                $code .= $pattern_num[rand(1, 9)];
            }

            if ($i == 10)
            {
                $query = $db->goResultOnce("
                    SELECT
                        *
                    FROM
                        `coupon`
                    WHERE
                        code = '$code'
                ");
        
                if (!empty($query))
                {
                    $i = 1;
                }
            }
        }

        $discount = (int) $discount;

        $query = $db->query("
            INSERT INTO `coupon`(
                `id`,
                `discount`,
                `code`,
                `product_id`,
                `user_id`
            )
            VALUES(
                NULL,
                $discount,
                '$code',
                $product_id,
                $user_id
            )
        ");

        return ['product_id' => $product_id, 'discount' => $discount, 'code' => $code];
    }
?>