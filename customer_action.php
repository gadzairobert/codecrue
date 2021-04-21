<?php

//customer_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO inventory_customer (inventory_order_name, inventory_order_address, phone_number, email_address, customer_status) 
		VALUES (:inventory_order_name, :inventory_order_address, :phone_number, :email_address, :customer_status)
		";	
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_order_name'		=>	$_POST["inventory_order_name"],
				':inventory_order_address'	=>	$_POST["inventory_order_address"],
                ':phone_number'		   	 	=>	$_POST["phone_number"],
				':email_address'		    =>	$_POST["email_address"],
				':customer_status'			=>	'active'
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'New Customer Added';
		}
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM inventory_customer WHERE customer_id = :customer_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':customer_id'	=>	$_POST["customer_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['inventory_order_name'] 	= $row['inventory_order_name'];
			$output['inventory_order_address']  = $row['inventory_order_address'];
            $output['phone_number']      		= $row['phone_number'];
			$output['email_address']         	= $row['email_address'];
		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['inventory_order_name'] != '')
		{
			$query = "
			UPDATE inventory_customer SET 
			inventory_order_name 		= '".$_POST["inventory_order_name"]."',
			inventory_order_address 	= '".$_POST["inventory_order_address"]."', 
			phone_number 				= '".$_POST["phone_number"]."', 
			email_address 				= '".$_POST["email_address"]."'
			WHERE customer_id 			= '".$_POST["customer_id"]."'
			";
		}
		else
		{
			$query = "
			UPDATE inventory_customer SET 
			inventory_order_name 		= '".$_POST["inventory_order_name"]."',
			inventory_order_address 	= '".$_POST["inventory_order_address"]."', 
			phone_number 				= '".$_POST["phone_number"]."', 
			email_address 				= '".$_POST["email_address"]."'
			WHERE customer_id 			= '".$_POST["customer_id"]."'
			";
		}
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Customer Details Edited';
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
		UPDATE inventory_customer 
		SET customer_status = :customer_status 
		WHERE customer_id 	= :customer_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':customer_status'	=>	$status,
				':customer_id'		=>	$_POST["customer_id"]
			)
		);	
		$result = $statement->fetchAll();	
		if(isset($result))
		{
			echo 'Customer Status changed to ' . $status;
		}
	}
}

?>