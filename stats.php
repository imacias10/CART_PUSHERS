<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Statistics</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Cart_Pushers - Statistics</h1>

  <a href="/~ewarren/employee.php">Employee Page</a>

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
            //echo '<h5>Stores:</h5>';
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

  <form action="stats.php" method="POST">
    <label>Enter Start Date (YYYY-MM-DD): <input type="text" name="start_date"></label>
    <label>Enter End Date (YYYY-MM-DD): <input type="text" name="end_date"></label>
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

    <input type="submit">
  </form>

  <?php
  if (isset($_POST["start_date"],$_POST["end_date"],$_POST["dropdown"])) {
    $store_id = $_POST["dropdown"];
    $start_date = $_POST["start_date"];
    $end_date = $_POST["end_date"];
    if($end_date == '' || $start_date = ''){
      echo 'Please insert value(s) for start and/or end date.';
    }
    if($stat_q = $db->prepare("CALL average_sales(?,?,?)")) {
      if ($stat_q->bind_param('ssi', $start_date,$end_date,$store_id)) {
        if ($stat_q->execute()) {
          if (mysqli_stmt_bind_result($stat_q, $avg_sales, $start_date, $end_date, $store_id)) {
              $stat_num = 0;
              while($stat_q->fetch()) {
                if (is_null($avg_sales)){
                  echo 'No sales found for Store ' . $_POST["dropdown"] . ' from ' . $start_date . ' to ' . $end_date . '.';
                  break;
                }
                if($stat_num == 0) {
                  echo '<table>';
                  echo '<tr><th>Average Sales</th><th>Start Date</th><th>End Date</th><th>Store ID</th></tr>';
                }
                echo '<tr id="sale' . $avg_sales . '">';
                echo '<td>' . '$' . $avg_sales . '</td>';
                echo '<td>' . $start_date . '</td>';
                echo '<td>' . $end_date . '</td>';
                echo '<td>' . $store_id . '</td>';
                echo '</tr>';
                $stat_num++;
              }
              if ($stat_num > 0) {
                //echo $stat_num . " stat(s) found.";
                echo '</table>';
              }
              $stat_q->close();
            }
          }
        }
      }
    }



  ?>

</body>
</html>
