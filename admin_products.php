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

       if ($image_size > 2000000) {
           return ['error' => 'Image size is too large.'];
       }

       return ['success' => $image_name];
      }

   return ['error' => 'No image uploaded.'];
}

// Add product to the database
if (isset($_POST['add_product'])) {
   // Ensure that 'name' field is not empty
   if(empty($_POST['name'])) {
      // Handle the error, for example:
      echo "Product name cannot be empty.";
      // You can also redirect back to the form page or display an error message
      exit; // Stop execution
   }

   // Sanitize other fields as needed
   $name = filter_var($_POST['name'], FILTER_SANITIZE_STRING);
   $category = filter_var($_POST['category'], FILTER_SANITIZE_STRING);
   $brand = filter_var($_POST['brand'], FILTER_SANITIZE_STRING);
   $price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
   $description = filter_var($_POST['description'], FILTER_SANITIZE_STRING);
   
   // Execute the statement to insert the product
    $stmt->execute();
    $product_id = $stmt->insert_id; // Get the ID of the inserted product
    $stmt->close(); 
   
   // Insert product into the main products table
$stmt = $conn->prepare("INSERT INTO `products` (name, category, brand, price, image) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("sssss", $name, $category, $brand, $price, $image_path);

// Assign values from $_POST to variables
$name = $_POST['name'];
$category = $_POST['category']; // Assuming this is properly set in the form
$brand = $_POST['brand']; // Assuming this is properly set in the form
$price = $_POST['price']; // Assuming this is properly set in the form
$description = $_POST['description']; // Assuming this is properly set in the form

// Handle image upload
$image_upload = handleImageUpload($_FILES['image']);
if (isset($image_upload['error'])) {
    // Handle the error
    echo $image_upload['error'];
    exit; // Stop execution
} else {
    $image_path = $image_upload['success'];
}

// Execute the statement to insert the product
$stmt->execute();
$product_id = $stmt->insert_id; // Get the ID of the inserted product
$stmt->close();

// Prepare the statement for inserting sizes and quantities into the product_sizes table
$stmt = $conn->prepare("INSERT INTO `product_sizes` (product_id, size, quantity) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $product_id, $size, $quantity);

// Loop through sizes and quantities and insert them into the database
for ($i = 0; $i < count($sizes); $i++) {
    $size = $sizes[$i];
    $quantity = $quantities[$i];
    $stmt->execute();
}
$stmt->close();

}

// Delete product
if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

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

   header('Location: admin_products.php');
   exit;
}

// Update product
if (isset($_POST['update_product'])) {
   $update_p_id = $_POST['update_p_id'];
   $update_name = filter_var($_POST['update_name'], FILTER_SANITIZE_STRING);
   $update_price = filter_var($_POST['update_price'], FILTER_VALIDATE_FLOAT);
   $update_quant = filter_var($_POST['update_quant'], FILTER_SANITIZE_NUMBER_INT);

   $stmt = $conn->prepare("UPDATE `products` SET name = ?, price = ?, quant = ? WHERE id = ?");
   $stmt->bind_param("sdii", $update_name, $update_price, $update_quant, $update_p_id);
   $stmt->execute();

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
               <option value="riding&gears">RIDING & GEARS</option>
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
                
            <input type="number" min="0" name="quantities[]" class="box" placeholder="Enter product quantity" required>

            <button type="button" id="addSizeQuantity" class="btn">Add Size & Quantity</button>

        <button type="submit" name="add_product" class="btn">Add Product</button>
    </form>

    </section>

    <!-- product CRUD section ends -->

    <!-- show products  -->

    <section class="show-products">
        <h1 class="title">Shop Products</h1>

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
