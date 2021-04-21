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
		function percKgs(rowNo, type){
			var n1 = parseFloat(document.getElementById('gross_quantity'+rowNo).value);
			var n2 = parseFloat(document.getElementById('deducted'+rowNo).value);
		console.log(n1,n2)
			if(type == 'kg') {
				var newVal = n1-n2;
				document.getElementById('net_quantity'+rowNo).value = newVal; 
			}else {
				var newVal = (n1-(n1*(n2/100)));
				document.getElementById('net_quantity'+rowNo).value = newVal; 
			}
		};
	</script>
	<script>
	$(document).ready(function(){
		$('#inventory_purchase_created_date').datepicker({
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
						<div class="col-lg-10 col-md-10 col-sm-5 col-xs-3">
                            <span><b>Current Month Total Sales:  </b><span style="color:green">R<?php echo count_total_purchase_value($connect); ?></span></span>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">New Purchase</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="purchase_data" class="table table-bordered table-striped" style="text-align:center">
                		<thead>
							<tr>
								<th style="text-align:center">PurchaseID</th>
								<th style="text-align:center">Purchase Date</th>
								<th style="text-align:center">Bought From</th>
								<th style="text-align:center">Total Value</th>
								<th style="text-align:center">Status</th>
								<th style="text-align:center">Pay</th>
								<th style="text-align:center">View Items</th>
								<th style="text-align:center">Print Invoice</th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="orderModal" class="modal fade">

    	<div class="modal-dialog">
    		<form method="post" id="purchase_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Create New Purchase</h4>
    				</div>
    				<div class="modal-body">
						<div class="row">
							<div class="col-md-4 hidden">
								<div class="form-group">
									<label>Purchase Date</label>
									<input type="text" name="inventory_purchase_created_date" value="<?php echo date('m/d/y');?>" id="inventory_purchase_created_date" class="form-control" />
								</div>
							</div>
						</div>
						<div class="col-md-12">
								<div class="form-group">
									<label>Select Seller's Name</label>
									<select name="agent_name" id="agent_name" class="form-control" required>
                                        <option value="">Select Agent Name</option>
                                        <?php echo fill_agent_list($connect);?>
                                    </select>
								</div>
							</div>
						<div class="form-group">
							<span id="span_product_details"></span>
							
						</div>
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="inventory_purchase_id" id="inventory_purchase_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    				</div>
    			</div>
    		</form>
    	</div>
    </div>

	<div id="purchasedetailsModal" class="modal fade">
		<div class="modal-dialog">
			<form method="post" id="purchase_form">
				<div class="modal-content">
					<div class="modal-body">
						<Div id="purchase_details"></Div>
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

    	var orderdataTable = $('#purchase_data').DataTable({
			"processing":true,
			"serverSide":true,
			"order":[],
			"ajax":{
				url:"purchase_fetch.php",
				type:"POST"
			},
			<?php
			if($_SESSION["type"] == 'master')
			{
			?>
			"columnDefs":[
				{
					"targets":[1, 2, 3, 4],
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
					"targets":[1, 2, 3, 4, 5],
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
			$('#purchase_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create New Purchase");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_product_details').html('');
			add_product_row();
		});

		function add_product_row(count = '')
		{
			var html = '';
			html += '<span id="row'+count+'"><div class="row">';
			html += '<div class="col-md-2 hidden">';
			html += '<select name="product_id[]" id="product_id'+count+'" class="form-control selectpicker" data-live-search="true">';
			html += '<?php echo fill_product_list($connect); ?>';
			html += '</select><input type="hidden" name="hidden_product_id[]" id="hidden_product_id'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<select name="stock_id[]" id="stock_id'+count+'" class="form-control selectpicker" data-live-search="true" required>';
			html += '<?php echo fill_stock_list1($connect); ?>';
			html += '</select>';
			html += '</div>';
			html += '<div class="col-md-2 hidden">';
			html += '<input type="text" class="form-control" id="item_name'+count+'" name="item_name[]" placeholder="enter item name"  />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" class="form-control" id="gross_quantity'+count+'" name="gross_quantity[]" placeholder="Gross" onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;"  required />';
			html += '</div>';
			html += '<div class="col-md-2">';
            html += '<div class="form-group">';
            html += '<div class="input-group">';
            html += '<input type="text" class="form-control" id="deducted'+count+'" name="deducted[]" placeholder="deducted" onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;"  required />'; 
			html += '<span class="input-group-addon">';
            html += '<input type="radio" id="kg'+count+'" onclick="percKgs('+count+',\'kg\')" name="product_unit'+count+'"> kg ';
            html += '<input type="radio" id="percent'+count+'" onclick="percKgs('+count+',\'percent\')" name="product_unit'+count+'"> % <br>';
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
			html += '<input type="text" id="net_quantity'+count+'" name="net_quantity[]" class="form-control" readonly/>';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" id="unit_price'+count+'" name="unit_price[]" class="form-control" placeholder="Unit Price" onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;" required />';
			html += '</div>';
			html += '<div class="col-md-1 hidden">';
			html += '<input type="hidden" id="net_price'+count+'" name="net_price[]" class="form-control"  readonly />';
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
			function percKg(rowNo, type){
				var n1 = parseFloat(document.getElementById('gross_quantity').value);
                var n2 = parseFloat(document.getElementById('deducted').value);

				if(type == "kg"){
                newVal = n1-n2;
                document.getElementById('net_quantity').value = newVal; 
				}else{
					newVal = (n1-(n1*(n2/100)));
					document.getElementById('net_quantity').value = newVal; 
				}

				function testResults (form) {
				var TestVar1 = form.input[0].checked;
				var TestVar2 = form.input[1].checked;
				if (TestVar1 == true) {
					form.textbox.value = "kg";
				} else if (TestVar2 == true){
					form.textbox.value = "%";
				} else if (TestVar1 == false && TestVar2 == false) {
					form.textbox.value = "";
				}
				}
			}
			
		// old code
            var newVal = "Unset";
            var minus = document.getElementById("kg");
            var times = document.getElementById("percent");
            var demoT=document.getElementById("demo");

            minus.onclick = function() {
                var n1 = parseFloat(document.getElementById('gross_quantity').value);
                var n2 = parseFloat(document.getElementById('deducted').value);
                newVal = n1-n2;
                document.getElementById('net_quantity').value = newVal; 
            }
            times.onclick = function() {
                var n1 = parseFloat(document.getElementById('gross_quantity').value);
                var n2 = parseFloat(document.getElementById('deducted').value);
                newVal = (n1-(n1*(n2/100)));
                
                document.getElementById('net_quantity').value = newVal; 

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

		$(document).on('submit', '#purchase_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"purchase_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#purchase_form')[0].reset();
					$('#orderModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					orderdataTable.ajax.reload();
				}
			});
		});

		//view order details
		$(document).on('click', '.view', function(){
			var inventory_purchase_id = $(this).attr("id");
			var btn_action = 'purchase_details';
			$.ajax({
				url:"purchase_action.php",
				method:"POST",
				data:{inventory_purchase_id:inventory_purchase_id, btn_action:btn_action},
				success:function(data){
					$('#purchasedetailsModal').modal('show');
					$('#purchase_details').html(data);
				}
			})
		});
		//------------------------

		$(document).on('click', '.update', function(){
			var inventory_purchase_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"purchase_action.php",
				method:"POST",
				data:{inventory_purchase_id:inventory_purchase_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$('#orderModal').modal('show');
					$('#inventory_purchase_created_date').val(data.inventory_purchase_created_date);
					$('#span_product_details').html(data.product_details);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Purchased Items");
					$('#inventory_purchase_id').val(inventory_purchase_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			})
		});

		$(document).on('click', '.delete', function(){
			var inventory_purchase_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"purchase_action.php",
					method:"POST",
					data:{inventory_purchase_id:inventory_purchase_id, status:status, btn_action:btn_action},
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