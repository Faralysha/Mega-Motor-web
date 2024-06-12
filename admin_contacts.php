<?php
include 'config.php';

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit;
}

// Handle message deletion
if(isset($_GET['delete']) && is_numeric($_GET['delete'])) {
    $message_id = $_GET['delete'];
    $delete_query = "DELETE FROM `message` WHERE id = $message_id";
    $delete_result = mysqli_query($conn, $delete_query);
    if($delete_result) {
        header("Location: admin_contacts.php");
        exit;
    } else {
        echo "Error: " . mysqli_error($conn);
        // Handle error condition, if necessary
    }
}

// Fetch contacts or messages
$query = "SELECT * FROM `message`";
$result = mysqli_query($conn, $query) or die('query failed');

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
include 'admin_header.php';
?>

<section class="messages">

   <h1 class="title"> messages </h1>

   <div class="box-container">
   <?php
      if(mysqli_num_rows($result) > 0){
         while($fetch_message = mysqli_fetch_assoc($result)){
      
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
