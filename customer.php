<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>Customer</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
<h1>Customer Page</h1>

<?php

date_default_timezone_set('America/Los_Angeles');
error_reporting(E_ALL);
ini_set("log_errors", 1);
ini_set("display_errors", 1);


function get_connection() {
    static $connection;
    
    if (!isset($connection)) {
        $connection = mysqli_connect('localhost', 'cart_pushers', 'srehsup_trac3420S22','cart_pushers')
            or die(mysqli_connect_error());
    }
    if ($connection === false) {
        echo "Unable to connect to database<br/>";
        echo mysqli_connect_error();
    }
  
    return $connection;
}

// Get a connection, prepare a query, and execute it
$db = get_connection();


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

    echo "$rowtext <br>";
}
?>

<form action="customer.php" method="POST">

<?php
// Now let's build a select option dropdown from the rows
echo "<select name='storeselect'>";

foreach($rows as $row) {
    $rowid = $row['StoreID'];
    $rowdata = $row['StoreName'];
    echo "<option value='$rowid'>$rowdata</option>";
}
echo "</select>";
?>

<?php
echo "<select name='ascordesc'>";
  echo "<option value='Ascending'>Ascending</option>";
  echo "<option value='Descending'>Descending</option>";
echo "</select>";
?>

<?php
echo "<select name='sortby'>";
  echo "<option value='Price'>Price</option>";
  echo "<option value='Quantity'>Quantity</option>";
  echo "<option value='ProductName'>Product Name</option>";
echo "</select>";
?>

  <input type="submit">

</form>

<?php
if (isset($_POST["store_name"])) {
    echo "You entered " . htmlspecialchars($_POST['store_name']) . " <br>";
}

if (isset($_POST["storeselect"])) {
    for($i = 0; $i < count($rows); $i++) {
        if ($rows[$i]['StoreID'] == $_POST['storeselect']) {
            $store_id = $_POST['storeselect'];
            $ascordesc = $_POST['ascordesc'];
            $sortby = $_POST['sortby'];     
            $db = get_connection();
            echo "You entered " . $store_id . " <br>";
            echo "You entered " . $ascordesc . " <br>";
            echo "You entered " . $sortby . " <br>"; 
            
            if ($ascordesc == 'Ascending' && $sortby == 'Price') {
            $store_select = $db->prepare("SELECT Quantity, Price, ProductName FROM Inventory NATURAL JOIN Product WHERE StoreID = ? ORDER BY Price ASC");
            $store_select->bind_param("i", $store_id);
            $store_select->execute();
            }
            
            else if ($ascordesc == 'Descending' && $sortby == 'Price') {
            $store_select = $db->prepare("SELECT Quantity, Price, ProductName FROM Inventory NATURAL JOIN Product WHERE StoreID = ? ORDER BY Price DESC");
            $store_select->bind_param("i", $store_id);
            $store_select->execute();
            }
            
            else if ($ascordesc == 'Ascending' && $sortby == 'Quantity') {
            $store_select = $db->prepare("SELECT Quantity, Price, ProductName FROM Inventory NATURAL JOIN Product WHERE StoreID = ? ORDER BY Quantity ASC");
            $store_select->bind_param("i", $store_id);
            $store_select->execute();
            }
            
            else if ($ascordesc == 'Descending' && $sortby == 'Quantity') {
            $store_select = $db->prepare("SELECT Quantity, Price, ProductName FROM Inventory NATURAL JOIN Product WHERE StoreID = ? ORDER BY Quantity DESC");
            $store_select->bind_param("i", $store_id);
            $store_select->execute();
            }
            
            else if ($ascordesc == 'Ascending' && $sortby == 'ProductName') {
            $store_select = $db->prepare("SELECT Quantity, Price, ProductName FROM Inventory NATURAL JOIN Product WHERE StoreID = ? ORDER BY ProductName ASC");
            $store_select->bind_param("i", $store_id);
            $store_select->execute();
            }
            
            else if ($ascordesc == 'Descending' && $sortby == 'ProductName') {
            $store_select = $db->prepare("SELECT Quantity, Price, ProductName FROM Inventory NATURAL JOIN Product WHERE StoreID = ? ORDER BY ProductName DESC");
            $store_select->bind_param("i", $store_id);
            $store_select->execute();
            }
            
            if (mysqli_stmt_bind_result($store_select, $Quantity, $Price, $ProductName)) {
                echo '<table>';
                echo '<tr><th>Product Name</th><th>Price</th><th>Quantity</th></tr>';
                  while($store_select->fetch()) {
                    echo '<td style="text-align:center;border: 1px solid #dddddd;width:1%">' . $ProductName . '</td>';
                    echo '<td style="text-align:center;border: 1px solid #dddddd;width:1%">' . "$" . $Price . '</td>';
                    echo '<td style="text-align:center;border: 1px solid #dddddd;width:1%">' . "Qnty: " . $Quantity . '</td>';
                    echo '</tr>';
                      } 
                      echo "</table>"; 
                }
            }
            
        }
          $store_select->close();
    }
?>

</body>
</html>
