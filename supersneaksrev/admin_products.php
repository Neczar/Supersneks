<?php
include 'config.php';
session_start();
$supplier_id = isset($_SESSION['supplier_id']) ? $_SESSION['supplier_id'] : null;

if (!isset($supplier_id)) {
   header('location:login.php');
}

if (isset($_POST['add_product'])) {
   // Retrieve form inputs
   $name = mysqli_real_escape_string($conn, $_POST['name']);
   $price = $_POST['price'];
   $description = mysqli_real_escape_string($conn, $_POST['description']); // Added product description
   $image = $_FILES['image']['name'];
   $image_size = $_FILES['image']['size'];
   $image_tmp_name = $_FILES['image']['tmp_name'];
   $image_folder = 'uploaded_img/' . $image;
   $category_id = $_POST['category_id'];

   $select_product_name = mysqli_query($conn, "SELECT name FROM products WHERE name = '$name'") or die('Query failed');

   if (mysqli_num_rows($select_product_name) > 0) {
      $message[] = 'Product name already added';
   } else {
      $add_product_query = mysqli_query($conn, "INSERT INTO products (category_id, supplier_id, name, price, image, description) VALUES ('$category_id','$supplier_id','$name', '$price','$image','$description')") or die('Error in Adding Products!');

      if ($add_product_query) {
         if ($image_size > 2000000) {
            $message[] = 'Image size is too large';
         } else {
            if (move_uploaded_file($image_tmp_name, $image_folder)) {
               $message[] = 'Product added successfully!';
            } else {
               $message[] = 'Failed to move uploaded file. Please check file permissions and directory structure.';
            }
         }
      } else {
         $message[] = 'Product could not be added!';
      }
   }
}

if (isset($_GET['delete'])) {
   $delete_id = $_GET['delete'];

   // Delete associated records from the 'discounts' table
   mysqli_query($conn, "DELETE FROM discounts WHERE product_id = '$delete_id'") or die('Query failed');

   // Select the image path for deletion
   $delete_image_query = mysqli_query($conn, "SELECT image FROM products WHERE product_id = '$delete_id'") or die('Query failed');
   $fetch_delete_image = mysqli_fetch_assoc($delete_image_query);
   $image_path = 'uploaded_img/' . $fetch_delete_image['image'];

   // Delete the product from the 'products' table
   mysqli_query($conn, "DELETE FROM products WHERE product_id = '$delete_id'") or die('Query failed');

   // Check if the file exists before deleting it
   if (file_exists($image_path)) {
      unlink($image_path);
   }

   header('location: admin_products.php');
   exit();
}

if (isset($_POST['update_product'])) {
   // Retrieve form inputs
   $update_p_id = $_POST['update_p_id'];
   $update_name = $_POST['update_name'];
   $update_price = $_POST['update_price'];
   $update_category_id = $_POST['update_category'];
   $update_description = mysqli_real_escape_string($conn, $_POST['update_description']); // Added product description

   // Update product details in the database
   mysqli_query($conn, "UPDATE products SET category_id ='$update_category_id',name = '$update_name', price = '$update_price', description='$update_description' WHERE product_id = '$update_p_id'") or die('Query failed');
   $update_image = $_FILES['update_image']['name'];
   $update_image_tmp_name = $_FILES['update_image']['tmp_name'];
   $update_image_size = $_FILES['update_image']['size'];
   $update_folder = 'uploaded_img/' . $update_image;
   $update_old_image = $_POST['update_old_image'];

   if (!empty($update_image)) {
      if ($update_image_size > 2000000) {
         $message[] = 'Image file size is too large';
      } else {
         move_uploaded_file($update_image_tmp_name, $update_folder);
         unlink('uploaded_img/' . $update_old_image);
      }
   }
   header('location:admin_products.php');
}

if (isset($_POST['add_discount'])) {
   $product_id = $_POST['product_id'];
   $discount_amount = $_POST['discount_amount'];
   $discount_start = $_POST['discount_start'];
   $discount_end = $_POST['discount_end'];

   $add_discount_query = mysqli_query($conn, "INSERT INTO discounts (product_id, discount_amount, start_date, end_date) VALUES ('$product_id', '$discount_amount', '$discount_start', '$discount_end')") or die('Query failed');

   if ($add_discount_query) {
      $message[] = 'Discount added successfully!';
   } else {
      $message[] = 'Failed to add discount!';
   }
}

   ?>

<!DOCTYPE html>
<html lang="en">

<head>
   <meta charset="UTF-8">
   <meta http-equiv="X-UA-Compatible" content="IE=edge">
   <meta name="viewport" content="width=device-width, initial-scale=1.0">
   <title>Products</title>
   <!-- font awesome cdn link  -->
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <!-- custom admin css file link  -->
   <link rel="stylesheet" href="css/admin_style.css">

</head>

<body>
   <?php include 'admin_header.php'; ?>

   <!-- product CRUD section starts  -->
   <section class="add-products">
      <h1 class="title">Shop Products</h1>
      <form action="" method="post" enctype="multipart/form-data">
         <h3>Add Product</h3>
         <input type="text" name="name" class="box" placeholder="Enter product name" required>
         <input type="number" min="0" name="price" class="box" placeholder="Enter product price" required>
         <textarea name="description" cols="25" rows="5" class="box description" placeholder="Enter product description"></textarea>
         <select name="category_id" class="box">
            <option value="1">Adidas</option>
            <option value="2">New Balance</option>
            <option value="3">Nike</option>
            <option value="4">Vans</option>
         </select>
         <input type="file" name="image" accept="image/jpg, image/jpeg, image/png" class="box" required>
         <input type="submit" value="Add Product" name="add_product" class="btn">
      </form>
   </section>
   <!-- product CRUD section ends -->

  <!-- show products  -->
