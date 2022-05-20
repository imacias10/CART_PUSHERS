<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Add Employee</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Cart_Pushers - Add Employee</h1>

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
  $query = $db->prepare("SELECT * FROM Department GROUP BY DptID");
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
  <form action="add_employee.php" method="POST">
    <h3>Add Employee:</h3>
    <label>Enter Employee ID: <input type="number" name="employee_id"></label><br>

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
    <label>Enter Starting Date: <input type="date" name="start_date"></label><br>
    <label>Enter DOB: <input type="date" name="DOB"></label><br>
    <label>Full Time?: <input type="text" name="full_time"></label><br>
    <label>First Name: <input type="text" name="first_name"></label><br>
    <label>Middle Initial: <input type="text" name="MI"></label><br>
    <label>Last Name: <input type="text" name="last_name"></label><br>
    <label>Phone Number: <input type="number" name="phone_number" min="0000000000" max ="9999999999"></label><br>
    <label>Address: <input type="text" name="address"></label><br>
    <label>Store ID: <input type="number" name="store_id"></label><br>
    <input type="submit" name="submit" value="SUBMIT">
  </form>

  <?php
  if (isset($_POST["employee_id"],$_POST["dpt_id"],$_POST["start_date"],$_POST["DOB"],$_POST["full_time"],$_POST["first_name"],$_POST["MI"],$_POST["last_name"],$_POST["phone_number"],$_POST["address"],$_POST["store_id"],$_POST["submit"])) {
    unset($_POST["submit"]);
    $employee_id =$_POST["employee_id"];
    $dpt_id = $_POST["dpt_id"];
    $start_date =$_POST["start_date"];
    $DOB = $_POST["DOB"];
    $full_time = $_POST["full_time"];
    $first_name = $_POST["first_name"];
    $MI = $_POST["MI"];
    $last_name = $_POST["last_name"];
    $phone_number = $_POST["phone_number"];
    $address = $_POST["address"];
    $store_id = $_POST["store_id"];
    if ($employee_id == 0 or $dpt_id == 0 or $phone_number == 0 or $store_id == 0){
      echo 'Error: Invalid value. Please check your values.';
    }
    else {
      if ($prod_add_q = $db->prepare("INSERT INTO Employee VALUES(?,?,?,?,?,?,?,?,?,?,?)")){
        if ($prod_add_q->bind_param('iissssssisi', $employee_id, $dpt_id, $start_date, $DOB, $full_time, $first_name, $MI, $last_name, $phone_number, $address, $store_id)) {
          if ($prod_add_q->execute()) {
            echo 'Employee added successfully';
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


</body>
</html>
