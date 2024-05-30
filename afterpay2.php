<?php

include 'config.php';

session_start();
$status_idpayment = $_GET['status_id'];
$user_id = $_SESSION['user_id'];

$temp_itemid = $_SESSION['idname'];



?>
<html>

<head>
    <style>
        body {
            margin: 0;
            padding: 0;
            /* background-image: url('css/images/loginback.jpg'); */
            /* Replace 'background-image.jpg' with the actual path to your background image */
            background-size: cover;
            background-position: center;
            font-family: Arial, sans-serif;
        }

        .container {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .box {
            background-color: rgba(255, 255, 255, 0.9);
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 24px;
            text-align: center;
            color: #333;
            margin: 0;
            padding: 0;
        }

        .centerdiv {
            /* border: 5px solid; */
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 10px;
        }

        .loader {
            border: 10px solid #f3f3f3;
            /* Light grey */
            border-top: 10px solid #3498db;
            /* Blue */
            border-radius: 50%;
            width: 120px;
            height: 120px;
            animation: spin 2s linear infinite;
        }

        @keyframes spin {
            0% {
                transform: rotate(0deg);
            }

            100% {
                transform: rotate(360deg);
            }
        }

        .p {
            font-style: ;
        }
    </style>
</head>

<body>
    <div class="centerdiv">
        <?php
        if ($status_idpayment == 1) {
            $message = 'Payment success';
            $spec_quant = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            if (mysqli_num_rows($spec_quant) > 0) {
                while ($fetch_quant = mysqli_fetch_assoc($spec_quant)) {
                    $newiditem = $fetch_quant['name'];
                    $newquant = $fetch_quant['quantity'];
        
                    // Update product quantity in the database
                    mysqli_query($conn, "UPDATE products SET quant = quant - $newquant WHERE name = '$newiditem'");
        
                    // Check if the product quantity has reached zero
                    $product_check = mysqli_query($conn, "SELECT quant FROM products WHERE name = '$newiditem'") or die('query failed');
                    $product_data = mysqli_fetch_assoc($product_check);
                    if ($product_data['quant'] == 0) {
                        // Update stock status to 'Sold'
                        mysqli_query($conn, "UPDATE products SET stock = 'Sold' WHERE name = '$newiditem'") or die('query failed');
                    }
                }
            }
        
            // Clear the cart after updating quantities
            mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
        
            ?>
            
            <div class="box">

                <h1>Payment Unsuccessful
                    <p>--------------------------------------------------</p>
                    <p>You will be redirected to order page 4 seconds</p>
                    <?php echo "Order ID:", $temp_itemid; ?>
                </h1>
            </div>
            <?php
            mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$temp_itemid'") or die('query failed');
        }
        // echo $message;
        ?><br />
        <?php
        // echo "Order ID:", $temp_itemid; ?></br>
        <?php
        // echo "--------------------------------------------------";
        ?>
        

        <!-- <div class="loader"></div> -->
        <script>
            var timer = setTimeout(function () {
                window.location = 'orders.php'
            }, 4000);
        </script>
    </div>
</body>

</html>