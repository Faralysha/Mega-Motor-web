<?php
include 'config.php';
session_start();

$admin_id = $_SESSION['admin_id'];
if (!isset($admin_id)) {
    header('location:admin_login.php');
    exit;
}

function handleImageUpload($file) {
    if ($file['error'] == UPLOAD_ERR_OK) {
        $image_name = basename($file['name']);
        $image_size = $file['size'];
        $image_temp = $file['tmp_name'];
        $image_ext = strtolower(pathinfo($image_name, PATHINFO_EXTENSION));
        $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif'];

        if (!in_array($image_ext, $allowed_extensions)) {
            return ['error' => 'Invalid image format.'];
        }

        if ($image_size > 2000000) {
            return ['error' => 'Image size is too large.'];
        }

        $upload_dir = 'uploaded_img/';
        $upload_path = $upload_dir . $image_name;

        if (move_uploaded_file($image_temp, $upload_path)) {
            return ['success' => $image_name];
        } else {
            return ['error' => 'Image upload failed.'];
        }
    }

    return ['error' => 'No image uploaded.'];
}

function generateSerialNumber($product_id, $size) {
    global $conn;
    $query = "SELECT MAX(serial_number) AS max_serial FROM product_details WHERE product_id = ? AND size = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("is", $product_id, $size);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $max_serial = $row['max_serial'];

    preg_match('/-(\d+)-/', $max_serial, $matches);
    $counter = isset($matches[1]) ? intval($matches[1]) : 0;

    $counter++;
    $new_serial_number = sprintf('%d-%05d-%s', $product_id, $counter, $size);

    return $new_serial_number;
}

