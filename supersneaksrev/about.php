<?php

include 'config.php';

session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
   header('location:login.php');
   exit; // Exit the script to prevent further execution
}


?>



<!DOCTYPE html>
<html lang="en">
<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>about</title>

   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

   <!-- custom css file link  -->
   <link rel="stylesheet" href="css/style.css">

</head>
<body>
   
<?php include 'header.php'; ?>

<div class="heading">
   <h3>about us</h3>
   <p> <a href="home.php">home</a> / about </p>
</div>

<section class="about">

   <div class="flex">

      <div class="image">
         <img src="images/about-img.jpg" alt="">
      </div>

      <div class="content">
         <h3>why choose us?</h3>
         <p>We champion continual progress for athletes and sport by taking action to help athletes reach their potential. Every job at Supersneaks, Inc. is grounded in a team-first mindset, cultivating a culture of innovation and a shared purpose to leave an enduring impact.</p>
         <p>Supersneaks, Inc: Keeping athletes at the center of everything we do. Supersneaks has acquired several apparel and footwear companies over the course of its history, some of which have since been sold.</p>
         <a href="contact.php" class="btn">contact us</a>
      </div>

   </div>


</section>




<section class="authors">

   <h1 class="title">Supersneaks Owners</h1>

   <div class="box-container">

      <div class="box">
         <img src="images/img1.jpg" alt="">
         <div class="share">
         <a href="https://www.facebook.com/aleriaphoebe18" class="fab fa-facebook-f"></a>
         <a href="https://www.instagram.com/itsmephoebeyang/" class="fab fa-instagram"></a>
         </div>
         <h3>Phoebe</h3>
      </div>

      <div class="box">
         <img src="images/img2.jpg" alt="">
         <div class="share">
         <a href="https://www.facebook.com/neczar.balagulan" class="fab fa-facebook-f"></a>
         <a href="https://www.instagram.com/neksarrr/" class="fab fa-instagram"></a>
         </div>
         <h3>Neczar</h3>
      </div>

      <div class="box">
         <img src="images/img3.jpg" alt="">
         <div class="share">
            <a href="https://www.facebook.com/alyssabual" class="fab fa-facebook-f"></a>
            <a href="https://www.instagram.com/alyssabual/" class="fab fa-instagram"></a>
         </div>
         <h3>Alyssa</h3>
      </div>

      <div class="box1">
         <img src="images/img4.jpg" alt="">
         <div class="share">
            <a href="https://www.facebook.com/chuiikaiibeii" class="fab fa-facebook-f"></a>
            <a href="https://www.instagram.com/rhyancalms/" class="fab fa-instagram"></a>
         </div>
         <h3>Rhyan</h3>
      </div>

      <div class="box1">
         <img src="images/img5.jpg" alt="">
         <div class="share">
            <a href="https://www.facebook.com/carloaintlucky" class="fab fa-facebook-f"></a>
            <a href="https://www.instagram.com/carlosamaaa/" class="fab fa-instagram"></a>
         </div>
         <h3>Carlo</h3>
      </div>

   </div>

</section>

<?php include 'footer.php'; ?>

<!-- custom js file link  -->
<script src="js/script.js"></script>

</body>
</html>
