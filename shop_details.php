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

// Fetch the product details
$get_product_details = mysqli_prepare($conn, "SELECT * FROM `products` WHERE id = ?");
mysqli_stmt_bind_param($get_product_details, "i", $product_id);
mysqli_stmt_execute($get_product_details);
$product_details_result = mysqli_stmt_get_result($get_product_details);
$product_details = mysqli_fetch_assoc($product_details_result);

if (!$product_details) {
    die('Product not found.');
}

// Fetch available sizes and quantities from product_details excluding "Sold" items
$sizes_quantities = [];
$get_sizes_quantities = mysqli_prepare($conn, "SELECT size, COUNT(*) as quantity FROM `product_details` WHERE product_id = ? AND stock != 'Sold' GROUP BY size");
mysqli_stmt_bind_param($get_sizes_quantities, "i", $product_id);
mysqli_stmt_execute($get_sizes_quantities);
$fetch_sizes_quantities = mysqli_stmt_get_result($get_sizes_quantities);

if ($fetch_sizes_quantities) {
    $sizes_quantities = mysqli_fetch_all($fetch_sizes_quantities, MYSQLI_ASSOC);
} else {
    $sizes_quantities = [];
}
mysqli_stmt_close($get_sizes_quantities);

// Calculate total quantity
$total_quantity = array_sum(array_column($sizes_quantities, 'quantity'));

