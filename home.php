<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:index.php');
}

// Calculate the total rating from product and history
$get_productrating = mysqli_query($conn, "SELECT * FROM `products`") or die('Query failed: Get product rating');
while ($productrating_get = mysqli_fetch_assoc($get_productrating)) {
    $get_nameproducts = $productrating_get['name'];

    $get_historyrating = mysqli_query($conn, "SELECT * FROM `history` WHERE product_name = '$get_nameproducts'") or die('Query failed: Get history rating');
    $totalRate = 0;
    $rateCount = 0;

    while ($historyrating_get = mysqli_fetch_assoc($get_historyrating)) {
        $totalRate += $historyrating_get['product_rate'];
        $rateCount++;
    }

    if ($rateCount > 0) {
        $averageRating = $totalRate / $rateCount;
        mysqli_query($conn, "UPDATE `products` SET pro_rates ='$averageRating' WHERE name = '$get_nameproducts'") or die('Query failed: Update product rating');
    }
}

if (isset($_POST['add_to_cart'])) {
   $product_id = mysqli_real_escape_string($conn, $_POST['product_id']);
   $product_name = mysqli_real_escape_string($conn, $_POST['product_name']);
   $product_category = mysqli_real_escape_string($conn, $_POST['product_category']);
   $product_price = mysqli_real_escape_string($conn, $_POST['product_price']);
   $product_size = mysqli_real_escape_string($conn, $_POST['product_size']);
   $product_image = mysqli_real_escape_string($conn, $_POST['product_image']);
   $product_quantity = intval($_POST['product_quantity']);

   // Check if the product is already in the cart for the user
   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE product_name = '$product_name' AND user_id = '$user_id'") or die('Query failed: Check cart');

   if (mysqli_num_rows($check_cart_numbers) > 0) {
       $message[] = 'Already added to cart!';
   } else {
       // Compare the product quantity with available stock
       $compare_quant = mysqli_query($conn, "SELECT quant FROM products WHERE id='$product_id'") or die('Query failed: Compare quantity');
       $fetch_quantitem = mysqli_fetch_assoc($compare_quant);

       if ($fetch_quantitem['quant'] > 0 && $product_quantity <= $fetch_quantitem['quant']) {
           // Insert the product into the cart
           mysqli_query($conn, "INSERT INTO `cart` (user_id, product_id, product_name, product_size, product_price, quantity, product_image) VALUES ('$user_id', '$product_id', '$product_name', '$product_size', '$product_price', '$product_quantity', '$product_image')") or die('Query failed: Add to cart');
           $message[] = 'Product added to cart!';
       } else {
           $message[] = 'Product out of stock or exceeds available quantity';
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
   <title>Home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/styleindex.css">
   <!-- Bootstrap link -->
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

   <style>

   .home .background-container{
   background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url(biker.png) no-repeat;
   background-size: cover;
   background-position: center;
   min-height: 100vh;
   position: relative;
   }

   .home .background-overlay {
   position: absolute;
   top: 0;
   left: 0;
   width: 100%;
   height: 100%;
   background: rgba(0, 0, 0, 0.3); /* 30% opacity black layer */
   display: flex;
   flex-direction: column;
   justify-content: center;
   align-items: center;
   color: white;
   }

   .home .background-overlay h1 {
   color: #FFF;
   text-align: center;
   font-family: Inter;
   font-size: 50px;
   font-style: normal;
   font-weight: 600;
   line-height: normal;
   margin-bottom: 10px;
   }

   .home .background-overlay h4 {
   color: #FFF;
   text-align: center;
   font-family: Inter;
   font-size: 20px;
   font-style: normal;
   font-weight: 600;
   line-height: normal;
   margin-bottom: 10px;
   }

 .home .background-container .background-overlay .discover-button {
         margin-top: 20px; /* Adjust as needed */
         background-color: #EA2525; /* Your button color */
         color: white;
         padding: 10px 20px;
         font-size: 28px;
         font-family: Inter;
         font-weight: 600;
         line-height: 32px;
         letter-spacing: 0.20px;
         word-wrap: break-word;
         text-decoration: none;
         border-radius: 5px;
         cursor: pointer;
      }

   .home .background-container .background-overlay .discover-button:hover {
         background-color:#cc004d; /* Your hover color */
      }

</style>

</head>
<body>
   
<?php include 'header.php'; ?>

<section class="home">

<div class="background-container">
   <div class="background-overlay">
   <h1>Stay Ahead of the Curve with Our Latest Collection</h1>
   <h4>Gear up for epic journeys with new helmets and accessories. Explore today and ride the future!</h4>
      <a href="about.php" class="discover-button">Discover More</a>
   </div>
</div>

</section>

<section class="products">

   <h1 class="title">Latest products</h1>

   <div class="box-container">

      <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT 6") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
     <form action="" method="post" class="box">
      <!-- Product Image -->
      <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="Product Image">
      
      <div class="brand-name"><?php echo $fetch_products['brand'] .' '. $fetch_products['name']; ?></div>
      <div class="category"><?php echo $fetch_products['category'];?></div> 
      <div class="price">RM <?php echo $fetch_products['price'];?></div> 
      
      <!-- Product Ratings -->
      <div class="pro_rates">
         <?php
         $rating = $fetch_products['pro_rates'];
         for ($i = 1; $i <= 5; $i++) {
            if ($i <= $rating) {
               echo '<i class="fa fa-star text-primary" style="margin-right: 5px;"></i>';
            } else {
               echo '<i class="fa fa-star text-secondary" style="margin-right: 5px;"></i>';
            }
         }
         ?>
         </div>
      
      <!-- Hidden Fields for Cart Processing -->
      <input type="hidden" name="product_quantity" value="1" class="form-control form-control-lg">
      <input type="hidden" name="product_id" value="<?php echo $fetch_products['id']; ?>">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
      <input type="hidden" name="product_size" value="<?php echo $fetch_products['size']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">      
      <a href="shop_details.php?id=<?php echo $fetch_products['id']; ?>" class="btn btn-primary btn-lg">View</a>
      
      <!-- Display popup -->
<div id="myModal<?php echo $fetch_products['id'] ?>" class="modal fade" role="dialog">
                            <div class="modal-dialog">
                                <div class="modal-content">
                                    <div class="modal-header">
                                        <h4 class="modal-title">Details</h4>
                                        <button type="button" class="close" data-dismiss="modal">&times;</button>
                                    </div>
                                    <div class="modal-body">
                                        <div class="container">
                                            <div class="imgBx">
                                                <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
                                            </div>
                                        </div>
                                        <div class="content">
                                            <h2>
                                                <?php echo $fetch_products['name']; ?>
                                                <br>
                                                <span>Berjaya Mega Motor</span>
                                            </h2>
                                            <p>
                                                <?php echo $fetch_products['description']; ?>
                                            </p>
                                            <p>
                                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor"
                                                class="bi bi-box" viewBox="0 0 16 16">
                                                    <path
                                                        d="M8.186 1.113a.5.5 0 0 0-.372 0L1.846 3.5 8 5.961 14.154 3.5 8.186 1.113zM15 4.239l-6.5 2.6v7.922l6.5-2.6V4.24zM7.5 14.762V6.838L1 4.239v7.923l6.5 2.6zM7.443.184a1.5 1.5 0 0 1 1.114 0l7.129 2.852A.5.5 0 0 1 16 3.5v8.662a1 1 0 0 1-.629.928l-7.185 2.874a.5.5 0 0 1-.372 0L.63 13.09a1 1 0 0 1-.63-.928V3.5a.5.5 0 0 1 .314-.464L7.443.184z" />
                                                </svg>
                                                BUBBLEWARP + 3 FREEGIFT, KOTAK, STOKING, KEYCHAIN
                                            </p>
                                            <h3>Size:
                                                <?php echo $fetch_products['size']; ?>
                                            </h3>
                                            <h3>Stock:
                                                <?php echo $fetch_products['quant']; ?>
                                            </h3>
                                            <h3>RM:
                                                <?php echo $fetch_products['price']; ?>
                                            </h3>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                     </form>   
                     <?php
                  }
               }else{
                  echo '<p class="empty">no products added yet!</p>';
               }
               ?>
               </div>
               
   <div class="load-more" style="margin-top: 2rem; text-align:center">
      <a href="shop.php" class="option-btn">Load More</a>
   </div>

</section>



<section class="home-contact">

   <div class="content">
      <h3>have any questions?</h3>
      <p>Feel free to contact us through "Contact Us" or directly message us through this contact</p>
      <a href="contact.php" class="white-btn">contact us</a>
   </div>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js">
   
</script>

</body>
</html>