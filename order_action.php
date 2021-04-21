<?php

//order_action.php

include('database_connection.php');

include('function.php');

$inventory_order_purchase_id = 0;

if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'load_brand')
	{
		echo fill_customer_list($connect, $_POST['inventory_order_name']);
	}
	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO inventory_order (user_id, inventory_order_total, inventory_order_name, inventory_order_status, inventory_order_created_date, invoice_no) 
		VALUES (:user_id, :inventory_order_total, :inventory_order_name, :inventory_order_status, :inventory_order_created_date,:invoice_no)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':user_id'						=>	$_SESSION["user_id"],
				':inventory_order_total'		=>	0,
				':inventory_order_name'			=>	$_POST['inventory_order_name'],
				':invoice_no'					=>	$_POST['invoice_no'],
				':inventory_order_status'		=>	'active',
				':inventory_order_created_date'	=>	$_POST['inventory_order_created_date'],
			)
		);
		$result = $statement->fetchAll();
		$statement = $connect->query("SELECT LAST_INSERT_ID()");
		$inventory_order_id = $statement->fetchColumn();

		if(isset($inventory_order_id))
		{
			$total_amount = 0;
			for($count = 0; $count<count($_POST["stock_id"]); $count++)
			{
				$product_details = fetch_stock_details($_POST["stock_id"][$count], $connect);
				$sub_query = "
				INSERT INTO inventory_order_product (inventory_order_id, stock_id, quantity, deduct, uom, qty_nett, price, nett_price, tax) VALUES (:inventory_order_id, :stock_id, :quantity, :deduct, :uom, :qty_nett, :price, :nett_price, :tax)
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':inventory_order_id'			=>	$inventory_order_id,
						':stock_id'						=>	$_POST["stock_id"][$count],
						':quantity'						=>	$_POST["quantity"][$count],
						':deduct'						=>	$_POST["deduct"][$count],
						':uom'							=>	$_POST["uom"][$count],
						':qty_nett'						=>	$_POST["qty_nett"][$count],
						':price'						=>	$_POST["price"][$count],
						':nett_price'					=>	($_POST["qty_nett"][$count] * $_POST["price"][$count]),
						':tax'							=>	'1.15'
					)
				);
				$base_price 	= $_POST["price"][$count];
				$net_price 		= $_POST["price"][$count] * $_POST["qty_nett"][$count];
				$total_amount 	= $total_amount + ($net_price * '1.15');

				$update_query1 = "
				UPDATE stock 
				SET stock.stock_qty = 
				stock.stock_qty - (SELECT quantity from inventory_order_product where stock_id = '".$_POST["stock_id"][$count]."'
				and inventory_order_id = '".$inventory_order_id."') 
				WHERE stock.stock_id = '".$_POST["stock_id"][$count]."' ";
				$statement = $connect->prepare($update_query1);
				$statement->execute();
			}
			$update_query = "
			UPDATE inventory_order 
			SET inventory_order_total 	= '".$total_amount."' 
			WHERE inventory_order_id 	= '".$inventory_order_id."'
			";
/* 
			if($_POST["inventory_order_purchase_id"]) {
				$update_query = "
				UPDATE purchase_items, inventory_order_product 
				SET purchase_items.gross_quantity = purchase_items.gross_quantity - inventory_order_product.quantity 
				WHERE purchase_items.inventory_order_purchase_id = '".$inventory_order_purchase_id."'
				";
			} */

			$statement = $connect->prepare($update_query);
			$statement->execute();
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo 'Order ';
				echo $inventory_order_id;
				echo ' created ';
			}
		}
	}
	
