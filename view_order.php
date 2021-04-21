<?php

//view_order.php

if(isset($_GET["pdf"]) && isset($_GET['order_id']))
{
	require_once 'pdf.php';
	include('database_connection.php');
	include('function.php');
	if(!isset($_SESSION['type']))
	{
		header('location:login.php');
	}
	$output = '';
	$statement = $connect->prepare("
		SELECT * FROM inventory_order as o
		inner join user_details as i
		on o.user_id = i.user_id
		WHERE inventory_order_id = :inventory_order_id
		LIMIT 1
	");
	$statement->execute(
		array(
			':inventory_order_id'       =>  $_GET["order_id"]
		)
	);
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output .= '
		<table width="100%" cellpadding="1"  cellspacing="0" style="font-family: Helvetica, Arial, sans-serif">
			<tr>
				<td colspan="2">
				<table width="100%" cellpadding="5">
					<tr>
						<td width="65%" style="text-align:center">
							
							<b><span style="font-size:23px">PICK n SELL RECYCLING</span></b><br /><br>
							<b>CASH SALES INVOICE</b><br />
							147 Eloff St, Selby, JHB, 2001<br>
							Tel: 071 091 5780<br>
							VAT no: 0902920<br><br>
							
							Slip no.: '.$row["inventory_order_id"].'<br>
							Sold to: '.$row["inventory_order_name"].'<br />	
							Invoice no.: '.$row["invoice_no"].'<br>
						</td>
					</tr>
				</table>
				<br>
				
				<table style="margin: 0px auto;" width="60%" cellpadding="1" cellspacing="0">
					<tr>
						<th rowspan="2" style="border-bottom: 1px solid black">No.</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Product</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Gross</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Deducted</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Nett</th>
						<th rowspan="2" style="border-bottom: 1px solid black">InPrice</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Total</th>
						
					</tr>
					<tr>
					</tr>
		';
		$statement = $connect->prepare("
			SELECT * FROM inventory_order_product 
			inner join stock
			on inventory_order_product.stock_id = stock.stock_id
			WHERE inventory_order_product.inventory_order_id = :inventory_order_id
		");
		$statement->execute(
			array(
				':inventory_order_id'       =>  $_GET["order_id"]
			)
		);
		$product_result = $statement->fetchAll();
		$count = 0;
		$total = 0;
		$total_actual_amount = 0;
		$total_tax_amount = 0;
		foreach($product_result as $sub_row)
		{
			$count = $count + 1;
			$product_data = fetch_product_detail($sub_row['stock_id'], $connect);
			$actual_amount = $sub_row["qty_nett"] * $sub_row["price"];
			$tax_amount = ($actual_amount * 0.15);
			$total_product_amount = $actual_amount + $tax_amount;
			$total_actual_amount = $total_actual_amount + $actual_amount;
			$total_tax_amount = $total_tax_amount + $tax_amount;
			$total = $total + $total_product_amount;
			$output .= '
				<tr>
					<td>'.$count.'</td>
					<td>'.$product_data['item_name'].'</td>
					<td>'.$sub_row["quantity"].'</td>
					<td>'.$sub_row["deduct"].''.$sub_row["uom"].'</td>
					<td>'.$sub_row["qty_nett"].'</td>
					<td aling="left">R'.$sub_row["price"].'</td>
					<td aling="left">R'.$sub_row["nett_price"].'</td>
					
				</tr>
			';
		}
		$output .= '
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="right" colspan="4"><b>Gross (VAT Excl)</b></td>
			<td>&nbsp;</td>
			<td align="left"><b>'.number_format($total_actual_amount, 2).'</b></td>
			<td align="left"><b></b></td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="right" colspan="4"><b>VAT (15%)</b></td>
			<td>&nbsp;</td>
			<td align="left"><b>'.number_format($total_tax_amount, 2).'</b></td>
			<td align="left"><b></b></td>
		</tr>
		<tr>
			<!--<td align="left"><b>'.number_format($total_actual_amount, 2).'</b></td>
			<td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="left"><b>'.number_format($total_tax_amount, 2).'</b></td>-->
			<td>&nbsp;</td>
			<td align="right" colspan="4"><b>Nett (VAT Incl)</b></td>
			<td>&nbsp;</td>
			<td align="left"><b>'.number_format($total, 2).'</b></td>
			
		</tr>
		';
		$output .= '
						</table>
						<p align="center" >_____________________________________________</p>
						<p align="center"><b>Cashier:</b> '.$row["user_name"].' | '.$row["inventory_order_created_date"].'  </p>

						<br />
					</td>
				</tr>
			</table>
		';
	}
	$pdf = new Pdf();
	$file_name = 'Order-'.$row["inventory_order_id"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}

?>