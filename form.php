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
?>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Cart_Pushers LOGIN</title>
        <style>
            body {
                font-family: Arial, Helvetica, sans-serif;
                padding: 50px;
            }
            input[type=text], input[type=password] {
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
                padding: 10px;
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

            @media screen and (min-width: 1500px) and (max-width: 3840px) {
                .login-form {
                    width: 30%;
                }
            }
    </style>
    </head>
    <body>
        <div class="div-container">
            <form class="login-form" action="form.php" method="POST">
                <div>
                    <div class="img-container">
                        <img src="food.png" alt="Picture of Food" class="food">
                    </div>
                    <!--<div class="drop-container">
                        <label class="choice"for="status">CHOOSE ONE</label>
                        <select name="user_status">
                            <option class="option" value="EMPLOYEE">EMPLOYEE</option>
                            <option class="option" value="CUSTOMER">CUSTOMER</option>
                        </select>
                    </div>-->
                    
                    <div class="form-container">
                        USERNAME:
                        <input type="text" placeholder="Enter Username" name="USERNAME">
                        
                        PASSWORD:
                        <input type="password" placeholder="Enter Password" name="PASSWORD">

			<button type="submit" name="SUBMIT" class="login-btn">LOGIN</button>
                    </div>
<?php
if (isset($_POST['SUBMIT'])) {
	//header('Content-type: application/json');
	unset($_POST['SUBMIT']);
	
	$db = get_connection();	

	$uname = $_POST['USERNAME'];
	$pword = $_POST['PASSWORD'];

	if (strlen($uname) == 0 || strlen($pword) == 0) {
		echo "Fields cannot be empty!";
		//header("Location: form.php");
		die();
	}

	$validation = $db->prepare("SELECT username, password, EmployeeID FROM UserAccounts WHERE username = ?");
	$validation->bind_param('s', $uname);
	$validation->execute();
	
	mysqli_stmt_bind_result($validation, $res_user, $res_password, $id);	

	if ($validation->fetch() && password_verify($pword, $res_password)) {
		if ($id != NULL) {
			header("Location: employee.php");
			echo "<h3 style=\"text-align:center;\">YOU ARE NOW LOGGED IN</h3>";
		} else {
			header("Location: customer.php");
			echo "LOGGED IN AS A CUSTOMER!";
		}
	} 
	else {
		echo "<h3 style=\"text-align:center;\">INCORRECT USERNAME/PASSWORD!</h3>";
	}
}
?>
                    <div class="create-container">
                        <a href="create.php" id="create-link">Create Account</a>
                    </div>
                </div>
            </form>
        </div>
    </body>
</html>
