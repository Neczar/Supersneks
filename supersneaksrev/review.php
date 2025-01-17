<?php

include 'config.php';

session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
   header('location:login.php');
}

// Fetch the reviews from the orders table with user and product information
$query = "SELECT r.review_text, r.rating, c.customer_name, p.image, p.name AS product_name
          FROM reviews r
          JOIN products p ON r.product_id = p.product_id
          JOIN customers c ON r.customer_id = c.customer_id";
$result = mysqli_query($conn, $query);

if (!$result) {
   // Print the specific error message
   echo "Error: " . mysqli_error($conn);
   exit;
}

// Check if there are any reviews
if (mysqli_num_rows($result) > 0) {
    $reviews = mysqli_fetch_all($result, MYSQLI_ASSOC);
} else {
    $reviews = [];
}

if (mysqli_num_rows($result) > 0) {
   $usernamesData = mysqli_fetch_all($result, MYSQLI_ASSOC);
   $usernames = array_column($usernamesData, 'name', 'user_id');
} else {
   $usernames = [];
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>contact</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>

<body>

   <?php include 'header.php'; ?>

   <div class="heading">
      <h3>REVIEW PAGE</h3>
      <p><a href="home.php">home</a> / customer reviews</p>
   </div>

   <section class="reviews">

      <h1 class="title">customer's reviews</h1>

      <div class="box-container">

         <?php foreach ($reviews as $review): ?>
            <div class="box">
               <div class="product-info">
               <h4 style="margin-top: 10px; font-size: 20px;"><?php echo $review['product_name']; ?></h4>
                  <img src="uploaded_img/<?php echo $review['image']; ?>" alt="<?php echo $review['product_name']; ?>">
                  
               </div>
               <h3><?php echo $review['customer_name']; ?></h3>
               <p>"<?php echo $review['review_text']; ?>"</p>
               <div class="stars">
                  <?php for ($i = 0; $i < $review['rating']; $i++): ?>
                     <i class="fas fa-star"></i>
                  <?php endfor; ?>
               </div>
            </div>
         <?php endforeach; ?>

      </div>

   </section>


   <?php include 'footer.php'; ?>

   <!-- custom js file link  -->
   <script src="js/script.js"></script>

</body>

</html>