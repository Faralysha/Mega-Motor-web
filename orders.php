<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if (isset($_GET['receive'])) {
   $status_id = $_GET['receive'];
   $value_stats = 1;
   
   // Update order status
   mysqli_query($conn, "UPDATE `orders` SET status = 1 WHERE id = '$status_id'") or die('Error updating order status');

   // Update order items status
   mysqli_query($conn, "UPDATE `order_items` SET status = 1 WHERE order_id = '$status_id'") or die('Error updating order items status');
   
   header('location:orders.php');
}

?>
<!-- 1. when they click payment, all the data will be inserted into the order database.
2. admin then can choose to accept and display the tracking number to the customer.
3. in customer, there is a button that indicated if the item is received or not.
4. when customer press the button, it updates the status of the item and display the item is already received 
in both admin and customer.
5. admin will not able to edit the status and in admin, track and order is simplified -->
<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/styleindex.css">

<style>
   .placed-orders .title{
   text-align: center ;
   font-size: 4rem;
   font-weight: bold;
   text-transform: uppercase;
   color:var(--black);
   max-width: 60rem;
   margin:0 auto;
   }
</style>

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>Orders</h3>
      <p> <a href="home.php">Home</a> / Orders </p>
   </div>

   <section class="placed-orders">
      <h1 class="title">placed orders</h1>

      <div class="box-container">
         <?php
         $order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE user_id = '$user_id'") or die('query failed');

         if (mysqli_num_rows($order_query) > 0) {
            while ($fetch_orders = mysqli_fetch_assoc($order_query)) {
               if ($fetch_orders['payment_status'] == 'Accepted' && $fetch_orders['status'] == '0') {
                  ?>
                  <div class="box">
                     <p>Order Id: <?php echo $fetch_orders['id']; ?></p>
                     <p> placed on : <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
                     <p> name : <span><?php echo $fetch_orders['name']; ?></span> </p>
                     <p> phone number : <span><?php echo $fetch_orders['number']; ?></span> </p>
                     <p> email : <span><?php echo $fetch_orders['email']; ?></span> </p>
                     <p> address : <span><?php echo $fetch_orders['address']; ?></span> </p>
                     <p> your orders : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
                     <p> total price : <span>RM<?php echo $fetch_orders['total_price']; ?>/-</span> </p>
                     <p> total payment :<span>RM<?php echo $fetch_orders['total_payment']; ?>/-</span> </p>
                     <p> payment status : <span style="color:<?php echo ($fetch_orders['payment_status'] == 'pending') ? 'red' : 'green'; ?>;"><?php echo $fetch_orders['payment_status']; ?></span> </p>
                     <p>Tracking number: <span style="color:blue">
                        <?php if (!empty($fetch_orders['tracknum'])): ?>
                           <a onclick="linkTrack(this.innerText)"><?php echo $fetch_orders['tracknum']; ?></a>
                           <button onclick="linkTrack('<?php echo $fetch_orders['tracknum']; ?>')">- [TRACK]</button>
                           <script src="//www.tracking.my/track-button.js"></script>
                           <script>
                              function linkTrack(num) {
                                 TrackButton.track({
                                    tracking_no: num
                                 });
                              }
                           </script>
                        <?php else: ?>
                           Not Available
                        <?php endif; ?>
                     </span></p>
                     <a href="orders.php?receive=<?php echo $fetch_orders['id']; ?>" class="btn btn-success btn-lg" onclick="return confirm('Are you sure you already received the item?');">Item received?</a>
                  </div>
                  <?php
               } elseif ($fetch_orders['status'] == '1') {
                  ?>
                  <div class="box">
                     <p>Order Id: <?php echo $fetch_orders['id']; ?> </p>
                     <p>Order Received and completed</p>
                     <p> placed on : <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
                     <p> name : <span><?php echo $fetch_orders['name']; ?></span> </p>
                     <p> phone number : <span><?php echo $fetch_orders['number']; ?></span> </p>
                     <p> your orders : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
                     <p> total price : <span>RM<?php echo $fetch_orders['total_price']; ?>/-</span> </p>
                     <p> total payment :<span>RM<?php echo $fetch_orders['total_payment']; ?>/-</span> </p>
                     <p> payment status :<span style="color:green"> Item received by the customer </span></p>
                  </div>
                  <?php
               } else {
                  ?>
                  <div class="box">
                     <p>Order Id: <?php echo $fetch_orders['id']; ?></p>
                     <p><span>Order in pending</span></p>
                     <p> placed on : <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
                     <p> name : <span><?php echo $fetch_orders['name']; ?></span> </p>
                     <p> phone number : <span><?php echo $fetch_orders['number']; ?></span> </p>
                     <p> email : <span><?php echo $fetch_orders['email']; ?></span> </p>
                     <p> address : <span><?php echo $fetch_orders['address']; ?></span> </p>
                     <p> your orders : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
                     <p> total price : <span>RM<?php echo $fetch_orders['total_price']; ?>/-</span> </p>
                     <p> total payment :<span>RM<?php echo $fetch_orders['total_payment']; ?>/-</span> </p>
                     <p> payment status : <span style="color:<?php echo ($fetch_orders['payment_status'] == 'pending') ? 'red' : 'green'; ?>;"><?php echo $fetch_orders['payment_status']; ?></span> </p>
                  </div>
                  <?php
               }
            }
         } else {
            echo '<p class="empty">No orders placed yet!</p>';
         }
         ?>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>
</body>

</html>
