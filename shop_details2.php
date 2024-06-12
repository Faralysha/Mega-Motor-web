<?php
include 'config.php';
session_start();

$user_id = $_SESSION['user_id'];

if (!isset($user_id)) {
    header('location:index.php');
    exit;
}

$product_id = $_GET['id'] ?? null;

if (!$product_id) {
    die('Product ID is not set.');
}

// Function to fetch product details
function getProductDetails($conn, $product_id) {
    $stmt = $conn->prepare("SELECT * FROM `products` WHERE id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    $stmt->close();
    return $product;
}

// Function to fetch sizes and quantities
function getSizesQuantities($conn, $product_id) {
    $stmt = $conn->prepare("SELECT size, quantity FROM `product_sizes` WHERE product_id = ?");
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $sizes_quantities = $result->fetch_all(MYSQLI_ASSOC);
    $stmt->close();
    return $sizes_quantities;
}

// Function to handle adding to cart
function addToCart($conn, $user_id, $product_id, $product_detail_id, $product_brand, $product_name, $product_size, $product_price, $product_quantity, $product_image) {
    $stmt = $conn->prepare("INSERT INTO `cart` (user_id, product_id, product_detail_id, product_brand, product_name, product_size, price, quantity, image) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("iiisssisd", $user_id, $product_id, $product_detail_id, $product_brand, $product_name, $product_size, $product_price, $product_quantity, $product_image);
    $stmt->execute();
    $stmt->close();
}

// Fetch product details
$product_query = "SELECT * FROM products WHERE id = $product_id";
$product_result = $conn->query($product_query);

$product = $product_result->fetch_assoc();

// Fetch product sizes
$size_query = "SELECT size, stock FROM product_details WHERE product_id = $product_id";
$size_result = $conn->query($size_query);
$sizes = [];
while ($row = $size_result->fetch_assoc()) {
    $sizes[] = $row;
}

// Fetch sizes and quantities for the current product
$sizes_quantities = getSizesQuantities($conn, $product_id);

// Calculate total quantity
$total_quantity = array_sum(array_column($sizes_quantities, 'quantity'));

if (isset($_POST['add_to_cart'])) {
    $product_brand = $product['brand'];
    $product_name = $product['name'];
    $product_price = $product['price'];
    $product_size = $_POST['product_size'] ?? null;
    $product_image = $product['image'];
    $product_quantity = intval($_POST['product_quantity']);


    if (!$product_size) {
        $message[] = 'Please select a size.';
    } else {
        $stmt = $conn->prepare("SELECT product_detail_id FROM `product_details` WHERE product_id = ? AND size = ?");
        $stmt->bind_param("is", $product_id, $product_size);
        $stmt->execute();
        $result = $stmt->get_result();
        $product_detail_id_row = $result->fetch_assoc();
        $product_detail_id = $product_detail_id_row['product_detail_id'] ?? null;
        $stmt->close();

        if (!$product_detail_id) {
            $message[] = 'Product detail ID not found.';
        } else {
            $stmt = $conn->prepare("SELECT quantity FROM `product_sizes` WHERE product_id = ? AND size = ?");
            $stmt->bind_param("is", $product_id, $product_size);
            $stmt->execute();
            $result = $stmt->get_result();
            $size_stock = $result->fetch_assoc();
            $stmt->close();

            if ($size_stock['quantity'] <= 0 || $product_quantity > $size_stock['quantity']) {
                $message[] = 'Product out of stock';
            } else {
                addToCart($conn, $user_id, $product_id, $product_detail_id, $product_brand, $product_name, $product_size, $product_price, $product_quantity, $product_image);
                $message[] = 'Product added to cart';
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
    <title>Shop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="css/styleindex.css">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <style>
        
        * {
            padding: 0;
            margin: 0;
            box-sizing: border-box;
        }
        body {
            font-family: Arial, sans-serif;
            background: #C1908B;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 100%;
            margin: auto;
            background: white;
            box-shadow: 5px 5px 10px 3px rgba(0, 0, 0, 0.3);
            padding: 20px;
            border-radius: 5px;
        }
        .flex {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .main_image {
            width: 40rem;
            height: 40rem;
            position: relative;
            overflow: hidden;
            border-radius: 1px;
            box-shadow: 5px 5px 8px 3px rgba(0, 0, 0, 0.3);
        }
        .main_image .size_image {
            position: absolute;
            top: 0;
            left: 0;
            width: 30rem;
            height: 30rem;
        }
        .option img {
            width: 75px;
            height: 75px;
            margin: 10px;
            border-radius: 2px;
            border: 0.7px solid black;
        }
        .right {
            padding: 50px 100px 50px 50px;
        }

        .left, .right {
            width: 48%;
            margin-bottom: 20px;
        }
        .left img {
            width: 100%;
            border-radius: 5px;
        }
        .right {
            padding: 0 20px;
        }
        h1 {
            color: #af827d;
            font-size: 28px;
            margin-bottom: 10px;
        }
        p {
            color: #837D7C;
            font-size: 16px;
            margin-bottom: 10px;
        }
        label {
            color: #af827d;
            font-size: 18px;
            margin-bottom: 10px;
            display: block;
        }
        .option {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }
        .size-box {
            padding: 10px 15px;
            border: 1px solid #ddd;
            margin: 5px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            background-color: #fff;
        }
        .size-box.disabled {
            background-color: #f0f0f0;
            color: #999;
            cursor: not-allowed;
        }
        .size-box.selected {
            border-color: #C1908B;
            background-color: #C1908B;
            color: white;
        }
        .size-quantity-details {
            margin-top: 10px;
            font-size: 16px;
            color: #837D7C;
        }
        .add {
            display: flex;
            align-items: center;
            margin-top: 10px;
        }
        input[type="number"] {
            width: 100px;
            height: 40px;
            font-size: 16px;
            border: 1px solid #ddd;
            border-radius: 5px;
            padding: 0 10px;
            margin-right: 10px;
        }
        input[type="submit"] {
            border: none;
            background-color: #C1908B;
            color: white;
            font-size: 18px;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #af827d;
        }
        @media only screen and (max-width: 768px) {
            .left, .right {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <?php include 'header.php'; ?>

    <section>
        <button class="btn btn-primary btn-lg btn-go-back" onclick="history.back()">Go Back</button>
        <div class="container flex">
            <div class="left">
                <div class="main_image">
                    <div class="size_image">
                    <img class="image" src="uploaded_img/<?php echo htmlspecialchars($product['image']); ?>" alt="Product Image">
                    </div>
                </div>
            </div>
            <div class="right">
                <h1><?php echo htmlspecialchars($product['name']); ?></h1>
                <p><?php echo htmlspecialchars($product['description']); ?></p>
                <p>Category: <?php echo htmlspecialchars($product['category']); ?></p>
                <p>Brand: <?php echo htmlspecialchars($product['brand']); ?></p>
                <p>Price: RM <?php echo htmlspecialchars($product['price']); ?></p>

                <label for="product-size">Select Size:</label>
                <div class="option">
                    <?php foreach ($sizes as $size): ?>
                        <button class="size-box <?php echo $size['stock'] == 'Sold' ? 'out-of-stock' : ''; ?>" data-size="<?php echo htmlspecialchars($size['size']); ?>" <?php echo $size['stock'] == 'Sold' ? 'disabled' : ''; ?>>
                            <?php echo htmlspecialchars($size['size']); ?> <?php echo $size['stock'] == 'Sold' ? '(Sold Out)' : ''; ?>
                            </button>
                    <?php endforeach; ?>
                </div>

                <div class="size-quantity-details">
                    <h2>Selected Quantity</h2>
                    <p id="size-stock"></p>
                </div>
                
                <form action="" method="post" id="add-to-cart-form">
                    <div class="add">
                    <input type="number" min="1" name="product_quantity" placeholder="Enter Quantity" value="1" class="form-control form-control-lg">
                    <input type="hidden" name="product_id" value="<?php echo htmlspecialchars($product_id); ?>">
                    <input type="hidden" name="product_brand" value="<?php echo htmlspecialchars($product['brand']); ?>">
                    <input type="hidden" name="product_name" value="<?php echo htmlspecialchars($product['name']); ?>">
                    <input type="hidden" name="product_size" id="selected-size" value="">
                    <input type="hidden" name="product_price" value="<?php echo htmlspecialchars($product['price']); ?>">
                    <input type="hidden" name="product_image" value="<?php echo htmlspecialchars($product['image']); ?>">
                    </div>
                    <input type="submit" value="Add to cart" class="btn btn-outline-primary btn-lg" name="add_to_cart">
                </form>
            </div>
        </section>

<script>
$(document).ready(function() {
    $('#product-size').on('change', function() {
        var selectedSize = $(this).val();
        if (selectedSize) {
            $.ajax({
                url: 'shop_details.php',
                type: 'GET',
                data: {
                    product_id: <?php echo $product_id; ?>,
                    size: selectedSize
                },
                success: function(data) {
                    var sizeDetails = JSON.parse(data);
                    $('#selected-size-details').show();
                    $('#size-stock').text('Stock: ' + sizeDetails.stock);
                },
                error: function() {
                    alert('Error fetching size details.');
                }
            });
        } else {
            $('#selected-size-details').hide();
        }
    });
});
</script>

    <?php include 'footer.php'; ?>
</body>
</html>