//---- Order Details Modal
	if($_POST['btn_action'] == 'order_details')
	{
		$query = "
		SELECT 
		inventory_order.inventory_order_id,
		user_details.user_name,
		stock.item_name,
		inventory_order.inventory_order_created_date,
		inventory_order.invoice_no,
		inventory_order_product.quantity,
		inventory_order_product.deduct,
		inventory_order_product.uom,
		inventory_order_product.qty_nett,
		inventory_order_product.price,
		inventory_order_product.nett_price
		FROM inventory_order_product  
		INNER JOIN inventory_order 
		ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id 
		INNER JOIN user_details 
		ON user_details.user_id = inventory_order.user_id 
		inner join stock
		on inventory_order_product.stock_id = stock.stock_id
		WHERE inventory_order_product.inventory_order_id = '".$_POST["inventory_order_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '
		<center><h4 class="modal-title" style="color:#069961;"><i class="fa fa-plus"></i> Order Details </h4></center>
		
		<div class="table-responsive">
			<table class="table table-boredered">
			<tr style="background-color:#069961; color:white">
				<th>Order No</th>
				<th>Order Date</th>
				<th>Cashier</th>
				<th>Product Name</th>
				<th>Invoice No</th>
				<th>Gross Qty</th>
				<th>Deducted</th>
				<th>Nett Qty</th>
				<th>Unit Price</th>
				<th>Net Price (VAT Excl)</th>
			</tr>
		';
		foreach($result as $row)
		{
			$output .= '
			
			<tr>
				<td>'.$row["inventory_order_id"].'</td>
				<td>'.$row["inventory_order_created_date"].'</td>
				<td>'.$row["user_name"].'</td>
				<td>'.$row["item_name"].'</td>
				<td>'.$row["invoice_no"].'</td>
				<td>'.$row["quantity"].'</td>
				<td>'.$row["deduct"].' '.$row["uom"].'</td>
				<td>'.$row["qty_nett"].'</td>
				<td>R '.$row["price"].'</td>
				<td>R '.$row["nett_price"].'</td>
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
		SELECT * FROM inventory_order WHERE inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_order_id'	=>	$_POST["inventory_order_id"]
			)
		);
		$result = $statement->fetchAll();
		$output = array();
		foreach($result as $row)
		{
			$output['inventory_order_created_date'] = $row['inventory_order_created_date'];
			$output['invoice_no'] = $row['invoice_no'];
		}
		$sub_query = "
		SELECT * FROM inventory_order_product 
		WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
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
				$("#inventory_order_id'.$count.'").selectpicker("val", '.$sub_row["inventory_order_id"].');
				$(".selectpicker").selectpicker();
			});
			</script>
			<span id="row'.$count.'">
				<div class="row">
					<div class="col-md-3">
						<select name="stock_id[]" id="stock_id'.$count.'" class="form-control selectpicker" data-live-search="true" required>
							'.fill_stock_list($connect).'
						</select>
						<input type="hidden" name="hidden_product_id[]" id="hidden_product_id'.$count.'" value="'.$sub_row["inventory_order_purchase_id"].'" />
					</div>
					<div class="col-md-3">
						<input type="text" name="quantity[]" placeholder="Gross Qty"  onkeyup="AutoCalc(this)" class="form-control" value="'.$sub_row["quantity"].'" required />
					</div>
					<div class="col-md-3">
						<input type="text" name="deduct[]" placeholder="Deduct"  onkeyup="AutoCalc(this)" class="form-control" value="'.$sub_row["deduct"].'" required />
					</div>
					<div class="col-md-2">
						<input type="hidden" name="qty_nett[]" class="form-control" onkeyup="AutoCalc1()" value="'.$sub_row["qty_nett"].'" readonly required />
					</div>
					<div class="col-md-2">
						<input type="text" name="price[]" class="form-control" onkeyup="AutoCalc1()" placeholder="Unit Price" value="'.$sub_row["price"].'" required />
					</div>
					<div class="col-md-1 hidden">
						<input type="hidden" name="nett_price[]" class="form-control" placeholder="nett Price" value="'.$sub_row["nett_price"].'" readonly required />
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
		DELETE FROM inventory_order_product 
		WHERE inventory_order_id = '".$_POST["inventory_order_id"]."'
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
				INSERT INTO inventory_order_product (inventory_order_id, stock_id, quantity, deduct, uom, qty_nett, price, nett_price, tax) VALUES (:inventory_order_id, :stock_id, :quantity, :deduct, :uom, :qty_nett, :price, :nett_price, :tax)
				";
				$statement = $connect->prepare($sub_query);
				$statement->execute(
					array(
						':inventory_order_id'			=>	$_POST["inventory_order_id"],
						':stock_id'						=>	$_POST["stock_id"][$count],
						':quantity'						=>	$_POST["quantity"][$count],
						':deduct'						=>	$_POST["deduct"][$count],
						':uom'							=>	$_POST["uom"][$count],
						':qty_nett'						=>	$_POST["qty_nett"][$count],
						':price'						=>	$_POST["price"][$count],
						':nett_price'					=>	($_POST["qty_nett"][$count] * $_POST["price"][$count]),
						':tax'							=>	'1.15',
					)
				);
				$base_price 	= $_POST["price"][$count];
				$net_price 		= $_POST["qty_nett"][$count] * $_POST["price"][$count];
				$total_amount 	= $net_price * '1.15';
			}
			$update_query = "
			UPDATE inventory_order SET 
			inventory_order_name 			= :inventory_order_name, 
			inventory_order_created_date 	= :inventory_order_created_date, 
			invoice_no 						= :invoice_no, 
			inventory_order_total 			= :inventory_order_total, 
			WHERE inventory_order_id 		= :inventory_order_id
			";
			$statement = $connect->prepare($update_query);
			$statement->execute(
				array(
					':inventory_order_name'			=>	$_POST["inventory_order_name"],
					':inventory_order_created_date'	=>	$_POST["inventory_order_created_date"],
					':invoice_no'					=>	$_POST["invoice_no"],
					':inventory_order_total'		=>	$total_amount,
					':inventory_order_id'			=>	$_POST["inventory_order_id"]
				)
			);
			$result = $statement->fetchAll();
			if(isset($result))
			{
				echo 'Order Edited...';
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
		UPDATE inventory_order 
		SET inventory_order_status = :inventory_order_status 
		WHERE inventory_order_id = :inventory_order_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':inventory_order_status'	=>	$status,
				':inventory_order_id'		=>	$_POST["inventory_order_id"]
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Order status change to ' . $status;
		}
	}
}


?>
