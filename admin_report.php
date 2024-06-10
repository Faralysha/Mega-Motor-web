<?php
include 'config.php';
$today_report = date('d-m-y');
$random = 0;

?>

<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">

<head>
    <title>Invoice</title>
    <style media="screen">
        body {
            font-family: 'Segoe UI', 'Microsoft Sans Serif', sans-serif;

        }

        /*
            These next two styles are apparently the modern way to clear a float. This allows the logo
            and the word "Invoice" to remain above the From and To sections. Inserting an empty div
            between them with clear:both also works but is bad style.
            Reference:
            http://stackoverflow.com/questions/490184/what-is-the-best-way-to-clear-the-css-style-float
        */
        header:before,
        header:after {
            content: " ";
            display: table;
        }

        header:after {
            clear: both;
        }

        .invoiceNbr {
            font-size: 40px;
            margin-right: 30px;
            margin-top: 30px;
            float: right;
        }

        .logo {
            float: left;
            border-radius: 50%;
        }

        #logoborder {
            border-radius: 20%;
            border: 1px solid black;
        }

        .from {
            float: left;
        }

        .to {
            float: right;
        }

        .fromto {
            border-style: solid;
            border-width: 1px;
            border-color: #e8e5e5;
            border-radius: 5px;
            margin: 20px;
            min-width: 200px;
        }

        .fromtocontent {
            margin: 10px;
            margin-right: 15px;
        }

        .panel {
            background-color: #e8e5e5;
            padding: 7px;
        }

        .items {
            clear: both;
            display: table;
            padding: 20px;

        }

        /* Factor out common styles for all of the "col-" classes.*/
        div[class^="col-"] {
            display: table-cell;
            padding: 7px;
        }

        /*for clarity name column styles by the percentage of width */
        .col-1-10 {
            width: 10%;
        }

        .col-1-52 {
            width: 52%;
        }

        .row {
            display: table-row;
            page-break-inside: avoid;
        }
    </style>

    <!-- These styles are exactly like the screen styles except they use points (pt) as units
        of measure instead of pixels (px) -->
    <style media="print">
        body {
            font-family: 'Segoe UI', 'Microsoft Sans Serif', sans-serif;
        }

        header:before,
        header:after {
            content: " ";
            display: table;
        }

        header:after {
            clear: both;
        }

        .invoiceNbr {
            font-size: 30pt;
            margin-right: 30pt;
            margin-top: 30pt;
            float: right;
        }

        .logo {
            float: left;
            border-radius: 50%;
        }

        #logoborder {
            border-radius: 50%;
        }

        .from {
            float: left;
        }

        .to {
            float: right;
        }

        .fromto {
            border-style: solid;
            border-width: 1pt;
            border-color: #e8e5e5;
            border-radius: 5pt;
            margin: 20pt;
            min-width: 200pt;
        }

        .fromtocontent {
            margin: 10pt;
            margin-right: 15pt;
        }

        .panel {
            background-color: #e8e5e5;
            padding: 7pt;
        }

        .items {
            clear: both;
            display: table;
            padding: 20pt;

        }

        div[class^="col-"] {
            display: table-cell;
            padding: 7pt;
        }

        .col-1-10 {
            width: 10%;
        }

        .col-1-52 {
            width: 52%;
        }

        .row {
            display: table-row;
            page-break-inside: avoid;
        }
    </style>

        <style>
            .bigger-box{
                padding-left: 80px;
                padding-right: 80px;
                padding-bottom: 100px;
            }
        </style>
</head>

