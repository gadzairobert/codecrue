<?php
//function.php

function fill_category_list($connect)
{
	$query = "
	SELECT * FROM category 
	WHERE category_status = 'active' 
	ORDER BY category_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["category_id"].'">'.$row["category_name"].'</option>';
	}
	return $output;
}

function fill_stock_list($connect)
{
	$query = "
	SELECT * FROM stock 
	WHERE item_status = 'active' and stock_qty > '0'
	ORDER BY item_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["stock_id"].'">'.$row["item_name"].' ['.$row["stock_qty"].']</option>';
	}
	return $output;
}

function fill_stock_list1($connect)
{
	$query = "
	SELECT * FROM stock 
	WHERE item_status = 'active'
	ORDER BY item_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["stock_id"].'">'.$row["item_name"].' ['.$row["stock_qty"].']</option>';
	}
	return $output;
}

function fill_stock_list2($connect)
{
	$query = "
	SELECT * FROM stock 
	WHERE item_status = 'active'
	ORDER BY item_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["stock_id"].'">'.$row["item_name"].'</option>';
	}
	return $output;
}

function fill_expense_list($connect)
{
	$query = "
	SELECT * FROM exp_list 
	WHERE exp_status = 'active' 
	ORDER BY exp_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["exp_id"].'">'.$row["exp_name"].'</option>';
	}
	return $output;
}

function fill_customer_list($connect)
{
	$query = "
	SELECT * FROM inventory_customer 
	WHERE customer_status = 'active' 
	ORDER BY inventory_order_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["inventory_order_name"].'">'.$row["inventory_order_name"].'</option>';
	}
	return $output;
}

function fill_agent_list($connect)
{
	$query = "
	SELECT * FROM sale_agent 
	WHERE agent_status = 'active' 
	ORDER BY agent_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["agent_name"].'">'.$row["agent_name"].'</option>';
	}
	return $output;
}


function fill_brand_list($connect, $category_id)
{
	$query = "SELECT * FROM brand 
	WHERE brand_status = 'active' 
	AND category_id = '".$category_id."'
	ORDER BY brand_name ASC";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<option value="">Select Brand</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["brand_id"].'">'.$row["brand_name"].'</option>';
	}
	return $output;
}

function get_user_name($connect, $user_id)
{
	$query = "
	SELECT user_name FROM user_details WHERE user_id = '".$user_id."'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return $row['user_name'];
	}
}

function fill_product_list($connect)
{
	$query = "
	SELECT * FROM purchase_items 
	WHERE item_status = 'active' 
	ORDER BY item_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["inventory_order_purchase_id"].'">'.$row["item_name"].' ('.$row["gross_quantity"].')</option>';
	}
	return $output;
}

function fill_purchase_list($connect)
{
	$query = "
	SELECT * FROM purchase_items 
	WHERE item_status = 'active' 
	ORDER BY item_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["inventory_order_id"].'">'.$row["item_name"].'</option>';
	}
	return $output;
}

function fetch_category_details($category_id, $connect)
{
	$query = "
	SELECT * FROM category 
	WHERE category_id = '".$category_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['category_name'] = $row["category_name"];
	}
	return $output;
}

function fetch_stock_details($stock_id, $connect)
{
	$query = "
	SELECT * FROM stock 
	WHERE stock_id = '".$stock_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['item_name'] = $row["item_name"];
	}
	return $output;
}

function fetch_expense_details($exp_id, $connect)
{
	$query = "
	SELECT * FROM exp_list 
	WHERE exp_id = '".$exp_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['exp_name'] = $row["exp_name"];
	}
	return $output;
}

function fetch_product_details($inventory_order_purchase_id, $connect)
{
	$query = "
	SELECT * FROM purchase_items
	WHERE inventory_order_purchase_id = '".$inventory_order_purchase_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['item_name'] = $row["item_name"];
	}
	return $output;
}

