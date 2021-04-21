<?php

//view_order.php

if(isset($_GET["pdf"]) && isset($_GET['payslip_code']))
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
    SELECT payrol.pay_id, 
    employee.employee_code, 
    employee.full_name, 
    employee.roles, 
    employee.emp_type, 
    employee.id_no, 
    employee.address, 
    employee.start_date, 
    payrol.pay_date, 
    payrol.full_name, 
    bank_details.bank_name, 
    bank_details.branch_code, 
    bank_details.bank_account, 
    bank_details.account_type,
    payrol.rate_per_hour, 
    payrol.basic_salary, 
    payrol.loan_advance, 
    payrol.over_time, 
    payrol.uif, 
    payrol.loan_repay, 
    payrol.net_salary,
    sum(payrol.basic_salary + payrol.loan_advance + payrol.over_time) total_earnings,
    sum(payrol.uif + payrol.loan_repay) total_deduc
    FROM payrol 
    inner join employee 
    on employee.full_name = payrol.full_name 
    inner join bank_details
    on bank_details.full_name = payrol.full_name
    where payrol.pay_id = :pay_id LIMIT 1
	");
	$statement->execute(
		array(
			':pay_id'       =>  $_GET["payslip_code"]
		)
	);
	$result = $statement->fetchAll();
	foreach($result as $row)
	{
		$output .= '
		<table  cellpadding="1" style="font-family: Helvetica, Arial, sans-serif; font-size:12px">
            <tr>
                <td style="text-align:left"><b>Company Name: &nbsp; &nbsp; &nbsp; &nbsp;<span style="font-weight:normal">PICK n SELL RECYCLING</span></td>
            </tr>
            <tr>
                <td style="text-align:left"><b>Company Address: &nbsp;<span style="font-weight:normal"> 147 ELOFF ST SELBY, JOHANNESBURG, 2001 </span></td>
            </tr>
            <tr>
                <td style="text-align:left"><b>Pay Date: &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;<span style="font-weight:normal">'.$row["pay_date"].'</span>
                <br><br><br>
                </td>
            </tr>
        </table>
        <table cellpadding="1" style="font-family: Helvetica, Arial, sans-serif; font-size:12px">
            <tr>
                <td><b>Employee Name:</b></td>
                <td> ' .$row["full_name"].' &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; </td>
                <td><b>Employee Code: </b></td>
                <td>' .$row["employee_code"].' &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp; </td>
            </tr>
            <tr>
                <td><b>ID/Passport No:</b></td>
                <td>' .$row["id_no"].' </td>
                <td><b>Employment Type:</b></td>
                <td>'.$row["emp_type"].'</td>
            </tr>
            <tr>
                <td><b>Employee Address: </b></td>
                <td>' .$row["address"].'</td>
            </tr>
        
            <tr>
                <td><b>Employment Date:</b></td>
                <td>'.$row["start_date"].'</td>
            </tr>
            <tr>
                <td><b>Department:</b></td>
                <td>'.$row["roles"].'</td>
                <td><b>Rate per Hour:</b></td>
                <td>'.$row["rate_per_hour"].'</td>
                
            </tr>
        </table><br>

        <table cellpadding="1" style="font-family: Helvetica, Arial, sans-serif; font-size:12px">
            <tr>
                <td><b>BANKING DETAILS</b></td>
            </tr>
            <tr>
                <td><b>Account Number</b> &nbsp; &nbsp; &nbsp;</td>
                <td><b>Branch Code</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
                <td><b>Account Type</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; </td>
                <td><b>Bank Name</b></td>
            </tr>
            <tr>
                <td>'.$row["bank_account"].'</td>
                <td>'.$row["branch_code"].'</td>
                <td>'.$row["account_type"].'</td>
                <td>'.$row["bank_name"].'</td>
            </tr>
            
        </table><br><hr>

        <table style="font-family: Helvetica, Arial, sans-serif; font-size:12px">
            <tr style="background-color:white">
                <td><b> EARNINGS</b> &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; </td>
                <td>&nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp;   &nbsp;    &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; </td>
                <td><b>AMOUNT</b>&nbsp; &nbsp;</td>
                <td><b>DEDUCTIONS</b>&nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;  
                &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; </td>
                <td><b>AMOUNT</b></td><hr>
            </tr>
        </table>
        <table style="font-family: Helvetica, Arial, sans-serif; font-size:12px">
            <tr>
                <td><b> Basic Salary</b> &nbsp; &nbsp;  &nbsp;  &nbsp; &nbsp; &nbsp;  &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; </td>
                <td>&nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;</td>
                <td style="text-align:right">'.$row["basic_salary"].'</td>
                <td><b>UIF</b> &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                <td>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</td>
                <td style="text-align:right">'.$row["uif"].'</td>
            </tr>
            <tr>
                <td><b>Loan Advance  </b></td>
                <td></td>
                <td style="text-align:right">'.$row["loan_advance"].' &nbsp; </td>
                <td><b>Loan Repayment </b></td>
                <td>&nbsp;</td>
                <td style="text-align:right">'.$row["loan_repay"].'</td>
            </tr>
            <tr>
            <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td style="background-color:#c9c7c5; border-top: 1px solid black; border-bottom: 1px solid black"><b>TOTAL EARNINGS</b></td>
                <td style="background-color:#c9c7c5; border-top: 1px solid black; border-bottom: 1px solid black">&nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;   &nbsp; &nbsp; &nbsp; &nbsp;</td>
                <td style="background-color:#c9c7c5; float:right; border-top: 1px solid black; border-bottom: 1px solid black">'.$row["total_earnings"].'&nbsp; </td>
                <td style="background-color:#c9c7c5; border-top: 1px solid black; border-bottom: 1px solid black"><b>TOTAL DEDUCTED</b> &nbsp; &nbsp; </td>
                <td style="background-color:#c9c7c5; border-top: 1px solid black; border-bottom: 1px solid black">&nbsp;</td>
                <td style="background-color:#c9c7c5; text-align:right; border-top: 1px solid black; border-bottom: 1px solid black">'.$row["total_deduc"].'  </td>
            </tr>
           
            <br><br>
        </table>
        <table cellpadding="1" style="font-family: Helvetica, Arial, sans-serif; font-size:12px">
            <tr style="background-color:white">
                <td><b>EMPLOYER CONTRIBUTIONS</b>  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; </td>
                <td>&nbsp; &nbsp;  &nbsp; &nbsp;  &nbsp; &nbsp;</td>
                <td><b>AMOUNT</b>&nbsp; &nbsp;</td>
                <td><b>YTD TOTALS</b>&nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;
                &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; </td>
               
                <td>  &nbsp; &nbsp; &nbsp;</td>
                <td><b>AMOUNT</b></td>
            </tr>
                <hr>
         </table>
        <table cellpadding="1" style="font-family: Helvetica, Arial, sans-serif; font-size:12px">
            <tr>
                <td>Unemployment Insurance Fund &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
                <td style="text-align:right;">0.00 &nbsp; &nbsp;</td>
                <td>Company Contributions &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;  
                &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; </td>
                <td style="text-align:right">0.00</td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="border-bottom: 1px solid black"></td>
                <td style="border-bottom: 1px solid black"></td>
            </tr>
            <tr>
                <td></td>
                <td></td>
                <td style="border-bottom: 1px solid black; background-color:#c9c7c5"><b>NETT SALARY </b></td>
                <td style="background-color:#c9c7c5; text-align:right; border-bottom: 1px solid black"><b>'.$row["net_salary"].'</b></td>
            </tr>
         </table>
             
		';
		$statement = $connect->prepare("
			SELECT * FROM payrol 
			WHERE pay_id = :pay_id
		");
		$statement->execute(
			array(
				':pay_id'       =>  $_GET["payslip_code"]
			)
		);
      
		
	}
	
	$pdf = new Pdf();
	$file_name = 'payslip for -'.$row["full_name"].'.pdf';
	$pdf->loadHtml($output);
	$pdf->render();
	$pdf->stream($file_name, array("Attachment" => false));
}

?>

<?php
include("footer.php");
?>