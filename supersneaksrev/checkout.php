<?php

include 'config.php';

session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
   header('location:login.php');
}

if (isset($_POST['order_btn'])) {
   $selectedOrder = $_POST['order'];
   $getMethod = $_POST['payment'];
   date_default_timezone_set('Asia/Manila');
   $paymentDate = date('Y-m-d H:i:s');
   mysqli_query($conn, "UPDATE payments SET payment_date = '$paymentDate', payment_method = '$getMethod' WHERE order_id = '$selectedOrder'");
   header('Location:orders.php');
}

// Retrieve orders that don't have a payment method assigned
$selectOrdersQuery = mysqli_query($conn, "SELECT order_id FROM orders WHERE customer_id = '$customer_id' AND order_id NOT IN (SELECT order_id FROM payments WHERE payment_method IS NOT NULL)");
$ordersWithoutPayment = mysqli_fetch_all($selectOrdersQuery, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>checkout</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>checkout</h3>
   <p> <a href="home.php">home</a> / checkout </p>
</div>

<section class="checkout">
   <form action="" method="post">
      <h3>PAYMENT METHOD</h3>
      <div class="flex">
         <div class="inputBox">
            <span>Select your order:</span>
            <select name="order">
               <?php
                  $selectOrdersQuery = mysqli_query($conn, "SELECT order_id FROM orders WHERE customer_id = '$customer_id'");
                  while ($orderRow = mysqli_fetch_assoc($selectOrdersQuery)) {
                     $orderId = $orderRow['order_id'];
                     $selected = ($selectedOrder == $orderId) ? 'selected' : '';
                     echo "<option value='$orderId' $selected>$orderId</option>";
                  }
               ?>
            </select>
            <span>Payment method:</span>
            <select name="payment">
               <option value="debit-card" disabled>Debit Card</option>
               <option value="credit-card" disabled>Credit Card</option>
               <option value="cash-on-delivery">Cash on Delivery</option>
               <option value="e-wallet" disabled>E-Wallet</option>
            </select>
         </div>
      </div>
      <input type="submit" value="Check your orders" class="btn" name="order_btn">
   </form>
</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>