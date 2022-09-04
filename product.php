<?php
//product.php

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
		$('#product_date').datepicker({
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
                            <span><b>TOTAL PURCHASES VALUE: </b><span style="color:green">R<?php echo count_total_cash_order_value($connect); ?></span></span>
                        </div>

                            <div class="col-lg-2 col-md-2 col-sm-4 col-xs-6" align='right'>
                                <button type="button" name="add" id="add_button" class="btn btn-success btn-xs">New Product</button>
                            </div>
                        </div>
                    </div>
                    <div class="panel-body">
                        <div class="row"><div class="col-sm-12 table-responsive">
                            <table id="product_data" class="table table-bordered table-striped">
                                <thead><tr>
                                    <th>ID</th>
                                    <th>Date</th>
                                    <th>Name</th>
                                    <th>Gross Qty</th>
                                    <th>Deduct</th>
                                    <th>Nett Qty</th>
                                    <th>Unit Price</th>
                                    <!--<th>Nett Price</th>-->
                                    <th>Status</th>
                                    <th>View</th>
                                    <th>PDF</th>
                                    <th>Update</th>
                                    <th>Delete</th>
                                </tr></thead>
                            </table>
                        </div></div>
                    </div>
                </div>
			</div>
		</div>

        <div id="productModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="product_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Add Product</h4>
                        </div>
                        <div class="modal-body">
                            <div class="row">
                            <div class="col-md-6 hidden">    
                                    <div class="form-group">
                                        <label>Product Date</label>
                                        <input type="hidden" name="product_date" id="product_date" value="<?php echo date('m/d/y');?>" class="form-control" readonly />
                                    </div>
                                </div>
                            </div> 
                                <!--<div class="form-group">
                                    <label>Enter Product Description</label>
                                    <textarea name="product_description" id="product_description" class="form-control" rows="5" required></textarea>
                                </div>-->
                            <div class="list_wrapper">
                                <div class="row">
                                <div class="col-md-1">
                                    <div class="form-group">
                                        <label> Category</label>
                                        <select name="category_id" id="category_id" class="form-control" required>
                                            <!--<option value=""> CAT</option>-->
                                            <?php echo fill_category_list($connect);?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-2">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                            <input type="text" name="product_name" id="product_name" placeholder="enter product name" class="form-control" required />
                                        </select>
                                    </div>
                                </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Gross Qty</label>
                                            <input type="text" name="product_quantity" name="product_quantity" id="product_quantity" placeholder="enter gross qty"  class="form-control" onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;" required /> 
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Deductions</label>
                                            <div class="input-group">
                                                <input type="text" name="product_minimum_order" id="product_minimum_order"  placeholder="deducted" onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;" class="form-control"  required />
                                               <span class="input-group-addon">
                                                    <input type="checkbox" id="kg" name="product_unit"> kg 
                                                    <input type="checkbox" id="percent" name="product_unit"> % <br>
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Nett Qty</label>
                                            <input type="text" name="product_tax"  id="product_tax" class="form-control"  readonly />
                                        </div>
                                    </div>
                                    <div class="col-md-2">
                                        <div class="form-group">
                                            <label>Unit Price</label>
                                            <input type="text" name="unit_price"  id="unit_price" onkeyup="AutoCalc2()" class="form-control" placeholder="enter unit price"  onfocus="this.oldvalue = this.value;" onkeyup="validateInput(this);this.oldvalue = this.value;" onchange="validateInput(this);this.oldvalue = this.value;" />
                                        </div>
                                    </div>
                                    <div class="col-md-2 hidden">
                                        <div class="form-group">
                                            <label>Nett Price</label>
                                            <input type="text"  name="product_base_price" id="product_base_price" readonly class="form-control" required  />
                                        </div>
                                    </div>
                                    <div class="col-md-1"><br>
                                    <button class="btn btn-primary list_add_button" type="button">+</button>
                                </div>
                                </div> 
                            </div>
                        </div>
                        <div class="modal-footer">
                            <input type="hidden" name="product_id" id="product_id" />
                            <input type="hidden" name="btn_action" id="btn_action" />
                            <input type="submit" name="action" id="action" class="btn btn-info" value="Add" />
                            <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div id="productdetailsModal" class="modal fade">
            <div class="modal-dialog">
                <form method="post" id="product_form">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal">&times;</button>
                            <h4 class="modal-title"><i class="fa fa-plus"></i> Product Details</h4>
                        </div>
                        <div class="modal-body">
                            <Div id="product_details"></Div>
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
    var productdataTable = $('#product_data').DataTable({
        "processing":true,
        "serverSide":true,
        "order":[],
        "ajax":{
            url:"product_fetch.php",
            type:"POST"
        },
        "columnDefs":[
            {
                "targets":[7, 8, 9],
                "orderable":false,
            },
        ],
        "pageLength": 10
    });

    $('#add_button').click(function(){
        $('#productModal').modal('show');
        $('#product_form')[0].reset();
        $('.modal-title').html("<i class='fa fa-plus'></i> Add Product");
        $('#action').val("Add");
        $('#btn_action').val("Add");
    });

    $('#category_id').change(function(){
        var category_id = $('#category_id').val();
        var btn_action = 'load_brand';
        $.ajax({
            url:"product_action.php",
            method:"POST",
            data:{category_id:category_id, btn_action:btn_action},
            success:function(data)
            {
                $('#brand_id').html(data);
            }
        });
    });

    $(document).on('submit', '#product_form', function(event){
        event.preventDefault();
        $('#action').attr('disabled', 'disabled');
        var form_data = $(this).serialize();
        $.ajax({
            url:"product_action.php",
            method:"POST",
            data:form_data,
            success:function(data)
            {
                $('#product_form')[0].reset();
                $('#productModal').modal('hide');
                $('#alert_action').fadeIn().html('<div class="alert alert-success">'+data+'</div>');
                $('#action').attr('disabled', false);
                productdataTable.ajax.reload();
            }
        })
    });

    $(document).on('click', '.view', function(){
        var product_id = $(this).attr("id");
        var btn_action = 'product_details';
        $.ajax({
            url:"product_action.php",
            method:"POST",
            data:{product_id:product_id, btn_action:btn_action},
            success:function(data){
                $('#productdetailsModal').modal('show');
                $('#product_details').html(data);
            }
        })
    });

    $(document).on('click', '.update', function(){
        var product_id = $(this).attr("id");
        var btn_action = 'fetch_single';
        $.ajax({
            url:"product_action.php",
            method:"POST",
            data:{product_id:product_id, btn_action:btn_action},
            dataType:"json",
            success:function(data){
                $('#productModal').modal('show');
                $('#product_date').val(data.product_date);
                $('#product_name').val(data.product_name);
                $('#category_id').val(data.category_id);
                $('#brand_id').html(data.brand_select_box);
                $('#brand_id').val(data.brand_id);
                $('#product_quantity').val(data.product_quantity);
                $('#product_minimum_order').val(data.product_minimum_order);
                $('#product_tax').val(data.product_tax);
                $('#product_unit').val(data.product_unit);
                $('#unit_price').val(data.unit_price);
                $('#product_base_price').val(data.product_base_price);
                $('.modal-title').html("<i class='fa fa-pencil-square-o'></i> Edit Product");
                $('#product_id').val(product_id);
                $('#action').val("Edit");
                $('#btn_action').val("Edit");
            }
        })
    });

    $(document).on('click', '.delete', function(){
        var product_id = $(this).attr("id");
        var status = $(this).data("status");
        var btn_action = 'delete';
        if(confirm("Are you sure you want to change status?"))
        {
            $.ajax({
                url:"product_action.php",
                method:"POST",
                data:{product_id:product_id, status:status, btn_action:btn_action},
                success:function(data){
                    $('#alert_action').fadeIn().html('<div class="alert alert-info">'+data+'</div>');
                    productdataTable.ajax.reload();
                }
            });
        }
        else
        {
            return false;
        }
    });

});
</script>
<script>
        function AutoCalc(obj) {
           var total = 0;

           if (isNaN(obj.value)) {
               alert("Please enter a number :(");
               obj.value = '';
               return false;
           }
           else {

               var textBox = new Array();
               textBox = document.getElementsByTagName('input')

               for (i = 0; i < textBox.length; i++) {
                   if (textBox[i].type == 'text') {
                       var inputVal = textBox[i].value;
                       if (inputVal == '')
                           inputVal = 0;
                       if ((textBox[i].id == 'product_quantity')) {
                           total = total + parseInt(inputVal);
                       }
                       if ((textBox[i].id == 'product_minimum_order')) {
                           total = total - parseInt(inputVal);
                       }
                   }
               }
               document.getElementById('product_tax').value = total;
           }
       }

       $(document).on('click', '#add_more', function(){
			count = count + 1;
			add_product_row(count);
		});
		$(document).on('click', '.remove', function(){
			var row_no = $(this).attr("id");
			$('#row'+row_no).remove();
		});
   //calculate total price
    function AutoCalc2(){
    var textValue1 = document.getElementById('product_tax').value;
    var textValue2 = document.getElementById('unit_price').value;
    
        if($.trim(textValue1) != '' && $.trim(textValue2) != ''){
    document.getElementById('product_base_price').value = textValue1 * textValue2; 
        }
    }

 //calculate total kgs and percentages
    var newVal = "Unset";
    var subtract = document.getElementById("kg");
    var multiply = document.getElementById("percent");
    var demoP=document.getElementById("demo");

    subtract.onclick = function() {
        var n1 = parseFloat(document.getElementById('product_quantity').value);
        var n2 = parseFloat(document.getElementById('product_minimum_order').value);
        newVal = n1-n2;
        document.getElementById('product_tax').value = newVal; 
    }
    multiply.onclick = function() {
        var n1 = parseFloat(document.getElementById('product_quantity').value);
        var n2 = parseFloat(document.getElementById('product_minimum_order').value);
        newVal = (n1-(n1*(n2/100)));
        
        document.getElementById('product_tax').value = newVal; 

    }


