<?php
include 'config.php';

session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
   header('location:login.php');
}

if (isset($_POST['update_cart'])) {
   $cart_id = $_POST['cart_id'];
   $cart_quantity = $_POST['quantity'];
   mysqli_query($conn, "UPDATE carts SET quantity = '$cart_quantity' WHERE cart_id = '$cart_id'") or die('query failed');
   $message[] = 'cart quantity updated!';
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];
   mysqli_query($conn, "DELETE FROM carts WHERE cart_id = '$delete_id'") or die('query failed');
   header('location:cart.php');
}

if (isset($_GET['delete_all'])) {
   mysqli_query($conn, "DELETE FROM carts WHERE customer_id = '$customer_id'") or die('query failed');
   header('location:cart.php');
}

if (isset($_POST['proceed_to_checkout'])) {
   // Get the current date
   date_default_timezone_set('Asia/Manila');
   $order_date = date('Y-m-d');

   // Retrieve cart items
   $select_cart = mysqli_query($conn, "SELECT c.product_id, c.cart_id, c.quantity, p.name, p.price, p.discounted_price FROM carts c INNER JOIN products p ON c.product_id = p.product_id WHERE c.customer_id = '$customer_id'") or die('query failed');


   // Insert cart items into orders table and update product quantity
   while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
      $cart_id = $fetch_cart['cart_id'];
      $quantity = $fetch_cart['quantity'];
      $product_id = $fetch_cart['product_id'];
      $product_price = $fetch_cart['price'];
      $discounted_price = $fetch_cart['discounted_price'];
   
   // Calculate subtotal based on discounted price if available
   if ($discounted_price > 0) {
      $subtotal = $discounted_price * $quantity;
   } else {
      $subtotal = $product_price * $quantity;
   }
   
   // Insert order details into orders table
   mysqli_query($conn, "INSERT INTO orders (customer_id, product_id, order_date, total_amount) VALUES ('$customer_id', '$product_id', '$order_date', '$subtotal')") or die('query failed');

   // Update product quantity
   mysqli_query($conn, "UPDATE orders SET order_quantity = '$quantity' WHERE product_id = '$product_id'") or die('query failed');
}


   // Clear the cart after placing the order
   mysqli_query($conn, "DELETE FROM carts WHERE customer_id = '$customer_id'") or die('query failed');

   header('location:checkout.php');
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Cart</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">
</head>
<body>
   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>Shopping Cart</h3>
      <p><a href="home.php">Home</a> / Cart</p>
   </div>

   <section class="shopping-cart">
      <h1 class="title">Products Added</h1>
      <div class="box-container">
         <?php
         $grand_total = 0;
         $select_cart = mysqli_query($conn, "SELECT c.cart_id, c.quantity, p.name, p.price, p.image, p.discounted_price FROM carts c INNER JOIN products p ON c.product_id = p.product_id WHERE c.customer_id = '$customer_id'") or die('query failed');
         if (mysqli_num_rows($select_cart) > 0) {
            while ($fetch_cart = mysqli_fetch_assoc($select_cart)) {
               $price = $fetch_cart['price'];
               $quantity = $fetch_cart['quantity'];
               $discounted_price = $fetch_cart['discounted_price'];
            
               // Calculate subtotal based on discounted price if available
               if ($discounted_price > 0) {
                  $subtotal = $discounted_price * $quantity;
               } else {
                  $subtotal = $price * $quantity;
               }
            
               $grand_total += $subtotal;
            
               // Rest of your code...
                     
               ?>
               <div class="box">
               <a href="cart.php?delete=<?php echo $fetch_cart['cart_id']; ?>" class="fas fa-times" onclick="return confirm('Delete this from cart?');"></a>
               <img src="uploaded_img/<?php echo $fetch_cart['image']; ?>" alt="">
               <div class="name"><?php echo $fetch_cart['name']; ?></div>
               <div class="price">
                  <?php if ($fetch_cart['discounted_price'] > 0) { ?>
                     <div class="original-price">$<?php echo $fetch_cart['price']; ?></div>
                     <div class="discounted-price">$<?php echo $fetch_cart['discounted_price']; ?></div>
                  <?php } else { ?>
                     <div>$<?php echo $fetch_cart['price']; ?></div>
                  <?php } ?>
               </div>
               <form action="" method="post">
                  <input type="hidden" name="cart_id" value="<?php echo $fetch_cart['cart_id']; ?>">
                  <input type="number" min="1" name="quantity" value="<?php echo $fetch_cart['quantity']; ?>">
                  <input type="submit" name="update_cart" value="Update" class="option-btn">
               </form>
               <div class="sub-total">Sub Total: <span>$<?php echo $subtotal; ?></span></div>
            </div>

         <?php
            }
         } else {
            echo '<p class="empty">Your cart is empty</p>';
         }
         ?>
      </div>
      <div style="margin-top: 2rem; text-align:center;">
         <a href="cart.php?delete_all" class="delete-btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>" onclick="return confirm('Delete all from cart?');">Delete All</a>
      </div>
      <div class="cart-total">
         <p>Grand Total: <span>$<?php echo $grand_total; ?></span></p>
         <div class="flex">
            <a href="shop.php" class="option-btn">Continue Shopping</a>
         <form action="" method="post">
            <input type="hidden" name="proceed_to_checkout" value="1">
            <input type="submit" name="submit" value="Proceed to Checkout" class="btn <?php echo ($grand_total > 1) ? '' : 'disabled'; ?>">
         </form>
         </div>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>
</body>
</html>