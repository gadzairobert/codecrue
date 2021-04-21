<?php

//user_action.php

include('database_connection.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO payrol (pay_date, full_name, rate_per_hour, basic_salary, loan_advance, uif, loan_repay, net_salary, pay_status) 
		VALUES (:pay_date, :full_name, :rate_per_hour, :basic_salary, :loan_advance, :uif, :loan_repay, :net_salary, :pay_status)
        ";	

		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':pay_date'	            =>	$_POST["pay_date"],
				':full_name'		    =>	$_POST["full_name"],
                ':rate_per_hour'		=>	$_POST["rate_per_hour"],
                ':basic_salary'		    =>	$_POST["basic_salary"],
                ':loan_advance'		    =>	$_POST["loan_advance"],
                ':uif'		            =>	$_POST["uif"],
                ':loan_repay'		    =>	$_POST["loan_repay"],
                ':net_salary'			=>	$_POST["basic_salary"] + $_POST["loan_advance"] - ($_POST["uif"] + $_POST["loan_repay"]),
				':pay_status'	        =>	'UNPAID'
            )
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Payslip Added';
		}
	}
	//------start pay view
	if($_POST['btn_action'] == 'pay_details')
	{
		$query = "
		Select p.pay_date, e.employee_code, p.full_name, bd.bank_name, bd.bank_account,
		p.rate_per_hour, p.basic_salary, p.loan_advance, p.uif, p.net_salary 
		from payrol p inner join employee e
		on p.full_name = e.full_name
		inner join bank_details bd 
		on e.employee_code = bd.employee_code 
		where p.pay_id = '".$_POST["pay_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '
		<center><h4 class="modal-title" style="color:#069961;"><i class="fa fa-plus"></i> PaySlip Details </h4></center>
		
		<div class="table-responsive">
			<table class="table table-boredered">
			<tr style="background-color:#069961; color:white">
				<th>Pay Date</th>
				<th>Emp Code</th>
				<th>Emp Name</th>
				<th>Bank Name</th>
				<th>Bank Acc</th>
				<th>Rate/Hr</th>
				<th>Gross Pay</th>
				<th>Loan Adv</th>
				<th>UIF </th>
				<th>Nett Pay </th>
			</tr>
		';
		foreach($result as $row)
		{
			$output .= '
			
			<tr>
				<td>'.$row["pay_date"].'</td>
				<td>'.$row["employee_code"].'</td>
				<td>'.$row["full_name"].'</td>
				<td>'.$row["bank_name"].'</td>
				<td>'.$row["bank_account"].'</td>
				<td>'.$row["rate_per_hour"].'</td>
				<td>'.$row["basic_salary"].'</td>
				<td>'.$row["loan_advance"].'</td>
				<td>'.$row["uif"].'</td>
				<td>'.$row["net_salary"].'</td>
			</tr>
			';
		}
		$output .= '
			</table>
		</div>
		';
		echo $output;
	}
	//------end pay view
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM payrol WHERE pay_id = :pay_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':pay_id'	=>	$_POST["pay_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['full_name']        = $row['full_name'];
			$output['pay_date']         = $row['pay_date'];
            $output['basic_salary']     = $row['basic_salary'];
			$output['loan_advance']     = $row['loan_advance'];
            $output['rate_per_hour']    = $row['rate_per_hour'];
            $output['uif']              = $row['uif'];
            $output['loan_repay']       = $row['loan_repay'];
            $output['net_salary']       = $row['net_salary'];
		}
		echo json_encode($output);
	}
	if($_POST['btn_action'] == 'Edit')
	{
		if($_POST['full_name'] != '')
		{
			$query = "
				UPDATE payrol SET 
				full_name   	= '".$_POST["full_name"]."',
				pay_date 		= '".$_POST["pay_date"]."',
				basic_salary 	= '".$_POST["basic_salary"]."', 
				loan_advance 	= '".$_POST["loan_advance"]."',
				rate_per_hour 	= '".$_POST["rate_per_hour"]."', 
				uif  			= '".$_POST["uif"]."',
				loan_repay 		= '".$_POST["loan_repay"]."',
				net_salary 		= '".$_POST["basic_salary"] + $_POST["loan_advance"] - ($_POST["uif"] + $_POST["loan_repay"])."',
				WHERE pay_id 	= '".$_POST["pay_id"]."'
			";
		}
		else
		{
			$query = "
				UPDATE payrol SET 
				full_name   	= '".$_POST["full_name"]."',
				pay_date 		= '".$_POST["pay_date"]."',
				basic_salary 	= '".$_POST["basic_salary"]."', 
				loan_advance 	= '".$_POST["loan_advance"]."',
				rate_per_hour 	= '".$_POST["rate_per_hour"]."', 
				uif  			= '".$_POST["uif"]."',
				loan_repay 		= '".$_POST["loan_repay"]."',
				net_salary 		= '".$_POST["basic_salary"] + $_POST["loan_advance"] - ($_POST["uif"] + $_POST["loan_repay"])."',
				WHERE pay_id 	= '".$_POST["pay_id"]."'
			";
		}
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Payslip Details Edited';
		}
	}
	if($_POST['btn_action'] == 'delete')
	{
		$status = 'PAID';
		if($_POST['status'] == 'PAID')
		{
			$status = 'PAID';
		}
		$query = "
		UPDATE payrol 
		SET pay_status 	= 'PAID'
		WHERE pay_id 	= :pay_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				//':pay_status'	=>	$status,
				':pay_id'		=>	$_POST["pay_id"]
			)
		);	
		$result = $statement->fetchAll();	
		if(isset($result))
		{
			echo '' . $status;
		}
	}
}

?>