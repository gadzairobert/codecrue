<?php
//stock.php

include('database_connection.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');

?>
<meta http-equiv="refresh" content="45">
	<span id="alert_action"></span>
	<div class="row">
		<div class="col-lg-12">
			<div class="panel panel-default">
                <div class="panel-heading">
                    <div class="col-lg-10 col-md-10 col-sm-8 col-xs-6">
                        <div class="row">
                            <h3 class="panel-title">Stock List</h3>
                        </div>
                    </div>
                    <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6">
                        <div class="row" align="right">
                             <button type="button" name="add" id="add_button" data-toggle="modal" data-target="#stockModal" class="btn btn-success btn-xs">New Product</button>   		
                        </div>
                    </div>
                    <div style="clear:both"></div>
                </div>
                <div class="panel-body">
                    <div class="row">
                    	<div class="col-sm-12 table-responsive">
                    		<table id="stock_data" class="table table-bordered table-striped" style="text-align:center">
                    			<thead><tr>
									<th style="text-align:center">Product Name</th>
                                    <th style="text-align:center">Qty On Hand</th>
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
    <div id="stockModal" class="modal fade">
    	<div class="modal-dialog">
    		<form method="post" id="stock_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Add Product</h4>
    				</div>
    				<div class="modal-body">
    					<label>Enter Product Name</label>
						<input type="text" name="item_name" id="item_name" class="form-control" required />
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="stock_id" id="stock_id"/>
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
		$('#stock_form')[0].reset();
		$('.modal-title').html("<i class='fa fa-plus'></i> Add Product");
		$('#action').val('Add');
		$('#btn_action').val('Add');
	});

	$(document).on('submit','#stock_form', function(event){
		event.preventDefault();
		$('#action').attr('disabled','disabled');
		var form_data = $(this).serialize();
		$.ajax({
			url:"stock_action.php",
			method:"POST",
			data:form_data,
			success:function(data)
			{
				$('#stock_form')[0].reset();
				$('#stockModal').modal('hide');
				$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
				$('#action').attr('disabled', false);
				stockdataTable.ajax.reload();
			}
		})
	});

	$(document).on('click', '.update', function(){
		var stock_id = $(this).attr("id");
		var btn_action = 'fetch_single';
		$.ajax({
			url:"stock_action.php",
			method:"POST",
			data:{stock_id:stock_id, btn_action:btn_action},
			dataType:"json",
			success:function(data)
			{
				$('#stockModal').modal('show');
				$('#item_name').val(data.item_name);
				$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Product");
				$('#stock_id').val(stock_id);
				$('#action').val('Edit');
				$('#btn_action').val("Edit");
			}
		})
	});

	var stockdataTable = $('#stock_data').DataTable({
		"processing":true,
		"serverSide":true,
		"order":[],
		"ajax":{
			url:"stock_fetch.php",
			type:"POST"
		},
		"columnDefs":[
			{
				"targets":[2, 3],
				"orderable":false,
			},
		],
		"pageLength": 50
	});
	$(document).on('click', '.delete', function(){
		var stock_id = $(this).attr('id');
		var status = $(this).data("status");
		var btn_action = 'delete';
		if(confirm("Are you sure you want to change status?"))
		{
			$.ajax({
				url:"stock_action.php",
				method:"POST",
				data:{stock_id:stock_id, status:status, btn_action:btn_action},
				success:function(data)
				{
					$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
					stockdataTable.ajax.reload();
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


				