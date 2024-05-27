<?php

include 'config.php';

?>
<?php
// fetch all order
$select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
// fetch order where status = received
$status_orders = mysqli_query($conn, "SELECT * FROM `orders` WHERE status = 1") or die('Query failed');
$stats_ord = mysqli_fetch_assoc($status_orders);
// detect if there is order in the database
if (mysqli_num_rows($select_orders) > 0) {
    // print out all the data
    while ($fetch_orders = mysqli_fetch_assoc($select_orders)) {
        if ($fetch_orders['status'] == 1) {
            // echo "this is to display item that has been reveiced with name: ";
            // echo $stats_ord['name'];
            ?>
            <div class="box">
                <p>Order received by the customer</p>
                <p> user id : <span>
                        <?php echo $fetch_orders['user_id']; ?>
                    </span> </p>
                <p> placed on : <span>
                        <?php echo $fetch_orders['placed_on']; ?>
                    </span> </p>
                <p> name : <span>
                        <?php echo $fetch_orders['name']; ?>
                    </span> </p>
                <p> number : <span>
                        <?php echo $fetch_orders['phone']; ?>
                    </span> </p>
                <p> email : <span>
                        <?php echo $fetch_orders['email']; ?>
                    </span> </p>
                <p> address : <span>
                        <?php echo $fetch_orders['address']; ?>
                    </span> </p>
                <p> total products : <span>
                        <?php echo $fetch_orders['total_products']; ?>
                    </span> </p>
                <p> total price : <span>RM
                        <?php echo $fetch_orders['total_price']; ?>
                    </span> </p>               
                <p>
                    Tracking number: <span>
                        <?php echo $fetch_orders['tracknum']; ?>
                    </span>
                </p>

            </div>
            <?php
        } else {
            // to display all order makde by the customer
            ?>
            <div class="box">
                <p> user id : <span>
                        <?php echo $fetch_orders['user_id']; ?>
                    </span> </p>
                <p> placed on : <span>
                        <?php echo $fetch_orders['placed_on']; ?>
                    </span> </p>
                <p> name : <span>
                        <?php echo $fetch_orders['name']; ?>
                    </span> </p>
                <p> number : <span>
                        <?php echo $fetch_orders['phone']; ?>
                    </span> </p>
                <p> email : <span>
                        <?php echo $fetch_orders['email']; ?>
                    </span> </p>
                <p> address : <span>
                        <?php echo $fetch_orders['address']; ?>
                    </span> </p>
                <p> total products : <span>
                        <?php echo $fetch_orders['total_products']; ?>
                    </span> </p>
                <p> total price : <span>RM
                        <?php echo $fetch_orders['total_price']; ?>
                    </span> </p>
                <form action="" method="post">
                    <input type="hidden" name="order_id" value="<?php echo $fetch_orders['id']; ?>">
                    <select name="update_payment">
                        <option value="" selected disabled>
                            <?php echo $fetch_orders['payment_status']; ?>
                        </option>
                        <option value="pending">pending</option>
                        <option value="Accepted">Accepted</option>
                    </select>
                    <input type="text" name="track-order" placeholder="Tracking number"
                        value="<?php echo $fetch_orders['tracknum']; ?>">
                    <input type="submit" value="Update track" name="update_order" class="option-btn">
                    <a href="admin_order.php?delete=<?php echo $fetch_orders['id']; ?>"
                        onclick="return confirm('Cancel this order?');" class="delete-btn">Cancel order</a>
                </form>

            </div>
            <?php
        }
    }
} else {
    echo '<p class="empty">no orders placed yet!</p>';
}
?>