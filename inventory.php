<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Inventory</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Cart_Pushers - Inventory</h1>

  <a href="/~imacias/form.php">Log out</a>
  <a href="/~ewarren/employee.php">Employee List</a>
  <a href="/~ewarren/add_employee.php">Add Employee</a>
  <a href="/~ewarren/stats.php">Statistics</a>
  <a href="/~ewarren/inventory.php">Inventory</a>
  <a href="/~ewarren/product.php">Product</a>

  <?php

  date_default_timezone_set('America/Los_Angeles');
  error_reporting(E_ALL);
  ini_set("log_errors", 1);
  ini_set("display_errors", 1);


  function get_connection() {
    static $connection;

    if (!isset($connection)) {
      // Connect to the cmps3420 database using username demo3420, password 3420.
      //$connection = mysqli_connect('localhost', 'hellodbuser', 'hellodbpassword','hellodb')
      $connection = mysqli_connect('localhost', 'cart_pushers', 'srehsup_trac3420S22','cart_pushers')
      or die(mysqli_connect_error());
    }
    if ($connection === false) {
      echo "Unable to connect to database<br/>";
      echo mysqli_connect_error();
    }

    return $connection;
  }
  //require_once "~/home/stu/rgrewal/config.php";

  // Get a connection, prepare a query, and execute it
  $db = get_connection();


  if ($store_q = $db->prepare("SELECT * FROM Store")) {
    if ($store_q->execute()) {
      if (mysqli_stmt_bind_result($store_q, $StoreID, $StoreName,$StoreAddress)) {
        $store_count = 0;
        while($store_q->fetch()) {
          if($store_count == 0) {
            echo '<h3>Stores:</h3>';
            echo '<table>';
            echo '<tr><th>Store ID</th><th>Store Name</th><th>Store Address</th></tr>';
          }
          echo '<tr id="store' . $StoreID . '">';
          echo '<td>' . $StoreID . '</td>';
          echo '<td>' . $StoreName . '</td>';
          echo '<td>' . $StoreAddress . '</td>';
          echo '</tr>';
          $store_count++;
        }
        if ($store_count > 0) {
          echo '</table>';
        }
        $store_q->close();
      }
    }
  }


  // Getting the results will bring the results from the database into PHP.
  // This lets you view each row as an associative array
  $query = $db->prepare("SELECT * FROM Store");
  $query->execute();
  $result = $query->get_result();

  $rows = [];

  while ($row = $result->fetch_assoc()) {
    // Do something with each row: add it to an array, render HTML, etc.
    $rows []= $row;

    // This example just iterates over the columns of the rows and builds a string
    $rowtext = "";

    foreach($row as $column) {
      $rowtext = $rowtext . "$column ";
    }

    // echo "$rowtext <br>";
  }
  ?>
  <form action="inventory.php" method="POST">
    <?php

  // Now let's build a select option dropdown from the rows
  echo "<label>Select Store:</label>";
  echo "<select name='dropdown'>";

  foreach($rows as $row) {
    $rowid = $row['StoreID'];
    $rowdata = $row['StoreName'];
    echo "<option value='$rowid'>$rowdata</option>";
  }

  echo "</select>";
  ?>

  <input type="submit" value="SUBMIT">
</form>

<form action="inventory.php" method="POST">
  <h3>Add Product to Inventory:</h3>
  <label>Enter Product ID: <input type="number" name="product_id"></label><br>
  <label>Enter Quantity: <input type="number" name="quantity"></label><br>

  <?php

  // Now let's build a select option dropdown from the rows
  echo "<label>Select Store:</label>";
  echo "<select name='store_id'>";

  foreach($rows as $row) {
    $rowid = $row['StoreID'];
    $rowdata = $row['StoreName'];
    echo "<option value='$rowid'>$rowdata</option>";
  }

  echo "</select><br>";
  ?>
  <input type="submit" name="submit" value="SUBMIT PRODUCT">
</form>

<?php
if (isset($_POST["product_id"],$_POST["quantity"],$_POST["store_id"],$_POST["submit"])) {
  unset($_POST["submit"]);
  $product_id = $_POST["product_id"];
  $quantity = $_POST["quantity"];
  $store_id = $_POST["store_id"];
  if ($product_id == 0 or $quantity <= 0 or $store_id == 0){
    echo 'Error: Invalid value. Please check your values.';
  }
  else {
    if ($prod_add_q = $db->prepare("UPDATE Inventory SET Quantity = Quantity + ? WHERE StoreID =? AND ProductID=?")){
      if ($prod_add_q->bind_param('iii', $quantity, $store_id, $product_id)) {
        if ($prod_add_q->execute()) {
          echo 'Products added successfully';
          unset($_POST["product_id"],$_POST["quantity"],$_POST["store_id"]);
        }
        else {
          echo 'Error: ' . mysqli_error($db);
        }
      }
    }
    else {
      echo mysqli_error($db);
    }
  }
}

if (isset($_POST["dropdown"])) {
  $store_id = $_POST["dropdown"];
  if($inv_q = $db->prepare("SELECT Quantity, ProductID, ProductName, Price FROM Inventory NATURAL JOIN Product WHERE StoreID =?")) {
    if ($inv_q->bind_param('i', $store_id)) {
      if ($inv_q->execute()) {
        if (mysqli_stmt_bind_result($inv_q, $quantity, $product_id, $product_name, $price)) {
          $inv_num = 0;
          $total_quantity = 0;
          while($inv_q->fetch()) {
            if($inv_num == 0) {
              echo '<h3>All Products:</h3>';
              echo '<table>';
              echo '<tr><th>Product ID</th><th>Product Name</th><th>Price</th><th>Quantity</th></tr>';
            }
            echo '<tr id="product' . $product_id . '">';
            echo '<td>' . $product_id . '</td>';
            echo '<td>' . $product_name . '</td>';
            echo '<td>' . '$'. $price . '</td>';
            echo '<td>' . $quantity . '</td>';
            echo '</tr>';
            $total_quantity += $quantity;
            $inv_num++;
          }
          if ($inv_num > 0) {
            echo $inv_num . " product(s) found for store " . $store_id . '.<br>';
            echo $total_quantity . " total product(s) currently in store.";
            echo '</table>';
          }
          $inv_q->close();
        }
        else {
          echo mysqli_error($db);
        }
      }
      else {
        echo mysqli_error($db);
      }
    }
  }
  else {
    echo mysqli_error($db);
  }
}
else {
  echo mysqli_error($db);
}



?>

</body>
</html>
