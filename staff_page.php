<?php

include 'config.php';

session_start();
// recheck if the user login is admin or not
// Check if the admin is login or not
$staff_id = $_SESSION['staff_id'];

if (!isset($staff_id)) {
   // IF no admin, then redirect to the LOGIN again
   header('location:staff_login.php');
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin panel</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">
   <!-- print button icon -->
   <link rel="stylesheet"
      href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:opsz,wght,FILL,GRAD@48,400,0,0" />
   <!-- ajax change report -->
   <script src="https://code.jquery.com/jquery-3.6.4.min.js"
      integrity="sha256-oP6HI9z1XaZNBrJURtCoUT5SUnxFr8s3BzRl+cbzUq8=" crossorigin="anonymous"></script>
   <script>
      $(document).ready(function () {
         $("#report-btn").click(function () {
            $("#report-display").load("admin_report.php");
         })
      })
   </script>
   <!-- Style for the report -->
   <style>
      header:before,
      header:after {
         content: " ";
         display: table;
      }

      header:after {
         clear: both;
      }

      .invoiceNbr {
         font-size: 40px;
         margin-right: 30px;
         margin-top: 30px;
         float: right;
      }

      .logo {
         float: left;
      }

      .from {
         float: left;
      }

      .to {
         float: right;
      }

      .fromto {
         border-style: solid;
         border-width: 1px;
         border-color: #e8e5e5;
         border-radius: 5px;
         margin: 20px;
         min-width: 200px;
      }

      .fromtocontent {
         margin: 10px;
         margin-right: 15px;
      }

      .panel {
         background-color: #e8e5e5;
         padding: 7px;
      }

      .items {
         clear: both;
         display: table;
         padding: 20px;
      }

      /* Factor out common styles for all of the "col-" classes.*/
      div[class^="col-"] {
         display: table-cell;
         padding: 7px;
      }

      /*for clarity name column styles by the percentage of width */
      .col-1-10 {
         width: 10%;
      }

      .col-1-52 {
         width: 52%;
      }

      .row {
         display: table-row;
         page-break-inside: avoid;
      }
   </style>

   <!-- These styles are exactly like the screen styles except they use points (pt) as units
        of measure instead of pixels (px) -->
   <style media="print">

      body {
         font-family: 'Segoe UI', 'Microsoft Sans Serif', sans-serif;
      }

      header:before,
      header:after {
         content: " ";
         display: table;
      }

      header:after {
         clear: both;
      }

      .invoiceNbr {
         font-size: 30pt;
         margin-right: 30pt;
         margin-top: 30pt;
         float: right;
      }

      .logo {
         float: left;
      }

      .from {
         float: left;
      }

      .to {
         float: right;
      }

      .fromto {
         border-style: solid;
         border-width: 1pt;
         border-color: #e8e5e5;
         border-radius: 5pt;
         margin: 20pt;
         min-width: 200pt;
      }

      .fromtocontent {
         margin: 10pt;
         margin-right: 15pt;
      }

      .panel {
         background-color: #e8e5e5;
         padding: 7pt;
      }

      .items {
         clear: both;
         display: table;
         padding: 20pt;
      }

      div[class^="col-"] {
         display: table-cell;
         padding: 7pt;
      }

      .col-1-10 {
         width: 10%;
      }

      .col-1-52 {
         width: 52%;
      }

      .row {
         display: table-row;
         page-break-inside: avoid;
      }
   </style>

   <!-- graph css -->
   <style> 
      .big-graph{
         padding-left: 70px;
         padding-right: 70px;
         padding-top: 20px;
         padding-bottom: 20px;
         /* border: 1px solid black; */
      }
   </style>
</head>

<body>

   <?php include 'staff_header.php'; ?>

   <!-- admin dashboard section starts  -->

   <section class="dashboard">
   <h1 class="title">dashboard</h1>

      <!-- Print Page -->
      <button class="btn" onclick="print()">
         <span class="material-symbols-outlined">
            print </span></button>
            <!-- Change report style -->
            <button id="report-btn" class="btn btn-outline-primary"><span class="material-symbols-outlined">
               lab_profile
            </span></button>
            
         </br>
         </br>
      <div id="report-display">
         <div class="box-container">
            <!-- <?php echo $today_report = date('d-m-y'); ?> -->

            <div class="box">
               <?php
               $total_pendings = 0;
               $select_pending = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'pending'") or die('query failed');
               if (mysqli_num_rows($select_pending) > 0) {
                  while ($fetch_pendings = mysqli_fetch_assoc($select_pending)) {
                     $total_price = $fetch_pendings['total_price'];
                     $total_pendings += $total_price;
                  }
                  ;
               }
               ;
               ?>
               <h3>RM
                  <?php echo $total_pendings; ?>
               </h3>
               <p>total pendings</p>
            </div>

            <div class="box">
               <?php
               $total_completed = 0;
               $select_completed = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'Accepted'") or die('query failed');
               if (mysqli_num_rows($select_completed) > 0) {
                  while ($fetch_completed = mysqli_fetch_assoc($select_completed)) {
                     $total_price = $fetch_completed['total_price'];
                     $total_completed += $total_price;
                  }
                  ;
               }
               ;
               ?>
               <h3>RM
                  <?php echo $total_completed; ?>
               </h3>
               <p>completed payments</p>
            </div>

            <div class="box">
               <?php
               $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
               $number_of_orders = mysqli_num_rows($select_orders);
               ?>
               <h3>
                  <?php echo $number_of_orders; ?>
               </h3>
               <p>order placed</p>
            </div>

            <div class="box">
               <?php
               $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
               $number_of_products = mysqli_num_rows($select_products);
               ?>
               <h3>
                  <?php echo $number_of_products; ?>
               </h3>
               <p>products added</p>
            </div>

            <div class="box">
               <?php
               $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
               $number_of_users = mysqli_num_rows($select_users);
               ?>
               <h3>
                  <?php echo $number_of_users; ?>
               </h3>
               <p>normal users</p>
            </div>

            <div class="box">
               <?php
               $select_staff = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'staff'") or die('query failed');
               $number_of_staff = mysqli_num_rows($select_staff);
               ?>
               <h3>
                  <?php echo $number_of_staff; ?>
               </h3>
               <p>admin users</p>
            </div>

            <div class="box">
               <?php
               $select_account = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
               $number_of_account = mysqli_num_rows($select_account);
               ?>
               <h3>
                  <?php echo $number_of_account; ?>
               </h3>
               <p>total accounts</p>
            </div>

            <div class="box">
               <?php
               $select_messages = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
               $number_of_messages = mysqli_num_rows($select_messages);
               ?>
               <h3>
                  <?php echo $number_of_messages; ?>
               </h3>
               <p>new messages</p>
            </div>

         </div>
      </div>
            </br>
            <div class="big-graph">
               <?php include 'admin_graph.php'; ?>
            </div>

   </section>

   <!-- admin dashboard section ends -->

   <!-- custom admin js file link  -->
   <script src="js/admin_script.js"></script>

</body>

</html>