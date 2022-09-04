<?php

//purchase_action.php

include('database_connection.php');

include('function.php');

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO purchase (user_id, agent_name, inventory_purchase_total, inventory_purchase_status, inventory_purchase_created_date) 
		VALUES (:user_id, :agent_name, :inventory_purchase_total, :inventory_purchase_status, :inventory_purchase_created_date)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':user_id'							=>	$_SESSION["user_id"],
				':agent_name'						=>	$_POST['agent_name'],
				':inventory_purchase_total'			=>	0,
				':inventory_purchase_status'		=>	'UNPAID',
				':inventory_purchase_created_date'	=>	date("Y-m-d")
			)
		);
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$inventory_purchase_id = $statement->fetchColumn();

		if(isset($inventory_purchase_id))
		{
			$total_amount = 0;
			for($count = 0; $count<count($_POST["stock_id"]); $count++)
			{
				$product_details = fetch_stock_details($_POST["stock_id"][$count], $connect);
				$sub_query = "
				INSERT INTO purchase_items (inventory_purchase_id, stock_id, gross_quantity, deducted, uom, net_quantity, unit_price, net_price,item_date) VALUES (:inventory_purchase_id, :stock_id,  :gross_quantity, :deducted, :uom, :net_quantity, :unit_price, :net_price, :item_date)
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':inventory_purchase_id'	=>	$inventory_purchase_id,
						':stock_id'					=>	$_POST["stock_id"][$count],
						':gross_quantity'			=>	$_POST["gross_quantity"][$count],
						':deducted'					=>	$_POST["deducted"][$count],
						':uom'						=>	$_POST["uom"][$count],
						':net_quantity'				=>	$_POST["net_quantity"][$count],
						':unit_price'				=>	$_POST["unit_price"][$count],
						':net_price'				=>	($_POST["net_quantity"][$count] * $_POST["unit_price"][$count]),
						':item_date'				=>	date("Y-m-d")
					)
				);
				$base_price = $_POST["unit_price"][$count];
				$net_price = $_POST["unit_price"][$count] * $_POST["net_quantity"][$count];
				$total_amount = $total_amount + $net_price;

				$update_query1 = "
				UPDATE stock 
				SET stock.stock_qty = 
				(SELECT gross_quantity from purchase_items where stock_id = '".$_POST["stock_id"][$count]."'
				and inventory_purchase_id = '".$inventory_purchase_id."') + stock.stock_qty
				WHERE stock.stock_id = '".$_POST["stock_id"][$count]."' ";
				$statement = $connect->prepare($update_query1);
				$statement->execute();
			}
			
			$update_query = "
			UPDATE purchase 
			SET inventory_purchase_total = '".$total_amount."' 
			WHERE inventory_purchase_id = '".$inventory_purchase_id."'
			";
			$statement = $connect->prepare($update_query);
			$statement->execute();
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo 'Purchase No ';
				echo $inventory_purchase_id;
				echo ' Created';
			}

		}
	}

	if($_POST['btn_action'] == 'purchase_details')
	{
		$query = "
		SELECT 
		purchase.inventory_purchase_id,
		purchase.inventory_purchase_created_date,
		stock.item_name,
		sale_agent.agent_name,
		purchase_items.gross_quantity,
		purchase_items.deducted,
		purchase_items.uom,
		purchase_items.net_quantity,
		purchase_items.unit_price,
		purchase_items.net_price
		FROM purchase_items  
		INNER JOIN purchase 
		ON purchase.inventory_purchase_id = purchase_items.inventory_purchase_id 
		inner join stock
		on stock.stock_id = purchase_items.stock_id
		inner join sale_agent
		on sale_agent.agent_name = purchase.agent_name
		WHERE purchase_items.inventory_purchase_id = '".$_POST["inventory_purchase_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '
		<center><h4 class="modal-title" style="color:#069961;"><i class="fa fa-plus"></i> Purchase Details </h4></center>
		
		<div class="table-responsive">
			<table class="table table-boredered">
			<tr style="background-color:#069961; color:white">
				<th>Purchase No</th>
				<th>Purchase Date</th>
				<th>Bought From</th>
				<th>Product Name</th>
				<th>Gross Qty</th>
				<th>Deducted</th>
				<th>Nett Qty</th>
				<th>Unit Price</th>
				<th>Net Price</th>
			</tr>
		';
		foreach($result as $row)
		{
			$output .= '
			
			<tr>
				<td>'.$row["inventory_purchase_id"].'</td>
				<td>'.$row["inventory_purchase_created_date"].'</td>
				<td>'.$row["agent_name"].'</td>
				<td>'.$row["item_name"].'</td>
				<td>'.$row["gross_quantity"].'</td>
				<td>'.$row["deducted"].' '.$row["uom"].'</td>
				<td>'.$row["net_quantity"].'</td>
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

	//---------------
	if($_POST['btn_action'] == 'fetch_single')
	{
		$query = "
		SELECT * FROM purchase WHERE inventory_purchase_id = :inventory_purchase_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_purchase_id'	=>	$_POST["inventory_purchase_id"]
			)
		);
		$result = $statement->fetchAll();
		$output = array();
		foreach($result as $row)
		{
			$output['inventory_purchase_total'] = $row['inventory_purchase_total'];
		}
		$sub_query = "
		SELECT * FROM purchase_items 
		WHERE inventory_purchase_id = '".$_POST["inventory_purchase_id"]."'
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
				$("#product_id'.$count.'").selectpicker("val", '.$sub_row["product_id"].');
				$(".selectpicker").selectpicker();
			});
			</script>
			<span id="row'.$count.'">
				<div class="row">
					<div class="col-md-2">
						<select name="stock_id[]" id="stock_id'.$count.'" value="'.$sub_row["stock_id"].'" class="form-control selectpicker" data-live-search="true" required>
							'.fill_stock_list($connect).'
						</select>
					</div>
					<div class="col-md-3">
						<input type="text" name="item_name[]" placeholder="enter item name" class="form-control" value="'.$sub_row["item_name"].'" required />
					</div>
					<div class="col-md-2">
						<input type="text" name="gross_quantity[]" placeholder="Gross Qty"  onkeyup="AutoCalc(this)" class="form-control" value="'.$sub_row["gross_quantity"].'" required />
					</div>
					<div class="col-md-2">
						<input type="text" name="deducted[]" placeholder="deducted"  onkeyup="AutoCalc(this)" class="form-control" value="'.$sub_row["deducted"].'" required />
					</div>
					<div class="col-md-2 hidden">
						<input type="hidden" name="net_quantity[]" class="form-control" onkeyup="AutoCalc1()" value="'.$sub_row["net_quantity"].'" readonly required />
					</div>
					<div class="col-md-2">
						<input type="text" name="unit_price[]" class="form-control" onkeyup="AutoCalc1()" placeholder="Unit Price" value="'.$sub_row["unit_price"].'" required />
					</div>
					<div class="col-md-1 hidden">
						<input type="hidden" name="net_price[]" class="form-control" placeholder="nett Price" value="'.$sub_row["net_price"].'" readonly  />
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
		DELETE FROM purchase_items 
		WHERE inventory_purchase_id = '".$_POST["inventory_purchase_id"]."'
		";
		$statement = $connect->prepare($delete_query);
		$statement->execute();
		$delete_result = $statement->fetchAll();
		if(isset($delete_result))
		{
			$total_amount = 0;
			for($count = 0; $count < count($_POST["stock_id"]); $count++)
			{
				$product_details = fetch_stock_details($_POST["stock_id"][$count], $connect);
				$sub_query = "
				INSERT INTO purchase_items (inventory_purchase_id,  stock_id, gross_quantity, deducted, uom, net_quantity, unit_price, net_price, item_date) VALUES (:inventory_purchase_id,  :stock_id, :gross_quantity, :deducted, :uom, :net_quantity, :unit_price, :net_price, :item_date)
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':inventory_purchase_id'	=>	$_POST["inventory_purchase_id"],
						':stock_id'					=>	$_POST["stock_id"][$count],
						':gross_quantity'			=>	$_POST["gross_quantity"][$count],
						':deducted'					=>	$_POST["deducted"][$count],
						':uom'						=>	$_POST["uom"][$count],
						':net_quantity'				=>	$_POST["net_quantity"][$count],
						':unit_price'				=>	$_POST["unit_price"][$count],
						':net_price'				=>	($_POST["net_quantity"][$count] * $_POST["unit_price"][$count]),
						':item_date'				=>	date("Y-m-d")
					)
				);
				$base_price = $_POST["unit_price"][$count];
				$net_price = $_POST["unit_price"][$count] * $_POST["net_quantity"][$count];
				$total_amount = $total_amount + $net_price;
			}

			$update_query = "
			UPDATE stock, purchase_items 
			SET stock.stock_qty = purchase_items.gross_quantity + stock.stock_qty 
			WHERE stock.stock_id :stock_id and purchase_items.inventory_purchase_id =:inventory_purchase_id
			";
			$statement = $connect->prepare($update_query);
			$statement->execute(
				array(
					':stock_id'					=>	$_POST["stock_id"],
					':inventory_purchase_id'	=>	$_POST["inventory_purchase_id"]
				)
			);

			$update_query = "
			UPDATE purchase 
			SET inventory_purchase_total = :inventory_purchase_total, 
			WHERE inventory_purchase_id = :inventory_purchase_id
			";
			$statement = $connect->prepare($update_query);
			$statement->execute(
				array(
					':inventory_purchase_total'			=>	$total_amount,
					':inventory_purchase_id'			=>	$_POST["inventory_purchase_id"]
				)
			);
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo 'Purchase Edited Successfully...';
			}
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
		UPDATE purchase 
		SET inventory_purchase_status = 'PAID' 
		WHERE inventory_purchase_id = :inventory_purchase_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				// ':inventory_purchase_status'	=>	$status,
				':inventory_purchase_id'		=>	$_POST["inventory_purchase_id"]
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
