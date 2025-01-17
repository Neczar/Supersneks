<?php
include 'config.php';
session_start();
$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
   header('location:login.php');
   exit;
}

if (isset($_POST['submit_review'])) {
   $reviewStars = $_POST['review_stars'];
   $reviewText = $_POST['review_text'];
   $productId = $_POST['product_id'];

   // Check if the user has already submitted a review for the same product
   $checkReviewQuery = "SELECT * FROM `reviews` WHERE `product_id`='$productId' AND `customer_id`='$customer_id'";
   $checkReviewResult = mysqli_query($conn, $checkReviewQuery);

   if (mysqli_num_rows($checkReviewResult) > 0) {
       // If the user already submitted a review, update the existing review
       $updateQuery = "UPDATE `reviews` SET `review_text`='$reviewText', `rating`='$reviewStars' WHERE `product_id`='$productId' AND `customer_id`='$customer_id'";
   } else {
       // If the user hasn't submitted a review, insert a new review
       $updateQuery = "INSERT INTO `reviews` (`product_id`, `customer_id`, `review_text`, `rating`) VALUES ('$productId', '$customer_id', '$reviewText', '$reviewStars')";
   }

   $result = mysqli_query($conn, $updateQuery);

   if ($result) {
       echo "<script>alert('Review submitted successfully!');</script>";
   } else {
       echo "<script>alert('Failed to submit review. Please try again.');</script>";
   }
}



if(isset($_POST['update_order'])){

   $order_update_id = $_POST['order_id'];
   $update_status = $_POST['update_status'];
   mysqli_query($conn, "UPDATE orders SET `status` = '$update_status' WHERE order_id = '$order_update_id'") or die('query failed');
   $message[] = 'status has been updated!';

}
?>


<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>orders</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

   <!-- modal css -->
   <link rel="stylesheet" href="css/modal.css">

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>your orders</h3>
      <p> <a href="home.php">home</a> / orders </p>
   </div>

   <section class="placed-orders">
      <h1 class="title">Placed Orders</h1>
      <div class="box-container">
         <?php
         $order_query = mysqli_query($conn, "SELECT *, p.product_id FROM `orders` o JOIN `products` p ON o.product_id = p.product_id JOIN `customers` c ON o.customer_id = c.customer_id WHERE o.customer_id = '$customer_id'") or die('query failed');

         if (mysqli_num_rows($order_query) > 0) {
            while ($fetch_orders = mysqli_fetch_assoc($order_query)) {
         ?>
               <div class="box">
                  <p> Placed on: <span><?php echo $fetch_orders['order_date']; ?></span> </p>
                  <p> Name: <span><?php echo $fetch_orders['customer_name']; ?></span> </p>
                  <p> Address: <span><?php echo $fetch_orders['shipping_address']; ?></span> </p>
                  <p> Email: <span><?php echo $fetch_orders['email']; ?></span> </p>
                  <p> Contact number: <span><?php echo $fetch_orders['contact_number']; ?></span> </p>
                  <p> Your orders: <span><?php echo $fetch_orders['name']; ?> (<?php echo $fetch_orders['order_quantity']; ?>)</span> </p>
                  <p> Total price: <span>$<?php echo $fetch_orders['total_amount']; ?></span> </p>
                  <p> status: <span style="color:<?php echo ($fetch_orders['status'] == 'processing') ? 'red' : 'green'; ?>"><?php echo $fetch_orders['status']; ?></span> </p>
                  <?php
                     if ($fetch_orders['status'] == 'shipped') {
                     ?>
                        <form action="" method="post">
                           <input type="hidden" name="order_id" value="<?php echo $fetch_orders['order_id']; ?>">
                           <select name="update_status">
                              <option value="" selected disabled><?php echo $fetch_orders['status']; ?></option>
                              <option value="shipped">shipped</option>
                              <option value="completed">completed</option>
                           </select>
                           <input type="submit" value="send" name="update_order" class="option-btn" style="margin-left: 12em;">
                        </form>
                     <?php
                     } elseif ($fetch_orders['status'] == 'completed') {
                     ?>
                     <button class="thoughts-btn btn" style="margin: 1em 7.5em;" onclick="openModal(<?php echo $fetch_orders['product_id']; ?>, <?php echo $fetch_orders['order_id']; ?>)">Send us your thoughts!</button>
                     <?php
                     }
                  ?>

               </div>

         <?php
            }
         } else {
            echo '<p class="empty">No orders placed yet!</p>';
         }
         ?>
      </div>
   </section>

   <!-- Modal -->
   <div id="modal" class="modal">
      <section class="review">
         <div class="review-container">
            <div class="post">
               <div class="text">Thank you for rating us!</div>
            </div>
            <div class="close-icon" onclick="closeModal()">&times;</div>
            <form action="" class="review-form" method="POST">
               <div class="review-stars star-widget">
                  <input type="radio" id="star5" name="review_stars" value="5">
                  <label for="star5">&#9733;</label>
                  <input type="radio" id="star4" name="review_stars" value="4">
                  <label for="star4">&#9733;</label>
                  <input type="radio" id="star3" name="review_stars" value="3">
                  <label for="star3">&#9733;</label>
                  <input type="radio" id="star2" name="review_stars" value="2">
                  <label for="star2">&#9733;</label>
                  <input type="radio" id="star1" name="review_stars" value="1">
                  <label for="star1">&#9733;</label>

                  <header></header>

                  <div class="review-textarea">
                     <textarea name="review_text" cols="30" placeholder="Describe your experience..."></textarea>
                  </div>
                  <input type="hidden" name="order_id" id="order_id">
                  <input type="hidden" name="product_id" id="product_id">
                  <div class="review-btn">
                     <button type="submit" name="submit_review">Send</button>
                  </div>
               </div>

            </form>

         </div>
      </div>
   </section>

   </div>

   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

   <!-- modal js -->
   <script>
      function openModal(productId, orderId) {
         const modal = document.getElementById('modal');
         const productIdField = document.getElementById('product_id');
         const orderIdField = document.getElementById('order_id');
         productIdField.value = productId;
         orderIdField.value = orderId;
         modal.style.display = 'block';
      }


      function closeModal() {
         const modal = document.getElementById('modal');
         modal.style.display = 'none';
      }
   </script>

</body>

</html>