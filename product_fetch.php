<?php

//product_fetch.php

include('database_connection.php');
include('function.php');

$query = '';

$output = array();
$query .= "
	SELECT * FROM product 
INNER JOIN category ON category.category_id = product.category_id 
INNER JOIN user_details ON user_details.user_id = product.product_enter_by 
";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE category.category_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.product_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.product_quantity LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR user_details.user_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR product.product_id LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY product_id DESC ';
}

if($_POST['length'] != -1)
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
	if($row['product_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['product_id'];
	$sub_array[] = $row['product_date'];
	$sub_array[] = $row['product_name'];
	$sub_array[] = available_product_quantity($connect, $row["product_id"]) . ' kg';
	$sub_array[] = $row['product_minimum_order'];
	$sub_array[] = $row['product_tax']. ' kg';
	$sub_array[] = 'R '.$row['unit_price'];
	$sub_array[] = 'R '.$row['product_base_price'];
	$sub_array[] = $status;
	$sub_array[] = '<button type="button" name="view" id="'.$row["product_id"].'" class="btn btn-info btn-xs view"> View</button>';
	$sub_array[] = '<a href="view_purchase.php?pdf=1&product_code='.$row["product_id"].'" class="btn btn-info btn-xs" target="_blank"> PDF</a>';
	$sub_array[] = '<button type="button" name="update" id="'.$row["product_id"].'" class="btn btn-success btn-xs update">Update</button>';
	$sub_array[] = '<button type="button" name="delete" id="'.$row["product_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["product_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare('SELECT * FROM product');
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