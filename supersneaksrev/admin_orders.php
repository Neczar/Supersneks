<?php

include 'config.php';

session_start();

$supplier_id = $_SESSION['supplier_id'];

if(!isset($supplier_id)){
   header('location:login.php');
}

$update_status = '';

if(isset($_POST['update_order'])){

   $order_update_id = $_POST['order_id'];
   $update_status = $_POST['update_status'];
   mysqli_query($conn, "UPDATE orders SET `status` = '$update_status' WHERE order_id = '$order_update_id'") or die('query failed');
   $message[] = 'status has been updated!';

}

if ($update_status === 'shipped') {
   // Get order details
   $order_details_query = mysqli_query($conn, "SELECT * FROM orders WHERE order_id = '$order_update_id'");
   $order_details = mysqli_fetch_assoc($order_details_query);

   // Get customer address
   $customer_id = $order_details['customer_id'];
   $customer_address_query = mysqli_query($conn, "SELECT shipping_address FROM customers WHERE customer_id = '$customer_id'");
   $customer_address = mysqli_fetch_assoc($customer_address_query)['shipping_address'];

   // Insert into shipping table
   date_default_timezone_set('Asia/Manila');
   $current_date = date("Y-m-d");
   mysqli_query($conn, "INSERT INTO shipping (order_id, shipping_date, address) VALUES ('$order_update_id', '$current_date', '$customer_address')") or die('query failed');
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM `orders` WHERE order_id = '$delete_id'") or die('query failed');
   header('location:admin_orders.php');
}


?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="orders">

   <h1 class="title">placed orders</h1>

   <div class="box-container">
      <?php
      $select_orders = mysqli_query($conn, "SELECT *, o.order_id
      FROM orders o
      JOIN products p ON o.product_id = p.product_id
      JOIN customers c ON o.customer_id = c.customer_id
      LEFT JOIN shipping s ON o.order_id = s.order_id
      LEFT JOIN payments pm ON o.order_id = pm.order_id;") or die('query failed');

      
      if(mysqli_num_rows($select_orders) > 0){
         while($fetch_orders = mysqli_fetch_assoc($select_orders)){
      ?>
      <div class="box">
         <p> order id : <span><?php echo $fetch_orders['order_id']; ?></span> </p>
         <p> user id : <span><?php echo $fetch_orders['customer_id']; ?></span> </p>
         <p> name : <span><?php echo $fetch_orders['customer_name']; ?></span> </p>
         <p> number : <span><?php echo $fetch_orders['contact_number']; ?></span> </p>
         <p> email : <span><?php echo $fetch_orders['email']; ?></span> </p>
         <p> address : <span><?php echo $fetch_orders['shipping_address']; ?></span> </p>
         <p> total products : <span><?php echo $fetch_orders['name']; ?> (<?php echo $fetch_orders['order_quantity']; ?>)</span> </p>
         <p> total price : <span>$<?php echo $fetch_orders['total_amount']; ?>/-</span> </p>
         <p> payment method : <span><?php echo $fetch_orders['payment_method']; ?></span> </p>
         <form action="" method="post">
            <input type="hidden" name="order_id" value="<?php echo $fetch_orders['order_id']; ?>">
            <select name="update_status">
               <option value="" selected disabled><?php echo $fetch_orders['status']; ?></option>
               <option value="processing">processing</option>
               <option value="shipped">shipped</option>
            </select>
            <input type="submit" value="update" name="update_order" class="option-btn">
            <a href="admin_orders.php?delete=<?php echo $fetch_orders['order_id']; ?>" onclick="return confirm('delete this order?');" class="delete-btn">delete</a>
         </form>
      </div>
      <?php
         }
      }else{
         echo '<p class="empty">no orders placed yet!</p>';
      }
      ?>
   </div>

</section>






<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>