function fetch_product_detail($stock_id, $connect)
{
	$query = "
	SELECT * FROM stock
	WHERE stock_id = '".$stock_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['item_name'] = $row["item_name"];
	}
	return $output;
}

function fetch_quantity_available($inventory_order_purchase_id, $connect)
{
	$query = "
	SELECT * FROM purchase_items
	WHERE inventory_order_purchase_id = '".$inventory_order_purchase_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['gross_quantity'] = $row["gross_quantity"];
	}
	return $output;
}

function fetch_total_value($inventory_order_purchase_id, $connect)
{
	$query = "
	SELECT sum(net_price * 1.15) FROM purchase_items
	WHERE inventory_order_purchase_id = '".$inventory_order_purchase_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['net_price'] = $row["net_price"];
	}
	return $output;
}

/*function available_product_quantity($connect, $inventory_order_purchase_id)
{
	$product_data = fetch_product_details($inventory_order_purchase_id, $connect);
	$query = "
	SELECT 	inventory_order_product.quantity FROM inventory_order_product 
	INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	WHERE inventory_order_product.inventory_order_purchase_id = '".$inventory_order_purchase_id."' AND
	inventory_order.inventory_order_status = 'active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$total = 0;
	foreach($result as $row)
	{
		$total = $total + $row['quantity'];
	}
	$available_quantity = intval($product_data['quantity']) - intval($total);
	if($available_quantity == 0)
	{
		$update_query = "
		UPDATE purchase_items SET 
		product_status = 'inactive' 
		WHERE inventory_order_purchase_id = '".$inventory_order_purchase_id."'
		";
		$statement = $connect->prepare($update_query);
		$statement->execute();
	}
	return $available_quantity;
}*/

function available_product_quantity($connect, $inventory_order_purchase_id)
{
	$product_data = fetch_product_details($inventory_order_purchase_id, $connect);
	$query = "
	SELECT 	inventory_order_product.quantity FROM inventory_order_product 
	INNER JOIN inventory_order ON inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	WHERE inventory_order_product.inventory_order_purchase_id = '".$inventory_order_purchase_id."' AND
	inventory_order.inventory_order_status = 'active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$total = 0;
	foreach($result as $row)
	{
		$total = $total + $row['quantity'];
	}
	$available_quantity = intval($product_data['quantity']) - intval($total);
	if($available_quantity == 0)
	{
		$update_query = "
		UPDATE purchase_items SET 
		item_status = 'inactive' 
		WHERE inventory_order_purchase_id = '".$inventory_order_purchase_id."'
		";
		$statement = $connect->prepare($update_query);
		$statement->execute();
	}
	return $available_quantity;
}

function count_total_user($connect)
{
	$query = "
	SELECT * FROM user_details WHERE user_status='active'";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_category($connect)
{
	$query = "
	SELECT * FROM category WHERE category_status='active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_brand($connect)
{
	$query = "
	SELECT * FROM brand WHERE brand_status='active'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_product($connect)
{
	$query = "
	SELECT * FROM stock WHERE stock_qty > '0'
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	return $statement->rowCount();
}

function count_total_order_value($connect)
{
	$query = "
	SELECT sum(nett_price * 1.15) as total_order_value FROM inventory_order_product 
	

	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function count_total_order_value_month($connect)
{
	$query = "
	SELECT sum(inventory_order_product.nett_price * 1.15) total_order_value 
	FROM inventory_order_product inner join inventory_order 
	on inventory_order.inventory_order_id = inventory_order_product.inventory_order_id
	WHERE inventory_order.inventory_order_created_date BETWEEN
	DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))- 1 DAY) and LAST_DAY(CURDATE())

	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function count_total_purchase_value($connect)
{
	$query = "
	SELECT sum(purchase_items.net_price) as total_purchase_value 
	FROM purchase_items
	INNER JOIN purchase
	ON purchase.inventory_purchase_id = purchase_items.inventory_purchase_id
	WHERE purchase.inventory_purchase_created_date BETWEEN
	DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))- 1 DAY) and LAST_DAY(CURDATE())
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_purchase_value'], 2);
	}
}

