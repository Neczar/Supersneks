<?php

include 'config.php';

session_start();

$supplier_id = $_SESSION['supplier_id'];

if (!isset($supplier_id)) {
   header('location: login.php');
}

if(isset($_GET['delete'])){
   $delete_id = $_GET['delete'];
   $user_type = $_GET['user_type'];

   if ($user_type == 'supplier') {
      mysqli_query($conn, "DELETE FROM suppliers WHERE supplier_id = '$delete_id'") or die('Supplier delete query failed');
   } elseif ($user_type == 'customer') {
      mysqli_query($conn, "DELETE FROM customers WHERE customer_id = '$delete_id'") or die('Customer delete query failed');
   }  
   
   header('location:admin_users.php');
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Users</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<section class="users">

   <h1 class="title">Supplier Accounts (<?php
      $supplier_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM suppliers") or die('Supplier count query failed');
      $supplier_row = mysqli_fetch_assoc($supplier_count);
      echo $supplier_row['count'];
   ?>)</h1>

   <div class="box-container">
      <?php
         $select_suppliers = mysqli_query($conn, "SELECT supplier_id as user_id, supplier_name AS name, supplier_address, email, contact_number, 'supplier' AS user_type FROM suppliers") or die('Supplier select query failed');
         while($fetch_suppliers = mysqli_fetch_assoc($select_suppliers)){
      ?>
      <div class="box">
         <p> Supplier ID: <span><?php echo $fetch_suppliers['user_id']; ?></span> </p>
         <p> Name: <span><?php echo $fetch_suppliers['name']; ?></span> </p>
         <p> Email: <span><?php echo $fetch_suppliers['email']; ?></span> </p>
         <p> Contact Number: <span><?php echo $fetch_suppliers['contact_number']; ?></span> </p>
         <p> Address: <span><?php echo $fetch_suppliers['supplier_address']; ?></span> </p>
         <p> User Type: <span style="color: var(--orange)"><?php echo $fetch_suppliers['user_type']; ?></span> </p>
         <a href="admin_users.php?delete=<?php echo $fetch_suppliers['user_id']; ?>&user_type=supplier" onclick="return confirm('Delete this user?');" class="delete-btn">Delete User</a>
      </div>
      <?php
         }
      ?>
   </div>

</section>

<section class="users">

   <h1 class="title">Customer Accounts (<?php
      $customer_count = mysqli_query($conn, "SELECT COUNT(*) as count FROM customers") or die('Customer count query failed');
      $customer_row = mysqli_fetch_assoc($customer_count);
      echo $customer_row['count'];
   ?>)</h1>

   <div class="box-container">
      <?php
         $select_customers = mysqli_query($conn, "SELECT customer_id as user_id, customer_name, email,shipping_address, contact_number, 'customer' as user_type FROM customers") or die('Customer select query failed');
         while($fetch_customers = mysqli_fetch_assoc($select_customers)){
      ?>
      <div class="box">
         <p> Customer ID: <span><?php echo $fetch_customers['user_id']; ?></span> </p>
         <p> Name: <span><?php echo $fetch_customers['customer_name']; ?></span> </p>
         <p> Email: <span><?php echo $fetch_customers['email']; ?></span> </p>
         <p> Contact Number: <span><?php echo $fetch_customers['contact_number']; ?></span> </p>
         <p> Address: <span><?php echo $fetch_customers['shipping_address']; ?></span> </p>
         <p> User Type: <span><?php echo $fetch_customers['user_type']; ?></span> </p>
         <a href="admin_users.php?delete=<?php echo $fetch_customers['user_id']; ?>&user_type=customer" onclick="return confirm('Delete this user?');" class="delete-btn">Delete User</a>
      </div>
      <?php
         }
      ?>
   </div>

</section>

<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>