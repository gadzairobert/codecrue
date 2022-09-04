<?php
include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');


?>
<!DOCTYPE html>
<html>
    <style>
        form {
        margin-left: 17%;
        margin-right:25%;
        width: 60%;
    }
    </style>
	<head>
		<title>Pick n Sell Recycling</title>
		<link href="logo2.png" rel="icon">	
        <link rel="stylesheet" href="css/bootstrap.min.css" />
	</head>
	<body>
        
    <div class="row">
		<div class="col-lg-12">
            <div class="panel-body ">
                <form action="" method="GET" class="form">
                    <div class="row justify-content-center" style="padding-top:10px;">
                    <center><h4>Purchases Report Per Product</h4></center>
                        <div class="col-md-3">
                            <div class="form-group">
                                <select name="stock_id" value="<?php if(isset($_GET['stock_id'])){ echo $_GET['stock_id']; } ?>" class="form-control" required>
                                    <option value="">Select Product</option>
                                    <?php echo fill_stock_list2($connect);?>
                                </select>
                            </div>
                        </div>

                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="date" name="from_date" value="<?php if(isset($_GET['from_date'])){ echo $_GET['from_date']; } ?>" class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <input type="date" name="to_date" value="<?php if(isset($_GET['to_date'])){ echo $_GET['to_date']; } ?>"  class="form-control" >
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group">
                                <button type="submit" class="btn btn-success">Submit</button>
                            </div>
                        </div>

                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
                <div class="panel-body">
                	<table id="expense_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
                                <th>Purchase Date</th>
                                <th>Item Name</th>
								<th>Gross Qty</th>
								<th>Deducted</th>
								<th>Nett Qty</th>
								<th>Unit Price</th>
								<th>Nett Price</th>
							</tr>
						</thead>
                        <tbody>
                            
                        <div class="card-body">
                            <?php
                            $con = mysqli_connect("localhost", "root", "", "pick");
                            if(isset($_GET['stock_id']) && (isset($_GET['from_date']) && isset($_GET['to_date'])))
                            {
                                $stock_id = $_GET['stock_id'];
                                $from_date = $_GET['from_date'];
                                $to_date = $_GET['to_date'];

                                $query = "SELECT * FROM purchase_items 
                                inner join stock on purchase_items.stock_id = stock.stock_id where 
                                stock.stock_id = '$stock_id' AND
                                purchase_items.item_date BETWEEN '$from_date' AND '$to_date' ";
                                $query_run = mysqli_query($con, $query);

                                if(mysqli_num_rows($query_run) > 0)
                                {
                                    foreach($query_run as $row)
                                    {
                                        ?>
                                        <tr>
                                            <td><?= $row['item_date']; ?></td>
                                            <td><?= $row['item_name']; ?></td>
                                            <td><?= $row['gross_quantity']; ?></td>
                                            <td><?= $row['deducted'],' ', $row['uom']; ?></td>
                                            <td><?= $row['net_quantity']; ?></td>
                                            <td><?= $row['unit_price']; ?></td>
                                            <td><?= $row['net_price']; ?></td>
                                        </tr>
                                        <?php
                                    }
                                }

                            }
                            ?>
                            
                        </tbody>
                	</table>
                </div>
            </div>
        </div>
    </div>