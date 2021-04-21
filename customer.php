<?php
//customer.php

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
	<span id="alert_action"></span>
		<div class="row">
			<div class="col-lg-12">
				<div class="panel panel-default">
                    <div class="panel-heading">
                    	<div class="row">
                        	<div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                            	<h3 class="panel-title">Customer List</h3>
                            </div>
                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            	<button type="button" name="add" id="add_button" data-toggle="modal" data-target="#customerModal" class="btn btn-success btn-xs">New Customer</button>
                        	</div>
                        </div>
                       
                        <div class="clear:both"></div>
                   	</div>
                   	<div class="panel-body">
                   		<div class="row"><div class="col-sm-12 table-responsive">
                   			<table id="customer_data" class="table table-bordered table-striped">
                   				<thead>
									<tr>
										<th>Customer Name</th>
										<th>Physical Address</th>
                                        <th>Phone Number</th>
										<th>Email Address.</th>
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
        <div id="customerModal" class="modal fade">
        	<div class="modal-dialog">
        		<form method="post" id="customer_form">
        			<div class="modal-content">
        			<div class="modal-header">
        				<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> New Customer</h4>
        			</div>
        			<div class="modal-body">
                        <div class="row">
							<div class="col-md-3">
                                <div class="form-group">
                                    <label> Customer Name</label>
                                    <input type="text" name="inventory_order_name" id="inventory_order_name" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Physical Address</label>
                                    <input type="text" name="inventory_order_address" id="inventory_order_address" class="form-control" required />
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label> Phone Number</label>
                                    <input type="number" name="phone_number" id="phone_number" oninput="javascript: if (this.value.length > this.maxLength) this.value = this.value.slice(0, this.maxLength);"  maxlength = "10" class="form-control" required />
                                </div>
							</div>
							<div class="col-md-3">
                                <div class="form-group">
                                    <label> Email Address</label>
                                    <input type="email" name="email_address" id="email_address" class="form-control" />
                                </div>
                            </div>
                        </div>
        			</div>
        			<div class="modal-footer">
        				<input type="hidden" name="customer_id" id="customer_id" />
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
		$('#customer_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Customer");
		$('#action').val("Add");
		$('#btn_action').val("Add");
	});

	var customerdataTable = $('#customer_data').DataTable({
		"processing": true,
		"serverSide": true,
		"order": [],
		"ajax":{
			url:"customer_fetch.php",
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

	$(document).on('submit', '#customer_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled','disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:"customer_action.php",
			method:"POST",
			data:form_data,
			success:function(data)
			{
				$('#customer_form')[0].reset();
				$('#customerModal').modal('hide');
				$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
				$('#action').attr('disabled', false);
				customerdataTable.ajax.reload();
			}
		})
	});

	$(document).on('click', '.update', function(){
		var customer_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:"customer_action.php",
			method:"POST",
			data:{customer_id:customer_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#customerModal').modal('show');
				$('#inventory_order_name').val(data.inventory_order_name);
                $('#inventory_order_address').val(data.inventory_order_address);
				$('#phone_number').val(data.phone_number);
				$('#email_address').val(data.email_address);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Customer");
				$('#customer_id').val(customer_id);
				$('#action').val('Edit');
				$('#btn_action').val('Edit');
			}
		})
	});

	$(document).on('click', '.delete', function(){
		var customer_id = $(this).attr("id");
		var status = $(this).data('status');
		var btn_action = "delete";
		if(confirm("Are you sure you want to change status?"))
		{
			$.ajax({
				url:"customer_action.php",
				method:"POST",
				data:{customer_id:customer_id, status:status, btn_action:btn_action},
				success:function(data)
				{
					$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
					customerdataTable.ajax.reload();
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