$(document).ready(function()
{
    var x = 0; //Initial field counter
    var list_maxField = 20; //Input fields increment limitation
    
    //Once add button is clicked
    $('.list_add_button').click(function()
        {
        //Check maximum number of input fields
        if(x < list_maxField){ 
            x++; //Increment field counter
            var list_fieldHTML = '<div class="row"><div class="col-md-1"><div class="form-group"><select name="list['+x+'][]" id="category_id" class="form-control" required><?php echo fill_category_list($connect);?></select></div></div> <div class="col-md-2"><div class="form-group"><input type="text" name="product_name" id="product_name" placeholder="enter product name" class="form-control" required /></div></div><div class="col-md-2"><div class="form-group"><input name="list['+x+'][]" type="number" id="product_quantity" placeholder="enter gross qty"  class="form-control" min="0" oninput="this.value = Math.abs(this.value)"/></div></div><div class="col-md-2"><div class="form-group"> <div class="input-group"><input name="list['+x+'][]" type="number" id="product_minimum_order" placeholder="deducted"  class="form-control" min="0" oninput="this.value = Math.abs(this.value)"/><span class="input-group-addon"><input type="checkbox" id="kg" name="product_unit"> kg <input type="checkbox" id="percent" name="product_unit"> % <br></span></div></div></div><div class="col-md-2 hidden"><div class="form-group"><input autocomplete="off" name="list['+x+'][]" type="text" name="product_tax" id="product_tax" onkeyup="AutoCalc2()" readonly class="form-control"/></div> </div> <div class="col-md-2"> <div class="form-group"> <input autocomplete="off" name="list['+x+'][]"  type="number" id="unit_price" onkeyup="AutoCalc2()" placeholder="enter unit price"  class="form-control" min="0" oninput="this.value = Math.abs(this.value)"></div> </div> <div class="col-md-2"><div class="form-group"><input autocomplete="off" name="list['+x+'][]" id="product_base_price" readonly class="form-control" required /></div></div><div class="col-xs-1 col-sm-7 col-md-1"><a href="javascript:void(0);" class="list_remove_button btn btn-danger">-</a></div></div>'; //New input field html 
            

            $('.list_wrapper').append(list_fieldHTML); //Add field html
        }
    });

    //Once remove button is clicked
    $('.list_wrapper').on('click', '.list_remove_button', function()
    {
       $(this).closest('div.row').remove(); //Remove field html
       x--; //Decrement field counter
    });
});

function validateInput(input){
      if(isNaN(input.value)||input.value<0)
      {
        input.value = input.oldvalue;
      }

    }
</script>