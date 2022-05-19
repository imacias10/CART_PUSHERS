<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <title>Employee Page</title>
  <link rel="stylesheet" href="style.css">
</head>
<body>
  <h1>Cart_Pushers - Employee</h1>

  <a href="/~ewarren/stats.php">Stats</a>

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


  <form action="employee.php" method="POST">
    <!--<label>Select Store: <input type="text" name="store_select"></label>-->
    <?php
    // Now let's build a select option dropdown from the rows
    echo "<select name='dropdown'>";

    foreach($rows as $row) {
      $rowid = $row['StoreID'];
      $rowdata = $row['StoreName'];
      echo "<option value='$rowid'>$rowdata</option>";
    }

    echo "</select>";
    ?>
    <input type="submit" value="SUBMIT">
    <br>

  </form>

  <form action="employee.php" method="POST">

    <input type="number" name="employee_id">
    <input type="submit" name="SUBMIT" value="REMOVE EMPLOYEE">

    <br>

    <?php
    if(isset($_POST['SUBMIT'])) {
      unset($_POST['SUBMIT']);
      $emp_id = $_POST['employee_id'];
      if($emp_id == ''){
        echo 'Please insert an employee ID to remove.';
      }
      else{
        if ($deletion = $db->prepare("DELETE FROM Employee WHERE EmployeeID=?")){
          if ($deletion->bind_param('i', $emp_id)) {
            if ($deletion->execute()) {
              echo 'Employee removed successfully.';
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
  if (isset($_POST["dropdown"])) {
    for($i = 0; $i < count($rows); $i++) {
      if ($rows[$i]['StoreID'] == $_POST['dropdown']) {
        $store_id = $_POST['dropdown'];
        //echo "You entered " . $store_id . " <br>";
        // $query = $db->prepare("select * from Employee where StoreID =?");
        // $query->bind_param('i', $store_id);
        // $query->execute();
        //$result = $query->get_result();
        if ($employee_q = $db->prepare("SELECT * FROM Employee WHERE StoreID =?")) {
          if ($employee_q->bind_param('i', $store_id)) {
            if ($employee_q->execute()) {
              if (mysqli_stmt_bind_result($employee_q, $EmployeeID, $DptID,$StartingDate, $DOB, $FullTime, $Fname, $MI, $Lname, $PhoneNumber, $Address, $StoreID)) {
                $employee_count = 0;
                while($employee_q->fetch()) {
                  if($employee_count == 0) {
                    //echo '<h5>Stores:</h5>';
                    echo '<table>';
                    echo '<tr><th>Employee ID</th><th>Dpt ID</th><th>Starting Date</th><th>DOB</th><th>Full Time</th><th>Fname</th><th>MI</th><th>Lname</th><th>Phone Number</th><th>Address</th><th>Store ID</th></tr>';
                  }
                  echo '<tr name="employee' . $EmployeeID . '">';
                  echo '<td>' . $EmployeeID . '</td>';
                  echo '<td>' . $DptID . '</td>';
                  echo '<td>' . $StartingDate . '</td>';
                  echo '<td>' . $DOB . '</td>';
                  echo '<td>' . $FullTime . '</td>';
                  echo '<td>' . $Fname . '</td>';
                  echo '<td>' . $MI . '</td>';
                  echo '<td>' . $Lname . '</td>';
                  echo '<td>' . $PhoneNumber . '</td>';
                  echo '<td>' . $Address . '</td>';
                  echo '<td>' . $StoreID . '</td>';
                  echo '</tr>';
                  $employee_count++;
                }
                if ($employee_count > 0) {
                  echo $employee_count . " Employee(s) found.";
                  echo '</table>';
                }
                $employee_q->close();
              }
            }
          }
        }
        //Employee History
        if ($employee_history_q = $db->prepare("SELECT * FROM employee_history WHERE StoreID =?")) {
          if ($employee_history_q->bind_param('i', $store_id)) {
            if ($employee_history_q->execute()) {
              if (mysqli_stmt_bind_result($employee_history_q, $EmployeeID, $DptID, $StartingDate, $DOB, $FullTime, $Fname, $MI, $Lname, $PhoneNumber, $Address, $StoreID)) {
                $employee_count = 0;
                while($employee_history_q->fetch()) {
                  if($employee_count == 0) {
                    //echo '<h5>Stores:</h5>';
                    echo '<br>';
                    echo '<table>';
                    echo '<tr><th>EmployeeID</th><th>DptID</th><th>Starting Date</th><th>DOB</th><th>FullTime</th><th>Fname</th><th>MI</th><th>Lname</th><th>PhoneNumber</th><th>Address</th><th>StoreID</th></tr>';
                  }
                  echo '<tr id="employee' . $EmployeeID . '">';
                  echo '<td>' . $EmployeeID . '</td>';
                  echo '<td>' . $DptID . '</td>';
                  echo '<td>' . $StartingDate . '</td>';
                  echo '<td>' . $DOB . '</td>';
                  echo '<td>' . $FullTime . '</td>';
                  echo '<td>' . $Fname . '</td>';
                  echo '<td>' . $MI . '</td>';
                  echo '<td>' . $Lname . '</td>';
                  echo '<td>' . $PhoneNumber . '</td>';
                  echo '<td>' . $Address . '</td>';
                  echo '<td>' . $StoreID . '</td>';
                  echo '</tr>';
                  $employee_count++;
                }
                if ($employee_count > 0) {
                  echo $employee_count . " Former Employee(s) found.";
                  echo '</table>';
                }
                $employee_history_q->close();
              }
            }
          }
        }

      }
    }
  }


  ?>

</body>
</html>
