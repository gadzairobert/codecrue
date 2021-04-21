<?php

//order_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
SELECT * FROM inventory_order 
inner join user_details
on inventory_order.user_id = user_details.user_id WHERE

-- WHERE  inventory_order_created_date BETWEEN  DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))- 1 DAY) and LAST_DAY(CURDATE()) AND
";

/* if($_SESSION['type'] == 'user')
{
	$query .= 'user_id = "'.$_SESSION["user_id"].'" AND ';
}
 */
if(isset($_POST["search"]["value"]))
{
	$query .= '(inventory_order.inventory_order_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order.inventory_order_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order.inventory_order_total LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order.inventory_order_status LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR inventory_order.inventory_order_created_date LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY inventory_order.inventory_order_id DESC ';
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
	if($row['inventory_order_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['inventory_order_id'];
	$sub_array[] = $row['inventory_order_created_date'];
	$sub_array[] = $row['invoice_no'];
	$sub_array[] = $row['inventory_order_name'];
	$sub_array[] = $row['inventory_order_total'];
	$sub_array[] = $status;
	//$sub_array[] = '<button class="btn btn-success btn-xs view name="view" id="'.$row["inventory_order_id"].'" "> <i class="fa fa-trash"></i> </button>';
    $sub_array[] = '<center><button class="btn btn-warning btn-sm view name="view" id="'.$row["inventory_order_id"].'"><i class="fa fa-eye"></i> </button></center>';
	//$sub_array[] = '<button type="button" name="update" id="'.$row["inventory_order_id"].'" class="btn btn-warning btn-xs update">Update</button>';
	$sub_array[] = '<center><a href="view_order.php?pdf=1&order_id='.$row["inventory_order_id"].'" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-eye"></i></a></center>';
	//$sub_array[] = '<button type="button" name="delete" id="'.$row["inventory_order_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["inventory_order_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM inventory_order");
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