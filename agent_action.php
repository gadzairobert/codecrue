<?php

//agent_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO sale_agent (agent_name, id_no, address, cell_no, agent_status) 
		VALUES (:agent_name, :id_no, :address, :cell_no, :agent_status)
		";	
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':agent_name'	=>	$_POST["agent_name"],
				':id_no'	    =>	$_POST["id_no"],
                ':address'		=>	$_POST["address"],
				':cell_no'		=>	$_POST["cell_no"],
				':agent_status'	=>	'active'
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'New Sale Agent Added';
		}
	}
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM sale_agent WHERE agent_id = :agent_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':agent_id'	=>	$_POST["agent_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['agent_name'] 	= $row['agent_name'];
			$output['id_no']        = $row['id_no'];
            $output['address']      = $row['address'];
			$output['cell_no']      = $row['cell_no'];
		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['agent_name'] != '')
		{
			$query = "
			UPDATE sale_agent SET 
			agent_name 		= '".$_POST["agent_name"]."',
			id_no 	        = '".$_POST["id_no"]."', 
			address 		= '".$_POST["address"]."', 
			cell_no 		= '".$_POST["cell_no"]."'
			WHERE agent_id 	= '".$_POST["agent_id"]."'
			";
		}
		else
		{
			$query = "
			UPDATE sale_agent SET 
			agent_name 		    = '".$_POST["agent_name"]."',
			id_no 	            = '".$_POST["id_no"]."', 
			address 			= '".$_POST["address"]."', 
			cell_no 			= '".$_POST["cell_no"]."'
			WHERE agent_id  	= '".$_POST["agent_id"]."'
			";
		}
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Sale Agent Details Edited';
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
		UPDATE sale_agent 
		SET agent_status =  :agent_status 
		WHERE agent_id 	    = :agent_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':agent_status'	=>	$status,
				':agent_id'		=>	$_POST["agent_id"]
			)
		);	
		$result = $statement->fetchAll();	
		if(isset($result))
		{
			echo 'Sale Agent Status changed to ' . $status;
		}
	}
}

?>