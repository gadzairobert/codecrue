<?php
//index.php
include('database_connection.php');
include('function.php');

if(!isset($_SESSION["type"]))
{
	header("location:login.php");
}

include('header.php');

?>
	<br />
	<div class="row">
	<?php
	if($_SESSION['type'] == 'master')
	{
	?>
	<meta http-equiv="refresh" content="45">
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Total Active Products</strong></div>
			<div class="panel-body" align="center">
				<h1><?php echo count_total_product($connect); ?></h1>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Today's Orders Sales Value</strong></div>
			<div class="panel-body" align="center">
				<h1>R<?php echo count_total_order_value_today($connect); ?></h1>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong>Today's Purchases Value</strong></div>
			<div class="panel-body" align="center">
				<h1>R<?php echo count_total_purchases_value_today($connect); ?></h1>
			</div>
		</div>
	</div>
	<div class="col-md-3">
		<div class="panel panel-default">
			<div class="panel-heading"><strong> Expenditure</strong></div>
			<div class="panel-body" align="center">
				<h1>R<?php echo count_total_expenses_value_today($connect); ?></h1>
			</div>
		</div>
	</div>
	
	
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Cash Sales Value</strong></div>
				<div class="panel-body" align="center">
					<h1>R<?php echo count_total_order_value($connect); ?></h1>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Purchases Value</strong></div>
				<div class="panel-body" align="center">
					<h1>R<?php echo count_total_cash_order_value($connect); ?></h1>
				</div>
			</div>
		</div>
		<div class="col-md-4">
			<div class="panel panel-default">
				<div class="panel-heading"><strong>Total Expenses Value</strong></div>
				<div class="panel-body" align="center">
					<h1>R<?php echo count_all_total_expenses_value($connect); ?></h1>
				</div>
			</div>
		</div> 

		
		<div class="col-md-6">
			<?php include("chart_purchases.php"); ?>
		</div>
		<div class="col-md-6">
			<?php include("chart_sales.php"); ?>
		</div>
		<?php
			}
		?>

	<?php
		if($_SESSION['type'] == 'user')
		{
	?>
	<center><div class="col-md-12">
		<div class="panel panel-default" style="padding-top:80px; padding-bottom:80px">
			<img src="logo.png" alt="">
		</div>

	</div>
	<center>

	<?php
		}
	?>

</div>
<?php
include("footer.php");
?>