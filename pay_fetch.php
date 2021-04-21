<?php

//user_fetch.php

include('database_connection.php');

$query = '';

$output = array();

$query .= "
SELECT * FROM payrol 
inner join employee
on payrol.full_name = employee.full_name
WHERE payrol.pay_date AND
-- BETWEEN DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))- 1 DAY) and LAST_DAY(CURDATE()) 
  
";

if(isset($_POST["search"]["value"]))
{
	$query .= '(payrol.employee_code LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR payrol.full_name LIKE "%'.$_POST["search"]["value"].'%" ';
	$query .= 'OR payrol.pay_status LIKE "%'.$_POST["search"]["value"].'%") ';
}

if(isset($_POST["order"]))
{
	$query .= 'ORDER BY '.$_POST['order']['0']['column'].' '.$_POST['order']['0']['dir'].' ';
}
else
{
	$query .= 'ORDER BY payrol.pay_id DESC ';
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
	if($row["pay_status"] == 'PAID')
	{
		$status = '<span class="label label-success">PAID</span>';
	}
	else
	{
		$status = '<span class="label label-danger">UNPAID</span>';
	}
	$sub_array = array();
	$sub_array[] = $row['pay_date'];
	$sub_array[] = $row['full_name'];
	$sub_array[] = $row['employee_code'];
    $sub_array[] = $row['basic_salary'];
    $sub_array[] = $row['loan_advance'] + $row['uif'] + $row['loan_repay'];
    $sub_array[] = $row['net_salary'];
    $sub_array[] = $status;
	$sub_array[] = '<button type="button" name="delete" id="'.$row["pay_id"].'" class="btn btn-danger btn-sm delete" data-status="'.$row["pay_status"].'"><i class="fa fa-eye"></i></button>';
	$sub_array[] = '<center><button class="btn btn-warning btn-sm view name="view" id="'.$row["pay_id"].'"><i class="fa fa-eye"></i> </button></center>';
	//$sub_array[] = '<button type="button" name="update" id="'.$row["pay_id"].'" class="btn btn-warning btn-sm update"><i class="fa fa-eye"></i></button>';
	$sub_array[] = '<a href="payslip.php?pdf=1&payslip_code='.$row["pay_id"].'" class="btn btn-info btn-sm" target="_blank"> <i class="fa fa-eye"></i></a>';
	$sub_array[] = '<a href="payslip.php?pdf=1&payslip_code='.$row["pay_id"].'" class="btn btn-info btn-sm" target="_blank"> <i class="fa fa-eye"></i></a>';
	
	$data[] = $sub_array;
}

$output = array(
	"draw"				=>	intval($_POST["draw"]),
	"recordsTotal"  	=>  $filtered_rows,
	"recordsFiltered" 	=> 	get_total_all_records($connect),
	"data"    			=> 	$data
);
echo json_encode($output);

function get_total_all_records($connect)
{
	$statement = $connect->prepare("SELECT * FROM payrol'");
	$statement->execute();
	return $statement->rowCount();
}

?>