<?php

//purchase_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	SELECT * FROM purchase 
	inner join user_details
	on purchase.user_id = user_details.user_id
	WHERE 

	-- inventory_purchase_created_date BETWEEN DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))- 1 DAY) and LAST_DAY(CURDATE())  AND
	
";
/* 
if($_SESSION['type'] == 'user')
{
	$query .= 'user_id = "'.$_SESSION["user_id"].'" AND ';
} */

if(isset($_POST["search"]["value"]))
{
	$query .= '(purchase.inventory_purchase_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR purchase.inventory_purchase_total LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR purchase.inventory_purchase_status LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY purchase.inventory_purchase_id DESC ';
}

if($_POST["length"] != -1)
{
	$query .= 'LIMIT ' . $_POST['start'] . ', ' . $_POST['length'];
}

$statement = $connect->prepare($query);
$statement->execute();
$result = $statement->fetchAll();
$data = array();
$filtered_rows = $statement->rowCount();
foreach($result as $row)
{
	$status = '';
	if($row['inventory_purchase_status'] == 'UNPAID')
	{
		$status = '<span class="label label-danger">UNPAID</span>';
	}
	else
	{
		$status = '<span class="label label-success">PAID</span>';
	}
	
	$sub_array = array();
	$sub_array[] = $row['inventory_purchase_id'];
	$sub_array[] = $row['inventory_purchase_created_date'];
	$sub_array[] = $row['agent_name'];
	$sub_array[] = $row['inventory_purchase_total'];
	$sub_array[] = $status;
	$sub_array[] = '<button type="submit" name="delete" id="'.$row["inventory_purchase_id"].'"  class="btn btn-danger btn-sm delete" data-status="'.$row["inventory_purchase_status"].'"></button>';
	//$sub_array[] = '<center><button name="update" id="'.$row["inventory_purchase_id"].'" class="btn btn-success btn-sm update"><i class="fa fa-eye"></i></button>';
	$sub_array[] = '<center><button class="btn btn-warning btn-sm view name="view" id="'.$row["inventory_purchase_id"].'"><i class="fa fa-eye"></i> </button></center>';
	$sub_array[] = '<center><a href="purchase_pdf.php?pdf=1&purchase_id='.$row["inventory_purchase_id"].'" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-eye"></i></a></center>';
	
	$data[] = $sub_array;
	
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM purchase");
	$statement->execute();
	return $statement->rowCount();
}


$output = array(
	"draw"    			=> 	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);	

echo json_encode($output);


?>
