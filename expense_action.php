<?php

//expense_action.php

include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO expense (user_id, expense_total, expense_status, expense_date) 
		VALUES (:user_id, :expense_total, :expense_status, :expense_date)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':user_id'			=>	$_SESSION["user_id"],
				':expense_total'	=>	0,
				':expense_status'   =>	'active',
				':expense_date'	    =>	date("Y-m-d")
			)
		);
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$expense_id = $statement->fetchColumn();

		if(isset($expense_id))
		{
			$total_amount = 0;
			for($count = 0; $count<count($_POST["exp_id"]); $count++)
			{
				$product_details = fetch_expense_details($_POST["exp_id"][$count], $connect);
				$sub_query = "
				INSERT INTO expense_items (expense_id, exp_id, expense_item, quantity, unit_price, net_price) 
                VALUES (:expense_id, :exp_id, :expense_item, :quantity, :unit_price, :net_price)
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':expense_id'	=>	$expense_id,
						':exp_id'		=>	$_POST["exp_id"][$count],
						':expense_item'	=>	$_POST["expense_item"][$count],
						':quantity'		=>	$_POST["quantity"][$count],
						':unit_price'	=>	$_POST["unit_price"][$count],
						':net_price'	=>	$_POST["quantity"][$count] * $_POST["unit_price"][$count],
                    )
				);
				$base_price 	= $_POST["unit_price"][$count];
				$net_price 		= $_POST["unit_price"][$count] * $_POST["quantity"][$count];
				$total_amount 	= $total_amount + $net_price;
			}
			$update_query = "
			UPDATE expense 
			SET expense_total = '".$total_amount."' 
			WHERE expense_id = '".$expense_id."'
			";
			$statement = $connect->prepare($update_query);
			$statement->execute();
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo 'Expense Created...Expense number ';
				echo $expense_id;
			}
		}
	}

	if($_POST['btn_action'] == 'expense_details')
	{
		$query = "
		SELECT 
		expense.expense_id,
		expense.expense_date,
		expense_items.expense_item,
		expense_items.quantity,
		expense_items.unit,
		expense_items.unit_price,
		expense_items.net_price
		FROM expense_items  
		INNER JOIN expense 
		ON expense_items.expense_id = expense.expense_id 
		WHERE expense_items.expense_id = '".$_POST["expense_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '
		<center><h4 class="modal-title" style="color:#069961;"><i class="fa fa-plus"></i> Expenditure Details </h4></center>
		
		<div class="table-responsive">
			<table class="table table-boredered">
			<tr style="background-color:#069961; color:white">
				<th>Expense No</th>
				<th>Expense Date</th>
				<th>Item Name</th>
				<th>Quantity</th>
				<th>Unit Price</th>
				<th>Net Price</th>
			</tr>
		';
		foreach($result as $row)
		{
			$output .= '
			
			<tr>
				<td>'.$row["expense_id"].'</td>
				<td>'.$row["expense_date"].'</td>
				<td>'.$row["expense_item"].'</td>
				<td>'.$row["quantity"].' '.$row["unit"].'</td>
				<td>'.$row["unit_price"].'</td>
				<td>'.$row["net_price"].'</td>
			</tr>
			';
		}
		$output .= '
			</table>
		</div>
		';
		echo $output;
	}

	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM expense WHERE expense_id = :expense_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':expense_id'	=>	$_POST["expense_id"]
			)
		);
		$result = $statement->fetchAll();
		$output = array();
		foreach($result as $row)
		{
			$output['expense_total'] = $row['expense_total'];
		}
		$sub_query = "
		SELECT * FROM expense_items 
		WHERE expense_id = '".$_POST["expense_id"]."'
		";
		$statement = $connect->prepare($sub_query);
		$statement->execute();
		$sub_result = $statement->fetchAll();
		$product_details = '';
		$count = '';
		foreach($sub_result as $sub_row)
		{
			$product_details .= '
			<script>
			$(document).ready(function(){
				$("#expense_id'.$count.'").selectpicker("val", '.$sub_row["expense_id"].');
				$(".selectpicker").selectpicker();
			});
			</script>
			<span id="row'.$count.'">
				<div class="row">
					<div class="col-md-2">
						<select name="exp_id[]" id="exp_id'.$count.'" class="form-control selectpicker" data-live-search="true" required>
							'.fill_expense_list($connect).'
						</select>
					</div>
					<div class="col-md-3">
						<input type="text" name="expense_item[]" class="form-control" value="'.$sub_row["expense_item"].'" required />
					</div>
					<div class="col-md-2">
						<input type="text" name="quantity[]" class="form-control" value="'.$sub_row["quantity"].'" required />
					</div>
					<div class="col-md-2">
						<select id="unit" name="unit[]" class="form-control" value="'.$sub_row["unit"].'" required>
							<option value="kg"> kg</option>
							<option value="litre"> litre</option>
							<option value="meter"> meter</option>
						</select>
					</div>
					<div class="col-md-2">
						<input type="text" name="unit_price[]" class="form-control" value="'.$sub_row["unit_price"].'" required />
					</div>
					<div class="col-md-1 hidden">
						<input type="hidden" name="net_price[]" class="form-control" value="'.$sub_row["net_price"].'" readonly />
					</div>
					<div class="col-md-1">
			';

			if($count == '')
			{
				$product_details .= '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			}
			else
			{
				$product_details .= '<button type="button" name="remove" id="'.$count.'" class="btn btn-danger btn-xs remove">-</button>';
			}
			$product_details .= '
						</div>
					</div>
				</div><br />
			</span>
			';
			$count = $count + 1;
		}
		$output['product_details'] = $product_details;
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$delete_query = "
		DELETE FROM expense_items 
		WHERE expense_id = '".$_POST["expense_id"]."'
		";
		$statement = $connect->prepare($delete_query);
		$statement->execute();
		$delete_result = $statement->fetchAll();
		if(isset($delete_result))
		{
			$total_amount = 0;
			for($count = 0; $count < count($_POST["exp_id"]); $count++)
			{
				$product_details = fetch_expense_details($_POST["exp_id"][$count], $connect);
				$sub_query = "
                INSERT INTO expense_items (expense_id, exp_id, expense_item, quantity, unit, unit_price, net_price) 
                VALUES (:expense_id, :exp_id, :expense_item, :quantity,:unit, :unit_price, :net_price)
                ";
				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':expense_id'	=>	$_POST["expense_id"],
						':exp_id'		=>	$_POST["exp_id"][$count],
						':expense_item'	=>	$_POST["expense_item"][$count],
						':quantity'		=>	$_POST["quantity"][$count],
						':unit'			=>	$_POST["unit"][$count],
						':unit_price'	=>	$_POST["unit_price"][$count],
						':net_price'	=>	$_POST["quantity"][$count] * $_POST["unit_price"][$count]
					)
				);
				$base_price = $_POST["unit_price"][$count];
				$net_price = $_POST["unit_price"][$count] * $_POST["quantity"][$count];
				$total_amount = $total_amount + $net_price;
			}
			$update_query = "
			UPDATE expense 
			SET expense_total = :expense_total, 
			WHERE expense_id = :expense_id
			";
			$statement = $connect->prepare($update_query);
			$statement->execute(
				array(
					':expense_total'		=>	$total_amount,
					':expense_id'			=>	$_POST["expense_id"]
				)
			);
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo 'Expense Edited Successfully...';
			}
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
		UPDATE expense 
		SET expense_status = :expense_status 
		WHERE expense_id = :expense_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':expense_status'	=>	$status,
				':expense_id'		=>	$_POST["expense_id"]
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
