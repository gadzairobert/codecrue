<?php
//order.php

include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');


?>
<?php
	$conn = mysqli_connect("localhost", "root", "", "pick");
	
	$post_at = "";
	$post_at_to_date = "";
	
	$queryCondition = "";
	if(!empty($_POST["search"]["post_at"])) {			
		$post_at = $_POST["search"]["post_at"];
		list($fid,$fim,$fiy) = explode("-",$post_at);
		
		$post_at_todate = date('Y-m-d');
		if(!empty($_POST["search"]["post_at_to_date"])) {
			$post_at_to_date = $_POST["search"]["post_at_to_date"];
			list($tid,$tim,$tiy) = explode("-",$_POST["search"]["post_at_to_date"]);
			$post_at_todate = "$tiy-$tim-$tid";
		}
		
		$queryCondition .= "WHERE pay_date BETWEEN '$fiy-$fim-$fid' AND '" . $post_at_todate . "'";
	}

	$sql = "SELECT * from payrol " . $queryCondition . " ORDER BY pay_date asc";
	$result = mysqli_query($conn,$sql);
?>




<html>
	<head>	
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

	<style>
	.table-content{border-top:#CCCCCC 4px solid; width:100%;}
	.table-content th {padding:5px 20px; background: #F0F0F0;vertical-align:top;} 
	.table-content td {padding:5px 20px; border-bottom: #F0F0F0 1px solid;vertical-align:top;} 
	</style>
	</head>
	
	<body>
    <div class="demo-content" style="padding-top:12px">
	<center><h4>Salaries Report</h4></center>
  <form name="frmSearch" method="post" action="">
  <center><p class="search_input">
		<input type="text" placeholder="From Date" id="post_at" name="search[post_at]"  value="<?php echo $post_at; ?>" class="input-control" />
	    <input type="text" placeholder="To Date" id="post_at_to_date" name="search[post_at_to_date]" style="margin-left:10px"  value="<?php echo $post_at_to_date; ?>" class="input-control"  />			 
		<input type="submit" name="go" value="Submit" >
	</p></center>
<?php if(!empty($result))	 { ?>
<table class="table-content">
          <thead>
        <tr>         
          <th ><span>Pay Date</span></th>
		  <th ><span>Pay ID</span></th>
		  <th ><span>Employee</span></th>
          <th><span>Basic Salary</span></th>
          <th><span>Loans Adv</span></th>          
          <th><span>Loan Repay</span></th>	
          <th><span>Net Salary</span></th>
          <th><span>Rate/Hr</span></th>  
        </tr>
      </thead>
    <tbody>
	<?php
		while($row = mysqli_fetch_array($result)) {
	?>
        <tr>
            <td><?php echo $row["pay_date"]; ?></td>
			<td><?php echo $row["pay_id"]; ?></td>
			<td><?php echo $row["full_name"]; ?></td>
			<td><?php echo $row["basic_salary"]; ?></td>
			<td><?php echo $row["loan_advance"]; ?></td>
			<td><?php echo $row["loan_repay"]; ?></td>
			<td><?php echo $row["net_salary"]; ?></td>
			<td><?php echo $row["rate_per_hour"]; ?></td>
		</tr>
   <?php
		}
   ?>
   <tbody>
  </table>
<?php } ?>
  </form>
  </div>
  <?php
include("footer.php");
?>
<script src="http://code.jquery.com/ui/1.10.3/jquery-ui.js"></script>
<script>
	$.datepicker.setDefaults({
	showOn: "button",
	buttonImage: "datepicker.png",
	buttonText: "Date Picker",
	buttonImageOnly: true,
	dateFormat: 'dd-mm-yy'  
	});
	$(function() {
	$("#post_at").datepicker();
	$("#post_at_to_date").datepicker();
	});
</script>
</body>
</html>
