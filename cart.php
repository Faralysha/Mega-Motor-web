<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:index.php');
    exit;
}

// Update cart quantity by user
if (isset($_POST['update_cart'])) {
    $cart_id = $_POST['cart_id'];
    $cart_quantity = $_POST['cart_quantity'];
    mysqli_query($conn, "UPDATE `cart` SET quantity = '$cart_quantity' WHERE id = '$cart_id'") or die('Query failed');
    $message[] = 'Cart quantity updated!';
}

// Delete specific product from the user cart
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id'") or die('Query failed');
    header('location:cart.php');
    exit;
}

// Delete all products from the user cart
if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
    header('location:cart.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom CSS File Link -->
   <link rel="stylesheet" href="css/styleindex.css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
   <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
   <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>Shopping Cart</h3>
        <p><a href="home.php">Home</a> / Cart</p>
    </div>

    <section class="shopping-cart">
        <h1 class="title">Products Added</h1>
        <div class="box-container">
            <?php
            // To display the items in the cart
            $grand_total = 0;
            $select_cart = mysqli_query($conn, "SELECT cart.*, products.price, products.image FROM `cart` INNER JOIN `products` ON cart.product_id = products.id WHERE cart.user_id = '$user_id'") or die('Query failed');
            if (mysqli_num_rows($select_cart) > 0) {
                while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                    ?>
                    <div class="box">
                        <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('Delete this from cart?');"></a>
                        <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
                        <div class="name">
                            <?php echo $fetch_cart['product_name']; ?>
                        </div>
                        <div class="price">RM <?php echo number_format($fetch_cart['price'], 2); ?></div>

                        <div class="name">Size:
                        <?php echo $fetch_cart['product_size']; ?> 
                        </div>
                        <div class="name">Quantity:
                            <?php echo $fetch_cart['quantity']; ?>
                        </div>
                        <form action="" method="post">
                            <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['id']; ?>">
                            <input type="number" min="1" name="cart_quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                            <input type="submit" name="update_cart" value="Update" class="option-btn">
                        </form>
                        <div class="sub-total">Subtotal: <span>RM
                                <?php echo number_format ($sub_total = ($fetch_cart['quantity'] * $fetch_cart['price']), 2); ?>
                            </span>
                        </div>
                    </div>
            <?php
                    $grand_total += $sub_total;
                }
            } else {
                echo '<p class="empty">Your cart is empty</p>';
            }
            ?>
        </div>

        <div style="margin-top: 2rem; text-align:center;">
            <a href="cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 0) ? '' : 'disabled'; ?>" onclick="return confirm('Delete all from cart?');">Delete All</a>
        </div>

        <div class="cart-total">
            <p>Shipping Fee: <span>RM 5.00</span></p>
            <p>Grand Total: <span>RM <?php echo number_format($grand_total, 2); ?></span></p>
            <?php $final_total_checkout = number_format(($grand_total + 5), 2) ?>
            <p>Total Payment: <span>RM <?php echo $final_total_checkout; ?></span></p>
            <div class="flex">
                <a href="shop.php" class="btn btn-warning btn-lg">Continue Shopping</a>
                <a href="checkout.php" class="btn btn-success btn-lg<?php echo ($grand_total > 0) ? '' : 'disabled'; ?>">Checkout</a>
            </div>
        </div>

    </section>

    <?php include 'footer.php'; ?>

    <!-- Custom JS file link -->
    <script src="js/script.js"></script>

</body>

</html>
