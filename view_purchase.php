<?php

//view_order.php

if(isset($_GET["pdf"]) && isset($_GET['product_code']))
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
		SELECT * FROM product
		LIMIT 1
	");
	$statement->execute(
		array(
			':product_id'       =>  $_GET["product_code"]
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
							
							<b><span style="font-size:23px"><b>PICK n SELL RECYCLING</span></b><br />
							<b>PURCHASES SLIP</b><br />
							147 Eloff St, Selby, JHB, 2001<br>
							Tel: 071 091 5780<br>
							VAT No: 0902920<br><br>
							Date: '.$row["product_date"].' <br>
							Slip no.: '.$row["product_id"].' 
							
						</td>
					</tr>
				</table>
				<br />
				<table style="margin: 0px auto;" width="85%" cellpadding="1" cellspacing="0">
					<tr>
						<th rowspan="2">No.</th>
						<th rowspan="2">Item</th>
						<th rowspan="2">UOM</th>
						<th rowspan="2">Gross</th>
						<th rowspan="2">Deduct</th>
						<th rowspan="2">Nett</th>
						<th rowspan="2">Unit Price</th>
						<th rowspan="2">Total Price</th>
						
					</tr>
					<tr>
					</tr>
		';
		$statement = $connect->prepare("
			SELECT * FROM product 
			WHERE product_id = :product_id
		");
		$statement->execute(
			array(
				':product_id'       =>  $_GET["product_code"]
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
			$product_data = fetch_product_details($sub_row['product_id'], $connect);
			$gross_qty = $sub_row["product_quantity"];
			$deductions = $sub_row["product_minimum_order"];
			$nett_qty = $sub_row["product_tax"];
			$nett_price = $sub_row["product_base_price"];
			$output .= '
				<tr>
					<td>'.$count.'</td>
					<td>'.$product_data['product_name'].'</td>
					<td>'.$sub_row["product_unit"].'</td>
					<td>'.$sub_row["product_quantity"].'</td>
					<td>'.$sub_row["product_minimum_order"].'</td>
					<td>'.$sub_row["product_tax"].'</td>
					<td align="left">R'.$sub_row["unit_price"].'</td>
					<td align="left">R'.$sub_row["product_base_price"].'</td>
					
				</tr>
			';
		}
		
		$output .= '
						</table>
						<br />
						<p align="center" >I hereby state that I am the lawful owner of the material
						listed above <br> and have sold them to PICK n SELL RECYCLING to dispose of as <br> 
						they wish.<br><br>
						National ID ___________________________ YES / NO</p>
						<br />
						<p align="center"> _________________________<br />Seller Signature
						<br />
					</td>
				</tr>
			</table>
		';
	}
	$pdf = new Pdf();
	$file_name = 'Product-'.$row["product_id"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}

?>