<?php
include 'config.php';

session_start();

$customer_id = $_SESSION['customer_id'];

if (!isset($customer_id)) {
    header('location:login.php');
}

if (isset($_GET['product_id'])) {
    $product_id = $_GET['product_id'];
    $product_query = "SELECT * FROM products WHERE product_id = $product_id";
    $product_result = mysqli_query($conn, $product_query);
    $product = mysqli_fetch_assoc($product_result);
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
    <title>Product Details</title>

    <!-- font awesome cdn link  -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- custom css file link  -->
    <link rel="stylesheet" href="css/style.css">

    <style>
    /* Magnifier Styles */
    .magnifier {
        position: relative;
        display: inline-block;
        overflow: hidden;
    }

    .magnifier-image {
        display: block;
        background-color: transparent;
    }

    .magnifier-zoom {
        position: fixed;
        width: 200px; /* Adjust the zoom window size as needed */
        height: 200px; /* Adjust the zoom window size as needed */
        background: rgba(255, 255, 255, 0.5); /* Adjust the transparency here */
        border: 1px solid #ddd;
        pointer-events: none;
        visibility: hidden;
        overflow: hidden;
        transform: scale(1);
        transform-origin: center center;
        transition: transform 0.3s ease;
    }

    .magnifier:hover .magnifier-zoom {
        visibility: visible;
        transform: scale(2); /* Adjust the zoom level as needed */
    }

    .magnifier-zoom img {
        display: block;
        width: 100%;
        height: 100%;
        object-fit: cover;
    }
</style>

</head>

<body>

    <?php include 'header.php'; ?>

    <div class="heading">
        <h3>products</h3>
        <p><a href="home.php">home</a> / products</p>
    </div>

    <section class="view-product">
        <h1 class="title">Product Details</h1>
        <div class="whole-area">
            <?php if ($product) { ?>
                <div class="magnifier">
                    <img src="uploaded_img/<?= $product['image']; ?>" alt="Product Image" class="magnifier-image">
                    <div class="magnifier-zoom">
                        <img src="uploaded_img/<?= $product['image']; ?>" alt="Product Image">
                    </div>
                </div>
                <div class="product-details">
                    <h2><?= $product['name']; ?> | $<?= $product['price']; ?></h2>
                    <p><?= $product['description']; ?></p>
                    <div class="amount-selection">
                        <form action="" method="post" class="box">
                            <input type="number" min="1" name="product_quantity" value="1" class="qty">
                            <input type="hidden" name="product_id" value="<?php echo $product['product_id']; ?>">
                            <input type="hidden" name="product_name" value="<?php echo $product['name']; ?>">
                            <input type="hidden" name="product_price" value="<?php echo $price; ?>">
                            <input type="hidden" name="product_image" value="<?php echo $product['image']; ?>">
                            <input type="submit" value="Add to Cart" name="add_to_cart" class="btn">
                        </form>
                    </div>
                </div>

            <?php } else { ?>
                <p>Product not found.</p>
            <?php } ?>
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

    <script>
        // Magnifier JavaScript code
         const magnifier = document.querySelector('.magnifier');
         const zoom = document.querySelector('.magnifier-zoom');
         let zoomLevel = 1;

         magnifier.addEventListener('mousemove', (e) => {
            const rect = magnifier.getBoundingClientRect();
            const mouseX = e.clientX - rect.left;
            const mouseY = e.clientY - rect.top;
            const zoomX = mouseX / rect.width;
            const zoomY = mouseY / rect.height;

            zoom.style.transformOrigin = `${zoomX * 100}% ${zoomY * 100}%`;
            zoom.style.left = `${e.clientX}px`;
            zoom.style.top = `${e.clientY}px`;
         });

         magnifier.addEventListener('wheel', (e) => {
            e.preventDefault();
            const zoomAmount = e.deltaY > 0 ? -0.1 : 0.1;
            zoomLevel += zoomAmount;

            // Limit the zoom level to a range of 0.5 to 3 (adjust as needed)
            zoomLevel = Math.max(0.5, Math.min(3, zoomLevel));

            zoom.style.transform = `scale(${zoomLevel})`;
         });
    </script>

</body>

</html>
