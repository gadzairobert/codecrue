<?php

//category_fetch.php

include('database_connection.php');

$query = '';

$output = array();

$query .= "SELECT * FROM exp_list ";

if(isset($_POST["search"]["value"]))
{
	$query .= 'WHERE exp_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR exp_status LIKE "%'.$_POST["search"]["value"].'%" ';
}

if(isset($_POST['order']))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY exp_id DESC ';
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
	if($row['exp_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['exp_name'];
	$sub_array[] = $status;
	$sub_array[] = '<center><button type="button" name="update" id="'.$row["exp_id"].'" class="btn btn-warning btn-sm update"><i class="fa fa-eye"></i></button></center>';
	$sub_array[] = '<center><button type="button" name="delete" id="'.$row["exp_id"].'" class="btn btn-danger btn-sm delete" data-status="'.$row["exp_status"].'"><i class="fa fa-eye"></i></button></center>';
	$data[] = $sub_array;
}

$output = array(
	"draw"			=>	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"				=>	$data
);

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM exp_list");
	$statement->execute();
	return $statement->rowCount();
}

echo json_encode($output);

?>