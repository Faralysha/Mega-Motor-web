<?php
// Include the configuration file
include 'config.php';

// Start the session
session_start();

// Check if the user is logged in and is a staff member
if (isset($_SESSION['user_type']) && $_SESSION['user_type'] === 'staff') {
    // Include the staff header if the user is a staff member
    include 'staff_header.php';
} else {
    // Include the admin header if the user is not a staff member (admin or other)
    include 'admin_header.php';
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>messages</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php
// Include the appropriate header file based on the user's role
if (isset($_SESSION['admin_id'])) {
    include 'admin_header.php';
} elseif (isset($_SESSION['staff_id'])) {
    include 'staff_header.php';
}
?>

<section class="messages">

   <h1 class="title"> messages </h1>

   <div class="box-container">
   <?php
      $select_message = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
      if(mysqli_num_rows($select_message) > 0){
         while($fetch_message = mysqli_fetch_assoc($select_message)){
      
   ?>
   <div class="box">
      <p> user id : <span><?php echo $fetch_message['user_id']; ?></span> </p>
      <p> name : <span><?php echo $fetch_message['name']; ?></span> </p>
      <p> Phone : <span><?php echo $fetch_message['phone']; ?></span> </p>
      <p> email : <span><?php echo $fetch_message['email']; ?></span> </p>
      <p> message : <span><?php echo $fetch_message['message']; ?></span> </p>
      <a href="admin_contacts.php?delete=<?php echo $fetch_message['id']; ?>" onclick="return confirm('delete this message?');" class="delete-btn">delete message</a>
   </div>
   <?php
      };
   }else{
      echo '<p class="empty">you have no messages!</p>';
   }
   ?>
   </div>

</section>









<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>