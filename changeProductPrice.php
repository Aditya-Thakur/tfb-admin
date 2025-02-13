<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
{	
header('location:index.php');
}
else{

if(isset($_POST['submit']))
{
$category=$_POST['category'];
$subcat=$_POST['subcategory'];
$productid=$_POST['productName'];
$quantityType = $_POST['quantityType'];
$productQuantity = $_POST['productQuantity'];
$productPrice = $_POST['productPrice'];
$ret=mysqli_query($con,"SELECT * FROM productPriceNew WHERE productId='$productid' AND quantityType = '$quantityType' AND productQuantity = '$productQuantity'");
$num=mysqli_fetch_array($ret);
if($num > 0 ) {
	$sql=mysqli_query($con,"UPDATE productPriceNew set productPrice = '$productPrice' WHERE productId='$productid' AND quantityType = '$quantityType' AND productQuantity = '$productQuantity'");
} else {
$sql=mysqli_query($con,"INSERT INTO productPriceNew(productId, quantityType, productQuantity, productPrice) values('$productid','$quantityType','$productQuantity','$productPrice')");
}
if($sql) {
$_SESSION['msg']="Product Price Updated Successfully !!";
} else {
$_SESSION['msg']="Product Price Update Is Unsuccessfull :( ";
}
}


?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Admin| Insert Product Price</title>
<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
<link type="text/css" href="css/theme.css" rel="stylesheet">
<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
<script src="http://js.nicedit.com/nicEdit-latest.js" type="text/javascript"></script>
<script type="text/javascript">bkLib.onDomLoaded(nicEditors.allTextAreas);</script>

<script>

function getSubcat(val) {
$.ajax({
type: "POST",
url: "get_subcat.php",
data:'cat_id='+val,
success: function(data){
$("#subcategory").html(data);
}
});
}

function getProduct(val) {
$.ajax({
type: "POST",
url: "get_product.php",
data:'subcat_id='+val,
success: function(data){
$("#product").html(data);
}
});
}

function selectCountry(val) {
$("#search-box").val(val);
$("#suggesstion-box").hide();
}

</script>	


</head>
<body>
<?php include('include/header.php');?>

<div class="wrapper">
<div class="container">
<div class="row">
<?php include('include/sidebar.php');?>				
<div class="span9">
<div class="content">

<div class="module">
<div class="module-head">
<h3>Insert Product</h3>
</div>
<div class="module-body">

<?php if(isset($_POST['submit']))
{?>
<div class="alert alert-success">
<button type="button" class="close" data-dismiss="alert">×</button>
<strong>Well done!</strong>	<?php echo htmlentities($_SESSION['msg']);?><?php echo htmlentities($_SESSION['msg']="");?>
</div>
<?php } ?>


<?php if(isset($_GET['del']))
{?>
<div class="alert alert-error">
<button type="button" class="close" data-dismiss="alert">×</button>
<strong>Oh snap!</strong> 	<?php echo htmlentities($_SESSION['delmsg']);?><?php echo htmlentities($_SESSION['delmsg']="");?>
</div>
<?php } ?>

<br />

<form class="form-horizontal row-fluid" name="insertproduct" method="post" enctype="multipart/form-data">

<div class="control-group">
<label class="control-label" for="basicinput">Category</label>
<div class="controls">
<select name="category" class="span8 tip" onChange="getSubcat(this.value);"  required>
<option value="">Select Category</option> 
<?php $query=mysqli_query($con,"select * from category");
while($row=mysqli_fetch_array($query))
{?>

<option value="<?php echo $row['id'];?>"><?php echo $row['categoryName'];?></option>
<?php } ?>
</select>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput">Sub Category</label>
<div class="controls">
<select   name="subcategory"  id="subcategory" class="span8 tip" onChange="getProduct(this.value);" required>
</select>
</div>
</div>


<div class="control-group">
<label class="control-label" for="basicinput">Product Name</label>
<div class="controls">
<select   name="productName"  id="product" class="span8 tip" required>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Choose quantity type</label>
<div class="controls">
<select   name="quantityType" value="Select here" id="product" class="span8 tip" required>
<option value="null" selected disabled></option>
<option value="gm">gm</option>
<option value="Kg">Kg</option>
<option value="ml">ml</option>
<option value="L">L</option>
<option value="Pc">Pc</option>
</select>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Quantity</label>
<div class="controls">
<input type="text"    name="productQuantity"  placeholder="Enter quantity" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<label class="control-label" for="basicinput">Product Price</label>
<div class="controls">
<input type="text"    name="productPrice"  placeholder="Enter Product Price for above quantity" class="span8 tip" required>
</div>
</div>

<div class="control-group">
<div class="controls">
<button type="submit" name="submit" class="btn">Insert</button>
</div>
</div>
</form>
</div>
</div>





</div><!--/.content-->
</div><!--/.span9-->
</div>
</div><!--/.container-->
</div><!--/.wrapper-->

<?php include('include/footer.php');?>

<script src="scripts/jquery-1.9.1.min.js" type="text/javascript"></script>
<script src="scripts/jquery-ui-1.10.1.custom.min.js" type="text/javascript"></script>
<script src="bootstrap/js/bootstrap.min.js" type="text/javascript"></script>
<script src="scripts/flot/jquery.flot.js" type="text/javascript"></script>
<script src="scripts/datatables/jquery.dataTables.js"></script>
<script>
$(document).ready(function() {
$('.datatable-1').dataTable();
$('.dataTables_paginate').addClass("btn-group datatable-pagination");
$('.dataTables_paginate > a').wrapInner('<span />');
$('.dataTables_paginate > a:first-child').append('<i class="icon-chevron-left shaded"></i>');
$('.dataTables_paginate > a:last-child').append('<i class="icon-chevron-right shaded"></i>');
} );
</script>
</body>
<?php } ?>