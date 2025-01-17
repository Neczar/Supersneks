<?php
include 'config.php';

session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
   header('location:login.php');
   exit;
}

if (isset($_POST['add_to_cart'])) {
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
      echo '<script>
               window.onload = function() {
                  document.getElementById("myModal").style.display = "block";
               }
            </script>';
   }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>shop</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

   <script>
      $(document).ready(function() {
      $('.swiper-slide.slide').click(function(e) {
         e.preventDefault();
         var brand = $(this).data('brand');
         if (brand === 'all') {
            loadAllProducts(); // Call the function to load all products
         } else {
            loadProductsByBrand(brand);
         }
      });

      function loadAllProducts() {
         $.ajax({
            url: 'get_all_products.php',
            type: 'POST',
            success: function(response) {
               $('.product-box').html(response);
               $('.image-container').hover(function() {
                  $(this).find('.image').css('transform', 'scale(1.5)'); // Increase scale on hover
               }, function() {
                  $(this).find('.image').css('transform', 'scale(1)'); // Reset scale on hover out
               });
            },
            error: function(xhr, status, error) {
               console.log(error);
            }
         });
      }

      function loadProductsByBrand(brand) {
         $.ajax({
            url: 'get_products.php',
            type: 'POST',
            data: {
               category_name: brand // Change the parameter name to category_name
            },
            success: function(response) {
               $('.product-box').html(response);
               $('.image-container').hover(function() {
                  $(this).find('.image').css('transform', 'scale(1.5)'); // Increase scale on hover
               }, function() {
                  $(this).find('.image').css('transform', 'scale(1)'); // Reset scale on hover out
               });
            },
            error: function(xhr, status, error) {
               console.log(error);
            }
         });
      }

   });
   </script>
</head>
<body>
   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>our shop</h3>
      <p><a href="home.php">home</a> / shop </p>
   </div>

   <section class="category">
      <h1 class="headings">Brands</h1>

      <a href="#" class="swiper-slide slide" data-brand="all">
         <img src="images/all-logo.png" alt="">
         <h3>All Shoes</h3>
      </a>

      <a href="#" class="swiper-slide slide" data-brand="adidas">
         <img src="images/adidas-logo.png" alt="">
         <h3>Adidas</h3>
      </a>

      <a href="#" class="swiper-slide slide" data-brand="new-balance">
         <img src="images/nb-logo.png" alt="">
         <h3>New Balance</h3>
      </a>

      <a href="#" class="swiper-slide slide" data-brand="nike">
         <img src="images/nike-logo.png" alt="">
         <h3>Nike</h3>
      </a>

      <a href="#" class="swiper-slide slide" data-brand="vans">
         <img src="images/vans-logo.png" alt="">
         <h3>Vans</h3>
      </a>
   </section>

   <section class="products">
      <h1 class="title">latest products</h1>
      <div class="box-container product-box">
   <?php
   $select_products = mysqli_query($conn, "SELECT p.*, d.discount_amount FROM products p LEFT JOIN discounts d ON p.product_id = d.product_id LIMIT 16") or die('query failed');
   if (mysqli_num_rows($select_products) > 0) {
      while ($fetch_products = mysqli_fetch_assoc($select_products)) {
         $price = $fetch_products['price'];
         $discountAmount = $fetch_products['discount_amount'];
         $discountedPrice = $price - $discountAmount;
   ?>
         <form action="" method="post" class="box">
         <a href="view_product.php?product_id=<?php echo $fetch_products['product_id']; ?>">
            <img class="image" src="uploaded_img/<?php echo $fetch_products['image']; ?>" name="product-select" alt="">
         </a>
            <div class="name"><?php echo $fetch_products['name']; ?></div>
            <div class="price">
               <?php if ($discountAmount > 0) { ?>
                  <span class="original1"><s>$<?php echo $price; ?></s></span>
                  <span class="discounted">$<?php echo $discountedPrice; ?></span>
               <?php } else { ?>
                  <span class="original">$<?php echo $price; ?></span>
               <?php } ?>
            </div>
            
            <input type="number" min="1" name="product_quantity" value="1" class="qty">
            <input type="hidden" name="product_id" value="<?php echo $fetch_products['product_id']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $fetch_products['name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $price; ?>">
            <input type="hidden" name="product_image" value="<?php echo $fetch_products['image']; ?>">
            <input type="submit" value="add to cart" name="add_to_cart" class="btn">
         </form>
   <?php
      }
   } else {
      echo '<p class="empty">no products added yet!</p>';
   }
   ?>
</div>



   

      <div id="myModal" class="modal">
         <div class="modal-content">
            <span class="close">&times;</span>
            <h2 style="font-size: 3em;text-align:center;">Product added to cart!</h2>
         </div>
      </div>
   </section>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>
</body>
</html>