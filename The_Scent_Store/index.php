<?php 

// Establish Connection
include 'createDatabase.php'; 

// Linking perfume and stocks table
$sql_insert_stocks = "
    INSERT INTO stocks (perfume_id, name)
    SELECT p.identifier, p.name
    FROM perfume p
    LEFT JOIN stocks s ON p.identifier = s.perfume_id
    WHERE s.perfume_id IS NULL
";

$add_stock = mysqli_query($conn, $sql_insert_stocks);

if (!$add_stock) {
    die("Error inserting records into stocks table: " . mysqli_error($conn));
}


// Handle add to cart form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_to_cart'])) {
    // Start the session if not already started
    session_start();

    $productId = mysqli_real_escape_string($conn, $_POST['product_id']);
    $productName = mysqli_real_escape_string($conn, $_POST['product_name']);
    $productImage = mysqli_real_escape_string($conn, $_POST['product_image']);
    $productPrice = mysqli_real_escape_string($conn, $_POST['product_price']);
    $quantity = 1;

    // Check current stock level
    $stock_check_query = "SELECT stock_quantity FROM stocks WHERE perfume_id='$productId'";
    $stock_result = mysqli_query($conn, $stock_check_query);
    $stock_row = mysqli_fetch_assoc($stock_result);

    if ($stock_row['stock_quantity'] > 0) {

        // Check if the product is already in the cart
        $check_query = "SELECT quantity FROM cart WHERE perfume_id='$productId'";
        $result = mysqli_query($conn, $check_query);

        if (mysqli_num_rows($result) > 0) {
            // If the product is already in the cart, update the quantity
            $row = mysqli_fetch_assoc($result);
            $new_quantity = $row['quantity'] + $quantity;
            $update_query = "UPDATE cart SET quantity=$new_quantity WHERE perfume_id='$productId'";
            if (!mysqli_query($conn, $update_query)) {
                echo "Error updating record: " . mysqli_error($conn);
            }
        } else {

            // If the product is not in the cart, insert a new record
            $sql = "INSERT INTO cart (perfume_id, name, price, image, quantity) VALUES ('$productId', '$productName', '$productPrice', '$productImage', '$quantity')";
            if (!mysqli_query($conn, $sql)) {
                echo "Error: " . mysqli_error($conn);
            }
        }

        // Reduce the stock quantity by 1 if it's greater than 0
        $reduce_stock_query = "UPDATE stocks SET stock_quantity = GREATEST(stock_quantity - 1, 0) WHERE perfume_id = '$productId'";
        if (!mysqli_query($conn, $reduce_stock_query)) {
            echo "Error updating stock: " . mysqli_error($conn);
        }
    } else {
        echo "<div class='out-of-stock'>No Stock Available</div>";
    }
}

// Fetch products data from the database
$sql = "SELECT p.identifier, p.name, p.image, p.price, s.stock_quantity 
        FROM perfume p
        LEFT JOIN stocks s ON p.identifier = s.perfume_id";

$result = mysqli_query($conn, $sql);


?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
  <!-- Shopping Cart Icon Link -->
  <script src="https://kit.fontawesome.com/e7cb8161f5.js" crossorigin="anonymous"></script>
  <title>The Scent</title>

  <link rel="icon" type="image/png" href="img/perfume2.png">

</head>

