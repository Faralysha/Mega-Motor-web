<?php
session_start();
include 'config.php';
$user_id = $_SESSION['user_id'];

if (empty($user_id)) {
    header('location:index.php');
    exit(); // Stop executing further code
}

if (isset($_POST['submit_rate'])) {
   $product_id = $_POST['product_id'];
   $product_rate = $_POST['product_rate'];

   mysqli_query($conn, "UPDATE `history` SET product_rate = '$product_rate' 
   WHERE id = '$product_id'") or die('query failed');
   $message[] = 'Rating submitted';
}

// Retrieve distinct order_ids from the database
$select_order_ids = mysqli_query($conn, "SELECT DISTINCT order_id FROM `history` 
WHERE user_id = '$user_id'") or die('query failed');

// Initialize variable to keep track of previous order ID
$prev_order_id = null;

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Purchase History</title>
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
   <link rel="stylesheet" href="css/styleindex.css">
   <style>
      .material-symbols-outlined {
         font-variation-settings:
            'FILL' 0,
            'wght' 400,
            'GRAD' 0,
            'opsz' 20;
      }

      .order-card {
         border: 1px solid #ddd;
         border-radius: 8px;
         background-color: #fff;
         margin-bottom: 20px;
         box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      }

      .order-header {
         padding: 15px;
         background-color: #f8f8f8;
         border-bottom: 1px solid #ddd;
         display: flex;
         justify-content: space-between;
         align-items: center;
      }

      .order-body {
         padding: 15px;
      }

      .product-container {
         display: flex;
         flex-wrap: wrap;
         gap: 20px;
      }

      .product {
         border: 1px solid #ddd;
         padding: 10px;
         border-radius: 5px;
         background-color: #fff;
         flex: 1;
         min-width: 220px;
         display: flex;
         flex-direction: column;
         justify-content: space-between;
      }

      .rating {
         display: flex;
         align-items: center;
         flex-direction: row-reverse;
         justify-content: center;
         margin-bottom: 15px;
      }

      .rating input[type="radio"] {
         display: none;
      }

      .rating label {
         font-size: 2rem;
         color: #ccc;
         cursor: pointer;
         margin-right: 0.5rem;
      }

      .rating label:hover,
      .rating label:hover~label,
      .rating input[type="radio"]:checked~label {
         color: orange;
      }

      .invoice-button {
         text-align: center;
         margin-top: 20px;
      }

      .accordion-button:focus {
         box-shadow: none;
      }

      .collapse:not(.show) {
         display: block;
         height: 0;
         overflow: hidden;
         transition: height 0.3s ease;
      }

      .collapse.show {
         height: auto;
      }

   </style>
   <link rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,200,0,0" />
</head>

<body>
   <!-- Header -->
   <?php include 'header.php'; ?>

   <!-- heading -->
   <div class="heading">
      <h3>History</h3>
      <p> <a href="home.php">Home</a> / History </p>
   </div>
   
   <section class="products">
      <h1 class="title">History purchase</h1>

      <div class="container my-4">
         <div class="accordion" id="orderAccordion">
         <?php
         if (mysqli_num_rows($select_order_ids) > 0) {
            while ($order_row = mysqli_fetch_assoc($select_order_ids)) {
               $order_id = $order_row['order_id'];
               
               if ($order_id != $prev_order_id) {
                  ?>
                  <div class="order-card">
                     <div class="order-header">
                        <h4>Order ID: <?php echo $order_id; ?></h4>
                        <button class="btn btn-link accordion-button" type="button" data-toggle="collapse" data-target="#collapse-<?php echo $order_id; ?>" aria-expanded="true" aria-controls="collapse-<?php echo $order_id; ?>">
                           View Details
                        </button>
                     </div>
                     <div id="collapse-<?php echo $order_id; ?>" class="collapse" aria-labelledby="heading-<?php echo $order_id; ?>" data-parent="#orderAccordion">
                        <div class="order-body">
                           <div class="product-container">
                           <?php
                           $select_products = mysqli_query($conn, "SELECT * FROM `history` WHERE user_id = '$user_id' AND order_id = '$order_id'") or die('query failed');

                           if (mysqli_num_rows($select_products) > 0) {
                              while ($fetch_products = mysqli_fetch_assoc($select_products)) {
                                 ?>
                                 <div class="product">
                                    <form action="" method="post">
                                       <div class="name">
                                          <strong>Brand:</strong> <?php echo $fetch_products['product_brand']; ?>
                                       </div>
                                       <div class="name">
                                          <strong>Product Name:</strong> <?php echo $fetch_products['product_name']; ?>
                                       </div>
                                       <div class="name">
                                          <strong>Product Size:</strong> <?php echo $fetch_products['product_size']; ?>
                                       </div>
                                       <div class="name">
                                          <strong>Order ID:</strong> <?php echo $fetch_products['order_id']; ?>
                                       </div>
                                       <h5>Rating:</h5>
                                       <div class="rating">
                                          <input type="radio" name="product_rate" id="star5-<?php echo $fetch_products['id']; ?>" value="5" <?php if ($fetch_products['product_rate'] == 5) echo 'checked'; ?>>
                                          <label for="star5-<?php echo $fetch_products['id']; ?>">&#9733;</label>
                                          <input type="radio" name="product_rate" id="star4-<?php echo $fetch_products['id']; ?>" value="4" <?php if ($fetch_products['product_rate'] == 4) echo 'checked'; ?>>
                                          <label for="star4-<?php echo $fetch_products['id']; ?>">&#9733;</label>
                                          <input type="radio" name="product_rate" id="star3-<?php echo $fetch_products['id']; ?>" value="3" <?php if ($fetch_products['product_rate'] == 3) echo 'checked'; ?>>
                                          <label for="star3-<?php echo $fetch_products['id']; ?>">&#9733;</label>
                                          <input type="radio" name="product_rate" id="star2-<?php echo $fetch_products['id']; ?>" value="2" <?php if ($fetch_products['product_rate'] == 2) echo 'checked'; ?>>
                                          <label for="star2-<?php echo $fetch_products['id']; ?>">&#9733;</label>
                                          <input type="radio" name="product_rate" id="star1-<?php echo $fetch_products['id']; ?>" value="1" <?php if ($fetch_products['product_rate'] == 1) echo 'checked'; ?>>
                                          <label for="star1-<?php echo $fetch_products['id']; ?>">&#9733;</label>
                                       </div>
                                       <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">                        
                                       <input type="hidden" name="product_name" value="<?php echo $fetch_products['product_name']; ?>">
                                       <div class="text-center">
                                          <input type="submit" class="btn btn-primary" value="Submit Rating" name="submit_rate">
                                       </div>
                                    </form>
                                 </div>
                                 <?php
                                 $_SESSION['invoice_number'] = $fetch_products['invoice_number'];
                              }
                           } else {
                              echo '<p class="empty text-center">No products added yet for this order ID!</p>';
                           }
                           ?>
                           </div>
                           <div class="invoice-button">
                              <form action="invoice.php" method="GET">
                                 <input type="hidden" name="invoice_number" value="<?php echo $_SESSION['invoice_number']; ?>">
                                 <button type="submit" class="btn btn-primary">View Invoice</button>
                              </form>
                           </div>
                        </div>
                     </div>
                  </div>
                  <?php
               }
               $prev_order_id = $order_id;
            }
         } else {
            echo '<p class="empty text-center">No orders found!</p>';
         }
         ?>
         </div>
      </div>
   </section>

   <!-- Footer -->
   <?php include 'footer.php'; ?>

   <!-- JS -->
   <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
   <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
   <script src="js/script.js"></script>
</body>

</html>
