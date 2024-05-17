<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:index.php');
}

if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `cart` WHERE name = '$product_name' AND user_id = '$user_id'") or die('query failed');

   if(mysqli_num_rows($check_cart_numbers) > 0){
      $message[] = 'already added to cart!';
   }else{
      mysqli_query($conn, "INSERT INTO `cart`(user_id, name, price, quantity, image) VALUES('$user_id', '$product_name', '$product_price', '$product_quantity', '$product_image')") or die('query failed');
      $message[] = 'product added to cart!';
   }

}

?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

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

   <h1 class="title">latest products</h1>

   <div class="box-container">

      <?php  
         $select_products = mysqli_query($conn, "SELECT * FROM `products` LIMIT 6") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
      ?>
     <form action="" method="post" class="box">
      <!-- Product Image -->
      <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="Product Image">
      
      <div class="name"><?php echo $fetch_products['name']; ?></div>
      <div class="brand"><?php echo $fetch_products['brand']; ?></div>
      <div class="price">RM <?php echo $fetch_products['price'];?></div> 
      <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
      <input type="hidden" name="product_brand" value="<?php echo $fetch_products['brand']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_products['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
      <a href="shop_details.php" class="btn btn-primary btn-lg">View</a>
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