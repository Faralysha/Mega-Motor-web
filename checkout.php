<?php

include 'config.php';

session_start();

if (!isset($_SESSION['user_id'])) {
    header('location:index.php');
    exit(); // Terminate script after redirection
}

$user_id = $_SESSION['user_id'];

$total_itemprice = 0;
$grand_total = 0;
$cart_products = [];

$select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed select cart');

if (mysqli_num_rows($select_cart) > 0) {
    while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
        if (isset($fetch_cart['price'], $fetch_cart['quantity'])) {
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
            $total_Iprice = $total_price;
            $total_itemprice += $total_Iprice;
        }

        if (isset($fetch_cart['name'], $fetch_cart['pro_size'], $fetch_cart['price'], $fetch_cart['quantity'])) {
            $cart_products[] = $fetch_cart['name'] . '[' . $fetch_cart['pro_size'] . '] (' . 'RM' . $fetch_cart['price'] . ' X ' . $fetch_cart['quantity'] . ')';
        }
    }
}

if (isset($_POST['order_btn'])) {
    // Sanitize user input
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, 'flat no. ' . $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['country'] . ' - ' . $_POST['pin_code']);
    $placed_on = date('d-M-Y');

    // Initialize variables
    $cart_total = 0;
    $cart_products = [];

    // Fetch cart items and calculate total
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($cart_query) > 0) {
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $cart_products[] = $cart_item['name'] . '[' . $cart_item['pro_size'] . ']' . '(' . $cart_item['quantity'] . ') ';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }

    $total_products = implode(', ', $cart_products);

    // Check if order already exists
    $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND email = '$email' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('Query failed');

    if ($cart_total == 0) {
        $message[] = 'Your cart is empty';
    } else {
        if (mysqli_num_rows($order_query) > 0) {
            $message[] = 'Order already placed!';
        } else {
            // Insert order details into orders table
            $tracknom = 0;
            mysqli_query($conn, "INSERT INTO `orders`(user_id, name, number, email, address, total_products, total_price, placed_on, tracknum) VALUES('$user_id', '$name', '$number', '$email', '$address', '$total_products', '$cart_total', '$placed_on', '$tracknom')") or die('query failed');

            // Fetch newly inserted order ID
            $getidddd = mysqli_query($conn, "SELECT id FROM `orders` WHERE user_id = $user_id");
            while ($getid = mysqli_fetch_assoc($getidddd)) {
                $_SESSION['idname'] = $getid['id'];
                $id_order_new = $getid['id'];
            }

            // Insert order history for each product
            if (mysqli_num_rows($cart_query) > 0) {
                while ($product_rating = mysqli_fetch_assoc($cart_query)) {
                    $product_userid_rate = $product_rating['user_id'];
                    $product_name_rate = $product_rating['name'];
                    $get_orderid = $id_order_new;
                    mysqli_query($conn, "INSERT INTO `history`(user_id, order_id,product_name) VALUES('$product_userid_rate','$get_orderid' ,'$product_name_rate')") or die('query failed');
                }
            }

            $message[] = 'Order placed successfully!';

            // Generate bill and redirect to payment page

            // Get user information
            $userdata = mysqli_query($conn, "SELECT * FROM `users` WHERE id='$user_id'");
            $get_useruser = mysqli_fetch_assoc($userdata);
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
                'billContentEmail'
                => 'Thank you for purchasing product from Berjaya Mega Motor!',
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
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/styleindex.css">

   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>checkout</h3>
      <p> <a href="home.php">home</a> / checkout </p>
   </div>

   <section class="display-order">

      <?php
      
$final_total_checkout = 0;
$grand_total = 0;
$select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed select cart');
if (mysqli_num_rows($select_cart) > 0) {
    while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
        if (isset($fetch_cart['price'], $fetch_cart['quantity'])) {
            $sub_total = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
            $total_itemprice += $total_Iprice;
        }
?>
        
<?php

         }
      } else {
         echo '<p class="empty">your cart is empty</p>';
      }
      ?>
      <div class="grand-total"> Total Payment: <span>RM
            <?php echo $total_itemprice; ?>
         </span> </div>

   </section>

   <section class="checkout">
      <?php
      $get_userdata = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'");
      $get_data = mysqli_fetch_assoc($get_userdata);
      ?>
      <form action="" method="post">
         <h3>place your order</h3>
         <<div class="flex">
            <div class="inputBox">
                <span>your name :</span>
                <input type="text" name="name" required placeholder="enter your name">
            </div>
            <div class="inputBox">
                <span>your number :</span>
                <input type="text" name="number" required placeholder="enter your number">
            </div>
            <div class="inputBox">
                <span>your email :</span>
                <input type="email" name="email" required placeholder="enter your email">
            </div>
            <div class="inputBox">
               <span>address line 01 :</span>
               <input type="text"  name="flat" required placeholder="e.g. flat no." value="">
            </div>
            <div class="inputBox">
               <span>address line 02 :</span>
               <input type="text" name="street" required placeholder="e.g. street name" value="">
            </div>
            <div class="inputBox">
               <span>city :</span>
               <input type="text" name="city" required placeholder="e.g. Batu pahat" value="">
            </div>
            <div class="inputBox">
               <span>state :</span>
               <input type="text" name="state" required placeholder="e.g. johor" value="">
            </div>
            <div class="inputBox">
               <span>country :</span>
               <input type="text" name="country" required placeholder="e.g. Malaysia" value="">
            </div>
            <div class="inputBox">
               <span>postcode :</span>
               <input type="number" name="postcode" required placeholder="e.g. 123456" value="">
            </div>
         </div>
        <form action="payment.php" method="post">
        <input type="submit" value="order now" class="btn btn-primary btn-lg" name="order_btn">
      </form>

   </section>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>
