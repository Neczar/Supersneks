<?php
include 'config.php';

session_start();

$customer_id = $_SESSION['customer_id'];

if(!isset($customer_id)){
   header('location:login.php');
};

if(isset($_POST['add_to_cart'])){

   $product_name = $_POST['product_name'];
   $product_price = $_POST['product_price'];
   $product_image = $_POST['product_image'];
   $product_quantity = $_POST['product_quantity'];

   $check_cart_numbers = mysqli_query($conn, "SELECT * FROM `carts` WHERE product_id = (SELECT product_id FROM `products` WHERE name = '$product_name') AND customer_id = '$customer_id'") or die('Query failed');

   if (mysqli_num_rows($check_cart_numbers) > 0) {
      $message = 'Product already added to cart!';
   } else {
      $product_id = mysqli_query($conn, "SELECT product_id FROM `products` WHERE name = '$product_name'") or die('Query failed');
      $product_id = mysqli_fetch_assoc($product_id)['product_id'];
      mysqli_query($conn, "INSERT INTO `carts` (customer_id, product_id, quantity) VALUES ('$customer_id', '$product_id', '$product_quantity')") or die('Query failed');
      $message = 'Product added to cart!';
   }

};

?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>search page</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>search page</h3>
   <p> <a href="home.php">home</a> / search </p>
</div>

<section class="search-form">
   <form action="" method="post">
      <input type="text" name="search" placeholder="search products..." class="box">
      <input type="submit" name="submit" value="search" class="btn">
   </form>
</section>

<section class="products" style="padding-top: 0;">

   <div class="box-container">
   <?php
      if(isset($_POST['submit'])){
         $search_item = $_POST['search'];
         $select_products = mysqli_query($conn, "SELECT * FROM `products` WHERE name LIKE '%{$search_item}%'") or die('query failed');
         if(mysqli_num_rows($select_products) > 0){
         while($fetch_product = mysqli_fetch_assoc($select_products)){
            $price = $fetch_product['price'];
            $discount = $fetch_product['discount'];
            $discounted_price = $price - ($price * $discount / 100); // Calculate the discounted price

            // Check if the product has a discount
            if ($discount > 0) {
               $discount_start = strtotime($fetch_product['discount_start']);
               $discount_end = strtotime($fetch_product['discount_end']);
               $current_time = time();

               // Check if the discount is currently active
               if ($current_time >= $discount_start && $current_time <= $discount_end) {
                  $discount_text = 'Discount available until ' . date('Y-m-d H:i:s', $discount_end);
               } else {
                  $discount_text = '';
               }
            } else {
               $discount_text = '';
            }
   ?>
   <form action="" method="post" class="box">
      <img src="uploaded_img/<?php echo $fetch_product['image']; ?>" alt="" class="image">
      <div class="name"><?php echo $fetch_product['name']; ?></div>
      
      <?php if ($discount > 0) { ?>
         <div class="price">
            <span class="original-price"><s>$<?php echo $price; ?></s></span>
            <span class="discounted-price">$<?php echo $discounted_price; ?></span>
         </div>
         <div class="discount-time"><?php echo $discount_text; ?></div>
      <?php } else { ?>
         <div class="price">$<?php echo $price; ?></div>
      <?php } ?>

      <input type="number"  class="qty" name="product_quantity" min="1" value="1">
      <input type="hidden" name="product_name" value="<?php echo $fetch_product['name']; ?>">
      <input type="hidden" name="product_price" value="<?php echo $fetch_product['price']; ?>">
      <input type="hidden" name="product_image" value="<?php echo $fetch_product['image']; ?>">
      <input type="submit" class="btn" value="add to cart" name="add_to_cart">
   </form>
   <?php
            }
         }else{
            echo '<p class="empty">no result found!</p>';
         }
      }else{
         echo '<p class="empty">search something!</p>';
      }
   ?>
   </div>
  

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
