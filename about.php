<?php

include 'config.php';

session_start();

$user_id = $_SESSION['user_id'];

if(!isset($user_id)){
   header('location:index.php');
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/styleindex.css">

   <!-- bootstrap -->
   <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">

</head>
<style>
   
.heading {
   display: flex;
   flex-flow: column;
   align-items: center;
   justify-content: center;
   gap: 10rem;
   background-image: url('heading.png');
   background-repeat: no-repeat;
   background-position: center;
   text-align: center;
   min-height: 30vh;
}

.heading h3{
   font-size: 5rem;
   color:var(--white);
   text-transform: uppercase;
   text-align: center;
}

.heading p{
   font-size: 2.5rem;
   color:var(--light-white);
}

.heading p a{
   color:var(--crimson);
}

.heading p a:hover{
   text-decoration: underline;
}

</style>
<body>

<?php include 'header.php'; ?>

<div class="heading">
   <h3>about us</h3>
   <p> <a href="home.php">Home</a> / About </p>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/aboutitem.jpg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p>At our ecommerce system, we offer an exceptional shopping experience. When you choose us, you gain access to a wide range of high-quality products that have been carefully curated to meet your needs. Rest assured that your data and transactions are secure, as we prioritize the privacy and protection of our customers. With our user-friendly interface, extensive product selection, and seamless checkout process, shopping with us is convenient and hassle-free.</p>
         <a href="contact.php" class="btn btn-primary btn-lg">contact us</a>
      </div>

   </div>

</section>

<section class="reviews">

   <h1 class="title">client's reviews</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/review_soleh.JPG" alt="">
         <p>I can't say enough good things about this website. It was a breeze to navigate, with a clean and modern design. The content was top-notch, providing valuable information and captivating articles. This website exceeded my expectations in every way.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Soleh</h3>
      </div>

      <div class="box">
         <img src="images/review_Nisa.JPG" alt="">
         <p>The website was user-friendly with a smooth interface and easy navigation. It loaded quickly on various devices. The content was informative and well-written, and the customer service was excellent. Overall, a great experience when using it!.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Nisa</h3>
      </div>

      <div class="box">
         <img src="images/review_danish.JPG" alt="">
         <p>This website was a pleasure to use. It was incredibly user-friendly, with intuitive navigation and a sleek interface. The content was informative and engaging. I recommend this website for its usability, quality content, and excellent support.</p>
         <div class="stars">
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star"></i>
            <i class="fas fa-star-half-alt"></i>
         </div>
         <h3>Danish</h3>
      </div>
   </div>

</section>

<section class="authors">

   <h1 class="title">Sales Department</h1>

   <div class="box-container">

   <div class="box">
         <img src="images/salesdepartment.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>Sales Department</h3>
      </div>

      <div class="box">
         <img src="images/sparepartdepartment.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>Sparepart Department</h3>
      </div>

      <div class="box">
         <img src="images/admindepartment.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>Admin Department</h3>
      </div>

      <div class="box">
         <img src="images/mechanicdepartment.jpg" alt="">
         <div class="share">
            <a href="#" class="fab fa-facebook-f"></a>
            <a href="#" class="fab fa-twitter"></a>
            <a href="#" class="fab fa-instagram"></a>
            <a href="#" class="fab fa-linkedin"></a>
         </div>
         <h3>Service Department</h3>
      </div>

   </div>

</section>

<h1>&nbsp;</h1>
<div class="h_line"></div>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
