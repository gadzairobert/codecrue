<?php
//exp_list.php

include('database_connection.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

if($_SESSION['type'] != 'master')
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
                    <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                        <div class="row">
                            <h3 class="panel-title">Expense List</h3>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <div class="row" align="right">
                             <button type="button" name="add" id="add_button" data-toggle="modal" data-target="#exp_listModal" class="btn btn-success btn-xs">New Expense</button>   		
                        </div>
                    </div>
                    <div style="clear:both"></div>
                </div>
                <div class="panel-body">
                    <div class="row">
                    	<div class="col-sm-12 table-responsive">
                    		<table id="exp_list_data" class="table table-bordered table-striped" style="text-align:center">
                    			<thead><tr>
									<th style="text-align:center">Expense Name</th>
									<th style="text-align:center">Status</th>
									<th style="text-align:center">Edit</th>
									<th style="text-align:center">Delete</th>
								</tr></thead>
                    		</table>
                    	</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div id="exp_listModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="exp_list_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Add Expense</h4>
    				</div>
    				<div class="modal-body">
    					<label>Enter Expense Name</label>
						<input type="text" name="exp_name" id="exp_name" class="form-control" required />
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="exp_id" id="exp_id"/>
    					<input type="hidden" name="btn_action" id="btn_action"/>
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
		$('#exp_list_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Expense");
		$('#action').val('Add');
		$('#btn_action').val('Add');
	});

	$(document).on('submit','#exp_list_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled','disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:"exp_list_action.php",
			method:"POST",
			data:form_data,
			success:function(data)
			{
				$('#exp_list_form')[0].reset();
				$('#exp_listModal').modal('hide');
				$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
				$('#action').attr('disabled', false);
				exp_listdataTable.ajax.reload();
			}
		})
	});

	$(document).on('click', '.update', function(){
		var exp_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:"exp_list_action.php",
			method:"POST",
			data:{exp_id:exp_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#exp_listModal').modal('show');
				$('#exp_name').val(data.exp_name);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Expenses");
				$('#exp_id').val(exp_id);
				$('#action').val('Edit');
				$('#btn_action').val("Edit");
			}
		})
	});

	var exp_listdataTable = $('#exp_list_data').DataTable({
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"exp_list_fetch.php",
			type:"POST"
		},
		"columnDefs":[
			{
				"targets":[1, 2],
				"orderable":false,
			},
		],
		"pageLength": 5
	});
	$(document).on('click', '.delete', function(){
		var exp_id = $(this).attr('id');
		var status = $(this).data("status");
		var btn_action = 'delete';
		if(confirm("Are you sure you want to change status?"))
		{
			$.ajax({
				url:"exp_list_action.php",
				method:"POST",
				data:{exp_id:exp_id, status:status, btn_action:btn_action},
				success:function(data)
				{
					$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
					exp_listdataTable.ajax.reload();
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

<?php
include('footer.php');
?>


				