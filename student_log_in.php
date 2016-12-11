<?php

$username = null;
$password = null;
$result = null;
$result_session = null;
$message = null;

$result_session = session_start();

if($result_session){
	if(isset($_SESSION['current-stud-log'])){
		header('location: student_log_in.php');
	}else{
		if(isset($_POST['username']) && isset($_POST['password'])){
			$username = $_POST['username'];
			$password = $_POST['password'];
			
			if(!(strlen($username))){
				$result = 5;
			}elseif(!(strlen($password))){
				$result = 4;
			}else{
				if(preg_match('#^[0-9A-Za-z\x20\x2D\x2E\x5F]+$#', $username)){
                    $connection = mysqli_connect("localhost", "root", "", "bankpay");
                    if(mysqli_errno($connection)){
                        echo "Connection Failed: " . mysqli_errno();
                        exit();
                    }else{
                        $query = "SELECT * FROM students WHERE username = '" . $username . "' && password = '" . $password . "'";
                        if($result = mysqli_query($connection, $query)){
                            $_SESSION['current-stud-log'] = result["username"];
					       header('location: mainmenu.php');
                        }else{
						  $result = 2;
					   }
                    }
                    
				}else{
					$result = 3;
				}
			}
		}
	}
}else{
	$result = 1;
}



switch($result){		
	case 1:
		$message = 'Something went wrong. Please report this incident to the system administrator.';
		break;
	
	case 2:
		$message = 'The username or password you entered is incorrect.';
		break;
		
	case 3:
		$message = 'Only alphanumeric characters are allowed on the username.';
		break;
	
	case 4:
		$message = 'Do not forget your password.';
		break;
	
	case 5:
		$message = 'Please enter your username.';
		break;	
}

?>



<html>
    <head>
        <link rel = "stylesheet" type = "text/css" href = "student_log_in.css">
        <title>
            HASTY
        </title>
    </head>
    <body>
        <IMG STYLE="position:absolute; TOP:-120px; LEFT:300px; HEIGHT:400px" SRC="HASTY2.png">
        <div id = "container" class = "div-major">
		<form action = "student_log_in.php" method = "POST">
			
			<div id = "div-log-in-title" class = "div-minor">
				LOG IN
				<br/>
				<span class = "userpass"></span>
			</div>
						
			<br/>
			
			<hr color = "AF0000" size = "3">

			<?php 
			echo '<span class = "message">' . $message . '</span>';
			?>
			
			<div id = "div-login-content" class = "div-minor">
				<div id = "username-title">Username:</div>
				<div id = "username-input"><input type = "text" name = "username" class = "username" /></div>
				
				<span style = "clear: both;"></span>
				
				<div id = "password-title" >Password:</div>							
				<div id = "password-input"><input type = "password" name = "password" class = "password" /></div>
				
				<div id = "log-in-input"><input type = "submit" name = "log-in" class = "log-in" value = "Log In" /></div>
			</div>
		</form>
	</div>
    </body>
</html>