<body>
    <!-- SQL Query -->
    <div class="box">
        <?php
        $total_pendings = 0;
        $total_countpending = 0;
        $select_pending = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'pending'") or die('query failed');
        if (mysqli_num_rows($select_pending) > 0) {
            while ($fetch_pendings = mysqli_fetch_assoc($select_pending)) {
                $total_price = $fetch_pendings['total_price'];
                $total_pendings += $total_price;
                $total_countpending = $total_countpending + 1;
            }

        }
        ?>
    </div>

    <div class="box">
        <?php
        $total_completed = 0;
        $total_countcompleted = 0;
        $select_completed = mysqli_query($conn, "SELECT total_price FROM `orders` WHERE payment_status = 'Accepted'") or die('query failed');
        if (mysqli_num_rows($select_completed) > 0) {
            while ($fetch_completed = mysqli_fetch_assoc($select_completed)) {
                $total_price = $fetch_completed['total_price'];
                $total_completed += $total_price;
                $total_countcompleted = $total_countcompleted + 1;
            }
            ;
        }
        ;
        ?>

    </div>

    <div class="box">
        <?php
        $select_orders = mysqli_query($conn, "SELECT * FROM `orders`") or die('query failed');
        $number_of_orders = mysqli_num_rows($select_orders);
        ?>

    </div>

    <div class="box">
        <?php
        $select_products = mysqli_query($conn, "SELECT * FROM `products`") or die('query failed');
        $number_of_products = mysqli_num_rows($select_products);
        ?>

    </div>

    <div class="box">
        <?php
        $select_users = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'user'") or die('query failed');
        $number_of_users = mysqli_num_rows($select_users);
        ?>

    </div>

    <div class="box">
        <?php
        $select_admins = mysqli_query($conn, "SELECT * FROM `users` WHERE user_type = 'admin'") or die('query failed');
        $number_of_admins = mysqli_num_rows($select_admins);
        ?>

    </div>

    <div class="box">
        <?php
        $select_account = mysqli_query($conn, "SELECT * FROM `users`") or die('query failed');
        $number_of_account = mysqli_num_rows($select_account);
        ?>

    </div>

    <div class="box">
        <?php
        $select_messages = mysqli_query($conn, "SELECT * FROM `message`") or die('query failed');
        $number_of_messages = mysqli_num_rows($select_messages);
        ?>

    </div>
    <!-- ------------------------------------------------------------------------------------- -->
    <!-- Report format -->
    <div class="bigger-box">

        <header>
            <div class="logo">
                <img src="images/logo.JPG" alt=" business logo" height="181" width="167" id="logoborder" />
            </div>
            <div class="invoiceNbr">
                <?php $random = rand(1, 10); ?>
                Invoice
                <?php echo $random ?>
                <br />
                <?php echo $today_report ?>
            </div>
        </header>

        <div class="fromto from">
            <div class="panel">FROM:</div>
            <div class="fromtocontent">
                <span>Deezek.co</span><br />
                <span>UTHM</span><br />
                <span>Parit Raja, Johor</span><br />
            </div>
        </div>
        <div class="fromto to">
            <div class="panel">TO:</div>
            <div class="fromtocontent">
                <span>Dinie Zikry[Deezekco]</span><br />
                <span>Parit Kassan</span><br />
                <span>Bukit Gambir, Johor</span>
            </div>
        </div>

        <section class="items">

            <!-- your favorite templating/data-binding library would come in handy here to generate these rows dynamically !-->
            <div class="row">
                <div class="col-1-10 panel">
                    Date
                </div>
                <div class="col-1-52 panel">
                    Description
                </div>
                <div class="col-1-10 panel">
                    Units
                </div>
                <div class="col-1-10 panel">
                    Rate
                </div>
                <div class="col-1-10 panel">
                    Sub Total
                </div>
            </div>

            <div class="row">
                <div class="col-1-10">
                    <?php echo $today_report ?>
                </div>
                <div class="col-1-52">
                    Total Pending Order
                </div>
                <div class="col-1-10">
                    <?php echo $total_countpending ?>
                </div>
                <div class="col-1-10">
                    -
                </div>
                <div class="col-1-10">
                    RM
                    <?php echo $total_pendings ?>
                </div>
            </div>
            <div class="row">
                <div class="col-1-10">
                    <?php echo $today_report ?>
                </div>
                <div class="col-1-52">
                    Total Completed Payments
                </div>
                <div class="col-1-10">
                    <?php echo $total_countcompleted ?>
                </div>
                <div class="col-1-10">
                    -
                </div>
                <div class="col-1-10">
                    RM
                    <?php echo $total_completed ?>
                </div>
            </div>
            <div class="row">
                <div class="col-1-10">
                    <?php echo $today_report ?>
                </div>
                <div class="col-1-52">
                    Total order place(pending + completed)
                </div>
                <div class="col-1-10">
                    <?php echo $number_of_orders ?>
                </div>
                <div class="col-1-10">
                    -
                </div>
                <div class="col-1-10">
                    RM
                    <?php echo $sub_total = $total_completed + $total_pendings; ?>
                </div>
            </div>
            <div class="row panel">
                <div class="col-1-10">

                </div>
                <div class="col-1-52">

                </div>
                <div class="col-1-10">

                </div>
                <div class="col-1-10">
                    Total amount:
                </div>
                <div class="col-1-10">
                    RM
                    <?php echo $final_total = $sub_total + $sub_total; ?>
                </div>
            </div>
            </br>
            <div class="row">
                <div class="col-1-10 panel">
                    Date
                </div>
                <div class="col-1-52 panel">
                    Products
                </div>
                <div class="col-1-10 panel">

                </div>
                <div class="col-1-10 panel">

                </div>
                <div class="col-1-10 panel">
                    Rate[x/5]
                </div>
            </div>
            <!-- looping modules -->
            <?php $display_report = mysqli_query($conn, "SELECT * FROM `products`");
            while ($fetch_report = mysqli_fetch_assoc($display_report)) {
                ?>
                <div class="row">
                    <div class="col-1-10">
                        <?php echo $today_report ?>
                    </div>
                    <div class="col-1-52">
                        <?php echo $fetch_report['name'] ?>
                    </div>
                    <div class="col-1-10">

                    </div>
                    <div class="col-1-10">

                    </div>
                    <div class="col-1-10">
                        <?php echo $fetch_report['pro_rates']; ?>
                    </div>
                </div>
                <?php
            }
            ?>

        </section>
    </div>
</body>

</html>