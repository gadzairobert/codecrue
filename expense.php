<?php
//expense.php

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
	$(document).ready(function(){
		$('#expense_date').datepicker({
			format: "yyyy-mm-dd hh-mm-ss",
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
                            <span><b>Current Month Total Expenditure: </b><span style="color:green">R<?php echo count_total_expense_value($connect); ?></span></span>
                        </div>

                        <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align="right">
                            <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">New Expense</button>    	
                        </div>
                    </div>
                </div>
                <div class="panel-body">
                	<table id="expense_data" class="table table-bordered table-striped">
                		<thead>
							<tr>
								<th>Expense No</th>
								<th>Expense Date</th>
								<th>Expense Total</th>
								<th>No. of Items</th>
								<th style="text-align:center">View</th>
								<!--<th style="text-align:center">Edit</th>-->
								<th style="text-align:center">Print</th>
							</tr>
						</thead>
                	</table>
                </div>
            </div>
        </div>
    </div>

    <div id="expenseModal" class="modal fade">

    	<div class="modal-dialog">
    		<form method="post" id="expense_form">
    			<div class="modal-content">
    				<div class="modal-header">
    					<button type="button" class="close" data-dismiss="modal">&times;</button>
						<h4 class="modal-title"><i class="fa fa-plus"></i> Create New Expense</h4>
    				</div>
    				<div class="modal-body">
						<div class="row">
							<div class="col-md-4 hidden">
								<div class="form-group">
									<label>Expense Date</label>
									<input type="text" name="expense_date" value="<?php echo date('m/d/y');?>" id="expense_date" class="form-control" />
								</div>
							</div>
						</div>

						<div class="form-group">
							<span id="span_expense_details"></span>
							
						</div>
    				</div>
    				<div class="modal-footer">
    					<input type="hidden" name="expense_id" id="expense_id" />
    					<input type="hidden" name="btn_action" id="btn_action" />
    					<input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
    				</div>
    			</div>
    		</form>
    	</div>
    </div>

	<div id="expensedetailsModal" class="modal fade">
		<div class="modal-dialog">
			<form method="post" id="expense_form">
				<div class="modal-content">
					<div class="modal-body">
						<Div id="expense_details"></Div>
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

    	var expensedataTable = $('#expense_data').DataTable({
			"processing":true,
			"serverSide":true,
			"order":[],
			"ajax":{
				url:"expense_fetch.php",
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
					"targets":[4, 5, 6, 7, 8],
					"orderable":false,
				},
			],
			<?php
			}
			?>
			"pageLength": 25
		});

		$('#add_button').click(function(){
			$('#expenseModal').modal('show');
			$('#expense_form')[0].reset();
			$('.modal-title').html("<i class='fa fa-plus'></i> Create New Expense");
			$('#action').val('Add');
			$('#btn_action').val('Add');
			$('#span_expense_details').html('');
			add_expense_row();
		});

		function add_expense_row(count = '')
		{
			var html = '';
			html += '<span id="row'+count+'"><div class="row">';
			html += '<div class="col-md-2 hidden">';
			html += '<select name="product_id[]" id="product_id'+count+'" class="form-control selectpicker" data-live-search="true">';
			html += '<?php echo fill_product_list($connect); ?>';
			html += '</select><input type="hidden" name="hidden_product_id[]" id="hidden_product_id'+count+'" />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<select name="exp_id[]" id="exp_id'+count+'" class="form-control selectpicker" data-live-search="true" required>';
			html += '<?php echo fill_expense_list($connect); ?>';
			html += '</select>';
			html += '</div>';
			html += '<div class="col-md-3">';
			html += '<input type="text" class="form-control" id="expense_item" name="expense_item[]" placeholder="enter iten name"  required />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" class="form-control" id="quantity" name="quantity[]" placeholder="enter quantity" required />';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<select id="unit" name="unit[]" class="form-control" required>';
			html += '<option value="kg"> kg</option>';
			html += '<option value="litre"> litre</option>';
			html += '<option value="meter"> meter</option>';
			html += '</select>';
			html += '</div>';
			html += '<div class="col-md-2">';
			html += '<input type="text" id="unit_price" name="unit_price[]" class="form-control" placeholder="enter unit price" required />';
			html += '</div>';
			html += '<div class="col-md-1 hidden">';
			html += '<input type="hidden" id="net_price" name="net_price[]" class="form-control"  readonly />';
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
			$('#span_expense_details').append(html);

			$('.selectpicker').selectpicker();

			function validateInput(input){
				if(isNaN(input.value)||input.value<0)
				{
					input.value = input.oldvalue;
				}
    		}
		}

		var count = 0;

		$(document).on('click', '#add_more', function(){
			count = count + 1;
			add_expense_row(count);
		});
		$(document).on('click', '.remove', function(){
			var row_no = $(this).attr("id");
			$('#row'+row_no).remove();
		});

		$(document).on('submit', '#expense_form', function(event){
			event.preventDefault();
			$('#action').attr('disabled', 'disabled');
			var form_data = $(this).serialize();
			$.ajax({
				url:"expense_action.php",
				method:"POST",
				data:form_data,
				success:function(data){
					$('#expense_form')[0].reset();
					$('#expenseModal').modal('hide');
					$('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
					$('#action').attr('disabled', false);
					expensedataTable.ajax.reload();
				}
			});
		});

		//view expense details
		$(document).on('click', '.view', function(){
			var expense_id = $(this).attr("id");
			var btn_action = 'expense_details';
			$.ajax({
				url:"expense_action.php",
				method:"POST",
				data:{expense_id:expense_id, btn_action:btn_action},
				success:function(data){
					$('#expensedetailsModal').modal('show');
					$('#expense_details').html(data);
				}
			})
		});
		//------------------------

		$(document).on('click', '.update', function(){
			var expense_id = $(this).attr("id");
			var btn_action = 'fetch_single';
			$.ajax({
				url:"expense_action.php",
				method:"POST",
				data:{expense_id:expense_id, btn_action:btn_action},
				dataType:"json",
				success:function(data)
				{
					$('#expenseModal').modal('show');
					$('#expense_date').val(data.expense_date);
					$('#span_expense_details').html(data.product_details);
					$('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Expense Items");
					$('#expense_id').val(expense_id);
					$('#action').val('Edit');
					$('#btn_action').val('Edit');
				}
			})
		});

		$(document).on('click', '.delete', function(){
			var expense_id = $(this).attr("id");
			var status = $(this).data("status");
			var btn_action = "delete";
			if(confirm("Are you sure you want to change status?"))
			{
				$.ajax({
					url:"expense_action.php",
					method:"POST",
					data:{expense_id:expense_id, status:status, btn_action:btn_action},
					success:function(data)
					{
						$('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
						expensedataTable.ajax.reload();
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


	<script>
		   //calculate total price
       function AutoCalc1(){
		var textValue1 = document.getElementById('net_quantity').value;
		var textValue2 = document.getElementById('unit_price').value;
		
			if($.trim(textValue1) != '' && $.trim(textValue2) != ''){
		document.getElementById('net_price').value = textValue1 * textValue2; 
			}
		}

//calculate total kgs and percentages
    var newVal = "Unset";
    var minus = document.getElementById("kgs");
    var times = document.getElementById("percents");
    var demoT=document.getElementById("demo");

    minus.onclick = function() {
        var n1 = parseFloat(document.getElementById('gross_quantity').value);
        var n2 = parseFloat(document.getElementById('deducted').value);
        newVal = n1-n2;
        document.getElementById('net_quantity').value = newVal; 
    }
    times.onclick = function() {
        var n1 = parseFloat(document.getElementById('gross_gross_quantity').value);
        var n2 = parseFloat(document.getElementById('deducted').value);
        newVal = (n1-(n1*(n2/100)));
        
        document.getElementById('net_quantity').value = newVal; 

    }

//prevenr negative input and allow decimals
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