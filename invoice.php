<?php
include 'config.php';

// Fetch order details from the database based on the invoice number or order ID
// For example, if you're passing the invoice number as a parameter in the URL:
$invoice_number = $_GET['invoice_number'];
$order_query = mysqli_query($conn, "SELECT * FROM `orders` WHERE invoice_number = '$invoice_number'");
$order = mysqli_fetch_assoc($order_query);

// Check if the order exists
if (!$order) {
    echo "Order not found!";
    exit;
}

// Fetch additional order details as needed

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Invoice</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
    <!-- Your CSS styles here -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }

        .container {
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2,
        h3 {
            text-align: center;
        }

        p {
            margin: 5px 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
        }

        th {
            background-color: #f2f2f2;
            text-align: left;
        }

        .row {
            margin-top: 20px;
        }

        .text-right {
            text-align: right;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>ORDER INVOICE</h2>
        <div class="row">
            <div class="col-md-6">
                <p><strong>Customer Name:</strong> <?php echo $order['customer_name']; ?></p>
                <p><strong>Invoice Number:</strong> <?php echo $order['invoice_number']; ?></p>
                <!-- Add other order details here -->
            </div>
            <div class="col-md-6 text-right">
                <p><strong>Invoice Date:</strong> <?php echo date('d/m/Y', strtotime($order['invoice_date'])); ?></p>
                <!-- Add other order details here -->
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <h3>Order Details</h3>
                <table>
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Product</th>
                            <th>Variation</th>
                            <th>Net Product Price</th>
                            <th>Qty</th>
                            <!-- Add more table headers as needed -->
                        </tr>
                    </thead>
                    <tbody>
                        <!-- Loop through order items and display them in rows -->
                        <?php
                        // Example code to fetch and display order items
                        $order_id = $order['id'];
                        $order_items_query = mysqli_query($conn, "SELECT * FROM `order_items` WHERE order_id = '$order_id'");
                        $counter = 1;
                        while ($order_item = mysqli_fetch_assoc($order_items_query)) {
                            ?>
                            <tr>
                                <td><?php echo $counter; ?></td>
                                <td><?php echo $order_item['product_name']; ?></td>
                                <td><?php echo $order_item['variation']; ?></td>
                                <td><?php echo $order_item['net_product_price']; ?></td>
                                <td><?php echo $order_item['quantity']; ?></td>
                                <!-- Add more table cells for additional order item details -->
                            </tr>
                        <?php
                            $counter++;
                        }
                        ?>
                    </tbody>
                </table>
            </div>
        </div>
        <!-- Add more sections for totals, payment information, etc. -->
    </div>
</body>

</html>
