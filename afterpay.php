<?php

include 'config.php';

session_start();
$status_idpayment = $_GET['status_id'];
$user_id = $_SESSION['user_id'];

$temp_itemid = $_SESSION['idname'];


?>
<!DOCTYPE html>
<html>

<head>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-image: url('css/images/loginback.jpg');
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
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            padding: 10px;
        }

        .loader {
            border: 10px solid #f3f3f3;
            border-top: 10px solid #3498db;
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
    </style>
</head>

<body>
    <div class="centerdiv">
        <?php
        if ($status_idpayment == 1) {
            // Debug information
            echo "Payment status is successful. Proceeding to update product quantities and clear the cart.<br>";

            $spec_quant = mysqli_query($conn, "SELECT * FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            if (mysqli_num_rows($spec_quant) > 0) {
                while ($fetch_quant = mysqli_fetch_assoc($spec_quant)) {
                    $newiditem = $fetch_quant['product_name'];
                    $newquant = $fetch_quant['quantity'];
                    mysqli_query($conn, "UPDATE products SET quant = quant - $newquant WHERE name = '$newiditem' ");
                }
            }

            // Debug information
            echo "Deleting cart items for user_id: $user_id<br>";

            $delete_cart = mysqli_query($conn, "DELETE FROM `cart` WHERE user_id = '$user_id'") or die('query failed');
            
            if ($delete_cart) {
                echo "Cart items deleted successfully.<br>";
            } else {
                echo "Failed to delete cart items.<br>";
            }
            ?>
            <div class="box">
                <h1>Payment Success</h1>
                <p>--------------------------------------------------</p>
                <p>You will be redirected to the order page in 4 seconds</p>
                <p><?php echo "Order ID:", $temp_itemid; ?></p>
            </div>
            <?php
        } else {
            ?>
            <div class="box">
                <h1>Payment Unsuccessful</h1>
                <p>--------------------------------------------------</p>
                <p>You will be redirected to the order page in 4 seconds</p>
                <p><?php echo "Order ID:", $temp_itemid; ?></p>
            </div>
            <?php
            mysqli_query($conn, "DELETE FROM `order_items` WHERE order_id = '$temp_itemid'") or die('query failed');
            mysqli_query($conn, "DELETE FROM `orders` WHERE id = '$temp_itemid'") or die('query failed');
        }
        ?>
        <button onclick="page()">order</button>
        <script>
            setTimeout(function () {
                window.location.href = 'orders.php';
            }, 4000);
            // function page(){
            //     window.location.href = 'orders.php';
            // }
        </script>
    </div>
</body>

</html>
