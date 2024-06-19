<?php
include 'config.php';
session_start();

if (isset($_POST['submit'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $pass = mysqli_real_escape_string($conn, md5($_POST['password']));

    $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('query failed');

    if (mysqli_num_rows($select_users) > 0) {
        $row = mysqli_fetch_assoc($select_users);

        // Check the user type
        if ($row['user_type'] == 'admin') {
            // Set session variables for admin
            $_SESSION['admin_id'] = $row['id'];
            $_SESSION['admin_name'] = $row['name']; // Assuming the admin's name is stored in the 'name' column
            $_SESSION['admin_email'] = $row['email']; // Assuming the admin's email is stored in the 'email' column

            // Redirect to admin_page.php after successful login
            header('location: admin_page.php');
            exit();
        } elseif ($row['user_type'] == 'staff') {
            // Set session variables for staff
            $_SESSION['staff_id'] = $row['id'];
            $_SESSION['staff_name'] = $row['name']; // Assuming the staff's name is stored in the 'name' column
            $_SESSION['staff_email'] = $row['email']; // Assuming the staff's email is stored in the 'email' column

            // Redirect to staff_page.php after successful login
            header('location: staff_page.php');
            exit();
        } else {
            // If the user type is neither admin nor staff, handle other cases or show an error message
            $message[] = 'You are not authorized to access this panel!';
        }
    } else {
        // Handle incorrect email or password
        $message[] = 'Incorrect email or password!';
    }
}

?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login Form</title>
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
   background: crimson;
   color: #fff;
}

.form-container form p{
   margin-top: 10px;
   font-size: 20px;
   color:#333;
}

.form-container form p a{
   color:crimson;
}

.form-container form .error-msg{
   margin:10px 0;
   display: block;
   background: crimson;
   color:#fff;
   border-radius: 5px;
   font-size: 20px;
   padding:10px;
}

.containerlogin .logintitle{
    display: flex;
    font-size: 40px;
    text-align: center;
    text-transform: uppercase;
    color: #ffff;
    text-align: center;
    justify-content: center;
}

</style>
</head>
<body>
   <div class="containerlogin">
      <h1 class="logintitle">
          <b> Welcome Admin</b>
      </h1>
   </div>

   <div class="form-container">
      <form action="" method="post">
         <h3>Login now</h3>
         <input type="email" name="email" required placeholder="Enter your email">
         <input type="password" name="password" required placeholder="Enter your password">
         <input type="submit" name="submit" value="Login" class="form-btn">
         <!-- Link to register new staff -->
         <p>Do you want to add new staff? <a href="admin_register.php">Add new staff</a></p>
      </form>
   </div>
</body>
</html>
