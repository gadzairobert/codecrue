<?php

//bank_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO bank_details (full_name, employee_code, bank_name, branch_code, bank_account, account_type, account_status) 
		VALUES (:full_name, :employee_code, :bank_name, :branch_code, :bank_account, :account_type, :account_status)
		";	
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':full_name'	    =>	$_POST["full_name"],
                ':employee_code'	=>	$_POST["employee_code"],
                ':bank_name'		=>	$_POST["bank_name"],
				':branch_code'		=>	$_POST["branch_code"],
                ':bank_account'		=>	$_POST["bank_account"],
                ':account_type'		=>	$_POST["account_type"],
				':account_status'	=>	'active'
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'New Bank Account Added';
		}
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM bank_details WHERE employee_id = :employee_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':employee_id'	=>	$_POST["employee_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['full_name']     = $row['full_name'];
            $output['employee_code'] = $row['employee_code'];
            $output['bank_name']     = $row['bank_name'];
			$output['branch_code']   = $row['branch_code'];
            $output['bank_account']  = $row['bank_account'];
            $output['account_type']  = $row['account_type'];
		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['full_name'] != '')
		{
			$query = "
			UPDATE bank_details SET 
			full_name 	        = '".$_POST["full_name"]."', 
            employee_code 		= '".$_POST["employee_code"]."', 
			bank_name 		    = '".$_POST["bank_name"]."', 
			branch_code 		= '".$_POST["branch_code"]."',
            bank_account 		= '".$_POST["bank_account"]."',
            account_type 		= '".$_POST["account_type"]."'
			WHERE employee_id 	= '".$_POST["employee_id"]."'
			";
		}
		else
		{
			$query = "
			UPDATE bank_details SET 
			full_name 	        = '".$_POST["full_name"]."', 
            employee_code 		= '".$_POST["employee_code"]."', 
			bank_name 		    = '".$_POST["bank_name"]."', 
			branch_code 		= '".$_POST["branch_code"]."',
            bank_account 		= '".$_POST["bank_account"]."',
            account_type 		= '".$_POST["account_type"]."'
			WHERE employee_id 	= '".$_POST["employee_id"]."'
			";
		}
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Bank Details Edited';
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
		UPDATE bank_details 
		SET account_status      = :account_status 
		WHERE employee_id 	    = :employee_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':account_status'	=>	$status,
				':employee_id'		=>	$_POST["employee_id"]
			)
		);	
		$result = $statement->fetchAll();	
		if(isset($result))
		{
			echo 'Account Status changed to ' . $status;
		}
	}
}

?>