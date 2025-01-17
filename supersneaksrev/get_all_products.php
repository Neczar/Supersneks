<div class="box-container product-box">
   <?php
   include 'config.php';

   // Prepare the SQL statement to fetch all products
   $query = "SELECT p.*, d.discount_amount FROM products p LEFT JOIN discounts d ON p.product_id = d.product_id LIMIT 8";
   $result = mysqli_query($conn, $query) or die('Query failed');

   if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
         $price = $row['price'];
         $discountAmount = $row['discount_amount'];
         $discountedPrice = $price - $discountAmount;
         ?>
         <form action="" method="post" class="box">
            <div class="image-container">
            <a href="view_product.php?product_id=<?php echo $row['product_id']; ?>">
               <img class="image" src="uploaded_img/<?php echo $row['image']; ?>" name="product-select" alt="">
            </a>
            </div>
            <div class="name"><?php echo $row['name']; ?></div>
            <div class="price">
               <?php if ($discountAmount > 0) { ?>
                  <span class="original"><s>$<?php echo $price; ?></s></span>
                  <span class="discounted">$<?php echo $discountedPrice; ?></span>
               <?php } else { ?>
                  <span>$<?php echo $price; ?></span>
               <?php } ?>
            </div>
            <input type="number" min="1" name="product_quantity" value="1" class="qty">
            <input type="hidden" name="product_id" value="<?php echo $row['product_id']; ?>">
            <input type="hidden" name="product_name" value="<?php echo $row['name']; ?>">
            <input type="hidden" name="product_price" value="<?php echo $price; ?>">
            <input type="hidden" name="product_image" value="<?php echo $row['image']; ?>">
            <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
         </form>
         <?php
      }
   } else {
      echo '<p class="empty">No products added yet!</p>';
   }
   ?>
</div>
