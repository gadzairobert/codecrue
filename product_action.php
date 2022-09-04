<?php

//product_action.php

include('database_connection.php');

include('function.php');


if(isset($_POST['btn_action']))
{
	if($_POST['btn_action'] == 'load_brand')
	{
		echo fill_brand_list($connect, $_POST['category_id']);
	}

	if($_POST['btn_action'] == 'Add')
	{
		$query = "
		INSERT INTO product (category_id, product_name, product_quantity,  unit_price, product_base_price, product_tax, product_minimum_order, product_enter_by, product_status, product_date) 
		VALUES (:category_id, :product_name, :product_quantity, :unit_price, :product_base_price, :product_tax, :product_minimum_order, :product_enter_by, :product_status, :product_date)
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':category_id'			=>	$_POST['category_id'],
				':product_name'			=>	$_POST['product_name'],
				':product_quantity'		=>	$_POST['product_quantity'],
				':unit_price'			=>	$_POST['unit_price'],
				':product_base_price'	=>	$_POST['product_base_price'],
				':product_tax'			=>	$_POST['product_tax'],
				':product_minimum_order'=>	$_POST['product_minimum_order'],
				':product_enter_by'		=>	$_SESSION["user_id"],
				':product_status'		=>	'active',
				':product_date'			=>	date("Y-m-d")
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Added';
		}
	}
	if($_POST['btn_action'] == 'product_details')
	{
		$query = "
		SELECT * FROM product 
		INNER JOIN category ON category.category_id = product.category_id 
		INNER JOIN user_details ON user_details.user_id = product.product_enter_by 
		WHERE product.product_id = '".$_POST["product_id"]."'
		";
		$statement = $connect->prepare($query);
		$statement->execute();
		$result = $statement->fetchAll();
		$output = '
		<div class="table-responsive">
			<table class="table table-boredered">
		';
		foreach($result as $row)
		{
			$status = '';
			if($row['product_status'] == 'active')
			{
				$status = '<span class="label label-success">Active</span>';
			}
			else
			{
				$status = '<span class="label label-danger">Inactive</span>';
			}
			$output .= '
			<tr>
				<td>Product Name</td>
				<td>Product Name</td>
				<td>Product Name</td>
				<td>Product Name</td>
				<td>Product Name</td>
				<td>Product Name</td>
				<td>Product Name</td>
				<!--<td>'.$row["product_date"].'</td>-->
			</tr>
			<tr>
				<td>'.$row["product_name"].'</td>
				<td>'.$row["product_name"].'</td>
				<td>'.$row["product_name"].'</td>
				<td>'.$row["product_name"].'</td>
				<td>'.$row["product_name"].'</td>
				<td>'.$row["product_name"].'</td>
				<td>'.$row["product_name"].'</td>
			</tr>
			<!--<tr>
				<td>Category</td>
				<td>'.$row["category_name"].'</td>
			</tr>
			<tr>
				<td>Gross Qty</td>
				<td>'.$row["product_quantity"].'</td>
			</tr>
			<tr>
				<td>Deductions</td>
				<td>'.$row["product_minimum_order"].'</td>
			</tr>
			<tr>
				<td>Nett Qty</td>
				<td>'.$row["product_tax"].'</td>
			</tr>
			<tr>
				<td>Unit Price</td>
				<td>'.$row["unit_price"].'</td>
			</tr>
				<td>Nett Price</td>
				<td>'.$row["product_base_price"].'</td>
			</tr>
			
			<tr>
				<td>Enter By</td>
				<td>'.$row["user_name"].'</td>
			</tr>
			<tr>
				<td>Status</td>
				<td>'.$status.'</td>
			</tr>-->
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
		SELECT * FROM product WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_id'	=>	$_POST["product_id"]
			)
		);
		$result = $statement->fetchAll();
		foreach($result as $row)
		{
			$output['product_date'] = $row['product_date'];
			$output['product_name'] = $row['product_name'];
			$output['category_id'] = $row['category_id'];
			$output['product_quantity'] = $row['product_quantity'];
			$output['product_minimum_order'] = $row['product_minimum_order'];
			$output['product_tax'] = $row['product_tax'];
			$output['product_unit'] = $row['product_unit'];
			$output['unit_price'] = $row['unit_price'];
			$output['product_base_price'] = $row['product_base_price'];
			
		}
		echo json_encode($output);
	}

	if($_POST['btn_action'] == 'Edit')
	{
		$query = "
			UPDATE product set
			product_date = :product_date,
			product_name = :product_name,
			category_id = :category_id, 
			product_quantity = :product_quantity, 
			product_minimum_order = :product_minimum_order, 
			product_tax = :product_tax, 
			product_unit = :product_unit,
			unit_price = :unit_price, 
			product_base_price = :product_base_price
			WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_date'			=>	$_POST['product_date'],
				':product_name'			=>	$_POST['product_name'],
				':category_id'			=>	$_POST['category_id'],
				':product_quantity'		=>	$_POST['product_quantity'],
				':product_minimum_order'=>	$_POST['product_minimum_order'],
				':product_tax'			=>	$_POST['product_tax'],
				':product_unit'			=>	$_POST['product_unit'],
				':unit_price'			=>	$_POST['unit_price'],
				':product_base_price'	=>	$_POST['product_base_price'],
				':product_id'			=>	$_POST['product_id']
			)
		);
		$result = $statement->fetchAll();
		if(isset($result))
		{
			echo 'Product Details Edited';
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
		UPDATE product 
		SET product_status = :product_status 
		WHERE product_id = :product_id
		";
		$statement = $connect->prepare($query);
		$statement->execute(
			array(
				':product_status'	=>	$status,
				':product_id'		=>	$_POST["product_id"]
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