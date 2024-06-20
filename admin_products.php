<?php
include 'config.php';

session_start();

if (!isset($_SESSION['admin_id'])) {
    header('Location: admin_login.php');
    exit();
}

function handleImageUpload($file) {
    if ($file['error'] == UPLOAD_ERR_OK) {
        $image_name = basename($file['name']);
        $image_size = $file['size'];

        if ($image_size > 2000000) {
            return ['error' => 'Image size is too large.'];
        }

        return ['success' => $image_name];
    }

    return ['error' => 'No image uploaded.'];
}

// Function to generate a unique serial number
function generateSerialNumber($product_id, $size) {
    global $conn;
    $query = "SELECT MAX(serial_number) AS max_serial FROM product_details WHERE product_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $max_serial = $row['max_serial'];

    preg_match('/-(\d+)-/', $max_serial, $matches);
    $counter = isset($matches[1]) ? intval($matches[1]) : 0;

    $counter++;
    $new_serial_number = sprintf('%d-%d-%s', $product_id, $counter, $size);

    return $new_serial_number;
}


// Add product to the database
if (isset($_POST['add_product'])) {
    if (empty($_POST['name'])) {
        echo "Product name cannot be empty.";
        exit;
    }

    // Sanitize other fields as needed
    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $brand = filter_var($_POST['brand'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

    // Handle image upload
    $image_upload = handleImageUpload($_FILES['image']);
    if (isset($image_upload['error'])) {
        echo $image_upload['error'];
        exit;
    } else {
        $image_path = $image_upload['success'];
    }

    // Calculate total quantity
    $sizes = $_POST['sizes'];
    $quantities = $_POST['quantities'];
    $total_quantity = array_sum($quantities);

    // Begin transaction
    $conn->begin_transaction();

    // Insert product into the main products table
    $stmt = $conn->prepare("INSERT INTO `products` (name, category, brand, price, image, quant) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $category, $brand, $price, $image_path, $total_quantity);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    // Insert sizes and quantities into the product_sizes table
    $stmt_sizes = $conn->prepare("INSERT INTO `product_sizes` (product_id, size, quantity) VALUES (?, ?, ?)");
    $stmt_sizes->bind_param("iss", $product_id, $size, $quantity);
    for ($i = 0; $i < count($sizes); $i++) {
        $size = $sizes[$i];
        $quantity = $quantities[$i];
        $stmt_sizes->execute();
    
    // Generate and insert serial numbers into the product_details table
    for ($j = 1; $j <= $quantity; $j++) {
        $serial_number = generateSerialNumber($product_id, $size);
        $stock = 'Available';

        $stmt_details = $conn->prepare("INSERT INTO `product_details` (product_id, serial_number, size, stock) VALUES (?, ?, ?, ?)");
        $stmt_details->bind_param("isss", $product_id, $serial_number, $size, $stock);
        $stmt_details->execute();
        $stmt_details->close();
    }
}
$stmt_sizes->close();
$conn->commit();
}

// Delete product
if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    // Begin transaction
    $conn->begin_transaction();

    // Delete related product details
    $stmt = $conn->prepare("DELETE FROM `product_details` WHERE product_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Delete related product sizes
    $stmt = $conn->prepare("DELETE FROM `product_sizes` WHERE product_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    // Delete the product
    $stmt = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message[] = 'Product deleted successfully!';
    } else {
        $message[] = 'Product could not be deleted!';
    }
    $stmt->close();

    // Commit transaction
    $conn->commit();

    header('Location: admin_products.php');
    exit;
}

// Update product
if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = filter_var($_POST['update_name'], FILTER_SANITIZE_STRING);
    $update_price = filter_var($_POST['update_price'], FILTER_VALIDATE_FLOAT);

    // Calculate total quantity
    $sizes = $_POST['sizes'];
    $quantities = $_POST['quantities'];
    $total_quantity = array_sum($quantities);

    // Begin transaction
    $conn->begin_transaction();

    // Update product information
    $stmt = $conn->prepare("UPDATE `products` SET name = ?, price = ?, quant = ? WHERE id = ?");
    $stmt->bind_param("sdii", $update_name, $update_price, $total_quantity, $update_p_id);
    $stmt->execute();

    // Delete existing sizes and quantities for the product
    $stmt = $conn->prepare("DELETE FROM `product_sizes` WHERE product_id = ?");
    $stmt->bind_param("i", $update_p_id);
    $stmt->execute();

    // Delete existing product details for the product
    $stmt = $conn->prepare("DELETE FROM `product_details` WHERE product_id = ?");
    $stmt->bind_param("i", $update_p_id);
    $stmt->execute();

    // Insert updated sizes and quantities into the product_sizes table
    $stmt = $conn->prepare("INSERT INTO `product_sizes` (product_id, size, quantity) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $update_p_id, $size, $quantity);
    for ($i = 0; $i < count($sizes); $i++) {
        $size = $sizes[$i];
        $quantity = $quantities[$i];
        $stmt->execute();

        for ($j = 1; $j <= $quantity; $j++) {
            $serial_number = generateSerialNumber($update_p_id, $size);
            $stock = 'Available';

            $stmt_details = $conn->prepare("INSERT INTO `product_details` (product_id, serial_number, size, stock) VALUES (?, ?, ?, ?)");
            $stmt_details->bind_param("isss", $update_p_id, $serial_number, $size, $stock);
            $stmt_details->execute();
        }
    }
    $stmt_details->close();


    // Handle image update
    if (!empty($_FILES['update_image']['name'])) {
        $update_image_upload = handleImageUpload($_FILES['update_image']);

        if (isset($update_image_upload['error'])) {
            $message[] = $update_image_upload['error'];
        } else {
            $update_image_path = $update_image_upload['success'];
            $update_old_image = $_POST['update_old_image'];

            $stmt = $conn->prepare("UPDATE `products` SET image = ? WHERE id = ?");
            $stmt->bind_param("si", $update_image_path, $update_p_id);
            $stmt->execute();

            if ($stmt->affected_rows > 0) {
                $old_image_path = 'uploaded_img/' . $update_old_image;
                if (file_exists($old_image_path)) {
                    unlink($old_image_path);
                }
                $message[] = 'Product updated successfully!';
            } else {
                $message[] = 'Product could not be updated!';
            }
        }
    } else {
        if ($stmt->affected_rows > 0) {
            $message[] = 'Product updated successfully!';
        } else {
            $message[] = 'Product could not be updated!';
        }
    }

    $conn->commit();
    $stmt->close();
    header('Location: admin_products.php');
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>admin_products</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- custom admin css file link  -->
    <link rel="stylesheet" href="css/admin_style.css">
    <style>
    textarea {
        border: 1px solid black;
        border-radius: 6px;
        width: 100%;
        resize: none;
        padding: 9px;
    }

    .product-table {
    width: 80%;
    margin: 0 auto;
    border-collapse: collapse;
    border: 1px solid var(--black);
    font-size: 16px; /* Increase font size */
}

.product-table th,
.product-table td {
    padding: 1rem;
    border: 1px solid var(--black);
    background-color: var(--white);
    text-align: left;
}

.product-table th {
    background-color: #ccc;
}

.product-table tr:nth-child(even) {
    background-color: #f5f5f5;
}

.product-table img {
    max-width: 100px;
    max-height: 100px;
}

.product-table .btn-container {
    display: flex;
    justify-content: space-between;
}

/* Add more size button for edit product form */
.edit-product-form .add-size {
   background-color: #dc3545; /* Crimson color */
   border: none;
   color: white;
   padding: 10px 20px;
   text-align: center;
   text-decoration: none;
   display: inline-block;
   font-size: 16px;
   margin: 10px 2px;
   cursor: pointer;
   border-radius: 5px;
   transition: background-color 0.3s ease;
}

.edit-product-form .add-size:hover {
   background-color: #c82333; /* Darker red */
}


/* Responsive styles */
@media only screen and (max-width: 768px) {
    .product-table {
        font-size: 14px; /* Decrease font size for smaller screens */
    }
}

@media only screen and (max-width: 576px) {
    .product-table th,
    .product-table td {
        padding: 0.5rem; /* Decrease padding for smaller screens */
    }
}
    </style>
</head>

<body>

<?php
include 'admin_header.php';
?>

    <!-- Add Product Form -->
<section class="add-products">
    <h1 class="title">shop products</h1>
    <form action="" method="post" enctype="multipart/form-data">
        <h3>add product</h3>
        <input type="text" name="name" class="box" placeholder="enter product name" required>
        
        <select hidden name="category" class="box">
           <option value="HELMETS & VISORS">HELMETS & VISORS</option>
           <option value="RIDING & GEARS">RIDING & GEARS</option>
           <option value="BRAKE SYSTEM">BRAKE SYSTEM</option>
           <option value="SHOCKS & SUSPENSIONS">SHOCKS & SUSPENSIONS</option>
           <option value="TIRES">TIRES</option>
           <option value="EXHAUST">EXHAUST</option>
           <option value="RACKING">RACKING</option>
           <option value="BOXES">BOXES</option>
           <option value="OTHERS">OTHERS</option>
        </select>

        <input type="text" name="brand" class="box" placeholder="enter product brand" required>

        <input type="number" min="0" step="0.01" name="price" id="price" class="box" placeholder="Enter product price" required>
    
        <textarea name="description" id="description" class="box" rows="4" placeholder="Enter product description" required></textarea>
    
        <input type="file" name="image" id="image" class="box" accept="image/*" required>

        <div id="sizeQuantityFields">
            <div class="size-quantity-field">
                <input type="text" name="sizes[]" class="box" placeholder="Enter product size" required>
                <input type="number" min="1" name="quantities[]" class="box" placeholder="Enter product quantity" required>
            </div>
        </div>

        <button type="button" id="addSizeQuantity" class="btn">Add Size & Quantity</button>
        <button type="submit" name="add_product" class="btn">Add Product</button>
    </form>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.getElementById('addSizeQuantity').addEventListener('click', function() {
            var sizeQuantityContainer = document.getElementById('sizeQuantityFields');
            var sizeQuantityField = document.createElement('div');
            sizeQuantityField.classList.add('size-quantity-field');

            var sizeInput = document.createElement('input');
            sizeInput.type = 'text';
            sizeInput.name = 'sizes[]';
            sizeInput.classList.add('box');
            sizeInput.placeholder = 'Enter product size';
            sizeInput.required = true;

            var quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.min = '1';
            quantityInput.name = 'quantities[]';
            quantityInput.classList.add('box');
            quantityInput.placeholder = 'Enter product quantity';
            quantityInput.required = true;

            sizeQuantityField.appendChild(sizeInput);
            sizeQuantityField.appendChild(quantityInput);
            sizeQuantityContainer.appendChild(sizeQuantityField);
        });
    });
</script>


    <!-- product CRUD section ends -->

    <!-- show products  -->

    <section class="show-products">

    <table class="product-table">
    <thead>
        <tr>
            <th>Name</th>
            <th>Category</th>
            <th>Brand</th>
            <th>Size</th>
            <th>Quantity</th>
            <th>Available</th>
            <th>Price</th>
            <th>Image URL</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php  
        $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('Query failed');
        if(mysqli_num_rows($select_products) > 0){
            while($fetch_products = mysqli_fetch_assoc($select_products)){
                echo "<tr>";
                echo "<td>{$fetch_products['name']}</td>";
                echo "<td>{$fetch_products['category']}</td>";
                echo "<td>{$fetch_products['brand']}</td>";

                // Retrieve sizes and quantities for the current product
                $stmt = $conn->prepare("SELECT size, quantity FROM `product_sizes` WHERE product_id = ?");
                $stmt->bind_param("i", $fetch_products['id']);
                $stmt->execute();
                $result = $stmt->get_result();
                $sizes_quantities = $result->fetch_all(MYSQLI_ASSOC);
                $stmt->close();
                
                // Display sizes and quantities
                echo "<td>";
                foreach ($sizes_quantities as $size_quantity) {
                    echo $size_quantity['size'] . "<br>";
                }
                echo "</td>";
                
                echo "<td>";
                foreach ($sizes_quantities as $size_quantity) {
                    echo $size_quantity['quantity'] . "<br>";
                }
                echo "</td>";

                // Retrieve and display the available quantities for each size
                echo "<td>";
                foreach ($sizes_quantities as $size_quantity) {
                    $size = $size_quantity['size'];
                    $quantity = $size_quantity['quantity'];

                    $stmt = $conn->prepare("SELECT COUNT(*) AS available FROM `product_details` WHERE product_id = ? AND size = ? AND stock = 'Available'");
                    $stmt->bind_param("is", $fetch_products['id'], $size);
                    $stmt->execute();
                    $result = $stmt->get_result();
                    $available = $result->fetch_assoc()['available'];
                    $stmt->close();

                    echo $available . "<br>";
                }
                echo "</td>";

                echo "<td>RM {$fetch_products['price']}</td>";
                echo "<td><a href=\"#\" onclick=\"openImagePopup('uploaded_img/{$fetch_products['image']}')\">uploaded_img/{$fetch_products['image']}</a></td>";
                echo "<td class=\"btn-container\">";
                echo "<a href=\"admin_products.php?update={$fetch_products['id']}\" class=\"option-btn small-btn\">Update</a>";
                echo "<a href=\"admin_products.php?delete={$fetch_products['id']}\" class=\"delete-btn small-btn\" onclick=\"return confirm('Delete this product?');\">Delete</a>";
                echo "</td>";
                echo "</tr>";
            }
        } else {
            echo '<tr><td colspan="9">No products added yet!</td></tr>';
        }
        ?>
    </tbody>
</table>

    </section>


    <!-- Edit Product Form -->
<section class="edit-product-form">
    <?php
    if (isset($_GET['update'])) {
        $update_id = $_GET['update'];
        $update_query = mysqli_query($conn, "SELECT * FROM `products` WHERE id = '$update_id'") or die('query failed');
        if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
    ?>
                <form action="" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['id']; ?>">
                    <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                    <div class="set-image">
                        <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                    </div>
                    <div class="set-form">
                        <label for="update_name">Name:</label>
                        <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Update new product name">

                        <label for="update_price">Price:</label>
                        <input type="number" min="0" step="0.01" name="update_price" value="<?php echo $fetch_update['price']; ?>" class="box" required placeholder="Update new product price">

                        <div class="size-quantity">
                            <?php
                            // Retrieve existing sizes and quantities for the product
                            $stmt_sizes = $conn->prepare("SELECT size, quantity FROM `product_sizes` WHERE product_id = ?");
                            $stmt_sizes->bind_param("i", $fetch_update['id']);
                            $stmt_sizes->execute();
                            $result_sizes = $stmt_sizes->get_result();

                            while ($row_sizes = $result_sizes->fetch_assoc()) {
                                $size = $row_sizes['size'];
                                $quantity = $row_sizes['quantity'];
                            ?>
                                <div class="size-quantity-field">
                                    <input type="text" name="sizes[]" value="<?php echo $size; ?>" class="box size-field" required placeholder="Enter product size">
                                    <input type="number" min="0" name="quantities[]" value="<?php echo $quantity; ?>" class="box quantity-field" required placeholder="Enter product quantity">
                                </div>
                            <?php } ?>
                        </div>

                        <button type="button" class="add-size">Add More Size</button>

                        <div class="button-container">
                            <input type="submit" value="Update" name="update_product" class="option-btn"> <!-- Added name attribute -->
                            <input type="reset" value="Cancel" id="close-update" class="option-btn">
                        </div>
                    </div>
                </form>
    <?php
            }
        }
    } else {
        echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
    }
    ?>
</section>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelector('.add-size').addEventListener('click', function() {
            var sizeQuantityContainer = document.querySelector('.size-quantity');
            var sizeQuantityField = document.createElement('div');
            sizeQuantityField.classList.add('size-quantity-field');

            var sizeInput = document.createElement('input');
            sizeInput.type = 'text';
            sizeInput.name = 'sizes[]';
            sizeInput.classList.add('box', 'size-field');
            sizeInput.placeholder = 'Enter product size';
            sizeInput.required = true;

            var quantityInput = document.createElement('input');
            quantityInput.type = 'number';
            quantityInput.min = '0';
            quantityInput.name = 'quantities[]';
            quantityInput.classList.add('box', 'quantity-field');
            quantityInput.placeholder = 'Enter product quantity';
            quantityInput.required = true;

            sizeQuantityField.appendChild(sizeInput);
            sizeQuantityField.appendChild(quantityInput);
            sizeQuantityContainer.appendChild(sizeQuantityField);
        });
    });
</script>


</body>

</html>
