<?php
include 'config.php';
session_start();
$supplier_id = $_SESSION['supplier_id'];

if (!isset($supplier_id)) {
   header('location: login.php');
   exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>admin panel</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>
<body>
   
<?php include 'admin_header.php'; ?>

<!-- admin dashboard section starts  -->
<section class="dashboard">

   <h1 class="title">dashboard</h1>

   <div class="box-container">

      <div class="box">
         <?php
         $total_pendings = 0;
         $select_pending = mysqli_query($conn, "SELECT total_amount FROM orders WHERE status = 'processing' OR status = 'shipped'") or die('query failed');
         if (mysqli_num_rows($select_pending) > 0) {
            while ($fetch_pendings = mysqli_fetch_assoc($select_pending)) {
               $total_amount = $fetch_pendings['total_amount'];
               $total_pendings += $total_amount;
            }
         }
         ?>

         <h3>$<?php echo $total_pendings; ?>/-</h3>
         <p>total pendings</p>
      </div>

      <div class="box">
         <?php
            $total_completed = 0;
            $select_completed = mysqli_query($conn, "SELECT total_amount FROM orders WHERE status = 'received'") or die('query failed');
            if (mysqli_num_rows($select_completed) > 0) {
               while ($fetch_completed = mysqli_fetch_assoc($select_completed)) {
                  $total_amount = $fetch_completed['total_amount'];
                  $total_completed += $total_amount;
               }
            }
         ?>
         <h3>$<?php echo $total_completed; ?>/-</h3>
         <p>completed payments</p>
      </div>

      <div class="box">
         <?php 
            $select_orders = mysqli_query($conn, "SELECT * FROM orders") or die('query failed');
            $number_of_orders = mysqli_num_rows($select_orders);
         ?>
         <h3><?php echo $number_of_orders; ?></h3>
         <p>order placed</p>
      </div>

      <div class="box">
         <?php 
            $select_products = mysqli_query($conn, "SELECT * FROM products") or die('query failed');
            $number_of_products = mysqli_num_rows($select_products);
         ?>
         <h3><?php echo $number_of_products; ?></h3>
         <p>products added</p>
      </div>

      <div class="box">
         <?php 
            $select_users = mysqli_query($conn, "SELECT c.customer_name, a.login_time, a.logout_time FROM `customers` c INNER JOIN `audittrail` a ON c.customer_id = a.customer_id") or die('query failed');
            $number_of_users = mysqli_num_rows($select_users);
         ?>
         <h3><?php echo $number_of_users; ?></h3>
         <p>customers</p>
      </div>

      <div class="box">
         <?php 
            $select_admins = mysqli_query($conn, "SELECT * FROM suppliers") or die('query failed');
            $number_of_admins = mysqli_num_rows($select_admins);
         ?>
         <h3><?php echo $number_of_admins; ?></h3>
         <p>admin users</p>
      </div>

      <div class="box">
         <?php 
            $select_accounts = mysqli_query($conn, "SELECT customer_id FROM customers UNION SELECT supplier_id FROM suppliers") or die('query failed');
            $number_of_accounts = mysqli_num_rows($select_accounts);
         ?>
         <h3><?php echo $number_of_accounts; ?></h3>
         <p>total accounts</p>
      </div>

      <div class="box1">
      <div class="title-wrapper">
         <h1>Users <span>Activity</span></h1>
      </div>
      <table>
         <thead>
            <tr>
               <th>Name</th>
               <th>Login Time</th>
               <th>Logout Time</th>
            </tr>
         </thead>
         <tbody>
            <?php
               while($row = mysqli_fetch_assoc($select_users)) {
                  $name = $row['customer_name'];
                  $loginTime = $row['login_time'];
                  $logoutTime = $row['logout_time'];
                  
                  echo "<tr>";
                  echo "<td>$name</td>";

                  // Check if login time is "0000-00-00 00:00:00"
                  if($loginTime == '0000-00-00 00:00:00'){
                     echo "<td>&nbsp;</td>";
                  } else {
                     $formattedLoginTime = date('M d, Y - h:i A', strtotime($loginTime));
                     echo "<td>$formattedLoginTime</td>";
                  }

                  // Check if logout time is "0000-00-00 00:00:00"
                  if($logoutTime == '0000-00-00 00:00:00'){
                     echo "<td>&nbsp;</td>";
                  } else {
                     $formattedLogoutTime = date('M d, Y - h:i A', strtotime($logoutTime));
                     echo "<td>$formattedLogoutTime</td>";
                  }

                  echo "</tr>";
               }

               // If there are no user sessions, display "0" for login and logout times
               if(mysqli_num_rows($select_users) == 0){
                  echo "<tr>";
                  echo "<td colspan='3'>0</td>";
                  echo "</tr>";
               }
            ?>
         </tbody>
      </table>
   </div>

   </div>

</section>
<!-- admin dashboard section ends -->


<!-- custom admin js file link  -->
<script src="js/admin_script.js"></script>

</body>
</html>