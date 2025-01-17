<?php

include 'config.php';
session_start();

if(isset($_POST['submit'])){
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $password = mysqli_real_escape_string($conn, $_POST['password']);

   $select_customer = mysqli_prepare($conn, "SELECT * FROM customers WHERE email = ?") or die('query failed');
   mysqli_stmt_bind_param($select_customer, "s", $email);
   mysqli_stmt_execute($select_customer);
   $result_customer = mysqli_stmt_get_result($select_customer);

   $select_supplier = mysqli_prepare($conn, "SELECT * FROM suppliers WHERE email = ?") or die('query failed');
   mysqli_stmt_bind_param($select_supplier, "s", $email);
   mysqli_stmt_execute($select_supplier);
   $result_supplier = mysqli_stmt_get_result($select_supplier);

   if(mysqli_num_rows($result_supplier) > 0){
      $row = mysqli_fetch_assoc($result_supplier);

      if(password_verify($password, $row['password'])){
         $_SESSION['supplier_name'] = $row['supplier_name'];
         $_SESSION['supplier_email'] = $row['email'];
         $_SESSION['supplier_id'] = $row['supplier_id'];
         header('location: admin_page.php');
         exit;
      }
   } elseif(mysqli_num_rows($result_customer) > 0) {
      $row = mysqli_fetch_assoc($result_customer);

      if(password_verify($password, $row['password'])){
         $_SESSION['customer_name'] = $row['customer_name'];
         $_SESSION['customer_email'] = $row['email'];
         $_SESSION['customer_id'] = $row['customer_id'];
      
         // Record login time in tbluser_sessions table
         date_default_timezone_set('Asia/Manila');
         // Record login time in tbluser_sessions table
         $customer_id = $_SESSION['customer_id'];
         $login_time = date('Y-m-d H:i:s');
         $logout_time = NULL; // Empty string or any specific value if desired
         mysqli_query($conn, "CALL UpdateSession($customer_id, '$login_time', '$logout_time')");
         header('location: home.php');

      }
   } 

   $message[] = 'Incorrect email or password!';
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Login</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
if(isset($message)){
   foreach($message as $message){
      echo '
      <div class="message">
         <span>'.$message.'</span>
         <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
      </div>
      ';
   }
}
?>
   
<div class="form-container">

   <form action="" method="post">
      <h3>login now</h3>
      <input type="email" name="email" placeholder="enter your email" required class="box">
      <input type="password" name="password" placeholder="enter your password" required class="box">
      <input type="submit" name="submit" value="login now" class="btn">
      <p>don't have an account? <a href="register.php">register now</a></p>
   </form>

</div>

</body>
</html>