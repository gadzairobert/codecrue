<?php
include('database_connection.php');

include('function.php');

if(!isset($_SESSION['type']))
{
	header('location:login.php');
}

include('header.php');


?>
	<link rel="stylesheet" href="css/datepicker.css">
	<script src="js/bootstrap-datepicker1.js"></script>
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/css/bootstrap-select.min.css">
	<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.12.2/js/bootstrap-select.min.js"></script>
	<script>
		function percKilo(rowNo, type){
			var n1 = parseFloat(document.getElementById('quantity'+rowNo).value);
			var n2 = parseFloat(document.getElementById('deduct'+rowNo).value);
		console.log(n1,n2)
			if(type == 'kg') {
				var newVal = n1-n2;
				document.getElementById('qty_nett'+rowNo).value = newVal; 
			}else {
				var newVal = (n1-(n1*(n2/100)));
				document.getElementById('qty_nett'+rowNo).value = newVal; 
			}
		};

	</script>
	<script>
	$(document).ready(function(){
		$('#inventory_order_created_date').datepicker({
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
						<div class="col-lg-10 col-md-5 col-sm-5 col-xs-3">
                            <span><b>Current Month Total Sales:  </b><span style="color:green">R<?php echo count_total_order_value_month($connect); ?></span></span>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">New Order</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="order_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Order No</th>
								<th>Order Date</th>
								<th>Invoice</th>
								<th>Customer</th>
								<th> Value (VAT Incl)</th>
								<th>Status</th>
								<th style="text-align: center">View Order</th>
								<th style="text-align: center">Print Invoice</th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="orderModal" class="modal fade">

    	<div class="modal-dialog">
    		<form method="post" id="order_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Create Order</h4>
    				</div>
    				<div class="modal-body">
						<div class="row">
							<div class="col-md-4">
								<div class="form-group">
									<label>Order Date</label>
									<input type="text" name="inventory_order_created_date" id="inventory_order_created_date" class="form-control" required />
								</div>
							</div>
							<div class="col-md-4">
								<div class="form-group">
									<label>Customer Name</label>
									<select name="inventory_order_name" id="inventory_order_name" class="form-control" required>
                                        <option value="">Select Customer</option>
                                        <?php echo fill_customer_list($connect);?>
                                    </select>
								</div>
							</div>
							<div class="col-md-3">
								<div class="form-group">
									<label>Invoice Number</label>
									<input type="text" name="invoice_no" id="invoice_no" class="form-control" required />
								</div>
							</div>
							<div class="col-md-1 hidden">
							</div>
						</div>

						<div class="form-group">
							<span id="span_product_details"></span>
							<hr />
						</div>
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="inventory_order_id" id="inventory_order_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    				</div>
    			</div>
    		</form>
    	</div>

    </div>
	<div id="orderdetailsModal" class="modal fade">
		<div class="modal-dialog">
			<form method="post" id="order_form">
				<div class="modal-content">
					<!--<div class="modal-header2">
						<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Order Details </h4>
					</div>-->
					<div class="modal-body">
						<Div id="order_details"></Div>
					</div>
					<div class="modal-footer">
						
						<button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
					</div>
				</div>
			</form>
		</div>
	</div>

<script type="text/javascript">
    $(document).ready(function(){

    	var orderdataTable = $('#order_data').DataTable({
			"processing":true,
			"serverSide":true,
			"order":[],
			"ajax":{
				url:"order_fetch.php",
				type:"POST"
			},
			<?php
			if($_SESSION["type"] == 'master')
			{
			?>
			"columnDefs":[
				{
					"targets":[1, 2, 3, 4, 5],
					"orderable":false,
				},
			],
			<?php
			}
			else
			{
			?>
			"columnDefs":[
				{
					"targets":[1, 2, 3, 4, 5, 6],
					"orderable":false,
				},
			],
			<?php
			}
			?>
			"pageLength": 50
		});

		$('#add_button').click(function(){
			$('#orderModal').modal('show');
			$('#order_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create Order");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_product_details').html('');
			add_product_row();
		});

		function add_product_row(count = '')
		{
			var html = '';
			html += '<span id="row'+count+'"><div class="row">';
			html += '<div class="col-md-2">';
			html += '<select name="stock_id[]'+count+'" id="stock_id" class="form-control selectpicker" data-live-search="true" required>';
			html += '<?php echo fill_stock_list($connect); ?>';
			html += '</select><input type="hidden" name="hidden_product_id[]" id="hidden_product_id'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" class="form-control" id="quantity'+count+'" name="quantity[]" placeholder="Gross" onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;" class="form-control"  required />';
			html += '</div>';
			html += '<div class="col-md-2">';
            html += '<div class="form-group">';
            html += '<div class="input-group">';
            html += '<input type="text" class="form-control" id="deduct'+count+'" name="deduct[]" onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;" placeholder="Deduct" required />'; 
            html += '<span class="input-group-addon">';
            html += '<input type="radio" id="kg'+count+'" onclick="percKilo('+count+',\'kg\')"  name="uom'+count+'" > kg ';
            html += '<input type="radio" id="percent'+count+'" onclick="percKilo('+count+',\'percent\')" name="uom'+count+'" > % <br>';
            html += '</span>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
			html += '<div class="col-md-1">';
			html += '<select id="uom'+count+'" name="uom[]" class="form-control selectpicker"  required>';
			html += '<option value="1">- uom -</option>';
			html += '<option value="kg">kg</option>';
			html += '<option value="%">%</option>';
			html += '</select>';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" id="qty_nett'+count+'" name="qty_nett[]" class="form-control" readonly />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" id="price'+count+'" name="price[]" class="form-control" placeholder="Unit Price" onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;" required />';
			html += '</div>';
			html += '<div class="col-md-1">';
			html += '<input type="hidden" id="nett_price'+count+'" name="nett_price[]" class="form-control"  readonly  />';
			html += '</div>';
			html += '<div class="col-md-1">';
			html += '<input type="hidden" id="tax'+count+'" name="tax[]" class="form-control"/>';
			html += '</div>';
			html += '<div class="col-md-1 hidden">';
			html += '<input type="hidden" id="item_date'+count+'" name="item_date[]" class="form-control" />';
			html += '</div>';
			html += '<div class="col-md-1">';
			if(count == '')
			{
				html += '<button type="button" name="add_more" id="add_more" class="btn btn-success btn-xs">+</button>';
			}
			else
			{
				html += '<button type="button" name="remove" id="'+count+'" class="btn btn-danger btn-xs remove">-</button>';
			}
			html += '</div>';
			html += '</div></div><br /></span>';
			$('#span_product_details').append(html);

			$('.selectpicker').selectpicker();
		
		//calculate total kg and percentage
		function percKilo(rowNo, type){
			var n1 = parseFloat(document.getElementById('quantity').value);
			var n2 = parseFloat(document.getElementById('deduct').value);

			if(type == "kg"){
			newVal = n1-n2;
			document.getElementById('qty_nett').value = newVal; 
			}else{
				newVal = (n1-(n1*(n2/100)));
				document.getElementById('qty_nett').value = newVal; 
			}
		}
		
		
		// old code
		var newVal = "Unset";
            var minus = document.getElementById("kg");
            var times = document.getElementById("percent");
            var demoT=document.getElementById("demo");

            minus.onclick = function() {
                var n1 = parseFloat(document.getElementById('quantity').value);
                var n2 = parseFloat(document.getElementById('deduct').value);
                newVal = n1-n2;
                document.getElementById('qty_nett').value = newVal; 
            }
            times.onclick = function() {
                var n1 = parseFloat(document.getElementById('quantity').value);
                var n2 = parseFloat(document.getElementById('deduct').value);
                newVal = (n1-(n1*(n2/100)));
                
                document.getElementById('qty_nett').value = newVal; 

            }
		}
		var count = 0;

		$(document).on('click', '#add_more', function(){
			count = count + 1;
			add_product_row(count);
		});
		$(document).on('click', '.remove', function(){
			var row_no = $(this).attr("id");
			$('#row'+row_no).remove();
		});


		$(document).on('submit', '#order_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"order_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#order_form')[0].reset();
					$('#orderModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					orderdataTable.ajax.reload();
				}
			});
		});

		//view order details
		$(document).on('click', '.view', function(){
			var inventory_order_id = $(this).attr("id");
			var btn_action = 'order_details';
			$.ajax({
				url:"order_action.php",
				method:"POST",
				data:{inventory_order_id:inventory_order_id, btn_action:btn_action},
				success:function(data){
					$('#orderdetailsModal').modal('show');
					$('#order_details').html(data);
				}
			})
		});
		//------------------------

		$(document).on('click', '.update', function(){
			var inventory_order_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"order_action.php",
				method:"POST",
				data:{inventory_order_id:inventory_order_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$('#orderModal').modal('show');
					$('#inventory_order_created_date').val(data.inventory_order_created_date);
					$('#inventory_order_name').val(data.inventory_order_name);
					$('#invoice_no').val(data.invoice_no);
					$('#span_product_details').html(data.product_details);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Order");
					$('#inventory_order_id').val(inventory_order_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			})
		});

		$(document).on('click', '.delete', function(){
			var inventory_order_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"order_action.php",
					method:"POST",
					data:{inventory_order_id:inventory_order_id, status:status, btn_action:btn_action},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						orderdataTable.ajax.reload();
					}
				})
			}
			else
			{
				return false;
			}
		});

    });
	
//prevent negative input and allow decimals
	function validateInput(input){
      if(isNaN(input.value)||input.value<0)
      {
        input.value = input.oldvalue;
      }

    }
	
</script>

<?php
include("footer.php");
?>