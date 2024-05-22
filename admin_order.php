<?php

include 'config.php';

session_start();

$admin_id = $_SESSION['admin_id'];

if (!isset($admin_id)) {
   header('location:admin_login.php');
}

if (isset($_POST['update_order'])) {

   $order_update_id = $_POST['order_id'];
   $track_number = $_POST['track-order'];
   $update_payment = $_POST['update_payment'];
   //  update tracking number to the order 
   mysqli_query($conn, "UPDATE `orders` SET payment_status = '$update_payment', tracknum='$track_number' WHERE id = '$order_update_id'") or die('query failed');
   $message[] = 'payment status has been updated!';

}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$delete_id'") or die('query failed');
   header('location:admin_orders.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin_orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">


   <!-- Jquery link for AJAX -->
   <script src="https://code.jquery.com/jquery-3.6.4.min.js"
      integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>

   <script>
      $(document).ready(function () {
         $("#refresh-btn").click(function () {
            $("#container-displayorder").load("admin_reloaded.php", function () {
               alert("Data refreshed");
            });
         })
      })
   </script>
   <!-- auto refresh the specific div-content -->
   <!-- <script>
      setInterval(function () {
         $('#container-displayorder').load("admin_reloaded.php");
      }, 10000)
   </script> -->
</head>

<body>

   <?php include 'admin_header.php'; ?>
   <section class="orders" id="orders">
      
      <h1 class="title">placed orders</h1>
      <button id="refresh-btn" class="btn btn-outline-primary">Refresh order</button>

      <div class="box-container" id="container-displayorder">
         <?php
         // fetch all order
         $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
         // fetch order where status = received
         $status_orders = mysqli_query($conn, "SELECT * FROM `orders` WHERE status = 1") or die('Query failed');
         $stats_ord = mysqli_fetch_assoc($status_orders);
         // detect if there is order in the database
         if (mysqli_num_rows($select_orders) > 0) {
            // print out all the data
            while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
               if ($fetch_orders['status'] == 1) {
                  // echo "this is to display item that has been reveiced with name: ";
                  // echo $stats_ord['name'];
                  ?>
                  <div class="box">
                     <p>Order received by the customer</p>
                     <p> user id : <span>
                           <?php echo $fetch_orders['user_id']; ?>
                        </span> </p>
                     <p> placed on : <span>
                           <?php echo $fetch_orders['placed_on']; ?>
                        </span> </p>
                     <p> name : <span>
                           <?php echo $fetch_orders['name']; ?>
                        </span> </p>
                     <p> number : <span>
                           <?php echo $fetch_orders['number']; ?>
                        </span> </p>
                     <p> email : <span>
                           <?php echo $fetch_orders['email']; ?>
                        </span> </p>
                     <p> address : <span>
                           <?php echo $fetch_orders['address']; ?>
                        </span> </p>
                     <p> total products : <span>
                           <?php echo $fetch_orders['total_products']; ?>
                        </span> </p>
                     <p> total price : <span>RM
                           <?php echo $fetch_orders['total_price']; ?>
                        </span> </p>
                     <p>
                        Tracking number: <span>
                           <?php echo $fetch_orders['tracknum']; ?>
                        </span>
                     </p>

                  </div>
                  <?php
               } else {
                  // to display all order makde by the customer
                  ?>
                  <div class="box">
                     <p> user id : <span>
                           <?php echo $fetch_orders['user_id']; ?>
                        </span> </p>
                     <p> placed on : <span>
                           <?php echo $fetch_orders['placed_on']; ?>
                        </span> </p>
                     <p> name : <span>
                           <?php echo $fetch_orders['name']; ?>
                        </span> </p>
                     <p> number : <span>
                           <?php echo $fetch_orders['number']; ?>
                        </span> </p>
                     <p> email : <span>
                           <?php echo $fetch_orders['email']; ?>
                        </span> </p>
                     <p> address : <span>
                           <?php echo $fetch_orders['address']; ?>
                        </span> </p>
                     <p> total products : <span>
                           <?php echo $fetch_orders['total_products']; ?>
                        </span> </p>
                     <p> total price : <span>RM
                           <?php echo $fetch_orders['total_price']; ?>
                        </span> </p>
                     <form action="" method="post">
                        <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                        <select name="update_payment">
                           <option value="" selected disabled>
                              <?php echo $fetch_orders['payment_status']; ?>
                           </option>
                           <option value="pending">pending</option>
                           <option value="Accepted">Accepted</option>
                        </select>
                        <input type="text" name="track-order" placeholder="Tracking number"
                           value="<?php echo $fetch_orders['tracknum']; ?>">
                        <input type="submit" value="Update track" name="update_order" class="option-btn">
                        <a href="admin_orders.php?delete=<?php echo $fetch_orders['id']; ?>"
                           onclick="return confirm('Cancel this order?');" class="delete-btn">Cancel order</a>
                     </form>

                  </div>
                  <?php
               }
            }
         } else {
            echo '<p class="empty">no orders placed yet!</p>';
         }
         ?>
      </div>

   </section>


   <!-- custom admin js file link  -->
   <script src="js/admin_script.js"></script>

   <!-- // $select_order_id = mysqli_query($conn, "SELECT * FROM `orders` WHERE id='$order_update_id'") or die('query failed');
   // mysqli_query($conn, "INSERT INTO `orders` (tracknum) VALUES ('$track_number') WHERE id='$order_update_id'") or die('query failed');   -->
</body>

</html>