<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Product</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Cart_Pushers - Product</h1>

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


  if ($dept_q = $db->prepare("SELECT DptID, Name FROM Department GROUP BY DptID")) {
    if ($dept_q->execute()) {
      if (mysqli_stmt_bind_result($dept_q, $DptID, $DptName)) {
        $dept_count = 0;
        while($dept_q->fetch()) {
          if($dept_count == 0) {
            echo '<h3>Departments:</h3>';
            echo '<table>';
            echo '<tr><th>ID</th><th>Name</th></tr>';
          }
          echo '<tr id="department' . $DptID . '">';
          echo '<td>' . $DptID . '</td>';
          echo '<td>' . $DptName . '</td>';
          echo '</tr>';
          $dept_count++;
        }
        if ($dept_count > 0) {
          echo '</table>';
        }
        $dept_q->close();
      }
    }
  }


  // Getting the results will bring the results from the database into PHP.
  // This lets you view each row as an associative array
  $query = $db->prepare("SELECT DptID, Name FROM Department GROUP BY DptID");
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
  <form action="product.php" method="POST">
    <h3>Add Product:</h3>
    <label>Enter ProductID: <input type="number" name="product_id"></label><br>

    <?php

    // Now let's build a select option dropdown from the rows
    echo "<label>Select Department:</label>";
    echo "<select name='dpt_id'>";

    foreach($rows as $row) {
      $rowid = $row['DptID'];
      $rowdata = $row['Name'];
      echo "<option value='$rowid'>$rowdata</option>";
    }

    echo "</select><br>";
    ?>
    <label>Enter Price: $<input type="number" name="price"></label><br>
    <label>Enter Product Name: <input type="text" name="product_name"></label><br>
    <input type="submit" name="submit" value="SUBMIT PRODUCT">
  </form>

  <?php
  if (isset($_POST["product_id"],$_POST["dpt_id"],$_POST["price"],$_POST["product_name"],$_POST["submit"])) {
  //if (isset($_POST["submit"])) {
    unset($_POST["submit"]);
    $product_id = $_POST["product_id"];
    $dpt_id = $_POST["dpt_id"];
    $price = $_POST["price"];
    $product_name = $_POST["product_name"];
    //echo $product_id . ' ' . $dpt_id . ' ' . $price . ' ' . $product_name;
    if ($product_id == 0 or $dpt_id == 0 or $price == 0 or $product_name == ''){
      echo 'Error: Invalid value. Please check your values.';
    }
    else {
      if ($prod_add_q = $db->prepare("INSERT INTO Product VALUES(?,?,?,?)")){
        if ($prod_add_q->bind_param('iiis', $product_id, $dpt_id, $price, $product_name)) {
          if ($prod_add_q->execute()) {
            echo 'Product added successfully';
            unset($_POST["product_id"],$_POST["dpt_id"],$_POST["price"],$_POST["product_name"]);
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
?>
  <form action="product.php" method="POST">

    <h3>Remove Product:</h3>
    <input type="number" name="product_id">
    <input type="submit" name="SUBMIT" value="REMOVE PRODUCT">

    <br>

    <?php
    if(isset($_POST['SUBMIT'])) {
      unset($_POST['SUBMIT']);
      $prod_id = $_POST['product_id'];
      if($prod_id == ''){
        echo 'Error: Please insert an product ID to remove.';
      }
      else{
        if ($deletion = $db->prepare("DELETE FROM Product WHERE ProductID=?")){
          if ($deletion->bind_param('i', $prod_id)) {
            if ($deletion->execute()) {
              echo 'Product removed successfully.';
              echo '<br>';
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
    }
    ?>
  </form>

<?php
  $prod_q = $db->prepare("SELECT * FROM Product");
  if ($prod_q->execute()) {
    if (mysqli_stmt_bind_result($prod_q, $product_id, $dpt_id, $price, $product_name)) {
      $prod_num = 0;
      while($prod_q->fetch()) {
        if($prod_num == 0) {
          echo '<h3>Product List:</h3>';
          echo '<table>';
          echo '<tr><th>Product ID</th><th>Department ID</th><th>Price</th><th>Product Name</th></tr>';
        }
        echo '<tr id="product' . $product_id . '">';
        echo '<td>' . $product_id . '</td>';
        echo '<td>' . $dpt_id . '</td>';
        echo '<td>' . '$'. $price . '</td>';
        echo '<td>' . $product_name . '</td>';
        echo '</tr>';
        $prod_num++;
      }
      if ($prod_num > 0) {
        //echo $prod_num . " product(s) found.";
        echo '</table>';
      }
      $prod_q->close();
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
