<?php

include 'config.php';

session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
   header('location:login.php');
}

if (isset($_POST['add_to_cart'])) {
   $product_id = $_POST['product_id'];
   $product_quantity = $_POST['product_quantity'];

   // Validate if the product exists in the products table
   $check_product = mysqli_query($conn, "SELECT * FROM products WHERE product_id = '$product_id'") or die('query failed');
   if (mysqli_num_rows($check_product) > 0) {
      // Product exists, proceed with adding to cart
      $check_cart_numbers = mysqli_query($conn, "SELECT * FROM carts WHERE product_id = '$product_id' AND customer_id = '$customer_id'") or die('query failed');

      if (mysqli_num_rows($check_cart_numbers) > 0) {
         $message[] = 'already added to cart!';
      } else {
         mysqli_query($conn, "INSERT INTO `carts`(customer_id, product_id, quantity) VALUES('$customer_id', '$product_id', '$product_quantity')") or die('query failed');
         $message[] = 'product added to cart!';
         echo '<script>
               window.onload = function() {
                  document.getElementById("myModal").style.display = "block";
               }
            </script>';
      }
   } else {
      $message[] = 'Product does not exist!';
   }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>home</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css">

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
         // Rest of the code...
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
   
   <section class="home">

      <div class="content">
         <h3>Unapologetically Original</h3>
         <p>Move. Explore. Bring your boldest. Get after summer’s endless possibilities with ready-for-anything fits.</p>
         <a href="about.php" class="white-btn">discover more</a>
      </div>

   </section>

   <section class="category">

      <h1 class="headings">Category Brands</h1>

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
   $select_products = mysqli_query($conn, "SELECT p.*, d.discount_amount FROM products p LEFT JOIN discounts d ON p.product_id = d.product_id LIMIT 8") or die('query failed');
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




<div class="load-more" style="margin-top: 2rem; text-align:center">
   <a href="shop.php" class="option-btn">load more</a>
</div>

<div id="myModal" class="modal">
   <div class="modal-content">
      <span class="close">&times;</span>
      <h2 style="font-size: 3em;text-align:center;">Product added to cart!</h2>
   </div>
</div>

</section>


   <section class="about">

      <div class="flex">

         <div class="image">
            <img src="images/about-img.jpg" alt="">
         </div>

         <div class="content">
            <h3>about us</h3>
            <p>The company’s world headquarters are situated near Cagayan de Oro City, Misamis Oriental, in the USTP School <Area></Area> (PHI). It is a major producer of sports equipment and one of the world’s largest suppliers of athletic shoes and apparel.</p>
            <a href="about.php" class="btn">read more</a>
         </div>

      </div>

   </section>

   <section class="home-contact">

      <div class="content">
         <h3>have any questions?</h3>
         <p>Let's Talk About Business</p>
         <a href="contact.php" class="white-btn">contact us</a>
      </div>

   </section>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>