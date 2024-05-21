<?php

include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:index.php');
    exit;
}

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    die('Product ID is not set.');
}

// Fetch product details
$get_idproduct = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$product_id'") or die('Query failed');

if (mysqli_num_rows($get_idproduct) == 0) {
    die('Product not found.');
}

$fetch_products = mysqli_fetch_assoc($get_idproduct);

// Fetch sizes and quantities for the current product
$get_sizes_quantities = mysqli_prepare($conn, "SELECT size, quantity FROM `product_sizes` WHERE product_id = ?");
mysqli_stmt_bind_param($get_sizes_quantities, "i", $product_id);
mysqli_stmt_execute($get_sizes_quantities);
$fetch_sizes_quantities = mysqli_stmt_get_result($get_sizes_quantities);
$sizes_quantities = mysqli_fetch_all($fetch_sizes_quantities, MYSQLI_ASSOC);
mysqli_stmt_close($get_sizes_quantities);

if (isset($_POST['add_to_cart'])) {
    $product_name = $fetch_products['name'];
    $product_price = $fetch_products['price'];
    $product_size = $_POST['product_size'];
    $product_image = $fetch_products['image'];
    $product_quantity = intval($_POST['product_quantity']);

    // Check if the product is already in the cart
    $check_cart_numbers = mysqli_prepare($conn, "SELECT * FROM `cart` WHERE product_name = ? AND user_id = ?");
    mysqli_stmt_bind_param($check_cart_numbers, "si", $product_name, $user_id);
    mysqli_stmt_execute($check_cart_numbers);
    $fetch_quantcart = mysqli_stmt_get_result($check_cart_numbers);
    $cart_item = mysqli_fetch_assoc($fetch_quantcart);
    mysqli_stmt_close($check_cart_numbers);

    // Fetch the available quantity of the product
    $compare_quant = mysqli_prepare($conn, "SELECT quant FROM `products` WHERE name = ?");
    mysqli_stmt_bind_param($compare_quant, "s", $product_name);
    mysqli_stmt_execute($compare_quant);
    $fetch_quantitem = mysqli_stmt_get_result($compare_quant);
    $product_stock = mysqli_fetch_assoc($fetch_quantitem);
    mysqli_stmt_close($compare_quant);

    if ($product_stock['quant'] == 0 || $product_stock['quant'] < 0 || $product_quantity > $product_stock['quant']) {
        // Product is out of stock or quantity exceeds available quantity
        $message[] = 'Product out of stock or quantity exceeds available quantity';
    } elseif ($cart_item) {
        // Product is already in the cart
        $message[] = 'Product already added to cart';
    } else {
        // Insert the product into the cart
    $insert_cart = mysqli_prepare($conn, "INSERT INTO `cart` (user_id, product_name, pro_size, price, quantity, image) VALUES (?, ?, ?, ?, ?, ?)");
    mysqli_stmt_bind_param($insert_cart, "isssis", $user_id, $product_name, $product_size, $product_price, $product_quantity, $product_image);
    mysqli_stmt_execute($insert_cart);
    mysqli_stmt_close($insert_cart);
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styleindex.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <style>
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }

        body {
            font-family: sans-serif;
            background: #C1908B;
        }

        .container {
            max-width: 100%;
            margin: auto;
            height: 80vh;
            margin-top: 5px;
            background: white;
            box-shadow: 5px 5px 10px 3px rgba(0, 0, 0, 0.3);
        }

        .left,
        .right {
            width: 50%;
            padding: 30px;
        }

        .flex {
            display: flex;
            justify-content: space-between;
        }

        .flex1 {
            display: flex;
        }

        .main_image {
            width: 40rem;
            height: 40rem;
            /* Set the width to 100% */
            position: relative;
            overflow: hidden;
            border-radius: 1px;
            box-shadow: 5px 5px 8px 3px rgba(0, 0, 0, 0.3);
        }

        .main_image .size_image {
            position: absolute;
            top: 0;
            left: 0;
            width: 30rem;
            height: 30rem;
        }

        .option img {
            width: 75px;
            height: 75px;
            margin: 10px;
            /* padding: 10px; */
            border-radius: 2px;
            border: 0.7px solid black;
        }

        .right {
            padding: 50px 100px 50px 50px;
        }

        h3 {
            color: #af827d;
            margin: 20px 0 20px 0;
            font-size: 25px;
        }

        h5,
        p,
        small {
            color: #837D7C;
        }

        h4 {
            color: red;
        }

        p {
            margin: 20px 0 50px 0;
            line-height: 25px;
        }

        h5 {
            font-size: 15px;
        }

        label,
        .add span,
        .color span {
            width: 25px;
            height: 25px;
            background: #000;
            border-radius: 50%;
            margin: 20px 10px 20px 0;
        }

        .color span:nth-child(2) {
            background: #EDEDED;
        }

        .color span:nth-child(3) {
            background: #D5D6D8;
        }

        .color span:nth-child(4) {
            background: #EFE0DE;
        }

        .color span:nth-child(5) {
            background: #AB8ED1;
        }

        .color span:nth-child(6) {
            background: #F04D44;
        }

        .add label,
        .add span {
            background: none;
            border: 1px solid #C1908B;
            color: #C1908B;
            text-align: center;
            line-height: 25px;
        }

        .add label {
            padding: 10px 30px 0 20px;
            border-radius: 50px;
            line-height: 0;
        }

        /* button {
            width: 100%;
            padding: 10px;
            border: none;
            outline: none;
            background: #C1908B;
            color: white;
            margin-top: 20%;
            border-radius: 30px;
        } */

        @media only screen and (max-width:768px) {
            .container {
                max-width: 90%;
                margin: auto;
                height: auto;
            }

            .left,
            .right {
                width: 100%;
            }

            .container {
                flex-direction: column;
            }
        }

            .left,
            .right {
                padding: 0;
            }

            img {
                width: 100%;
                height: 100%;
            }

            .option {
                display: flex;
                flex-wrap: wrap;
            }

            .size-box {
            border: 1px solid black;
            border-radius: 5px;
            width: 50px;
            height: 50px;
            display: inline-block;
            margin: 5px;
            text-align: center;
            cursor: pointer;
            transition: background-color 0.3s;
            }

            .size-box.selected {
            background-color: crimson;
            }

            .size-box.out-of-stock {
            background-color: grey;
            cursor: not-allowed;
            }

            /* Existing CSS styles */

            .size-quantity-details {
            margin-bottom: 10px;
            }

            .size-quantity-details div {
            margin-bottom: 5px;
            }

            #available-quantity {
            font-weight: bold;
        }

        @media only screen and (max-width:511px) {
            .container {
                max-width: 100%;
                height: auto;
                padding: 10px;
            }

    }
