-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Jun 16, 2023 at 12:31 PM
-- Server version: 10.4.27-MariaDB
-- PHP Version: 8.1.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `it221_pit`
--

DELIMITER $$
--
-- Procedures
--
CREATE DEFINER=`root`@`localhost` PROCEDURE `UpdateSession` (IN `customerId` INT, IN `loginTime` DATETIME, IN `logoutTime` DATETIME)   BEGIN
    -- Check if an existing session record exists for the user
    IF EXISTS (SELECT 1 FROM audittrail WHERE customer_id = customerId) THEN
        -- Update the existing session record
        UPDATE audittrail SET login_time = loginTime, logout_time = logoutTime
        WHERE customer_id = customerId;
    ELSE
        -- Insert a new session record
        INSERT INTO audittrail (customer_id, login_time, logout_time)
        VALUES (customerId, loginTime, logoutTime);
    END IF;
END$$

DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `audittrail`
--

CREATE TABLE `audittrail` (
  `session_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `login_time` datetime NOT NULL,
  `logout_time` datetime DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `audittrail`
--

INSERT INTO `audittrail` (`session_id`, `customer_id`, `login_time`, `logout_time`) VALUES
(7, 12, '2023-06-16 04:39:16', '0000-00-00 00:00:00'),
(8, 14, '2023-06-16 15:34:59', '0000-00-00 00:00:00'),
(9, 16, '2023-06-16 02:36:06', '0000-00-00 00:00:00');

-- --------------------------------------------------------

--
-- Table structure for table `carts`
--

CREATE TABLE `carts` (
  `cart_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `category`
--

CREATE TABLE `category` (
  `category_id` int(11) NOT NULL,
  `category_name` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `category`
--

INSERT INTO `category` (`category_id`, `category_name`) VALUES
(1, 'adidas'),
(2, 'new-balance'),
(3, 'nike'),
(4, 'vans');

-- --------------------------------------------------------

--
-- Table structure for table `customers`
--

CREATE TABLE `customers` (
  `customer_id` int(11) NOT NULL,
  `customer_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `shipping_address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `customers`
--

INSERT INTO `customers` (`customer_id`, `customer_name`, `email`, `shipping_address`, `password`, `contact_number`) VALUES
(12, 'traviscott', 'cidd@gmail.com', 'Cithomes', '$2y$10$zvj60xrj6y03xiPFLLeHPOvj09x/aL8KGBTf.9VDuYKDv9ny1M7ny', '093564512357'),
(14, 'Carlo Dominic', 'user@gmail.com', '0909', '$2y$10$JB0Lvfao2HQIRLUcuK911.7IKV4UXLv./.fq5EeoZGH2xErsc6Y6i', 'Zone 4, Taboc, Opol,'),
(16, 'rhyan calam', 'calms@gmail.com', 'conso', '$2y$10$ZYaqRXPx7GT/rC9x4GqPrOVWVyVjn6rM314lLBvhtQAXFrg5VqZm6', '0932312434');

-- --------------------------------------------------------

--
-- Table structure for table `discounts`
--

CREATE TABLE `discounts` (
  `discount_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `discount_amount` decimal(10,2) NOT NULL,
  `start_date` date NOT NULL,
  `end_date` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `discounts`
--

INSERT INTO `discounts` (`discount_id`, `product_id`, `discount_amount`, `start_date`, `end_date`) VALUES
(12, 31, '100.00', '2023-06-16', '2023-06-17'),
(13, 16, '50.00', '2023-06-16', '2023-06-17'),
(14, 25, '30.00', '2023-06-16', '2023-06-18');

--
-- Triggers `discounts`
--
DELIMITER $$
CREATE TRIGGER `update_discounted_price` AFTER INSERT ON `discounts` FOR EACH ROW BEGIN
  DECLARE original_price DECIMAL(10,2);
  DECLARE discounted_price DECIMAL(10,2);

  -- Retrieve the original price of the product
  SELECT price INTO original_price FROM products WHERE product_id = NEW.product_id;

  -- Calculate the discounted price
  SET discounted_price = original_price - NEW.discount_amount;

  -- Update the discounted_price in the products table
  UPDATE products SET discounted_price = discounted_price WHERE product_id = NEW.product_id;
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `inventory`
--

CREATE TABLE `inventory` (
  `product_id` int(11) NOT NULL,
  `quantity` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `orders`
--

CREATE TABLE `orders` (
  `order_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `product_id` int(11) DEFAULT NULL,
  `order_date` date NOT NULL,
  `order_quantity` int(11) DEFAULT NULL,
  `total_amount` decimal(10,2) NOT NULL,
  `status` varchar(50) DEFAULT 'processing'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `orders`
--

INSERT INTO `orders` (`order_id`, `customer_id`, `product_id`, `order_date`, `order_quantity`, `total_amount`, `status`) VALUES
(40, 16, 5, '2023-06-16', 1, '200.00', 'shipped'),
(63, 14, 16, '2023-06-16', 2, '130.00', 'completed'),
(64, 14, 16, '2023-06-16', 2, '260.00', 'processing'),
(65, 14, 25, '2023-06-16', 1, '250.00', 'processing');

--
-- Triggers `orders`
--
DELIMITER $$
CREATE TRIGGER `orders_after_insert` AFTER INSERT ON `orders` FOR EACH ROW BEGIN
    INSERT INTO payments (order_id, amount)
    VALUES (NEW.order_id, NEW.total_amount);
END
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `payment_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `payment_date` date DEFAULT NULL,
  `amount` decimal(10,2) NOT NULL,
  `payment_method` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`payment_id`, `order_id`, `payment_date`, `amount`, `payment_method`) VALUES
(23, 63, '2023-06-16', '130.00', 'cash-on-delivery'),
(24, 64, NULL, '260.00', NULL),
(25, 65, '2023-06-16', '250.00', 'cash-on-delivery');

-- --------------------------------------------------------

--
-- Table structure for table `products`
--

CREATE TABLE `products` (
  `product_id` int(11) NOT NULL,
  `category_id` int(11) NOT NULL,
  `supplier_id` int(11) NOT NULL,
  `name` varchar(100) NOT NULL,
  `price` decimal(10,2) NOT NULL,
  `image` varchar(100) NOT NULL,
  `discounted_price` decimal(10,2) DEFAULT 0.00,
  `description` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `products`
--

INSERT INTO `products` (`product_id`, `category_id`, `supplier_id`, `name`, `price`, `image`, `discounted_price`, `description`) VALUES
(16, 1, 5, 'ALPHABOOST V1', '130.00', 'ALPHABOOST V1.png', '80.00', 'Step up your weekend running game in these adidas Alphaboost shoes. Lace them up and hit the track, the local park or the treadmill. A Bounce midsole makes the ride soft and springy. BOOST on the heel returns energy as you go, so you always have something left in the tank for the end of your run. Made with a series of recycled materials, this upper features at least 50% recycled content. This product represents just one of our solutions to help end plastic waste.'),
(17, 1, 5, 'NMD S1', '150.00', 'NMD S1.png', '0.00', 'Take the innovation of cutting-edge running shoes and the effortlessness of everyday wear, and you get adidas NMD shoes — sport built for life. This edition has all the details to make them a breeze to wear, from the BOOST midsole that steps with your stride to the soft lining that hugs your feet. A smooth leather upper is durable and easy to clean, and rubber outsoles grip the ground beneath you.'),
(18, 1, 5, 'NMD S2', '140.00', 'NMD S2.png', '0.00', 'These adidas shoes keep you firmly planted, even though they feel like you\'re walking on clouds. A sock-like upper hugs your feet for ultimate comfort while encapsulated BOOST cushioning with midsole plugs returns energy with every step. This look is all about embracing what grounds you, even in the ever-changing city. Designed to inspire you to experience more, they\'re get-out-there-and-go ready.'),
(19, 1, 5, 'X_PLRPHASE', '135.00', 'X_PLRPHASE.png', '0.00', 'Let the super-soft cushioning in these adidas shoes be your reminder get some fresh air. Find a park. Walk the long route back to the office. These shoes were designed for you to move around and feel good. The BOOST and Bounce midsole makes every step feel effortless.'),
(20, 2, 5, '574', '135.00', '574.png', '0.00', '‘The most New Balance shoe ever’ says it all, right? No, actually. The 574 might be our unlikeliest icon. The 574 was built to be a reliable shoe that could do a lot of different things well rather than as a platform for revolutionary technology, or as a premium materials showcase. This unassuming, unpretentious versatility is exactly what launched the 574 into the ranks of all-time greats. As hybrid road/trail design built on a wider last than the previous generation’s narrow racing silhouettes, the 574 offered a uniquely versatile mix of new, different, uncomplicated, rugged, durable, and comfortable that was adopted as a closet staple across the globe. That’s why today, the 574 is synonymous with the boundary defying New Balance style, and worn by anyone.'),
(21, 2, 5, 'Fresh Foam Arishi ', '110.00', 'Fresh Foam Arishi v4 WARISLT4.png', '0.00', 'Fresh Foam X midsole foam with approximately 3% bio-based content delivers our most cushioned Fresh Foam experience for incredible comfort. Bio-based content is made from renewable resources to help reduce our carbon footprint.'),
(22, 2, 5, 'MS237SE', '130.00', 'MS237SE.png', '0.00', 'The 237 is a contemporary reinterpretation of the 70s running heritage that melds a range of heritage design inspirations into one deceptively simple, exceptionally versatile, contemporary silhouette. An EVA midsole, herringbone outsole, and streamlined upper offer a classic launching point for modern flourishes like oversized N branding, extended midsole length, and gator-inspired foxing and mudguard. You could call the cohesion of the 237s familiar feel and exaggerated updates the simplest way to make a statement.'),
(23, 2, 5, 'Naked Copenhagen', '145.00', 'Naked Copenhagen U574CA2.png', '0.00', 'Lightweight EVA foam cushioning in the midsole and heel increases comfort, ENCAP midsole cushioning combines lightweight foam with a durable polyurethane rim to deliver all-day support, Suede and mesh upper.'),
(24, 3, 5, 'Air Jordan 1 Low SE', '180.00', 'Air Jordan 1 Low SE.png', '0.00', 'Always fresh and never out of style, the Air Jordan 1 Low SE is one of the most iconic sneakers of all time. It looks to natural landscapes for inspiration with its earth tones and canvas details for a grounding refresh to a soaring legend. Encapsulated Air-Sole unit and foam midsole cushion every step. Low-cut, cushioned collar creates a comfortable fit around your ankle. Rubber sole gives you durable traction.'),
(25, 3, 5, 'Lebron XX', '250.00', 'Lebron 20.png', '220.00', 'Whether you\'re a dunk-competition-like leaper or a below-the-rim wonder roaming the baseline, feel faster, lower to the court and assured in the LeBron XX. We specifically tailored it to meet the demands of today\'s fast-paced game so that you can stay ahead of the opposition with your speed and force in all directions.'),
(26, 3, 5, 'Nike Cortez', '150.00', 'Nike Cortez.png', '0.00', 'The Nike Classic Cortez Shoe is Nike\'s original running shoe, designed by Bill Bowerman and released in 1972. This version features a leather and synthetic leather construction for added durability.'),
(27, 3, 5, 'Nike Dunk Low Retro Premium', '175.00', 'Nike Dunk Low Retro Premium.png', '0.00', 'From backboards to skateboards, the Dunk Low is your emblem of tried and tested. Crafted with crisp synthetic underlays and premium leather overlays, it refines the wardrobe staple with on-the-ball seasonal flair. Foam cushioning and a padded, low-cut collar let you take your game anywhere—in comfort.'),
(28, 4, 5, 'BMX OLD SKOOL GRADIENT ', '140.00', 'BMX OLD SKOOL GRADIENT SHOE.png', '0.00', 'The iconic Vans-style upper with DURACAP™ reinforcement, on top of our WAFFLECUP™ BMX outsole, gives the BMX Old Skool maximum pedalfeel and support with timeless style. Inside the shoe, POPCUSH™ energy return sockliners offer superior cushioning and impact protection. Add in our Van Doren Factory Pedal Recipe for a specifically formulated gum rubber compound to enhance pedal grip and durability, and you’ve got unrivaled BMX innovation to support your progression. This iconic low top BMX shoe is made with sturdy suede and 10 oz canvas uppers featuring colorful gradient details.\r\n\r\n'),
(29, 4, 5, 'SLIP-ON 138 SIDESTRIPE', '100.00', 'CLASSIC SLIP-ON 138 SIDESTRIPE.png', '0.00', 'The Classic Slip-On has always been one of the most iconic shoes from Vans, but it’s never been paired with our signature Sidestripe—until now. The Sidestripe Classic Slip-On 138 brings our heritage Slip-On shoe to the next level with rubber toe caps and raised embroidery, drawing Sidestripes exactly where you’d find them on our famous Sk8-Hi and Old Skool shoes. It’s the perfect intersection of everything you love from Vans. You can’t get more Off The Wall than this.'),
(30, 4, 5, 'CLASSIC SLIP-ON', '90.00', 'CLASSIC SLIP-ON.png', '0.00', 'Sleek, easy, and effortlessly stylish. Vans White Slip-On shoes are the ultimate get-up-and-go footwear. The low-profile Slip-On canvas upper offers unbeatable convenience, while the clean design makes this all-white Slip-On the perfect choice for anyone with places to go and things to do. One of the most popular designs, Vans’ Classic Slip-On shoes are the perfect middle ground between style and convenience.'),
(31, 4, 5, 'OLD SKOOL VR3', '110.00', 'OLD SKOOL VR3.png', '10.00', 'This season, some of our most iconic Classics have been rebuilt with purposeful choices about the materials we use. Along with uppers made from suede and organic cotton canvas, the Old Skool VR3 utilizes a biobased foam VR3Cush™ footbed for comfort you can feel good about and the new VR3Waffle™ outsole that uses natural rubber while still maintaining the grip and durability that Vans has been known for since ’66.');

-- --------------------------------------------------------

--
-- Table structure for table `reviews`
--

CREATE TABLE `reviews` (
  `review_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `customer_id` int(11) NOT NULL,
  `review_text` text DEFAULT NULL,
  `rating` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `reviews`
--

INSERT INTO `reviews` (`review_id`, `product_id`, `customer_id`, `review_text`, `rating`) VALUES
(1, 16, 12, 'sheeesh', 5),
(2, 25, 12, 'wow', 5),
(3, 31, 12, 'sheeeshhhh', 4),
(4, 17, 12, ':<', 1),
(5, 16, 14, '5 star I like it', 5);

-- --------------------------------------------------------

--
-- Table structure for table `shipping`
--

CREATE TABLE `shipping` (
  `shipping_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  `shipping_date` date NOT NULL,
  `address` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `shipping`
--

INSERT INTO `shipping` (`shipping_id`, `order_id`, `shipping_date`, `address`) VALUES
(16, 40, '2023-06-16', 'conso'),
(17, 40, '2023-06-16', 'conso'),
(29, 63, '2023-06-16', '0909');

-- --------------------------------------------------------

--
-- Table structure for table `suppliers`
--

CREATE TABLE `suppliers` (
  `supplier_id` int(11) NOT NULL,
  `supplier_name` varchar(255) NOT NULL,
  `supplier_address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `contact_number` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `suppliers`
--

INSERT INTO `suppliers` (`supplier_id`, `supplier_name`, `supplier_address`, `password`, `email`, `contact_number`) VALUES
(5, 'neczar', 'cdo', '$2y$10$h6ZLvq36Jpt6E1o.Dydvp.P8RGSiNl1rW9cIq5jARXt1MqTK/J9L2', 'balagulan.neczar@gmail.com', '09356123986'),
(7, 'admin carlo', 'Secret', '$2y$10$eb66AC1Zfb22JtH6O9tjhu87D2KOuEULdHCXYJzjQGk8QXCmoJ.Fu', 'admin2@gmail.com', '090909009'),
(8, 'Alyssa', 'Citihomes', '$2y$10$7s/.EYFI6GXbwclXST5TReoLDr.lZaTfQIeH.fBNuFDudPdh.ZDVC', 'elara@gmail.com', '213543534');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `audittrail`
--
ALTER TABLE `audittrail`
  ADD PRIMARY KEY (`session_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `carts`
--
ALTER TABLE `carts`
  ADD PRIMARY KEY (`cart_id`),
  ADD KEY `fk_carts_customers` (`customer_id`),
  ADD KEY `fk_carts_products` (`product_id`);

--
-- Indexes for table `category`
--
ALTER TABLE `category`
  ADD PRIMARY KEY (`category_id`);

--
-- Indexes for table `customers`
--
ALTER TABLE `customers`
  ADD PRIMARY KEY (`customer_id`);

--
-- Indexes for table `discounts`
--
ALTER TABLE `discounts`
  ADD PRIMARY KEY (`discount_id`),
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `inventory`
--
ALTER TABLE `inventory`
  ADD KEY `product_id` (`product_id`);

--
-- Indexes for table `orders`
--
ALTER TABLE `orders`
  ADD PRIMARY KEY (`order_id`),
  ADD KEY `fk_orders_customers` (`customer_id`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`payment_id`),
  ADD KEY `fk_payments_orders` (`order_id`);

--
-- Indexes for table `products`
--
ALTER TABLE `products`
  ADD PRIMARY KEY (`product_id`),
  ADD KEY `fk_products_category` (`category_id`),
  ADD KEY `fk_products_suppliers` (`supplier_id`);

--
-- Indexes for table `reviews`
--
ALTER TABLE `reviews`
  ADD PRIMARY KEY (`review_id`),
  ADD KEY `product_id` (`product_id`),
  ADD KEY `customer_id` (`customer_id`);

--
-- Indexes for table `shipping`
--
ALTER TABLE `shipping`
  ADD PRIMARY KEY (`shipping_id`),
  ADD KEY `fk_shipping_orders` (`order_id`);

--
-- Indexes for table `suppliers`
--
ALTER TABLE `suppliers`
  ADD PRIMARY KEY (`supplier_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `audittrail`
--
ALTER TABLE `audittrail`
  MODIFY `session_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `carts`
--
ALTER TABLE `carts`
  MODIFY `cart_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `customers`
--
ALTER TABLE `customers`
  MODIFY `customer_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=17;

--
-- AUTO_INCREMENT for table `discounts`
--
ALTER TABLE `discounts`
  MODIFY `discount_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `orders`
--
ALTER TABLE `orders`
  MODIFY `order_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=66;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `payment_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=26;

--
-- AUTO_INCREMENT for table `products`
--
ALTER TABLE `products`
  MODIFY `product_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=32;

--
-- AUTO_INCREMENT for table `reviews`
--
ALTER TABLE `reviews`
  MODIFY `review_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `shipping`
--
ALTER TABLE `shipping`
  MODIFY `shipping_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `suppliers`
--
ALTER TABLE `suppliers`
  MODIFY `supplier_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `audittrail`
--
ALTER TABLE `audittrail`
  ADD CONSTRAINT `audittrail_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`) ON DELETE CASCADE;

--
-- Constraints for table `carts`
--
ALTER TABLE `carts`
  ADD CONSTRAINT `carts_ibfk_1` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `carts_ibfk_2` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`),
  ADD CONSTRAINT `fk_carts_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `fk_carts_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `discounts`
--
ALTER TABLE `discounts`
  ADD CONSTRAINT `discounts_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `inventory`
--
ALTER TABLE `inventory`
  ADD CONSTRAINT `inventory_ibfk_1` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `orders`
--
ALTER TABLE `orders`
  ADD CONSTRAINT `fk_orders_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`);

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `fk_payments_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;

--
-- Constraints for table `products`
--
ALTER TABLE `products`
  ADD CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `category` (`category_id`) ON DELETE CASCADE,
  ADD CONSTRAINT `fk_products_suppliers` FOREIGN KEY (`supplier_id`) REFERENCES `suppliers` (`supplier_id`) ON DELETE CASCADE;

--
-- Constraints for table `reviews`
--
ALTER TABLE `reviews`
  ADD CONSTRAINT `fk_reviews_customers` FOREIGN KEY (`customer_id`) REFERENCES `customers` (`customer_id`),
  ADD CONSTRAINT `fk_reviews_products` FOREIGN KEY (`product_id`) REFERENCES `products` (`product_id`);

--
-- Constraints for table `shipping`
--
ALTER TABLE `shipping`
  ADD CONSTRAINT `fk_shipping_orders` FOREIGN KEY (`order_id`) REFERENCES `orders` (`order_id`) ON DELETE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