if (isset($_POST['add_to_cart'])) {
    $product_brand = $product_details['brand'];
    $product_name = $product_details['name'];
    $product_price = $product_details['price'];
    $product_size = $_POST['product_size'] ?? null;
    $product_image = $product_details['image'];
    $product_quantity = intval($_POST['product_quantity']);

    if (!$product_size) {
        $message[] = 'Please select a size.';
    } else {
        $get_product_detail_id = mysqli_prepare($conn, "SELECT product_detail_id FROM `product_details` WHERE product_id = ? AND size = ? AND stock != 'Sold' LIMIT 1");
        mysqli_stmt_bind_param($get_product_detail_id, "is", $product_id, $product_size);
        mysqli_stmt_execute($get_product_detail_id);
        $product_detail_id_result = mysqli_stmt_get_result($get_product_detail_id);
        $product_detail_id_row = mysqli_fetch_assoc($product_detail_id_result);
        $product_detail_id = $product_detail_id_row['product_detail_id'] ?? null;

        if (!$product_detail_id) {
            $message[] = 'Product detail ID not found.';
        } else {
            $compare_quant = mysqli_prepare($conn, "SELECT COUNT(*) as quantity FROM `product_details` WHERE product_id = ? AND size = ? AND stock != 'Sold'");
            mysqli_stmt_bind_param($compare_quant, "is", $product_id, $product_size);
            mysqli_stmt_execute($compare_quant);
            $fetch_quantitem = mysqli_stmt_get_result($compare_quant);
            $size_stock = mysqli_fetch_assoc($fetch_quantitem);
            mysqli_stmt_close($compare_quant);

            if ($size_stock['quantity'] <= 0 || $product_quantity > $size_stock['quantity']) {
                $message[] = 'Product out of stock';
            } else {
                $insert_cart = mysqli_prepare($conn, "INSERT INTO `cart` (user_id, product_id, product_detail_id, product_brand, product_name, product_size, price, quantity, image) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
                mysqli_stmt_bind_param($insert_cart, "iiisssisd", $user_id, $product_id, $product_detail_id, $product_brand, $product_name, $product_size, $product_price, $product_quantity, $product_image);
                mysqli_stmt_execute($insert_cart);
                mysqli_stmt_close($insert_cart);
                $message[] = 'Product added to cart';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="css/styleindex.css">

    <style>

    .container {
        max-width: 1200px;
        margin: auto;
        background: white;
        padding: 20px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 8px;
    }
    .left, .right {
        width: 50%;
        padding: 20px;
    }
    .flex {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
    }
    .main_image {
        width: 100%;
        position: relative;
        overflow: hidden;
        border-radius: 8px;
        box-shadow: 0 0 8px rgba(0, 0, 0, 0.1);
    }
    .main_image img {
        width: 100%;
        height: auto;
    }
    .option img {
        width: 75px;
        height: 75px;
        margin: 10px;
        border-radius: 2px;
        border: 0.7px solid black;
    }
    .right {
        padding: 20px;
    }
    h3 {
        color: #2f3542;
        margin: 20px 0;
        font-size: 30px;
        font-weight: bold;
    }
    h5, p, small {
        color: #57606f;
    }

    #available-quantity {
        color: #57606f;
        font-size: 20px; /* Increased font size for price and quantity */
        font-weight: bold;
    }

    h4 {
        color: #dc143c;
        font-size: 20px; /* Increased font size for price and quantity */
        font-weight: bold;
    }
    p {
        margin: 20px 0 50px;
        line-height: 1.6;
        font-size: 16px;
    }
    h5 {
        font-size: 16px;
        margin: 10px 0;
    }
    .size-box {
        display: inline-block;
        padding: 10px 15px;
        border: 1px solid #ddd;
        margin: 5px;
        cursor: pointer;
        transition: all 0.3s ease;
        font-size: 18px; /* Bigger font size */
        font-weight: bold; /* Bold font */
        text-align: center;
    }
    .size-box.selected {
        border-color: #dc143c;
        background-color: #dc143c;
        color: white;
    }
    .size-box.out-of-stock {
        border-color: #ddd;
        background-color: #f0f0f0;
        color: #999;
        cursor: not-allowed;
    }
    .size-quantity-details {
        margin-top: 10px;
    }
    @media only screen and (max-width: 768px) {
        .container {
            flex-direction: column;
        }
        .left, .right {
            width: 100%;
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
                    <img src="uploaded_img/<?php echo htmlspecialchars($product_details['image']); ?>" alt="Product Image">
                </div>
            </div>
            <div class="right">
                <div class="size-quantity-container">
                    <div class='product-details'>
                        <h3><?php echo htmlspecialchars($product_details['name']); ?></h3>
                        <h5><strong>Brand:</strong> <?php echo htmlspecialchars($product_details['brand']); ?></h5>
                        <h5><strong>Category:</strong> <?php echo htmlspecialchars($product_details['category']); ?></h5>
                        <p><?php echo nl2br(htmlspecialchars($product_details['description'])); ?></p>
                        
                        <div class='size-quantity-details'>
                            <h5><strong>Sizes:</strong></h5>
                            <?php if (is_array($sizes_quantities) && !empty($sizes_quantities)) { ?>
                                <?php foreach ($sizes_quantities as $size_quantity) { ?>
                                    <div class="size-box <?php echo $size_quantity['quantity'] <= 0 ? 'out-of-stock' : ''; ?>" data-size="<?php echo htmlspecialchars($size_quantity['size']); ?>" data-quantity="<?php echo htmlspecialchars($size_quantity['quantity']); ?>" onclick="selectSize(this)">
                                        <?php echo htmlspecialchars($size_quantity['size']); ?>
                                    </div>
                                <?php } ?>
                            <?php } else { ?>
                                <div>No sizes available</div>
                            <?php } ?>
                        </div>

                        <div class="size-quantity-details">
                            <h5><strong>Total Available Quantity:</strong></h5>
                            <div id="available-quantity"><?php echo $total_quantity; ?> available</div>
                        </div>

                        <div class='size-quantity-details'>
                            <h4>RM <?php echo htmlspecialchars($product_details['price']); ?></h4>
                        </div>
                    </div>
                </div>

                <form action="" method="post" id="add-to-cart-form">
                    <div class="add flex1">
                        <input type="number" min="1" name="product_quantity" placeholder="Enter Quantity" value="1" class="form-control form-control-lg">
                    </div>
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                    <input type="hidden" name="product_brand" value="<?php echo htmlspecialchars($product_details['brand']); ?>">
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product_details['name']); ?>">
                    <input type="hidden" name="product_size" id="selected-size" value="">
                    <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product_details['price']); ?>">
                    <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product_details['image']); ?>">
                    <input type="submit" value="Add to cart" class="btn btn-outline-primary btn-lg add-to-cart-btn" name="add_to_cart">
                </form>
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

                var availableQuantity = element.getAttribute('data-quantity');
                document.getElementById('available-quantity').textContent = availableQuantity + " available";
            }
        }
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>
