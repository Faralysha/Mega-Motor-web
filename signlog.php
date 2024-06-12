<?php

include 'config.php';
session_start();

if (isset($_POST['loginn'])) {

	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$pass = mysqli_real_escape_string($conn, md5($_POST['password']));

	$select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('login failed');
if (mysqli_num_rows($select_users) > 0) {

				$row = mysqli_fetch_assoc($select_users);

				if ($row['user_type'] == 'admin') {

					$_SESSION['admin_name'] = $row['name'];
					$_SESSION['admin_email'] = $row['email'];
					$_SESSION['admin_id'] = $row['id'];
					header('location:admin_page.php');

				} elseif ($row['user_type'] == 'user') {

					$_SESSION['user_name'] = $row['name'];
					$_SESSION['user_email'] = $row['email'];
					$_SESSION['user_id'] = $row['id'];
					header('location:home.php');

				}

			} 
}


// For register new user
if (isset($_POST['signuppp'])) {
	// Variable declaration
	$name = mysqli_real_escape_string($conn, $_POST['name']);
	$email = mysqli_real_escape_string($conn, $_POST['email']);
	$pass = mysqli_real_escape_string($conn, md5($_POST['password']));
	$cpass = mysqli_real_escape_string($conn, md5($_POST['cpassword']));
	$user_type = $_POST['user_type'];
	$pnum = "0";

	$select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE email = '$email' AND password = '$pass'") or die('register not success');
	// See if the user already exist
	if (mysqli_num_rows($select_users) > 0) {
		$message[] = 'user already exist!';
		popalert();
	} else {
		if ($pass != $cpass) {
			// Check the reconfirm password 
			$message[] = 'confirm password not matched!';
			header('location:signlog.php');

		} else {
			// Insert new user to the
			mysqli_query($conn, "INSERT INTO `users`(name, email, password, user_type,pnumber) VALUES('$name', '$email', '$cpass', '$user_type','$pnum')") or die('insert failed');
			$message[] = 'registered successfully!';
			header('location:signlog.php');
		}
	}
}

?>
<!DOCTYPE html>
<html>

<head>
	<title>SignUp and Login</title>
	<link rel="stylesheet" type="text/css" href="../css/signlog.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<!-- captcha -->
	<script src="https://www.google.com/recaptcha/api.js" async defer></script>
	<style>
		.callout {
			position: fixed;
			bottom: 35px;
			right: 20px;
			margin-left: 20px;
			max-width: 300px;
		}

		/* Callout header */
		.callout-header {
			padding: 25px 15px;
			background: #555;
			font-size: 30px;
			color: white;
		}

		/* Callout container/body */
		.callout-container {
			padding: 15px;
			background-color: #ccc;
			color: black
		}

		/* Close button */
		.closebtn {
			position: absolute;
			top: 5px;
			right: 15px;
			color: white;
			font-size: 30px;
			cursor: pointer;
		}

		/* Change color on mouse-over */
		.closebtn:hover {
			color: lightgrey;
		}
	</style>
</head>

<body>

	<?php
	function popalert()
	{

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
	}
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
	<div class="container" id="container">
		<div class="form-container sign-up-container">

			<form action="" method="post">
				<h1>Create Account</h1>
				<!-- <div class="social-container">
		<a href="#" class="social"><i class="fa fa-facebook"></i></a>
		<a href="#" class="social"><i class="fa fa-google"></i></a>
		<a href="#" class="social"><i class="fa fa-linkedin"></i></a> 
		</div> -->
				<!-- <span>or use your email for registration</span> -->
				</br>

				<input type="text" name="name" placeholder="Username">
				<input type="email" name="email" placeholder="Email">
				<input type="password" name="password" placeholder="Password">
				<input type="password" name="cpassword" placeholder="Confirm your password" required />
				</br>

				<select hidden name="user_type">
					<option value="user">user</option>
					<!-- <option value="user">admin</option> -->
				</select>
				<input type="submit" name="signuppp" value="signup" class="signupbtn">

				<!-- <button value="signuppp">SignUp</button> -->
			</form>
		</div>

		<div class="form-container sign-in-container">
			<form action="" method="post">
				<h1>Sign In</h1>
				<!-- <div class="social-container">
		<a href="#" class="social"><i class="fa fa-facebook"></i></a>
		<a href="#" class="social"><i class="fa fa-google"></i></a>
		<a href="#" class="social"><i class="fa fa-linkedin"></i></a> 
		</div> -->
				<!-- <span>or use your account</span> -->
				</br>
				<input type="email" name="email" placeholder="Email">
				<input type="password" name="password" placeholder="Password">
				<!-- captcha design -->

				<!-- <div class="g-recaptcha" data-sitekey="6LcEeccmAAAAAADkCxirgZE1HQV0HlocMoEtRXXc" requried></div> -->
				<!-- <input type="submit" name="submit" value="Login" class="submitlogin"/> -->
				<!-- <a href="#">Forgot Your Password</a> -->
				<input type="submit" name="loginn" value="Login" class="loginbtn">
			</form>

		</div>
		<!-- -------------------------------------------------------------------------- -->
		<div class="overlay-container">
			<div class="overlay">
				<div class="overlay-panel overlay-left">
					<h1>Welcome Back!</h1>
					<p>To keep connected with us please login with your personal info</p>
					<button class="ghost" id="signIn">Sign In</button>
				</div>
				<div class="overlay-panel overlay-right">
					<h1>Hello, Friend!</h1>
					<p>Enter your details and start journey with us</p>
					<button class="ghost" id="signUp">Sign Up</button>
				</div>
			</div>
		</div>
	</div>
	<!-- //////////////////////////////////////////////////////////////////// -->
	<!-- <div class="callout">
		<div class="callout-header">Captcha validation</div>
		<span class="closebtn" onclick="this.parentElement.style.display='none';">&times;</span>
		<div class="callout-container">
			<p>
				<?php if (!empty($response)) { ?>
				<div class="form-group col-12 text-center">
					<div class="alert text-center <?php echo $response['status']; ?>">
						<?php echo $response['message']; ?>
					</div>
				</div>
			<?php } ?>

			</p>
		</div>
	</div> -->
	<!-- callback validate -->
	<!-- <script type="text/javascript">
		var onloadCallback = function () {
			alert("grecaptcha is ready!");
		};
	</script> -->

	<script src="https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit" async defer>
	</script>
	<script type="text/javascript">
		const signUpButton = document.getElementById('signUp');
		const signInButton = document.getElementById('signIn');
		const container = document.getElementById('container');

		signUpButton.addEventListener('click', () => {
			container.classList.add("right-panel-active");
		});
		signInButton.addEventListener('click', () => {
			container.classList.remove("right-panel-active");
		});
	</script>


</body>

</html>