if (isset($_POST['add_product'])) {
    if (empty($_POST['name'])) {
        echo "Product name cannot be empty.";
        exit;
    }

    $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
    $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
    $brand = filter_var($_POST['brand'], FILTER_SANITIZE_STRING);
    $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
    $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);

    $image_upload = handleImageUpload($_FILES['image']);
    if (isset($image_upload['error'])) {
        echo $image_upload['error'];
        exit;
    } else {
        $image_path = $image_upload['success'];
    }

    $sizes = $_POST['sizes'];
    $quantities = $_POST['quantities'];
    $total_quantity = array_sum($quantities);

    $conn->begin_transaction();

    $stmt = $conn->prepare("INSERT INTO `products` (name, category, brand, price, image, quant) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $name, $category, $brand, $price, $image_path, $total_quantity);
    $stmt->execute();
    $product_id = $stmt->insert_id;
    $stmt->close();

    $stmt_sizes = $conn->prepare("INSERT INTO `product_sizes` (product_id, size, quantity) VALUES (?, ?, ?)");
    $stmt_sizes->bind_param("iss", $product_id, $size, $quantity);
    for ($i = 0; $i < count($sizes); $i++) {
        $size = $sizes[$i];
        $quantity = $quantities[$i];
        $stmt_sizes->execute();

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

if (isset($_GET['delete'])) {
    $delete_id = $_GET['delete'];

    $conn->begin_transaction();

    $stmt = $conn->prepare("DELETE FROM `product_details` WHERE product_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM `product_sizes` WHERE product_id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();
    $stmt->close();

    $stmt = $conn->prepare("DELETE FROM `products` WHERE id = ?");
    $stmt->bind_param("i", $delete_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        $message[] = 'Product deleted successfully!';
    } else {
        $message[] = 'Product could not be deleted!';
    }
    $stmt->close();
    $conn->commit();

    header('Location: admin_products.php');
    exit;
}

if (isset($_POST['update_product'])) {
    $update_p_id = $_POST['update_p_id'];
    $update_name = filter_var($_POST['update_name'], FILTER_SANITIZE_STRING);
    $update_price = filter_var($_POST['update_price'], FILTER_VALIDATE_FLOAT);

    $sizes = $_POST['sizes'];
    $quantities = $_POST['quantities'];
    $total_quantity = array_sum($quantities);

    $conn->begin_transaction();

    $stmt = $conn->prepare("UPDATE `products` SET name = ?, price = ?, quant = ? WHERE id = ?");
    $stmt->bind_param("sdii", $update_name, $update_price, $total_quantity, $update_p_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM `product_sizes` WHERE product_id = ?");
    $stmt->bind_param("i", $update_p_id);
    $stmt->execute();

    $stmt = $conn->prepare("DELETE FROM `product_details` WHERE product_id = ?");
    $stmt->bind_param("i", $update_p_id);
    $stmt->execute();

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
           width: 80%; /* Adjust width as needed */
           margin: 0 auto; /* Center the table */
           border-collapse: collapse;
           border: 1px solid var(--black); /* Black thin border */
        }

        .product-table th,
        .product-table td {
           padding: 1rem; /* Add padding to table cells */
           border: 1px solid var(--black); /* Black thin border for each cell */
           background-color: var(--white); /* White background for cells */
        }

        .product-table th {
           background-color: #ccc; /* Grey header */
           text-align: left;
        }

        .product-table tr:nth-child(even) {
           background-color: #f5f5f5; /* Set background color for even rows */
        }

        .product-table img {
           max-width: 100px;
           max-height: 100px;
        }

        .product-table .btn-container {
           display: flex;
           justify-content: space-between;
        }
    </style>
</head>

<body>

    <?php include 'admin_header.php'; ?>
    <!-- ----------------------------------------------------------------------------------------- -->
    <!--  -->

    <!-- product CRUD section starts  -->
    <!-- Display item from database -->
    <section class="add-products">

        <h1 class="title">shop products</h1>

        <form action="" method="post" enctype="multipart/form-data">
            <h3>add product</h3>
            <input type="text" name="name" class="box" placeholder="enter product name" required>
            
            <select hidden name="category" class="box">
               <option value="helemets&visor">HELMETS & VISORS</option>
               <option value="riding&gears">RIDING GEARS</option>
               <option value="brakesystem">BRAKE SYSTEM</option>
               <option value="shocks&suspension">SHOCKS & SUSPENSIONS</option>
               <option value="tires">TIRES</option>
               <option value="exhaust">EXHAUST</option>
               <option value="racking">RACKING</option>
               <option value="others">OTHERS</option>
            </select>

            <input type="text" name="brand" class="box" placeholder="enter product brand" required>

            <input type="number" min="0" step="0.01" name="price" id="price" class="box" placeholder="Enter product price" required>
        
            <textarea name="description" id="description" class="box" rows="4" placeholder="Enter product description" required></textarea>
        
            <input type="file" name="image" id="image" class="box" accept="image/*" required>

            <div id="sizeQuantityFields"></div>

            <input type="text" name="sizes[]" class="box" placeholder="Enter product size" required>
                
            <input type="number" min="1" name="quantities[]" class="box" placeholder="Enter product quantity" required>

            <button type="button" id="addSizeQuantity" class="btn">Add Size & Quantity</button>

        <button type="submit" name="add_product" class="btn">Add Product</button>
    </form>

    </section>

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
        $stmt->bind_param("i", $fetch_products['id']); // Use "i" for integer parameter
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

        echo "<td>RM {$fetch_products['price']}</td>";
        echo "<td><a href=\"#\" onclick=\"openImagePopup('uploaded_img/{$fetch_products['image']}')\">uploaded_img/{$fetch_products['image']}</a></td>";
        echo "<td class=\"btn-container\">";
        echo "<a href=\"admin_products.php?update={$fetch_products['id']}\" class=\"option-btn small-btn\">Update</a>";
        echo "<a href=\"admin_products.php?delete={$fetch_products['id']}\" class=\"delete-btn small-btn\" onclick=\"return confirm('Delete this product?');\">Delete</a>";
        echo "</td>";
        echo "</tr>";
    }
} else {
    echo '<tr><td colspan="8">No products added yet!</td></tr>';
}
?>
            </tbody>
        </table>
    </section>


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
               <input type="text" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Update new product price">
               <label for="update_quant">Quantity:</label>
               <input type="number" name="update_quant" value="<?php echo $fetch_update['quant']; ?>" min="0" class="box" required placeholder="Update new product quantity">
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



    <!-- custom admin js file link  -->
    <script src="js/admin_script.js"></script>
    <script>
    function openImagePopup(imageUrl) {
        // You can implement your logic for displaying the image popup here
        alert("Image URL: " + imageUrl);
    }

    document.addEventListener('DOMContentLoaded', function() {
    console.log("DOM Loaded");
    document.getElementById('addSizeQuantity').addEventListener('click', function() {
        console.log("Add Size & Quantity button clicked");
        var sizeQuantityContainer = document.getElementById('sizeQuantityFields');
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
