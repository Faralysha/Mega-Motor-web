<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($_SESSION['user_id'])) {
    header('location:index.php');
    exit();
}

// Function to generate a unique invoice number
function generateInvoiceNumber() {
    // Get current date and time
    $currentDateTime = date('YmdHis');
    // Generate a random 4-digit number
    $randomNumber = sprintf("%04d", mt_rand(1, 9999));
    // Combine date/time and random number to create the invoice number
    $invoiceNumber = $currentDateTime . $randomNumber;
    return $invoiceNumber;
}

$total_itemprice = 0;
$choose_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
if (mysqli_num_rows($choose_cart) > 0) {
    while ($fetch_cart = mysqli_fetch_assoc($choose_cart)) {
        $total_Iprice = ($fetch_cart['price'] * $fetch_cart['quantity']);
        $total_itemprice += $total_Iprice;
    }
    $final_price = $total_itemprice * 100;
}

if (isset($_POST['order_btn'])) {
    // Sanitize user input
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $number = mysqli_real_escape_string($conn, $_POST['number']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $address = mysqli_real_escape_string($conn, $_POST['flat'] . ', ' . $_POST['street'] . ', ' . $_POST['city'] . ', ' . $_POST['state'] . ' - ' . $_POST['pin_code']);
    $placed_on = date('d-M-Y');

    // Initialize variables
    $cart_total = 0;
    $cart_products = [];

    // Fetch cart items and calculate total
    $cart_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
    if (mysqli_num_rows($cart_query) > 0) {
        while ($cart_item = mysqli_fetch_assoc($cart_query)) {
            $cart_products[] = $cart_item['product_brand'] . ' ' . $cart_item['product_name'] . '[' . $cart_item['product_size'] . ']' . '(' . $cart_item['quantity'] . ')';
            $sub_total = ($cart_item['price'] * $cart_item['quantity']);
            $cart_total += $sub_total;
        }
    }

    $total_products = implode('- ', $cart_products);

    // Check if order already exists
    $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE name = '$name' AND number = '$number' AND email = '$email' AND address = '$address' AND total_products = '$total_products' AND total_price = '$cart_total'") or die('query failed');
    $userdata = mysqli_query($conn, "SELECT * FROM `users` WHERE id='$user_id' ");
    $get_useruser = mysqli_fetch_assoc($userdata);

    if ($cart_total == 0) {
        $message[] = 'Your cart is empty';
    } else {
        if (mysqli_num_rows($order_query) > 0) {
            $message[] = 'Order already placed!';
        } else {
            // Generate invoice number
            $invoice_number = generateInvoiceNumber();
            $invoice_date = date('Y-m-d');

            // Insert order details into orders table
            $tracknom = 0;
            mysqli_query($conn, "INSERT INTO `orders` (user_id, name, number, email, address, total_products, total_price, placed_on, tracknum, invoice_number) 
            VALUES ('$user_id', '$name', '$number', '$email', '$address', '$total_products', '$cart_total', '$placed_on', '$tracknom', '$invoice_number')") or die('query failed');

            $order_id = mysqli_insert_id($conn); // Get the last inserted order ID

            // Store order_id in the session
            $_SESSION['idname'] = $order_id;

            // Initialize counter for serial number
            $counter = 1;

            // Insert order items into order_items table
            $product_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            if (mysqli_num_rows($product_query) > 0) {
                while ($product = mysqli_fetch_assoc($product_query)) {
                    $product_detail_id = $product['product_detail_id'];
                    $quantity = $product['quantity'];
                    $price = $product['price'] * $quantity;

                    // Generate serial number
                    $serial_number = $product['product_id'] . '-' . $counter . '-' . $product['product_size'];

                    // Update product status to 'Sold'
                    mysqli_query($conn, "UPDATE `product_details` SET stock = 'Sold' WHERE product_detail_id = '$product_detail_id'") or die('query failed');

                    // Insert order item with serial number
                    mysqli_query($conn, "INSERT INTO `order_items` (order_id, product_detail_id, quantity, price, serial_number) 
                    VALUES ('$order_id', '$product_detail_id', '$quantity', '$price', '$serial_number')") or die('query failed');

                    // Increment counter
                    $counter++;
                }
            }

            // Insert order history
            $history_query = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            while ($history = mysqli_fetch_assoc($history_query)) {
                $product_userid_rate = $history['user_id'];
                $get_orderid = $order_id;
                $product_brand_rate = $history['product_brand'];
                $product_name_rate = $history['product_name'];
                $product_id_rate = $history['product_id'];
                $product_size_rate = $history['product_size'];
                mysqli_query($conn, "INSERT INTO `history` (user_id, order_id, product_brand, product_name, product_id, product_size, invoice_number) VALUES ('$product_userid_rate', '$get_orderid', '$product_brand_rate', '$product_name_rate', '$product_id_rate', '$product_size_rate', '$invoice_number')") or die('query failed');
            }
            $message[] = 'Order placed successfully!';

            // Generate bill and redirect to payment page
            $bill_name = $get_useruser['name'];
            $bill_email = $get_useruser['email'];
            $bill_pnumber = $get_useruser['pnumber'];

            $some_data = array(
                'userSecretKey' => '8jyl43vl-asxv-d2cs-1kec-gu2vw7rt2347',
                'categoryCode' => 'nrkbtcqd',
                'billName' => $bill_name,
                'billDescription' => $total_products,
                'billPriceSetting' => 1,
                'billPayorInfo' => 1,
                'billAmount' => $final_price,
                'billReturnUrl' => 'http://localhost/MEGA MOTOR WEB_OLD/afterpay.php',
                'billCallbackUrl' => 'http://localhost/MEGA MOTOR WEB_OLD/cart.php',
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
            curl_close($curl);
            $obj = json_decode($result, true);
            $billcode = $obj[0]['BillCode'];

            ?>

            <script type="text/javascript">
                window.location.href = "https://dev.toyyibpay.com/<?php echo $billcode; ?>";
            </script>

            <?php
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

      $grand_total = 0;
      $select_cart = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
      if (mysqli_num_rows($select_cart) > 0) {
         while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
            $total_price = ($fetch_cart['price'] * $fetch_cart['quantity']);
            $grand_total += $total_price;
            ?>
            <p>
               <?php echo $fetch_cart['product_name']; ?>
               Size:
               <?php echo $fetch_cart['product_size'] ?> <span>(
                  <?php echo 'RM' . $fetch_cart['price'] . ' X ' . $fetch_cart['quantity']; ?>)
               </span>
            </p>
            <?php
         }
      } else {
         echo '<p class="empty">your cart is empty</p>';
      }
      ?>
      <div class="grand-total"> Total Payment (including shipping fee): <span>RM <?php echo $grand_total + 5; ?>
   </span> </div>


   </section>

   <section class="checkout">
      <?php
      $get_userdata = mysqli_query($conn, "SELECT * FROM `users` WHERE id = '$user_id'");
      $get_data = mysqli_fetch_assoc($get_userdata);
      ?>
      <form action="" method="post">
         <h3>place your order</h3>
         <div class="flex">
            <div class="inputBox">
               <span>your name :</span>
               <input type="text" name="name" required placeholder="enter your name"
                  value="<?php echo $get_data['name'] ?>">
            </div>
            <div class="inputBox">
               <span>your number :</span>
               <input type="number" name="number" required placeholder="enter your number">
            </div>
            <div class="inputBox">
               <span>your email :</span>
               <input type="email" name="email" required placeholder="enter your email"
                  value="<?php echo $get_data['email'] ?>" readonly>
            </div>
            <div class="inputBox">
               <span>address line 01 :</span>
               <input type="text" name="flat" required placeholder="e.g. flat no." value="">
            </div>
            <div class="inputBox">
               <span>address line 02 :</span>
               <input type="text" name="street" required placeholder="e.g. street name" value="">
            </div>
            <div class="inputBox">
               <span>city :</span>
               <input type="text" name="city" required placeholder="e.g. Klang" value="">
            </div>
            <div class="inputBox">
               <span>state :</span>
               <input type="text" name="state" required placeholder="e.g. Selangor" value="">
            </div>
            <div class="inputBox">
               <span>pin code :</span>
               <input type="number" min="0" name="pin_code" required placeholder="e.g. 123456" value="">
            </div>
         </div>
         <!-- <a href="payment.php">Payment </a> -->
         <input type="submit" value="order now" class="btn btn-primary btn-lg" name="order_btn">
      </form>

   </section>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>
