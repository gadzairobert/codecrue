<?php

//expense_fetch.php

include('database_connection.php');

include('function.php');

$query = '';

$output = array();

$query .= "
	SELECT * FROM expense WHERE expense_date BETWEEN
	DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))- 1 DAY) and LAST_DAY(CURDATE()) AND
	
";

if($_SESSION['type'] == 'user')
{
	$query .= 'expense.user_id = "'.$_SESSION["expense.user_id"].'" AND ';
}

if(isset($_POST["search"]["value"]))
{
	$query .= '(expense.expense_id LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR expense.expense_total LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR expense.expense_status LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY expense.expense_id DESC ';
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
	if($row['expense_status'] == 'active')
	{
		$status = '<span class="label label-success">Active</span>';
	}
	else
	{
		$status = '<span class="label label-danger">Inactive</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['expense_id'];
	$sub_array[] = $row['expense_date'];
	$sub_array[] = $row['expense_total'];
	$sub_array[] = $row['expense_total'];
	//$sub_array[] = count_expense_items($connect, $row["count_items"]) ;
	//$sub_array[] = $status;
	$sub_array[] = '<center><button class="btn btn-warning btn-sm view name="view" id="'.$row["expense_id"].'"><i class="fa fa-eye"></i> </button></center>';
	
	//$sub_array[] = '<center><button type="button" name="update" id="'.$row["expense_id"].'" class="btn btn-success btn-sm update"><i class="fa fa-eye"></i></button></center>';
	$sub_array[] = '<center><a href="expense_pdf.php?pdf=1&expenses_id='.$row["expense_id"].'" class="btn btn-info btn-sm" target="_blank"><i class="fa fa-eye"></i></a></center>';

	//$sub_array[] = '<button type="button" name="delete" id="'.$row["inventory_purchase_id"].'" class="btn btn-danger btn-xs delete" data-status="'.$row["inventory_purchase_status"].'">Delete</button>';
	$data[] = $sub_array;
}

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM expense");
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