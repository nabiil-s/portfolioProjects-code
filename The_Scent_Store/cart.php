<?php

    $servername = "localhost";
    $username = "root";
    $password = "root";
    $dbname = "productDB";

    // Create connection
    $conn = mysqli_connect($servername, $username, $password, $dbname);

    // Check connection
    if (!$conn) {
        die("Connection failed: " . mysqli_connect_error());
    }

?>

<!DOCTYPE html>
<html lang="en">

<head>

  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" href="style.css">
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
     
    </aside>


    <main>

      <h3 class="cartHeader">The Cart</h3>
      
      <?php

        // Fetch cart items
        $sql = "SELECT perfume_id, name, price, quantity FROM cart";
        $result = mysqli_query($conn, $sql);

        $totalQuantity = 0;  // Initialize total quantity

        // Display and create table for Cart 
        if (mysqli_num_rows($result) > 0) {
            $totalAmount = 0;
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th><th>Price</th><th>Quantity</th><th>Total</th></tr>';
            while ($row = mysqli_fetch_assoc($result)) {
                $total = $row["price"] * $row["quantity"];
                $totalAmount += $total;
                $totalQuantity += $row["quantity"];  // Add item quantity to total quantity
                echo "<tr>
                        <td>{$row['perfume_id']}</td>
                        <td>{$row['name']}</td>
                        <td>£{$row['price']}</td>
                        <td>{$row['quantity']}</td>
                        <td>£{$total}</td>
                      </tr>";
            }
            echo "<tr>
                    <td colspan='3'>Total Amount</td>
                    <td id='totalQuantity'>{$totalQuantity}</td>
                    <td>£{$totalAmount}</td>
                  </tr>";
            echo '</table>';



        } else {
            // Layout if cart is empty
            echo "<div class='emptyBasket-full'><p>There are no items in the basket. Please proceed to <a class='basketLink' href='index.php'>shop</a></p></div>";

            echo "<div class='emptyBasket-mobile'><p>No items in basket <br><br> <a class='basketLink' href='index.php'>Buy Now</a></p></div>";
        }

      ?>
     
    </main>


  <footer>
    <p style="font-size: 17px;">&copy; 2023 - <?php echo date("Y");?> Nabil Sabar. All Rights Reserved.</p>
  </footer>

  </div>

</body>
</html>

