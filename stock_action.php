<?php

//stock_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO stock (item_name) VALUES (:item_name)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':item_name'	=>	$_POST["item_name"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Name Added';
		}
	}
	
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "SELECT * FROM stock WHERE stock_id = :stock_id";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':stock_id'	=>	$_POST["stock_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['item_name'] = $row['item_name'];
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
		UPDATE stock set item_name = :item_name  
		WHERE stock_id = :stock_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':item_name'	                =>	$_POST["item_name"],
				':stock_id'		=>	$_POST["stock_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Name Edited';
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
		UPDATE stock 
		SET item_status = :item_status 
		WHERE stock_id = :stock_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':item_status'	   =>	$status,
				':stock_id' 		=>	$_POST["stock_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product status change to ' . $status;
		}
	}
}

?>