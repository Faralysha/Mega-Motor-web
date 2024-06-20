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

   <!-- Bootstrap link -->
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
   
   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/styleindex.css">
   <style>
      @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap');

   .home {
      min-height: 70vh;
      background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), url(biker.png) no-repeat;
      background-size: cover;
      background-position: center;
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative; /* Add relative positioning */
   }

   .home .background-overlay {
      position: absolute;
      top: 0;
      left: 0;
      height: 100%; /* Set height to cover entire parent (home) */
      width: 100%; /* Cover entire parent (home) */
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
   <h4>Gear up for epic journeys with new helmets and accessories.<br><span>Explore today and ride the future!</span></h4>
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
      <input type="hidden" name="product_brand" value="<?php echo $fetch_products['brand']; ?>">
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">      
      <a href="shop_details.php?id=<?php echo $fetch_products['id']; ?>" class="btn btn-primary btn-lg">View</a>
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

<section class="products">
   <h1 class="title">Recommended Product</h1>
   <div class="box-container">
      <?php  
         $select_recommendation = mysqli_query($conn, "SELECT p.*, COUNT(h.product_id) AS purchase_count
         FROM products p
         LEFT JOIN history h ON p.id = h.product_id
         GROUP BY p.id
         ORDER BY purchase_count DESC
         LIMIT 3") or die('Query failed: Get recommendation products');
         
         if(mysqli_num_rows($select_recommendation) > 0){
            while($fetch_recommendation = mysqli_fetch_assoc($select_recommendation)){
      ?>
     <form action="" method="post" class="box">
      <!-- Product Image -->
      <img class="image" src="uploaded_img/<?php echo $fetch_recommendation['image']; ?>" alt="Product Image">
      
      <div class="brand-name"><?php echo $fetch_recommendation['brand'] .' '. $fetch_recommendation['name']; ?></div>
      <div class="category"><?php echo $fetch_recommendation['category'];?></div> 
      <div class="price">RM <?php echo $fetch_recommendation['price'];?></div> 
      
      <!-- Product Ratings -->
      <div class="pro_rates">
         <?php
         $rating = $fetch_recommendation['pro_rates'];
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
      <input type="hidden" name="product_id" value="<?php echo $fetch_recommendation['id']; ?>">
      <input type="hidden" name="product_brand" value="<?php echo $fetch_recommendation['brand']; ?>">
      <input type="hidden" name="product_name" value="<?php echo $fetch_recommendation['name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_recommendation['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_recommendation['image']; ?>">      
      <a href="shop_details.php?id=<?php echo $fetch_recommendation['id']; ?>" class="btn btn-primary btn-lg">View</a>
                     </form>   
                     <?php
                  }
               } else {
                  echo '<p class="empty">No recommendation products yet!</p>';
               }
      ?>
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