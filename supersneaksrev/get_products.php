<?php
include 'config.php';

if (isset($_POST['category_name'])) {
   $category_name = $_POST['category_name'];

   // Prepare the SQL statement to fetch products based on the selected category
   $query = "SELECT p.*, c.category_name, d.discount_amount FROM products p INNER JOIN category c ON p.category_id = c.category_id LEFT JOIN discounts d ON p.product_id = d.product_id WHERE c.category_name = '$category_name' LIMIT 8";
   $result = mysqli_query($conn, $query) or die('Query failed');

   $output = '';

   if (mysqli_num_rows($result) > 0) {
      while ($row = mysqli_fetch_assoc($result)) {
         $price = $row['price'];
         $discountAmount = $row['discount_amount'];
         $discountedPrice = $price - $discountAmount;

         $output .= '
            <form action="" method="post" class="box">
            <div class="image-container">
               <a href="view_product.php?product_id=' . $row['product_id'] . '">
                  <img class="image" src="uploaded_img/' . $row['image'] . '" alt="">
               </a>
            </div>
               <div class="name">' . $row['name'] . '</div>
               <div class="price">
                  ' . ($discountAmount > 0 ? '<span class="original"><s>$' . $price . '</s></span> <span class="discounted">$' . $discountedPrice . '</span>' : '$' . $price) . '
               </div>
               <input type="number" min="1" name="product_quantity" value="1" class="qty">
               <input type="hidden" name="product_id" value="' . $row['product_id'] . '">
               <input type="hidden" name="product_name" value="' . $row['name'] . '">
               <input type="hidden" name="product_price" value="' . $price . '">
               <input type="hidden" name="product_image" value="' . $row['image'] . '">
               <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
            </form>
         ';
      }
   } else {
      $output = '<p class="empty">No products found for this category!</p>';
   }

   echo $output;
}
?>
