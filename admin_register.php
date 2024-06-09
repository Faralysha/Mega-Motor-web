<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));
    $cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
    $pnumber = mysqli_real_escape_string($conn, $_POST['pnumber']);
    $user_type = 'staff'; // Adjust as needed, e.g., 'admin' or 'staff'

    if ($pass != $cpass) {
        $message[] = 'Passwords do not match!';
    } else {
        $check_email = mysqli_query($conn, "SELECT email FROM `users` WHERE email = '$email'") or die('query failed');
        
        if (mysqli_num_rows($check_email) > 0) {
            $message[] = 'Email already registered!';
        } else {
            $insert = mysqli_query($conn, "INSERT INTO `users` (name, email, password, user_type, pnumber) VALUES ('$name', '$email', '$pass', '$user_type', '$pnumber')") or die('query failed');
            
            if ($insert) {
                $message[] = 'Staff registered successfully';
                header('location: admin_login.php'); // Adjust as needed, e.g., redirect to login page
                exit();
            } else {
                $message[] = 'Registration failed!';
            }
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
      <title>login form</title>
   
      <!-- custom css file link  -->
      <link rel="stylesheet" href="css/adminstyle.css">
   
   <style>
   
   *{
      font-family: 'Poppins', sans-serif;
      margin:0; padding:0;
      box-sizing: border-box;
      outline: none; border:none;
      text-decoration: none;
   }
   
   body {
      margin: 0;
      padding: 0;
      font-family: 'Poppins', sans-serif;
      box-sizing: border-box;
      outline: none;
      border: none;
      text-decoration: none;
      background-image: url(images/admincover.jpg);
      background-repeat: no-repeat;
      background-size: cover;
     }
   
   .container{
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding:20px;
      padding-bottom: 60px;
   }
   
   .container .content{
      text-align: center;
   }
   
   .container .content h3{
      font-size: 30px;
      color:#333;
   }
   
   .container .content h3 span{
      background: crimson;
      color:#fff;
      border-radius: 5px;
      padding:0 15px;
   }
   
   .container .content h1{
      font-size: 50px;
      color:#333;
   }
   
   .container .content h1 span{
      color:crimson;
   }
   
   .container .content p{
      font-size: 25px;
      margin-bottom: 20px;
   }
   
   .container .content .btn{
      display: inline-block;
      padding:10px 30px;
      font-size: 20px;
      background: #333;
      color:#fff;
      margin:0 5px;
      text-transform: capitalize;
   }
   
   .container .content .btn:hover{
      background: crimson;
   }
   
   .form-container{
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      padding:20px;
      padding-bottom: 60px;
   }
   
   .form-container form{
      padding:20px;
      border-radius: 5px;
      box-shadow: 0 5px 10px rgba(0,0,0,.1);
      background: #fff;
      text-align: center;
      width: 500px;
   }
   
   .form-container form h3{
      font-size: 30px;
      text-transform: uppercase;
      margin-bottom: 10px;
      color:#333;
   }
   
   .form-container form input,
   .form-container form select{
      width: 100%;
      padding: 10px 15px;
      font-size: 17px;
      margin: 8px 0;
      background: #eee;
      border: 1px solid #333; /* Add a thin border around the email input */
      border-radius: 5px;
   }
   
   .form-container form select option{
      background: #fff;
   }
   
   .form-container form .form-btn {
      background: #EA2525;
      color: #fff;
      text-transform: capitalize;
      font-size: 20px;
      cursor: pointer;
      border: none; /* Add this line to remove the border */
   }
   
   .form-container form .form-btn:hover {
      background: #dc143c;
      color: #fff;
   }
   
   .form-container form p{
      margin-top: 10px;
      font-size: 20px;
      color:#333;
   }
   
   .form-container form p a{
      color:#dc143c;
   }
   
   .form-container form .error-msg{
      margin:10px 0;
      display: block;
      background: #dc143c;
      color:#fff;
      border-radius: 5px;
      font-size: 20px;
      padding:10px;
   }
   </style>
   
   </head>
   <body>
      
   <?php
   if (isset($message)) {
      foreach ($message as $message) {
         echo '
      <div class="message">
         <span>' . $message . '</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
      }
   }
   ?>
   <div class="form-container">
       <form action="" method="post">
           <h3>Add New Staff</h3>
           <input type="text" name="name" placeholder="Enter staff name" required class="box">
           <input type="email" name="email" placeholder="Enter staff email" required class="box">
           <input type="password" name="password" placeholder="Enter staff password" required class="box">
           <input type="password" name="cpassword" placeholder="Confirm staff password" required class="box">
           <input type="text" name="pnumber" placeholder="Phone Number" required class="box">
           <input type="submit" name="submit" value="Add New Staff" class="form-btn">
       </form>
   </div>
</body>
</html>