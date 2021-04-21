<?php
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
		
		$queryCondition .= "WHERE '$fiy-$fim-$fid' AND '" . $post_at_todate . "'";
	}

    if(isset($start_date))
		{

	$sql = "CALL pick.report_all_one('".$_POST['start_date']."','".$_POST['end_date']."')" . $queryCondition . " ";
	$result = mysqli_query($conn,$sql);
        } else {
            $sql = "CALL pick.report_all_one('100-01-01', '9999-04-27')" . $queryCondition . " ";
            $result = mysqli_query($conn,$sql);
        }
?>




<html>
	<head>	
	<script src="http://code.jquery.com/jquery-1.9.1.js"></script>
	<link rel="stylesheet" href="http://code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">

	<style>
	.table-content{border-top:#CCCCCC 4px solid; width:100%;}
	.table-content th {padding:5px 20px; background: #F0F0F0;vertical-align:top;} 
	.table-content td {padding:5px 20px; border-bottom: #F0F0F0 1px solid;vertical-align:top;} 

    .negative { color : red; }
	.positive { color : green; }
	</style>
	</head>
	
	<body>
    <center><h4>Report List Breakdown by Month</h4></center>
   <!-- <div class="demo-content" style="padding-top:12px">
  <form name="frmSearch" method="post" action="">
  <center><p class="search_input">
		<input type="text" placeholder="From Date" id="post_at" name="start_date"  value="<?php echo $post_at; ?>" class="input-control" />
	    <input type="text" placeholder="To Date" id="post_at_to_date" name="end_date" style="margin-left:10px"  value="<?php echo $post_at_to_date; ?>" class="input-control"  />			 
		<input type="submit" name="go" value="Submit" >
	</p></center>-->
<?php if(!empty($result))	 { ?>
<table class="table-content">
          <thead>
        <tr>         
            <th><span>Month</span></th>
            <th><span>Net Salaries</span></th>
            <th><span>Net Purchases</span></th>
            <th><span>Net Expenses</span></th>          
            <th><span>Nett Sales</span></th>
            <th><span>Nett Outflow</span></th>
            <th><span>Profit</span></th>	 
        </tr>
      </thead>
    <tbody>
	<?php
		while($row = mysqli_fetch_array($result)) {
	?>
        <tr>
            <td style="background-color:#d0d0d6"><?php echo $row["months"]; ?></td>
			<td><?php echo $row["nett_salary"]; ?></td>
			<td><?php echo $row["nett_purchases"]; ?></td>
			<td><?php echo $row["nett_expenses"]; ?></td>
			<td><?php echo $row["nett_sales"]; ?></td>
            <td style="background-color:#d5ded8"></style><?php echo $row["total_cash_out"]; ?></td>
            <td style="background-color:#d0d0d6" class="plusmin"><?php echo $row["profit"]; ?></td>

		</tr>
   <?php
		}
   ?>
   <tbody>
  </table>
<?php } ?>
  </form>
  <?php
include("footer.php");
?>
  </div>
  
 
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
    
//color pickers for profit and loss
    function MakePosNeg() {
    var TDs = document.querySelectorAll('.plusmin');

    for (var i = 0; i < TDs.length; i++) {
        var temp = TDs[i];
        if (temp.firstChild.nodeValue.indexOf('-') == 0) {temp.className = "negative";}
        else {temp.className = "positive";}
    }
    }
    onload = MakePosNeg()
</script>


</body>
</html>
