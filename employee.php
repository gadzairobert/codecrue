<?php
//employee.php

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


?>
<link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>

    <script>
	$(document).ready(function(){
		$('#dob1').datepicker({
			format: "yyyy-mm-dd",
			autoclose: true
		});
	});
    </script>
		<span id="alert_action"></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
                    <div class="panel-heading">
                    	<div class="row">
                        	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="panel-title">Employee List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            	<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#employeeModal" class="btn btn-success btn-xs">New Employee</button>
                        	</div>
                        </div>
                       
                        <div class="clear:both"></div>
                   	</div>
                   	<div class="panel-body">
                   		<div class="row"><div class="col-sm-12 table-responsive">
                   			<table id="employee_data" class="table table-bordered table-striped">
                   				<thead>
									<tr>
										<th>Emp Code</th>
										<th>Full Name</th>
										<th>Phone No.</th>
                                        <th>Email</th>
										<th>ID No.</th>
                                        <th>Roles</th>
                                        <th>Emp Type</th>
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
        <div id="employeeModal" class="modal fade">
        	<div class="modal-dialog">
        		<form method="post" id="employee_form">
        			<div class="modal-content">
        			<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> New Employee</h4>
        			</div>
        			<div class="modal-body">
                        <div class="row">
							<div class="col-md-3">
                                <div class="form-group">
                                    <label> Employee Code</label>
                                    <input type="text" name="employee_code" id="employee_code" placeholder=" employee code" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Full Name</label>
                                    <input type="text" name="full_name" id="full_name"  placeholder=" first name and surname" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Phone Number</label>
                                    <input type="number" name="phone_no" id="phone_no"  placeholder="10 digit, eg 0828229282" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  maxlength = "10" class="form-control" required />
                                </div>
							</div>
							<div class="col-md-3">
                                <div class="form-group">
                                    <label> Email Address</label>
                                    <input type="email" name="email" id="email"  placeholder="eg, staff@picknsell.com" class="form-control" />
                                </div>
                            </div>
                        </div>
                        <div class="row">
							<div class="col-md-3">
                                <div class="form-group">
                                    <label> Physical Address</label>
                                    <input type="text" name="address" id="address" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Date of Birth</label>
                                    <input type="date" name="dob" id="dob" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> National ID No</label>
                                    <input type="text" name="id_no" id="id_no" class="form-control" required />
                                </div>
							</div>
							<div class="col-md-3">
                                <div class="form-group">
                                    <label> Nationality</label>
                                    <input type="text" name="nationality" id="nationality" class="form-control" required />
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label> Employee Roles</label>
									<select name="roles" id="roles" class="form-control" required> 
										<option value="Director"> -- Select Employee Role -- </option>	
										<option value="Director">Director</option>
										<option value="Manager">Manager</option>
										<option value="Supervisor">Supervisor</option>
										<option value="Clerk">Clerk</option>
										<option value="Other Staff">Other Staff</option>    
									</select>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label> Start Date</label>
                                    <input type="date" name="start_date" id="start_date" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label> Employment Type</label>
									<select name="emp_type" id="emp_type" class="form-control" required> 
										<option value="Director"> -- Select Employment Type -- </option>	
										<option value="Permanent">Permanent</option>
										<option value="Contract">Contract</option>
										<option value="Intern">Intern</option>
										<option value="General">General</option>
										<option value="Others">Others</option>    
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
		$('#employee_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Employee");
		$('#action').val("Add");
		$('#btn_action').val("Add");
	});

	var employeedataTable = $('#employee_data').DataTable({
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax":{
			url:"employee_fetch.php",
			type:"POST"
		},
		"columnDefs":[
			{
				"target":[4,5],
				"orderable":false
			}
		],
		"pageLength": 25
	});

	$(document).on('submit', '#employee_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled','disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:"employee_action.php",
			method:"POST",
			data:form_data,
			success:function(data)
			{
				$('#employee_form')[0].reset();
				$('#employeeModal').modal('hide');
				$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
				$('#action').attr('disabled', false);
				employeedataTable.ajax.reload();
			}
		})
	});

	$(document).on('click', '.update', function(){
		var employee_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:"employee_action.php",
			method:"POST",
			data:{employee_id:employee_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#employeeModal').modal('show');
				$('#employee_code').val(data.employee_code);
				$('#full_name').val(data.full_name);
                $('#phone_no').val(data.phone_no);
				$('#email').val(data.email);
				$('#address').val(data.address);
                $('#dob').val(data.dob);
				$('#id_no').val(data.id_no);
                $('#nationality').val(data.nationality);
				$('#roles').val(data.roles);
                $('#start_date').val(data.start_date);
				$('#emp_type').val(data.emp_type);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Employee");
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
				url:"employee_action.php",
				method:"POST",
				data:{employee_id:employee_id, status:status, btn_action:btn_action},
				success:function(data)
				{
					$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
					employeedataTable.ajax.reload();
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


