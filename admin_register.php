<?php

include 'config.php';
session_start();

// Check if admin_id is not set or empty, then redirect to admin_login.php
if (!isset($_SESSION['admin_id']) || empty($_SESSION['admin_id'])) {
   header('location:admin_login.php');
   exit(); // Add an exit statement to stop further execution
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `users` WHERE id = '$delete_id'") or die('query failed');
   // header('location:admin_users.php');
}

if(isset($_POST['submit'])){

   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
   $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
   $user_type = $_POST['user_type'];

   $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

   if(mysqli_num_rows($select_users) > 0){
      $message[] = 'user already exist!';
   }else{
      if($pass != $cpass){
         $message[] = 'confirm password not matched!';
      }else{
         mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type) VALUES('$name', '$email', '$cpass', '$user_type')") or die('query failed');
         $message[] = 'registered successfully!';
         header('location:index.php');
      }
   }

}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>add new staff</h3>
      <input type="text" name="name" placeholder="Enter staff name" required class="box">
      <input type="email" name="email" placeholder="Enter staff email" required class="box">
      <input type="password" name="password" placeholder="Enter staff password" required class="box">
      <input type="password" name="cpassword" placeholder="Confirm staff password" required class="box">
      <input type="pnumber" name="pnumber" placeholder="Phone Number" required class="box">

      <select hidden name="user_type" class="box">
         <option value="admin">user</option>
         <!-- <option value="user">admin</option> -->
      </select>
      <input type="submit" name="submit" value="Add New Staff" class="btn">
   </form>

</div>

</body>
</html>