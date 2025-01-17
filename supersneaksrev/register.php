<?php
include 'config.php';

if(isset($_POST['submit'])){
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $email = mysqli_real_escape_string($conn, $_POST['email']);
   $address = mysqli_real_escape_string($conn, $_POST['address']);
   $contact_number = mysqli_real_escape_string($conn, $_POST['contact_number']);
   $password = mysqli_real_escape_string($conn, $_POST['password']);
   $confirmPassword = mysqli_real_escape_string($conn, $_POST['cpassword']);

   // Check if the email already exists in customers table
   $select_customers = mysqli_query($conn, "SELECT * FROM `customers` WHERE email = '$email'") or die('Query failed');

   if(mysqli_num_rows($select_customers) > 0){
      $message = 'Customer already exists!';
   } else {
      // Check if the email already exists in suppliers table
      $select_suppliers = mysqli_query($conn, "SELECT * FROM `suppliers` WHERE email = '$email'") or die('Query failed');

      if(mysqli_num_rows($select_suppliers) > 0){
         $message = 'Supplier already exists!';
      } else {
         if($password !== $confirmPassword){
            $message = 'Confirm password does not match!';
         } else {
            // Hash the password
            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

            // Determine the user type based on the selected option
            $userType = ($_POST['user_type'] == 'admin') ? 'suppliers' : 'customers';

            // Insert the user into the appropriate table
            if ($userType == 'customers') {
               mysqli_query($conn, "INSERT INTO `customers` (customer_name, email, shipping_address, contact_number, password) VALUES ('$name', '$email', '$address', '$contact_number', '$hashedPassword')") or die('Query failed');
            } else {
               mysqli_query($conn, "INSERT INTO `suppliers` (supplier_name, supplier_address, email, contact_number, password) VALUES ('$name', '$address', '$email', '$contact_number', '$hashedPassword')") or die('Query failed');
            }

            $message = 'Registered successfully!';
            header('location: login.php');
            exit;
         }
      }
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Register</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>

<?php
if(isset($message)){
   echo '
   <div class="message">
      <span>'.$message.'</span>
      <i class="fas fa-times" onclick="this.parentElement.remove();"></i>
   </div>
   ';
}
?>

<div class="form-container">
   <form action="" method="post">
      <h3>Register now</h3>
      <input type="text" name="name" placeholder="Enter your name" required class="box">
      <input type="email" name="email" placeholder="Enter your email" required class="box">
      <input type="text" name="address" placeholder="Enter address " required class="box">
      <input type="text" name="contact_number" placeholder="Enter contact number " required class="box">
      <input type="password" name="password" placeholder="Enter your password" required class="box">
      <input type="password" name="cpassword" placeholder="Confirm your password" required class="box">
      <select name="user_type" class="box">
         <option value="user">Customer</option>
         <option value="admin">Supplier</option>
      </select>
      <input type="submit" name="submit" value="Register now" class="btn">
      <p>Already have an account? <a href="login.php">Login now</a></p>
   </form>
</div>

</body>
</html>