function count_total_items($connect)
{
	$query = "
	SELECT count(item_name) as total_items
	FROM purchase_items
	WHERE purchase.inventory_purchase_id = :inventory_purchase_id
	
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_items'], 2);
	}
}


function count_total_order_value_today($connect)
{
	$query = "
	SELECT sum(a.nett_price*1.15) as total_order_value 
	FROM inventory_order_product as a
	INNER JOIN inventory_order as b
	ON a.inventory_order_id = b.inventory_order_id
	WHERE b.inventory_order_created_date = CURDATE()
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function count_total_purchases_value_today($connect)
{
	$query = "
	SELECT sum(purchase_items.net_price) as total_purchases_value
	FROM purchase_items
	inner join purchase
	on purchase_items.inventory_purchase_id = purchase.inventory_purchase_id
	WHERE purchase.inventory_purchase_created_date = CURDATE()
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_purchases_value'], 2);
	}
}

function count_total_cash_order_value($connect)
{
	$query = "
	SELECT sum(net_price) as total_product_value FROM purchase_items 
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_product_value'], 2);
	}
}

function count_total_pay_value($connect)
{
	$query = "
	SELECT sum(net_salary) as total_salary_value FROM payrol WHERE pay_date BETWEEN
	DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))- 1 DAY) and LAST_DAY(CURDATE())
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_salary_value'], 2);
	}
}

function count_total_credit_order_value($connect)
{
	$query = "
	-- SELECT sum(inventory_order_total) as total_order_value FROM inventory_order WHERE payment_status = 'credit' AND inventory_order_status='active'
	
	select a.product_id, (sales_total - purchase_total) as total_order_value
	from (select product_id, sum((a.nett_price) * 1.15) as sales_total
	from inventory_order_product a
	group by product_id
	) a left join
	(select b.product_id, sum(b.product_base_price) as purchase_total
	from product b
	) b
	on a.product_id = b.product_id;

	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_order_value'], 2);
	}
}

function get_user_wise_total_order($connect)
{
	$query = '
	SELECT sum(inventory_order.inventory_order_total) as order_total, 
	SUM(CASE WHEN inventory_order.payment_status = "cash" THEN inventory_order.inventory_order_total ELSE 0 END) AS cash_order_total, 
	SUM(CASE WHEN inventory_order.payment_status = "credit" THEN inventory_order.inventory_order_total ELSE 0 END) AS credit_order_total, 
	user_details.user_name 
	FROM inventory_order 
	INNER JOIN user_details ON user_details.user_id = inventory_order.user_id 
	WHERE inventory_order.inventory_order_status = "active" GROUP BY inventory_order.user_id
	';
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '
	<div class="table-responsive">
		<table class="table table-bordered table-striped">
			<tr>
				<th>User Name</th>
				<th>Total Order Value</th>
				<th>Total Cash Order</th>
				<th>Total Credit Order</th>
			</tr>
	';

	$total_order = 0;
	$total_cash_order = 0;
	$total_credit_order = 0;
	foreach($result as $row)
	{
		$output .= '
		<tr>
			<td>'.$row['user_name'].'</td>
			<td align="right">$ '.$row["order_total"].'</td>
			<td align="right">$ '.$row["cash_order_total"].'</td>
			<td align="right">$ '.$row["credit_order_total"].'</td>
		</tr>
		';

		$total_order = $total_order + $row["order_total"];
		$total_cash_order = $total_cash_order + $row["cash_order_total"];
		$total_credit_order = $total_credit_order + $row["credit_order_total"];
	}
	$output .= '
	<tr>
		<td align="right"><b>Total</b></td>
		<td align="right"><b>R '.$total_order.'</b></td>
		<td align="right"><b>R '.$total_cash_order.'</b></td>
		<td align="right"><b>R '.$total_credit_order.'</b></td>
	</tr></table></div>
	';
	return $output;
}

