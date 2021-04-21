<?php
//bank.php

include('database_connection.php');

if(!isset($_SESSION["type"]))
{
	header('location:login.php');
}

if($_SESSION["type"] != 'master')
{
	header("location:index.php");
}

include('header.php');
include('function.php');


?>
	<span id="alert_action"></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
                    <div class="panel-heading">
                    	<div class="row">
                        	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="panel-title">Banking Details List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            	<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#bankModal" class="btn btn-success btn-xs">New Banking Details</button>
                        	</div>
                        </div>
                       
                        <div class="clear:both"></div>
                   	</div>
                   	<div class="panel-body">
                   		<div class="row"><div class="col-sm-12 table-responsive">
                   			<table id="bank_data" class="table table-bordered table-striped">
                   				<thead>
									<tr>
                                        <th>Full Name</th>
                                        <th>Employee Code</th>
										<th>Bank Name</th>
                                        <th>Branch</th>
                                        <th>Account Number</th>
                                        <th>Account Type</th>
                                        <th>Status</th>
										<th>Edit</th>
										<th>Delete</th>
									</tr>
								</thead>
                   			</table>
                   		</div>
                   	</div>
               	</div>
           	</div>
        </div>
        <div id="bankModal" class="modal fade">
        	<div class="modal-dialog">
        		<form method="post" id="bank_form">
        			<div class="modal-content">
        			<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> New Bank Details</h4>
        			</div>
        			<div class="modal-body">
                        <div class="row">
                            <div class="col-md-9">
                                <div class="form-group">
                                    <label> Fullname</label>
                                    <select name="full_name" id="full_name" class="form-control" required>
                                        <?php echo fill_employee_name($connect);?>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-3">
                                    <div class="form-group">
                                        <label> Employee Code</label>
                                        <input type="number" name="employee_code" id="employee_code" class="form-control" />
                                    </div>
                                </div>
                        </div>
                        <div class="row">

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Bank Name</label>
                                    <select name="bank_name" id="bank_name" class="form-control" required> 
                                        <option value="ABSA Bank">ABSA</option>
                                        <option value="Capitec Bank">Capitec</option>
                                        <option value="First National Bank SA">FNB</option>
                                        <option value="NedBank">NedBank</option>
                                        <option value="Standard Bank">Standard Bank</option>
                                        <option value="Others">Others</option>    
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Branch Code</label>
                                    <input type="number" name="branch_code" id="branch_code" class="form-control" />
                                </div>
							</div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Account Number</label>
                                    <input type="number" name="bank_account" id="bank_account" class="form-control" />
                                </div>
							</div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>Account Type</label>
                                    <select name="account_type" id="account_type" class="form-control" required> 
                                        <option value="Savings">Savings</option>
                                        <option value="Cheque">Cheque</option>   
                                    </select>
                                </div>
                            </div>
                        </div>
        			</div>
        			<div class="modal-footer">
        				<input type="hidden" name="employee_id" id="employee_id" />
        				<input type="hidden" name="btn_action" id="btn_action" />
        				<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
        				<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        			</div>
        		</div>
        		</form>

        	</div>
        </div>
<script>
$(document).ready(function(){

	$('#add_button').click(function(){
		$('#bank_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Banking Details");
		$('#action').val("Add");
		$('#btn_action').val("Add");
	});

	var bankdataTable = $('#bank_data').DataTable({
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax":{
			url:"bank_fetch.php",
			type:"POST"
		},
		"columnDefs":[
			{
				"target":[5, 6, 7, 8],
				"orderable":false
			}
		],
		"pageLength": 25
	});

	$(document).on('submit', '#bank_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled','disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:"bank_action.php",
			method:"POST",
			data:form_data,
			success:function(data)
			{
				$('#bank_form')[0].reset();
				$('#bankModal').modal('hide');
				$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
				$('#action').attr('disabled', false);
				bankdataTable.ajax.reload();
			}
		})
	});

	$(document).on('click', '.update', function(){
		var employee_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:"bank_action.php",
			method:"POST",
			data:{employee_id:employee_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#bankModal').modal('show');
                $('#full_name').val(data.full_name);
                $('#employee_code').val(data.employee_code);
				$('#bank_name').val(data.bank_name);
                $('#branch_code').val(data.branch_code);
                $('#bank_account').val(data.bank_account);
                $('#account_type').val(data.account_type);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Banking Details");
				$('#employee_id').val(employee_id);
				$('#action').val('Edit');
				$('#btn_action').val('Edit');
			}
		})
	});

	$(document).on('click', '.delete', function(){
		var employee_id = $(this).attr("id");
		var status = $(this).data('status');
		var btn_action = "delete";
		if(confirm("Are you sure you want to change status?"))
		{
			$.ajax({
				url:"bank_action.php",
				method:"POST",
				data:{employee_id:employee_id, status:status, btn_action:btn_action},
				success:function(data)
				{
					$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
					bankdataTable.ajax.reload();
				}
			})
		}
		else
		{
			return false;
		}
	});

});
</script>

