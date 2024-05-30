<?php
include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:index.php');
    exit;
}

// Delete cart item
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];
    mysqli_query($conn, "DELETE FROM `cart` WHERE id = '$delete_id' AND user_id = '$user_id'") or die('Query failed');
    header('location:cart.php');
    exit;
}

// Delete all cart items for the user
if (isset($_GET['delete_all'])) {
    mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
    header('location:cart.php');
    exit;
}

if (isset($_POST['order_btn'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = $_POST['number'];
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, 'flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code']);
    $placed_on = date('d-M-Y');

    $cart_total = 0;
    $cart_products = [];
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('Query failed');
    if (mysqli_num_rows($cart_query) > 0) {
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $cart_products[] = $cart_item['product_brand'] . ' ' . $cart_item['product_name'] . '[' . $cart_item['product_size'] . ']' . '(' . $cart_item['quantity'] . ') ';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }

    $total_products = implode(', ', $cart_products);

    $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND phone = '$number' AND email = '$email' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('Query failed');
    $userdata = mysqli_query($conn, "SELECT * FROM `users` WHERE id='$user_id' ");
    $get_useruser = mysqli_fetch_assoc($userdata);

    if ($cart_total == 0) {
        $message[] = 'Your cart is empty';
    } else {
        if (mysqli_num_rows($order_query) > 0) {
            $message[] = 'Order already placed!';
        } else {
            mysqli_query($conn, "INSERT INTO `orders`(user_id, name, phone, email, address, total_products, total_price, placed_on) VALUES('$user_id', '$name', '$number', '$email', '$address', '$total_products', '$cart_total', '$placed_on')") or die('Query failed');
            $message[] = 'Order placed successfully!';

            // Get user information
            $bill_name = $get_useruser['name'];
            $bill_email = $get_useruser['email'];
            $bill_pnumber = $get_useruser['phone'];

            // Construct bill data
            $final_price = $cart_total;
            $some_data = array(
                'userSecretKey' => '8jyl43vl-asxv-d2cs-1kec-gu2vw7rt2347',
                'categoryCode' => 'nrkbtcqd',
                'billName' => $bill_name,
                'billDescription' => $total_products,
                'billPriceSetting' => 0,
                'billPayorInfo' => 1,
                'billAmount' => $final_price,
                'billReturnUrl' => 'http://localhost:8080/afterpay.php',
                'billCallbackUrl' => 'http://localhost:8080/cart.php',
                'billExternalReferenceNo' => 'AFR341DFI',
                'billTo' => 'Mega Motor Web',
                'billEmail' => $bill_email,
                'billPhone' => $bill_pnumber,
                'billSplitPayment' => 0,
                'billSplitPaymentArgs' => '',
                'billPaymentChannel' => '0',
                'billContentEmail' => 'Thank you for purchasing product from Berjaya Mega Motor!',
                'billChargeToCustomer' => 1,
            );

            // Send bill creation request
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_POST, 1);
            curl_setopt($curl, CURLOPT_URL, 'https://dev.toyyibpay.com/index.php/api/createBill');
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $some_data);

            $result = curl_exec($curl);
            $info = curl_getinfo($curl);

            // Handle errors
            if ($result === false) {
                $message[] = 'Failed to create bill: ' . curl_error($curl);
            } else {
                // Decode bill creation response
                $obj = json_decode($result, true);

                // Check if bill code is received
                if (isset($obj[0]['BillCode'])) {
                    $billcode = $obj[0]['BillCode'];
                    // Redirect user to payment page
                    echo '<script type="text/javascript"> window.location.href = "https://dev.toyyibpay.com/' . $billcode . '"; </script>';
                } else {
                    $message[] = 'Failed to create bill: Invalid response received';
                }
            }

            curl_close($curl);
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
    <title>Checkout</title>
   
    <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/styleindex.css">
<!-- bootstrap -->
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

</head>
<style>   

    .shopping-cart{
        background-color: var(--white);
        overflow: hidden; /* Ensure no overflow */

    }
    </style>

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
            $select_cart = mysqli_query($conn, "SELECT cart.*, products.price, products.image, products.brand, products.name FROM `cart` INNER JOIN `products` ON cart.product_id = products.id WHERE cart.user_id = '$user_id'") or die('Query failed');
            if (mysqli_num_rows($select_cart) > 0) {
                while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
                    ?>
                     <div class="box">
                    <a href="cart.php?delete=<?php echo $fetch_cart['id']; ?>" class="fas fa-times" onclick="return confirm('Delete this from cart?');"></a>
                    <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
                    
                    <div class="brand-name">
                    <?php echo $fetch_cart['product_brand'] . ' ' . $fetch_cart['product_name']; ?>
                    </div>
                    <div class="price">RM <?php echo number_format($fetch_cart['price'], 2); ?></div>
                    <div class="size">Size:
                        <?php echo $fetch_cart['product_size']; ?>  
                    </div>
                    <div class="quantity">Quantity:
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
