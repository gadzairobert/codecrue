<?php
//login.php


include('database_connection.php');

if(isset($_SESSION['type']))
{
	header("location:index.php");
}

$message = '';

if(isset($_POST["login"]))
{
	$query = "
	SELECT * FROM user_details 
		WHERE user_email = :user_email
	";
	$statement = $connect->prepare($query);
	$statement->execute(
		array(
				'user_email'	=>	$_POST["user_email"]
			)
	);
	$count = $statement->rowCount();
	if($count > 0)
	{
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			if($row['user_status'] == 'Active')
			{
				if(password_verify($_POST["user_password"], $row["user_password"]))
				{
				
					$_SESSION['type'] = $row['user_type'];
					$_SESSION['user_id'] = $row['user_id'];
					$_SESSION['user_name'] = $row['user_name'];
					header("location:index.php");
				}
				else
				{
					$message = "<label>Wrong Password</label>";
				}
			}
			else
			{
				$message = "<label>Your account is disabled, Contact Master</label>";
			}
		}
	}
	else
	{
		$message = "<label>Wrong Email Address</labe>";
	}
}

?>

<!DOCTYPE html>
<html>
	<head>
		<title>Pick n Sell Recycling</title>		
		<script src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body style="background: url(mt4.jpeg) no-repeat no-repeat center center fixed; -webkit-background-size: cover; -moz-background-size: cover;
  				-o-background-size: cover; background-size: cover;">
		<br />
		<div class="container" align="center">
			<h2 align="center"><img src="logo.png" width="250px" alt="Logo"></h2>
			<br />
			<div class="panel panel-default" style="width:50%">
				<div class="panel-heading" style="background-color: #069961; color:white">Pick n' Sell Recycling POS - User LogIn</div>
				<div class="panel-body">
					<form method="post">
						<?php echo $message; ?>
						<div class="form-group" style="padding:5px;">
							<input type="text" name="user_email" placeholder="enter your valid email address" class="form-control" required />
						</div>
						<div class="form-group" style="padding:5px">
							<input type="password" name="user_password"  placeholder="enter your valid password"  class="form-control" required />
						</div>
						<div class="form-group">
							<input type="submit" name="login" value="Login" class="btn btn-info"/>
						</div>
					</form>
				</div>
			</div>
		</div>
	</body>
</html>