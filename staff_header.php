<?php

include 'config.php';

if (!isset($_SESSION['user_id'])) {
   header('location:index.php');
   exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user details from the database
$stmt = $conn->prepare('SELECT * FROM users WHERE id = ?');
$stmt->bind_param('i', $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

if (!$user) {
    header('Location: index.php');
    exit();
}

if(isset($message)){
   foreach($message as $msg){
      echo '
      <div class="message">
         <span>'.$msg.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>

<header class="header">
   <div class="flex">
      <a href="admin_page.php" class="logo">Berjaya Mega Motor<span>Staff</span></a>
      <nav class="navbar">
         <a href="staff_page.php">Home</a>
         <a href="staff_products.php">Products</a>
         <a href="staff_order.php">Orders</a>
         <a href="staff_contacts.php">Messages</a>
      </nav>
      <div class="icons">
         <div id="menu-btn" class="fas fa-bars"></div>
         <div id="user-btn" class="fas fa-user"></div>
      </div>
      <div class="account-box">
         <p>Username: <span><?php echo htmlspecialchars($user['name']); ?></span></p>
         <p>Email: <span><?php echo htmlspecialchars($user['email']); ?></span></p>
         <a href="staff_logout.php" class="delete-btn">Logout</a>
         <!-- <div>New <a href="login.php">Login</a> | <a href="register.php">Register</a></div> -->
      </div>
   </div>
</header>