<section class="show-products">
   <div class="box-container">
      <?php
      $select_products = mysqli_query($conn, "SELECT * FROM products") or die('Query failed');
      if (mysqli_num_rows($select_products) > 0) {
         while ($fetch_products = mysqli_fetch_assoc($select_products)) {
            $price = $fetch_products['price'];
            $discounted_price = $fetch_products['discounted_price'];
            $is_discounted = !empty($discounted_price);

            
            if ($discounted_price == 0) {
               $result = '<span class="">$' . $price . '</span>';
             } else {
               $result = '<span class="original-price"><strike>$' . $price . '</strike></span> <span class="discounted-price">$' . $discounted_price . '</span>';
             }
      ?>
            <div class="box">
               <img src="uploaded_img/<?php echo $fetch_products['image']; ?>" alt="">
               <div class="name"><?php echo $fetch_products['name']; ?></div>
               <div class="price">
                  <?php echo $result; ?>
               </div>
               <a href="admin_products.php?update=<?php echo $fetch_products['product_id']; ?>" class="option-btn">Update</a>
               <a href="admin_products.php?delete=<?php echo $fetch_products['product_id']; ?>" class="delete-btn" onclick="return confirm('Delete this product?');">Delete</a>
               <a href="admin_products.php?add_discount=<?php echo $fetch_products['product_id']; ?>" class="option-btn">Add Discount</a>
            </div>
      <?php
         }
      } else {
         echo '<p class="empty">No products added yet!</p>';
      }
      ?>
   </div>
</section>

   <section class="edit-product-form">
      <?php
      if (isset($_GET['update'])) {
         $_id = $_GET['update'];
         $update_query = mysqli_query($conn, "SELECT * FROM products WHERE product_id = '$_id'") or die('Query failed');
         if (mysqli_num_rows($update_query) > 0) {
            while ($fetch_update = mysqli_fetch_assoc($update_query)) {
      ?>
               <form action="" method="post" enctype="multipart/form-data">
                  <input type="hidden" name="update_p_id" value="<?php echo $fetch_update['product_id']; ?>">
                  <input type="hidden" name="update_old_image" value="<?php echo $fetch_update['image']; ?>">
                  <img src="uploaded_img/<?php echo $fetch_update['image']; ?>" alt="">
                  <input type="text" name="update_name" value="<?php echo $fetch_update['name']; ?>" class="box" required placeholder="Enter product name">
                  <input type="number" name="update_price" value="<?php echo $fetch_update['price']; ?>" min="0" class="box" required placeholder="Enter product price">
                  <textarea name="update_description" cols="25" rows="5" class="box description" placeholder="Edit description"><?php echo $fetch_update['description']; ?></textarea>
                  <select name="update_category" class="box">
                     <option value="1" <?php if ($fetch_update['category_id'] == '1') echo 'selected'; ?>>Adidas</option>
                     <option value="2" <?php if ($fetch_update['category_id'] == '2') echo 'selected'; ?>>New Balance</option>
                     <option value="3" <?php if ($fetch_update['category_id'] == '3') echo 'selected'; ?>>Nike</option>
                     <option value="4" <?php if ($fetch_update['category_id'] == '4') echo 'selected'; ?>>Vans</option>
                  </select>
                  <input type="file" name="update_image" class="box">
                  <input type="submit" name="update_product" value="Update Product" class="option-btn">
                  <input type="button" value="Cancel" onclick="location.href='admin_products.php';" class="option-btn">
               </form>
      <?php
            }
         }
      } else {
         echo '<script>document.querySelector(".edit-product-form").style.display = "none";</script>';
      }
      ?>
   </section>

   <section class="discount-form">
   <?php
   if (isset($_GET['add_discount'])) {
      $_id = $_GET['add_discount'];
      $discount_query = mysqli_query($conn, "SELECT * FROM products WHERE product_id = '$_id'") or die('Query failed');
      if (mysqli_num_rows($discount_query) > 0) {
         $fetch_product = mysqli_fetch_assoc($discount_query);
   ?>
         <form action="" method="post">
            <input type="hidden" name="product_id" value="<?php echo $fetch_product['product_id']; ?>">
            <input type="number" name="discount_amount" min="0" max="100" class="box" required placeholder="Enter discount percentage">
            <input type="date" name="discount_start" class="box" required placeholder="Enter discount start date">
            <input type="date" name="discount_end" class="box" required placeholder="Enter discount end date">
            <input type="submit" name="add_discount" value="Add Discount" class="option-btn">
            <input type="button" value="Cancel" onclick="location.href='admin_products.php';" class="option-btn">
         </form>
   <?php
      }
   } else {
      echo '<script>document.querySelector(".discount-form").style.display = "none";</script>';
   }
   ?>
   </section>

   <!-- show products ends -->

   <!-- custom js file link  -->
   <script src="js/script.js"></script>
</body>

</html>