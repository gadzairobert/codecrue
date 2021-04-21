<?php
//header.php
?>
<!DOCTYPE html>
<html>
	<head>
		<title>Pick n Sell Recycling</title>
		<link href="logo2.png" rel="icon">
		<script src="js/jquery-1.10.2.min.js"></script>
		<link rel="stylesheet" href="css/bootstrap.min.css" />
		<script src="js/jquery.dataTables.min.js"></script>
		<script src="js/dataTables.bootstrap.min.js"></script>		
		<link rel="stylesheet" href="css/dataTables.bootstrap.min.css" />
		<script src="js/bootstrap.min.js"></script>
	</head>
	<body>
		<div class="container" style="background-color: #fafcfc; border:1px solid #cecece;">
			<h2 align="center">
			<img src="logo.png" width="250px" alt=""></h2>
			<nav class="navbar navbar-inverse">
				<div class="container-fluid" style="background-color: #069961">
					<div class="navbar-header">
						<a href="index.php" style="border-right: 1px solid #276136;" class="navbar-brand">Home</a>
					</div>
					<ul class="nav navbar-nav">
						<?php
							if($_SESSION['type'] == 'master')
							{
						?>
						<?php
						}
						?>
						<?php
							if($_SESSION['type'] == 'master')
							{
						?>
						<li class="dropdown">
							<a href="#" style="border-right: 1px solid #276136;" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill"></span>Users</a>
							<ul class="dropdown-menu">
								<li><a href="user.php">Login Credentials</a></li>
								<li><a href="employee.php">Employees List</a></li>
							</ul>
						</li>
						<?php
						}
						?>
						<?php
							if($_SESSION['type'] == 'master')
							{
						?>
						<li class="dropdown">
							<a href="#" style="border-right: 1px solid #276136;" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill"></span>Payrol</a>
							<ul class="dropdown-menu">
								<li><a href="bank.php">Banking Details</a></li>
								<li><a href="pay.php">Salaries</a></li>
							</ul>
						</li>
						<?php
						}
						?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" style="border-right: 1px solid #276136;" data-toggle="dropdown"><span class="label label-pill"></span>Transact</a>
							<ul class="dropdown-menu">
							<li style="background-color:#daf0f0"><a href="order.php">Cash Sales</a></li>
							<li style="background-color:#daf0f0"><a href="purchase.php"> Purchases</a></li>
							<?php
							if($_SESSION['type'] == 'master')
							{
							?>
							<li style="background-color:#f5dade"><a href="customer.php">Customers</a></li>
							<li style="background-color:#f5dade"><a href="agent.php"> Sales Agents</a></li>
							<?php
								}
							?>
								
							</ul>
						</li>
						<li>
							<a href="stock.php" style="border-right: 1px solid #276136;"><span class="label label-pill"></span>Stock</a>
						</li>
						<?php
							if($_SESSION['type'] == 'master')
							{
						?>
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" style="border-right: 1px solid #276136;" data-toggle="dropdown"><span class="label label-pill"></span>Expenses</a>
							<ul class="dropdown-menu">
								<li><a href="exp_list.php">Categories</a></li>
								<li><a href="expense.php">Expenses List</a></li>
							</ul>
						</li>
						
						<li class="dropdown">
							<a href="#" style="border-right: 1px solid #276136;" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill"></span>Reports</a>
							<ul class="dropdown-menu">
								<li><a href="fetch_purchases.php">Purchases </a></li>
								<li><a href="fetch_sales.php">Order Sales  </a></li>
								<li><a href="report_expenses.php">Expenditures</a></li>
								<li><a href="report_salaries.php">Salaries</a></li>
								<li><a href="report_all.php">Summary </a></li>
							</ul>
						</li>
						<?php
							}
						?>
					</ul>
					
					<ul class="nav navbar-nav navbar-right">
						<li class="dropdown">
							<a href="#" class="dropdown-toggle" data-toggle="dropdown"><span class="label label-pill label-danger count"></span> <?php echo $_SESSION["user_name"]; ?></a>
							<ul class="dropdown-menu">
								<li><a href="profile.php">Profile</a></li>
								<li><a href="logout.php">Logout</a></li>
							</ul>
						</li>
					</ul>
				</div>
			</nav>
		