<?php

//user_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO employee (employee_code, full_name, phone_no, email, address, dob, id_no, nationality, roles, start_date, emp_type, employee_status) 
		VALUES (:employee_code, :full_name, :phone_no, :email, :address, :dob, :id_no, :nationality, :roles, :start_date, :emp_type, :employee_status)
		";	
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':employee_code'	=>	$_POST["employee_code"],
				':full_name'		=>	$_POST["full_name"],
                ':phone_no'		    =>	$_POST["phone_no"],
				':email'		    =>	$_POST["email"],
				':address'		    =>	$_POST["address"],
                ':dob'      		=>	$_POST["dob"],
                ':id_no'		    =>	$_POST["id_no"],
                ':nationality'		=>	$_POST["nationality"],
                ':roles'		    =>	$_POST["roles"],
                ':start_date'		=>	$_POST["start_date"],
                ':emp_type'		    =>	$_POST["emp_type"],
				':employee_status'	=>	'active'
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'New Employee Added';
		}
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM employee WHERE employee_id = :employee_id
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
			$output['employee_code'] = $row['employee_code'];
			$output['full_name']     = $row['full_name'];
            $output['phone_no']      = $row['phone_no'];
			$output['email']         = $row['email'];
			$output['address']       = $row['address'];
            $output['dob']           = $row['dob'];
            $output['id_no']         = $row['id_no'];
            $output['nationality']   = $row['nationality'];
            $output['roles']         = $row['roles'];
            $output['start_date']    = $row['start_date'];
			$output['emp_type']      = $row['emp_type'];
		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['full_name'] != '')
		{
			$query = "
			UPDATE employee SET 
				employee_code 		= '".$_POST["employee_code"]."',
                full_name 			= '".$_POST["full_name"]."', 
                phone_no 			= '".$_POST["phone_no"]."', 
				email 				= '".$_POST["email"]."', 
				address 			= '".$_POST["address"]."', 
                dob 				= '".$_POST["dob"]."', 
				id_no 				= '".$_POST["id_no"]."',
                nationality 		= '".$_POST["nationality"]."', 
                roles 				= '".$_POST["roles"]."', 
                start_date 			= '".$_POST["start_date"]."', 
                emp_type 			= '".$_POST["emp_type"]."' 
				WHERE employee_id 	= '".$_POST["employee_id"]."'
			";
		}
		else
		{
			$query = "
			UPDATE employee SET 
			employee_code			= '".$_POST["employee_code"]."', 
			full_name 				= '".$_POST["full_name"]."',
            phone_no 				= '".$_POST["phone_no"]."', 
			email 					= '".$_POST["email"]."', 
			address 				= '".$_POST["address"]."', 
            dob 					= '".$_POST["dob"]."', 
			id_no 					= '".$_POST["id_no"]."', 
            nationality 			= '".$_POST["nationality"]."', 
            roles 					= '".$_POST["roles"]."', 
            start_date 				= '".$_POST["start_date"]."', 
            emp_type 				= '".$_POST["emp_type"]."' 
            WHERE employee_id 		= '".$_POST["employee_id"]."'
			";
		}
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Employee Details Edited';
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'Active';
		if($_POST['status'] == 'Active')
		{
			$status = 'Inactive';
		}
		$query = "
		UPDATE employee 
		SET employee_status = :employee_status 
		WHERE employee_id = :employee_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':employee_status'	=>	$status,
				':employee_id'		=>	$_POST["employee_id"]
			)
		);	
		$result = $statement->fetchAll();	
		if(isset($result))
		{
			echo 'Employee Status change to ' . $status;
		}
	}
}

?>