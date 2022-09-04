<?php
//pay.php

include('database_connection.php');
include('function.php');

if(!isset($_SESSION["type"]))
{
    header('location:login.php');
}

if($_SESSION['type'] != 'master')
{
    header('location:index.php');
}

include('header.php');


?>
<link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
    
    <script>
	$(document).ready(function(){
		$('#pay_date1').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
	</script>
        <span id='alert_action'></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
                    <div class="panel-heading">
                    	<div class="row">
                        
                            <div class="col-lg-10 col-md-10 col-sm-5 col-xs-3">
                            <span><b>Current Month Total Salaries:  </b><span style="color:green">R<?php echo count_total_pay_value($connect); ?></span></span>
                        </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align='right'>
                                <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">Pay Employee</button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="pay_data" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>PayDate</th>
                                    <th>Emp Name</th>
                                    <th>Emp Code</th>
                                    <th>Gross Pay</th>
                                    <th>Deducted</th>
                                    <th>Net Pay</th>
                                    <th>Status</th>
                                    <th>Pay</th>
                                    <th style="text-align: center">View</th>
                                    <th>Print</th>
                                    
                                </tr></thead>
                            </table>
                        </div></div>
                    </div>
                </div>
			</div>
		</div>

        <div id="payModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="pay_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Pay Employee</h4>
                        </div>
                        <div class="modal-body">
                            <div class="list_wrapper">
                                <div class="row">
                                <div class="col-md-3">
                                        <div class="form-group">
                                            <label> Fullname</label>
                                            <select name="full_name" id="full_name" class="form-control" required>
                                                <?php echo fill_employee_name($connect);?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label> Pay Date</label>
                                            <input type="date" name="pay_date" id="pay_date" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Gross Salary</label>
                                            <input type="number" name="basic_salary" onkeyup="AutoCalc()" id="basic_salary" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label>Loan/Salary Advance</label>
                                            <input type="number" name="loan_advance" onkeyup="AutoCalc()"  id="loan_advance" class="form-control" /> 
                                        </div>
                                    </div>
                                </div> 

                                <div class="row">
                                <div class="col-md-4">
                                        <div class="form-group">
                                            <label> Rate per Hour</label>
                                            <input type="text" name="rate_per_hour" id="rate_per_hour" class="form-control" required>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Unemployement Insurance Fund</label>
                                            <input type="number" name="uif" onkeyup="AutoCalc()"  id="uif" class="form-control">
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label>Loan Repayment</label>
                                            <input type="number" name="loan_repay" onkeyup="AutoCalc()"  id="loan_repay" class="form-control" /> 
                                        </div>
                                    </div>
                                    <div class="col-md-3 hidden">
                                        <div class="form-group">
                                            <label>Nett Salary</label>
                                            <div class="input-group">
                                                <input type="number" name="net_salary" onkeyup="AutoCalc()" id="net_salary" class="form-control" readonly/>
                                            </div>
                                        </div>
                                    </div>
                                </div> 
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="pay_id" id="pay_id" />
                            <input type="hidden" name="btn_action" id="btn_action" />
                            <input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        
        <div id="paydetailsModal" class="modal fade">
		<div class="modal-dialog">
			<form method="post" id="pay_form">
				<div class="modal-content">
					<div class="modal-body">
						<Div id="pay_details"></Div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>
		</div>
	</div>
    
<script>
$(document).ready(function(){
    var paydataTable = $('#pay_data').DataTable({
        "processing":true,
        "serverSide":true,
        "order":[],
        "ajax":{
            url:"pay_fetch.php",
            type:"POST"
        },
        "columnDefs":[
            {
                "targets":[5, 6, 7],
                "orderable":false,
            },
        ],
        "pageLength": 50
    });

    $('#add_button').click(function(){
        $('#payModal').modal('show');
        $('#pay_form')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Salary");
        $('#action').val("Add");
        $('#btn_action').val("Add");
    });

    $('#pay_id').change(function(){
        var pay_id = $('#pay_id').val();
        var btn_action = 'load_brand';
        $.ajax({
            url:"pay_action.php",
            method:"POST",
            data:{pay_id:pay_id, btn_action:btn_action},
            success:function(data)
            {
                $('#brand_id').html(data);
            }
        });
    });

    $(document).on('submit', '#pay_form', function(event){
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var form_data = $(this).serialize();
        $.ajax({
            url:"pay_action.php",
            method:"POST",
            data:form_data,
            success:function(data)
            {
                $('#pay_form')[0].reset();
                $('#payModal').modal('hide');
                $('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
                $('#action').attr('disabled', false);
                paydataTable.ajax.reload();
            }
        })
    });

    $(document).on('click', '.view', function(){
        var pay_id = $(this).attr("id");
        var btn_action = 'pay_details';
        $.ajax({
            url:"pay_action.php",
            method:"POST",
            data:{pay_id:pay_id, btn_action:btn_action},
            success:function(data){
                $('#paydetailsModal').modal('show');
                $('#pay_details').html(data);
            }
        })
    });

    $(document).on('click', '.update', function(){
        var pay_id = $(this).attr("id");
        var btn_action = 'fetch_single';
        $.ajax({
            url:"pay_action.php",
            method:"POST",
            data:{pay_id:pay_id, btn_action:btn_action},
            dataType:"json",
            success:function(data){
                $('#payModal').modal('show');
                $('#full_name').val(data.full_name);
                $('#pay_date').val(data.pay_date);
                $('#basic_salary').val(data.basic_salary);
                $('#loan_advance').val(data.loan_advance);
                $('#rate_per_hour').val(data.rate_per_hour);
                $('#uif').val(data.uif);
                $('#loan_repay').val(data.loan_repay);
                $('#net_salary').val(data.net_salary);
                $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Pay");
                $('#pay_id').val(pay_id);
                $('#action').val("Edit");
                $('#btn_action').val("Edit");
            }
        })
    });

    $(document).on('click', '.delete', function(){
        var pay_id = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'delete';
        if(confirm("Are you sure you want to pay employee? "))
        { 
            $.ajax({
                url:"pay_action.php",
                method:"POST",
                data:{pay_id:pay_id, status:status, btn_action:btn_action},
                success:function(data){
                    $('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
                    paydataTable.ajax.reload();
                }
            });
        }
        else
        {
            return false;
        }
    });

     //calculate nett salary
     function AutoCalc(){
    var textValue1 = document.getElementById('basic_salary').value;
    var textValue2 = document.getElementById('loan_advance').value;
    var textValue5 = document.getElementById('uif').value;
    var textValue6 = document.getElementById('load_repay').value;
    
        if($.trim(textValue1) != '' && $.trim(textValue2) != ''){
    document.getElementById('nett_salary').value = textValue1 + textValue2 + textValue4 - textValue5 - textValue6; 
        }
    }

});
</script>
<?php
include("footer.php");
?>