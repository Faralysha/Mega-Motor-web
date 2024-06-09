<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id']; // Assuming user_id is the session variable set during login

if (!isset($user_id)) {
   header('location:index.php');
}

if (isset($_POST['update_order'])) {
   $order_update_id = $_POST['order_id'];
   $track_number = $_POST['track-order'];
   $update_payment = isset($_POST['update_payment']) ? $_POST['update_payment'] : null;

   if ($update_payment !== null) {
       // Update tracking number and payment status in the order
       mysqli_query($conn, "UPDATE `orders` SET payment_status = '$update_payment', tracknum = '$track_number' 
       WHERE id = '$order_update_id'") or die('query failed');
       $message[] = 'Payment status has been updated!';
   } else {
       $message[] = 'Please select a payment status.';
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // Delete associated order items
   mysqli_query($conn, "DELETE FROM `order_items` WHERE order_id = '$delete_id'") or die('query failed deleting order items');

   // Delete the order
   mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed deleting order');

   header('location:staff_order.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Admin Orders</title>

   <!-- Font Awesome CDN Link -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- Custom Admin CSS File Link -->
   <link rel="stylesheet" href="css/admin_style.css">

   <!-- jQuery Link for AJAX -->
   <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

   <!-- External JavaScript Library for Tracking -->
   <script src="//www.tracking.my/track-button.js"></script>

   <script>
      $(document).ready(function () {
         $("#refresh-btn").click(function () {
            $("#container-displayorder").load("admin_reloaded.php", function () {
               alert("Data refreshed");
            });
         });
      });

      // Function to handle tracking number click
      function linkTrack(num) {
         TrackButton.track({
            tracking_no: num
         });
      }
   </script>

</head>

<body>

   <?php include 'staff_header.php'; ?>
   
   <section class="orders" id="orders">
      <h1 class="title">Placed Orders</h1>
      <button id="refresh-btn" class="btn btn-outline-primary">Refresh Orders</button>

      <div class="box-container" id="container-displayorder">
         <?php
         // Fetch all orders
         $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');

         // Detect if there are orders in the database
         if (mysqli_num_rows($select_orders) > 0) {
            // Print out all the data
            while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
               if ($fetch_orders['status'] == 1) {
                  ?>
                  <div class="box">
                     <p>Order received by the customer</p>
                     <p> User ID : <span><?php echo $fetch_orders['user_id']; ?></span> </p>
                     <p> Placed on : <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
                     <p> Name : <span><?php echo $fetch_orders['name']; ?></span> </p>
                     <p> Number : <span><?php echo $fetch_orders['number']; ?></span> </p>
                     <p> Email : <span><?php echo $fetch_orders['email']; ?></span> </p>
                     <p> Address : <span><?php echo $fetch_orders['address']; ?></span> </p>
                     <p> Total Products : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
                     <p> Total Price : <span>RM<?php echo $fetch_orders['total_price']; ?></span> </p>
                     <p> Tracking Number: <span><?php echo $fetch_orders['tracknum']; ?></span> </p>
                  </div>
                  <?php
               } else {
                  ?>
                  <div class="box">
                     <p> User ID : <span><?php echo $fetch_orders['user_id']; ?></span> </p>
                     <p> Placed on : <span><?php echo $fetch_orders['placed_on']; ?></span> </p>
                     <p> Name : <span><?php echo $fetch_orders['name']; ?></span> </p>
                     <p> Number : <span><?php echo $fetch_orders['number']; ?></span> </p>
                     <p> Email : <span><?php echo $fetch_orders['email']; ?></span> </p>
                     <p> Address : <span><?php echo $fetch_orders['address']; ?></span> </p>
                     <p> Total Products : <span><?php echo $fetch_orders['total_products']; ?></span> </p>
                     <p> Total Price : <span>RM<?php echo $fetch_orders['total_price']; ?></span> </p>
                     <p>Tracking Number: <span style="color:blue">
                           <a onclick="linkTrack(this.innerText)"><?php echo $fetch_orders['tracknum']; ?></a>
                           <button onclick="linkTrack('<?php echo $fetch_orders['tracknum']; ?>')">&nbsp- [TRACK]</button>
                        </span>
                     </p>
                     <form action="" method="post">
                        <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                        <select name="update_payment" required>
                        <?php
                        $payment_status = $fetch_orders['payment_status'];
                        $selected_pending = $payment_status === 'pending' ? 'selected' : '';
                        $selected_accepted = $payment_status === 'Accepted' ? 'selected' : '';
                        ?>
                        <option value="pending" <?php echo $selected_pending; ?>>Pending</option>
                        <option value="Accepted" <?php echo $selected_accepted; ?>>Accepted</option>
                        </select>
                        <input type="text" name="track-order" placeholder="Tracking number" value="<?php echo $fetch_orders['tracknum']; ?>">
                        <input type="submit" value="Update track" name="update_order" class="option-btn">
                        <a href="admin_order.php?delete=<?php echo $fetch_orders['id']; ?>" onclick="return confirm('Cancel this order?');" class="delete-btn">Cancel order</a>
                     </form>
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

   <!-- Custom Admin JS File Link -->
   <script src="js/admin_script.js"></script>

</body>

</html>
