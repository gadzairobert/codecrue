<?php

//view_expense.php

if(isset($_GET["pdf"]) && isset($_GET['expenses_id']))
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
		SELECT * FROM expense as o
		inner join user_details as i
		on o.user_id = i.user_id
		WHERE expense_id = :expense_id
		LIMIT 1
	");
	$statement->execute(
		array(
			':expense_id'       =>  $_GET["expenses_id"]
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
							<b>EXPENSES INVOICE</b><br />
							147 Eloff St, Selby, JHB, 2001<br>
							Tel: 071 091 5780<br>
							VAT no: 0902920<br><br>

							Slip no.: '.$row["expense_id"].'<br>
						</td>
					</tr>
				</table>
				<br>
				
				<table style="margin: 0px auto;" width="60%" cellpadding="1" cellspacing="0">
					<tr>
						<th rowspan="2" style="border-bottom: 1px solid black">No.</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Name</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Qty</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Unit Price</th>
						<th rowspan="2" style="border-bottom: 1px solid black">Nett Price</th>
						
					</tr>
					<tr>
					</tr>
		';
		$statement = $connect->prepare("
			SELECT * FROM expense_items 
			WHERE expense_id = :expense_id
		");
		$statement->execute(
			array(
				':expense_id'       =>  $_GET["expenses_id"]
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
			$item_name = $sub_row["expense_item"];
			$actual_amount = $sub_row["quantity"] * $sub_row["unit_price"];
			//$total_product_amount = $actual_amount;
			$total_actual_amount = $total_actual_amount + $actual_amount;
			$total = $total;
			$output .= '
				<tr>
					<td>'.$count.'</td>
					<td>'.$sub_row['expense_item'].'</td>
					<td>'.$sub_row["quantity"].''.$sub_row["unit"].'</td>
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
			
			<td align="center" colspan="4"><b>TOTAL EXPENSE</b></td>
			
            <td align="left"><b>'.number_format($total_actual_amount, 2).'</b></td>
            <td>&nbsp;</td>
			<td>&nbsp;</td>
			<td align="left"><b></b></td>
		</tr>
		';
		$output .= '
						</table>
						<p align="center" >_____________________________________________</p>
						<p align="center"><b>Cashier:</b> '.$row["user_name"].' | '.$row["expense_date"].'  </p>

						<br />
					</td>
				</tr>
			</table>
		';
	}
	$pdf = new Pdf();
	$file_name = 'Expense-'.$row["expense_id"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}

?>