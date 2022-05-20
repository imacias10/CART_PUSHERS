<?php
// Name:    Ivan Macias
// course:  CMPS 3420
// @source: create.php
//
// This file will allow users to create an account for the cart_pushers database.
// If the user is an employee, then they must enter an EmployeeID.
// Else, that field will be NULL and the user will be recognized as a customer.

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

?>
<?php
            if (isset($_POST['SUBMIT'])) {
                unset($_POST['SUBMIT']);
                
                $db = get_connection();
                
                $uname = $_POST['username'];
                $pword = $_POST['password'];
                $name1 = $_POST['first_name'];
                $name2 = $_POST['last_name'];
                $phone = $_POST['phone_number'];
		$id    = $_POST['employee_id'];

                if (strlen($uname) == 0 || strlen($pword) == 0 || strlen($name1) == 0
                    || strlen($name2) == 0 || strlen($phone) == 0) {
                        echo "Cannot leave these fields empty!!!";
                        header("Location: create.php");
		}

		if ($uname == $pword) {
			echo "<h3 style=\"color: red; text-align: center;\">PASSWORD AND USERNAME CANNOT BE THE SAME!</h3>";
		}
		else {	
			if (strlen($id) == 0) {
				$id = NULL;
			}

			$hash = password_hash($pword, PASSWORD_DEFAULT);	
			$query = $db->prepare("INSERT INTO UserAccounts VALUES (?, ?, ?, ?, ?, ?)");
			$query->bind_param("ssssii", $uname, $hash, $name1, $name2, $phone, $id);
		
			if (!$query->execute()) {
				echo mysqli_error($db);
			} else {
				header("Location: form.php");
			}

		}

            }
?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create an Account</title>
    <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                padding: 50px;
            }
            input[type=text], input[type=password], input[type=number], input[type=submit] {
                width: 100%;
                padding: 12px 20px;
                margin: 15px 0;
                display: inline-block;
                border: 1px solid #ccc;
                box-sizing: border-box;
                border-radius: 5px;
            }
            .login-form {
                background-color: rgb(252, 252, 252);
                margin: auto;
                border: 1px solid #888;
                box-shadow: 10px 10px 10px rgba(0,0,0, 0.7);
                width: 60%;
                padding: 15px;
            }
            
            form {
                border: 3px solid rgb(227, 227, 227);
                border-radius: 15px;
            }
            
            .img-container {
                text-align: center;
                margin: 24px 0 24px 0;
                position: relative;
            }
            
            img.food {
                width: 40%;
                border-radius: 50%;
            }

            .div-container {
                position: fixed;
                z-index: 1;
                left: 0;
                top: 0;
                width: 100%;
                height: 100%;
                background-color: rgba(0, 0, 0, 0.6);
                padding-top: 250px;
            }
            
            select {
                padding: 1px 0;
                border: 1px solid rgb(135, 135, 135);
                border-radius: 5px;
            }
            
            .form-container {
                padding: 16px;
            }

            button:hover {
                opacity: 0.7;
            }

            button {
                background-color: #04AA6D;
                color: rgb(255,255,255);
                width: 100%;
                padding: 14px 20px;
                margin: 8px 0;
                border: none;
                border-radius: 10px;
                cursor: pointer;
            }

            .drop-container {
                padding: 12px 16px;
            }

            .choice {
                font-size: 14px;
            }
            
            h1 {
                font-family: Arial, Helvetica, sans-serif;
                text-align: center;
                padding: 20px 10px;
            }

            .create-container {
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            #create-link {
                text-decoration: none;
	    }

	    .submit-btn {
		background-color: green;
		color: rgb(255, 255, 255);
	    }

            @media screen and (min-width: 1500px) and (max-width: 3840px) {
                .login-form {
                    width: 30%;
                }
            }
    </style>
</head>
<body>
    <div class="div-container">
        <form action="create.php" class="login-form" method="POST">
            <h1>CREATE NEW ACCOUNT</h1>
    
            <label for="User-Name">USERNAME</label>
            <input type="text" name="username" id="username" placeholder="Enter a username" required>
            
            <label for="text">PASSWORD</label>
            <input type="password" name="password"  id="password" placeholder="Enter Password" required>
    
            <label for="First-Name">FIRST NAME</label>
            <input type="text" name="first_name" id="Fname" placeholder="Enter First Name" required>
    
            <label for="Last-Name">LAST NAME</label>
            <input type="text" name="last_name" id="Lname" placeholder="Enter Last Name" required>
    
            <label for="Phone-Number">PHONE NUMBER</label>
            <input type="number" name="phone_number" id="phonenumber" placeholder="##########" required>
           
	    <label for="Employee-status">ENTER EMPLOYEE ID # (EMPLOYEES ONLY)</label>
            <input type="number" name="employee_id" placeholder="00000000"> 
	    
	    <input type="submit" name="SUBMIT" class="submit-btn" value="CREATE ACCOUNT">


        </form>
    </div>
</body>
</html>