</style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section>
    <button class="btn btn-primary btn-lg btn-go-back" onclick="history.back()">Go Back</button>
    <div class="container flex">
        <div class="left">
            <div class="main_image">
                <div class="size_image">
                    <img class="image" src="uploaded_img/<?php echo htmlspecialchars($fetch_products['image'] ?? ''); ?>" alt="Product Image">
                </div>
            </div>
        </div>
        <div class="right">
            <div class="size-quantity-container">
                <div class='product-details'>
                    <div><strong>Category:</strong> <?php echo htmlspecialchars($fetch_products['category'] ?? 'N/A'); ?></div>
                    <div><strong>Brand:</strong> <?php echo htmlspecialchars($fetch_products['brand'] ?? 'N/A'); ?></div>

                    <div class='size-quantity-details'>
                        <div><strong>Sizes:</strong></div>
                        <?php foreach ($sizes_quantities as $size_quantity) { ?>
                            <div class="size-box" data-size="<?php echo htmlspecialchars($size_quantity['size']); ?>" onclick="selectSize(this)">
                                <?php echo htmlspecialchars($size_quantity['size']); ?>
                            </div>
                        <?php } ?>
                    </div>

                    <div class='size-quantity-details'>
                        <div><strong>Available Quantity:</strong></div>
                        <div id="available-quantity">0</div>
                    </div>

                    <div class='size-quantity-details'>
                        <div><strong>Price:</strong> RM <?php echo htmlspecialchars($fetch_products['price'] ?? 'N/A'); ?></div>
                    </div>
                </div>
            </div>

            <form action="" method="post" id="add-to-cart-form">
                <div class="add flex1">
                    <input type="number" min="1" name="product_quantity" placeholder="Enter Quantity" value="1" class="form-control form-control-lg">
                </div>
                <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($fetch_products['name'] ?? ''); ?>">
                <input type="hidden" name="product_size" id="selected-size" value="">
                <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($fetch_products['price'] ?? ''); ?>">
                <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($fetch_products['image'] ?? ''); ?>">
                <input type="submit" value="Add to cart" class="btn btn-outline-primary btn-lg" name="add_to_cart">
            </form>

            <?php if (!empty($message)): ?>
                <div class="alert alert-info mt-3">
                    <?php foreach ($message as $msg): ?>
                        <p><?php echo $msg; ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</section>
<script>
    function selectSize(element) {
        var sizeBoxes = document.querySelectorAll('.size-box');
        sizeBoxes.forEach(function(box) {
            box.classList.remove('selected');
        });

        if (!element.classList.contains('out-of-stock')) {
            element.classList.add('selected');
            document.getElementById('selected-size').value = element.getAttribute('data-size');

            // Get the selected size
            var selectedSize = element.getAttribute('data-size');

            // Find the corresponding quantity for the selected size
            var quantities = <?php echo json_encode($sizes_quantities); ?>;
            var availableQuantity = 0;
            for (var i = 0; i < quantities.length; i++) {
                if (quantities[i].size === selectedSize) {
                    availableQuantity = quantities[i].quantity;
                    break;
                }
            }

            // Update the available quantity display
            document.getElementById('available-quantity').textContent = availableQuantity;
        }
    }
</script>


    <?php include 'footer.php'; ?>
</body>
</html>