<body>

  <div id="container">

    <header>
      <p id="logo"><a href="index.php">The Scent</a></p>
      <nav>
        <ul>
          <li><a href="index.php" class="special-nav">
            <span class="home-nav home-width">Home</span>
          </a></li>
        </ul>
      </nav>
    </header>

    <aside id="aside">
      <!-- Shopping Cart Icon -->
      <a href="cart.php" class="cart"> <i class="fa-solid fa-cart-shopping"></i><span class="itemNum"><sup></sup></span></a>
    </aside>

    <main>

        <?php

            // Initialize the search variable
            $search = '';

            // Check if form is submitted
            if ($_SERVER['REQUEST_METHOD'] === 'POST') {
                if (isset($_POST['reset'])) {
                    // If reset button is clicked, clear the search term
                    $search = '';
                } elseif (isset($_POST['search'])) {
                    // Sanitize and assign search term
                    $search = mysqli_real_escape_string($conn, $_POST['search']);
                }
            }
        ?>

        <form method="post" class="search" action="">

            <input type="text" name="search" class="searchInput" value="<?php echo htmlspecialchars($search); ?>" placeholder="Search Perfume...">
            <button type="submit" class="searchBtn">Search</button>

            <!-- Reset button displays if search bar not empty -->
            <?php if (!empty($search)): ?>
                <button type="submit" name="reset" id="resetBtn" class="resetBtn">Reset</button>
            <?php endif; ?>

        </form>

        <?php

        // Perform the appropriate query based on whether there is a search term
        if (!empty($search)) {
            $sql = "SELECT p.identifier, p.name, p.image, p.price, s.stock_quantity 
                    FROM perfume p
                    LEFT JOIN stocks s ON p.identifier = s.perfume_id
                    WHERE p.name LIKE '%$search%'";
        } else {
            $sql = "SELECT p.identifier, p.name, p.image, p.price, s.stock_quantity 
                    FROM perfume p
                    LEFT JOIN stocks s ON p.identifier = s.perfume_id";
        }

        $result = mysqli_query($conn, $sql);

        if (!$result) {
            die("Error fetching data: " . mysqli_error($conn));
        }

        ?>

        <p class="greetingFull"> Welcome To The Scent Store. We only supply the BEST fragrances! </p>
        <p class="greetingMobile"> Welcome To The Scent Store! </p>

        <div class="image-container">

            <?php

            if (mysqli_num_rows($result) > 0) {

                //Loop to display perfumes from MySQL database
                while ($row = mysqli_fetch_assoc($result)) {

                    echo "<div class='product' id='product" . $row["identifier"] . "'>";
                    echo "<h2 class='perfumeTitle'>" . htmlspecialchars($row["name"]) . "</h2>";
                    echo "<img src='" . htmlspecialchars($row["image"]) . "' alt='" . htmlspecialchars($row["name"]) . "'>";
                    echo "<div class='perfumeSum'>Eau de Parfum Spray</div>";
                    echo "<div class='perfumeSum'>£" . htmlspecialchars($row["price"]) . " - 100ml</div>";

                    $perfumeId = $row["identifier"];
                    $stockQuery = "SELECT stock_quantity FROM stocks WHERE perfume_id = $perfumeId";
                    $stockResult = mysqli_query($conn, $stockQuery);
                    $stockRow = mysqli_fetch_assoc($stockResult);
                    $stockQuantity = $stockRow ? $stockRow["stock_quantity"] : 0;

                    // Display stocks
                    if ($stockQuantity > 0) {
                        echo "<div class='stock'>In Stock: " . $stockQuantity . "</div>";
                    } else {
                        echo "<div class='stock out-of-stock'>Out Of Stock</div>";
                    }

                    // Form and button
                    echo "<form method='post' action=''>";
                    echo "<input type='hidden' name='product_id' value='" . $row["identifier"] . "'>";
                    echo "<input type='hidden' name='product_name' value='" . htmlspecialchars($row["name"]) . "'>";
                    echo "<input type='hidden' name='product_image' value='" . htmlspecialchars($row["image"]) . "'>";
                    echo "<input type='hidden' name='product_price' value='" . htmlspecialchars($row["price"]) . "'>";
                    echo "<button type='submit' name='add_to_cart' class='buyNow'" . ($stockQuantity > 0 ? "" : " disabled") . ">Add To Cart</button>";
                    echo "</form>";

                    echo "</div>"; 
                }

            } else {
                echo "No results found";
            }

            ?>

        </div>

    </main>

    <footer>
      <p style="font-size: 17px;">&copy; 2023 - <?php echo date("Y"); ?> Nabil Sabar. All Rights Reserved. See My <a class="footer-link" href="../Master_portfolio/indexDark.html">Portfolio</a></p>
    </footer>

  </div>

  <script>

        //Resest button functionality for search bar
        document.getElementById('resetBtn').addEventListener('click', function() {

            document.querySelector('.searchInput').value = '';
            document.querySelector('.search').submit();

        });

  </script>

</body>

</html>
