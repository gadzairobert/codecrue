<?php

//exp_list_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO exp_list (exp_name) 
		VALUES (:exp_name)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':exp_name'	=>	$_POST["exp_name"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Expense Name Added';
		}
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM exp_list WHERE exp_id = :exp_id";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':exp_id'	=>	$_POST["exp_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['exp_name'] = $row['exp_name'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
		UPDATE exp_list set exp_name = :exp_name  
		WHERE exp_id = :exp_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':exp_name'	=>	$_POST["exp_name"],
				':exp_id'	=>	$_POST["exp_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Expense Name Edited';
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'active';
		if($_POST['status'] == 'active')
		{
			$status = 'inactive';	
		}
		$query = "
		UPDATE exp_list 
		SET exp_status = :exp_status 
		WHERE exp_id = :exp_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':exp_status'	=>	$status,
				':exp_id'		=>	$_POST["exp_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Expense status change to ' . $status;
		}
	}
}

?>