//pay

function fill_employee_name($connect)
{
	$query = "
	SELECT  * FROM employee 
	WHERE employee_status = 'active' 
	ORDER BY full_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<option value="">-- Select Employee Name --</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["full_name"].'">'.$row["full_name"].'</option>';
	}
	return $output;
}

function fill_employee_code($connect)
{
	$query = "
	SELECT * FROM employee 
	WHERE employee_status = 'active' 
	ORDER BY full_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '<option value="">-- Select Employee Code --</option>';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["employee_id"].'">'.$row["employee_code"].'</option>';
	}
	return $output;
}

function fill_employee_address($connect)
{
	$query = "
	SELECT * FROM employee 
	WHERE employee_status = 'active' 
	ORDER BY full_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["employee_id"].'">'.$row["address"].'</option>';
	}
	return $output;
}

function fill_employee_start_date($connect)
{
	$query = "
	SELECT * FROM employee 
	WHERE employee_status = 'active' 
	ORDER BY full_name ASC
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	$output = '';
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["employee_id"].'">'.$row["start_date"].'</option>';
	}
	return $output;
}

function fetch_pay_details($product_id, $connect)
{
	$query = "
	SELECT * FROM payrol 
	WHERE pay_id = '".$product_id."'";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output['employee_code'] = $row["employee_code"];
		$output['full_name'] = $row["full_name"];
	}
	return $output;
}

function fill_employee_code_list($connect, $category_id)
{
	$query = "SELECT * FROM employee 
	WHERE employee_status = 'active' 
	AND full_name = '".$full_name."'
	ORDER BY full_name ASC";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output .= '<option value="'.$row["full_name"].'">'.$row["employee_code"].'</option>';
	}
	return $output;
}


function count_total_salary_value($connect)
{
	$query = "
	SELECT sum(net_salary) as total_salary_value FROM payrol where pay_status = 'paid' 
	";
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_salary_value'], 2);
	}
}

function count_total_expense_value($connect)
{
	$query = "
	SELECT sum(expense_items.net_price) as total_expense_value 
	FROM expense_items
	INNER JOIN expense
	ON expense.expense_id = expense_items.expense_id
	WHERE expense.expense_date BETWEEN
	DATE_SUB(LAST_DAY(NOW()),INTERVAL DAY(LAST_DAY(NOW()))- 1 DAY) and LAST_DAY(CURDATE()) 
	-- expense.expense_date BETWEEN CURDATE() - INTERVAL 30 DAY AND CURDATE()
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_expense_value'], 2);
	}
}

function count_total_expenses_value_today($connect)
{
	$query = "
	SELECT sum(a.net_price) as total_expense_value 
	FROM expense_items as a
	INNER JOIN expense as b
	ON a.expense_id = b.expense_id
	WHERE b.expense_date = CURDATE()  
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_expense_value'], 2);
	}
}

function count_all_total_expenses_value($connect)
{
	$query = "
	SELECT sum(net_price) as total_expenses FROM expense_items 
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['total_expenses'], 2);
	}
}

function count_expense_items($connect, $expense_id)
{
	$query = "
	SELECT count(expense_item) as count_items FROM expense_items 
	where expense_id = $expense_id
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['count_items'], 2);
	}
}


function fetch_stock_qty($stock)
{
	$query = "
	SELECT stock_qty from stock WHERE stock_id = ‘$stock’ LIMIT 1 
	";
	if($_SESSION['type'] == 'user')
	{
		$query .= ' AND user_id = "'.$_SESSION["user_id"].'"';
	}
	$statement = $connect->prepare($query);
	$statement->execute();
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		return number_format($row['stock_check'], 2);
	}
}


?>
