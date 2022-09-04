

<?php

//view_purchase.php

if(isset($_GET["pdf"]) && isset($_GET['purchase_id']))
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
		SELECT * FROM purchase as o
		inner join user_details as i
		on o.user_id = i.user_id
		inner join sale_agent as s 
		on o.agent_name = s.agent_name
		WHERE inventory_purchase_id = :inventory_purchase_id
		LIMIT 1
	");
	$statement->execute(
		array(
			':inventory_purchase_id'       =>  $_GET["purchase_id"]
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
						
							<b><span style="font-size:23px">PICK n SELL RECYCLING</span></b><br /><br />
							<b>CASH PURCHASES INVOICE</b><br />
							147 Eloff St, Selby, JHB, 2001<br>
							Tel: 071 091 5780<br>
							VAT no: 0902920<br><br>
							
							Slip no.: '.$row["inventory_purchase_id"].'<br>
							Status: <b>'.$row["inventory_purchase_status"].'</b>
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
			SELECT * FROM purchase_items
			inner join stock 
			on purchase_items.stock_id = stock.stock_id
			WHERE purchase_items.inventory_purchase_id = :inventory_purchase_id
		");
		$statement->execute(
			array(
				':inventory_purchase_id'       =>  $_GET["purchase_id"]
			)
		);
		$product_result = $statement->fetchAll();
		$count = 0;
		$total = 0;
		$total_actual_amount = 0;
		foreach($product_result as $sub_row)
		{
			$count = $count + 1;
			$item_name = $sub_row["item_name"];
			$actual_amount = ($sub_row["net_quantity"] * $sub_row["unit_price"]);
			//$tax_amount = 1.15;
			$total_actual_amount = $total_actual_amount + $actual_amount;
			$total = $total;
			$output .= '
				<tr>
					<td>'.$count.'</td>
					<td>'.$sub_row['item_name'].'</td>
					<td>'.$sub_row["gross_quantity"].'</td>
					<td>'.$sub_row["deducted"].''.$sub_row["uom"].' </td>
					<td>'.$sub_row["net_quantity"].'</td>
					<td aling="left">R'.$sub_row["unit_price"].'</td>
					<td aling="left">R'.$sub_row["net_price"].'</td>
					
				</tr>
			';
		}
		$output .= '
		<tr>
			<td>&nbsp;</td>
		</tr>
		<tr>
			<td>&nbsp;</td>
			<td align="right" colspan="4"><b>Total Purchase Price</b></td>
			<td>&nbsp;</td>
			<td align="left"><b>'.number_format($total_actual_amount, 2).'</b></td>
			<td align="left"><b></b></td>
		</tr>
		';
		$output .= '
		
						</table>
						<p style="padding-left:150px">
						<b>Seller\'s Name:</b> &nbsp; '.$row["agent_name"].' &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; 
						<b>Cell No:</b> &nbsp;  &nbsp; &nbsp; '.$row["cell_no"].'<br> 
						<b>ID/Passport:  &nbsp; &nbsp;&nbsp;</b> '.$row["id_no"].' &nbsp; &nbsp; &nbsp; &nbsp;
						<b>Address:&nbsp; &nbsp; &nbsp;</b> '.$row["address"].'<br><br>
						<b>Seller Signature</b> ________________________________</p><br>
						<p align="center"><b>Cashier:</b> '.$row["user_name"].' | '.$row["inventory_purchase_created_date"].'  </p>

						<br />
					</td>
				</tr>
			</table>
		';
	}
	$pdf = new Pdf();
	$file_name = 'Order-'.$row["inventory_purchase_id"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}

?>
