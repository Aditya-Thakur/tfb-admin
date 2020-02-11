
<?php
session_start();
include('include/config.php');
if(strlen($_SESSION['alogin'])==0)
	{	
header('location:index.php');
}
else{
date_default_timezone_set('Asia/Kolkata');// change according timezone
$currentTime = date( 'd-m-Y h:i:s A', time () );

// Function to fetch the data from the database
function fetch_data($con)  
{
	$st='Delivered';
	$fetchQuery=mysqli_query($con,"select users.name as username,users.email as useremail,
	users.contactno as usercontact,users.shippingAddress as shippingaddress,
	users.shippingCity as shippingcity,users.shippingState as shippingstate,
	users.shippingPincode as shippingpincode,products.productName as productname, products.id as productId,
	products.shippingCharge as shippingcharge,orderItems.quantity as quantity,
	orders.orderDate as orderdate,products.productPrice as productprice,
	orders.id as id  from orders join users on  
	orders.userId=users.id join orderItems on orderItems.orderId=orders.id
	 join products on products.id=orderItems.productId where  orders.orderStatus='$st'");
	
	$productData=array();

	while($fetchedResponse=mysqli_fetch_array($fetchQuery)){
		array_push($productData,$fetchedResponse);
	}
	
	$output='';
	$count=1; // To be used as table row count
	$totalAmount=0;
	foreach($productData as $row){
		$amount=$row['quantity']*$row['productprice']+$row['shippingcharge'];
		$totalAmount=$totalAmount+$amount;
		$output.='
				<tr>
					<td>'.$count.'</td>
					<td>'.$row['username'].'</td>
					<td>'.$row['useremail']. '/' . $row['usercontact'].'</td>
					<td>'.$row['shippingaddress'].$row['shippingcity'].$row['shippingstate'].$row['shippingpincode']. '</td>

					<td>'.$row['productId'].'</td>
					<td>'.$row['productname'].'</td>
					<td>'.$row['quantity'].'</td>
					<td>'.$amount.'</td>
					<td>'.$row['orderdate'].'</td>										
				</tr>
		';
		$count=$count+1; // Increment the count
	}

	return [$output,$totalAmount];
}
// End of fetch_data function

//Print pdf-
if(isset($_POST["generate_pdf"]))  
 {  
      require_once('TCPDF/tcpdf.php');  
      $obj_pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);  
      $obj_pdf->SetCreator(PDF_CREATOR);  
      $obj_pdf->SetTitle("The Flying Basket- Invoice ");  
      $obj_pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
      $obj_pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
      $obj_pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
      $obj_pdf->SetDefaultMonospacedFont('helvetica');  
      $obj_pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
      $obj_pdf->SetMargins(PDF_MARGIN_LEFT, '10', PDF_MARGIN_RIGHT);  
      $obj_pdf->setPrintHeader(false);  
      $obj_pdf->setPrintFooter(false);  
      $obj_pdf->SetAutoPageBreak(TRUE, 10);  
      $obj_pdf->SetFont('helvetica', '', 11);  
	  $obj_pdf->AddPage();
	  
	  $pdfData=fetch_data($con);
      $content = '';  
      $content .= '  
	  <h2 align="center"><u>The Flying Basket</u></h2>
      <h3 align="center">(Invoice- Delivered Orders)</h3><br /> 
	  
      <table border="1" cellspacing="0" cellpadding="3">  
	  <tr>
		<th>#</th>
		<th> Name</th>
		<th width="50">Email /Contact no</th>
		<th>Shipping Address</th>
		<th>Product ID </th>
		<th>Product </th>
		<th>Qty </th>
		<th>Amount </th>
		<th>Order Date</th>
	  </tr> 
      ';  
      $content .= $pdfData[0];  
	  $content .= '</table>';
	  $content .= '<div><h4> Total Amount= Rs. '.$pdfData[1].'/- </h4></div>';  
	  $obj_pdf->writeHTML($content);  
	  
	  // Clean any content of the output buffer
	  ob_end_clean();

	  $obj_pdf->Output('file.pdf', 'I'); 
 }




?>
<!DOCTYPE html>
<html lang="en">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Admin| Pending Orders</title>
	<link type="text/css" href="bootstrap/css/bootstrap.min.css" rel="stylesheet">
	<link type="text/css" href="bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
	<link type="text/css" href="css/theme.css" rel="stylesheet">
	<link type="text/css" href="images/icons/css/font-awesome.css" rel="stylesheet">
	<link type="text/css" href='http://fonts.googleapis.com/css?family=Open+Sans:400italic,600italic,400,600' rel='stylesheet'>
	<script language="javascript" type="text/javascript">
var popUpWin=0;
function popUpWindow(URLStr, left, top, width, height)
{
 if(popUpWin)
{
if(!popUpWin.closed) popUpWin.close();
}
popUpWin = open(URLStr,'popUpWin', 'toolbar=no,location=no,directories=no,status=no,menubar=no,scrollbars=yes,resizable=no,copyhistory=yes,width='+600+',height='+600+',left='+left+', top='+top+',screenX='+left+',screenY='+top+'');
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
								<h3>Pending Orders</h3>
							</div>
							<div class="module-body table">
	<?php if(isset($_GET['del']))
{?>
									<div class="alert alert-error">
										<button type="button" class="close" data-dismiss="alert">×</button>
									<strong>Oh snap!</strong> 	<?php echo htmlentities($_SESSION['delmsg']);?><?php echo htmlentities($_SESSION['delmsg']="");?>
									</div>
<?php } ?>

									<br />

							
								<table cellpadding="0" cellspacing="0" border="0" class="datatable-1 table table-bordered table-striped	 display" >
									<thead>
										<tr>
											<th>#</th>
											<th> Name</th>
											<th width="50">Email /Contact no</th>
											<th>Shipping Address</th>
											<th>Product </th>
											<th>Qty </th>
											<th>Amount </th>
											<th>Order Date</th>
											<th>Action</th>
											
										
										</tr>
									</thead>
								
<tbody>
<?php 
$st='Delivered';
$query=mysqli_query($con,"select users.name as username,users.email as useremail,
users.contactno as usercontact,users.shippingAddress as shippingaddress,
users.shippingCity as shippingcity,users.shippingState as shippingstate,
users.shippingPincode as shippingpincode,products.productName as productname, products.id as productId,
products.shippingCharge as shippingcharge,orderItems.quantity as quantity,
orders.orderDate as orderdate,products.productPrice as productprice,
orders.id as id  from orders join users on  
orders.userId=users.id join orderItems on orderItems.orderId=orders.id
 join products on products.id=orderItems.productId where  orders.orderStatus='$st'");
$cnt=1;
$total = 0;
while($row=mysqli_fetch_array($query))
{
	$total=$total+$row['quantity']*$row['productprice']+$row['shippingcharge'];
?>										
										<tr>
											<td><?php echo htmlentities($cnt);?></td>
											<td><?php echo htmlentities($row['username']);?></td>
											<td><?php echo htmlentities($row['useremail']);?>/<?php echo htmlentities($row['usercontact']);?></td>
										
											<td><?php echo htmlentities($row['shippingaddress'].",".$row['shippingcity'].",".$row['shippingstate']."-".$row['shippingpincode']);?></td>
											<td><?php echo htmlentities($row['productId']);?></td>
											<td><?php echo htmlentities($row['productname']);?></td>
											<td><?php echo htmlentities($row['quantity']);?></td>
											<td><?php echo htmlentities($row['quantity']*$row['productprice']+$row['shippingcharge']);?></td>
											<td><?php echo htmlentities($row['orderdate']);?></td>
											<td>    <a href="updateorder.php?oid=<?php echo htmlentities($row['id']);?>" title="Update order" target="_blank"><i class="icon-edit"></i></a>
											</td>
											</tr>

										<?php $cnt=$cnt+1; } ?>
										</tbody>
										<div style="padding-left: 16px;">
											<h2> Total Amount= Rs. <?php echo htmlentities($total);?>/-</h2>
											<form method="post">  
												<input type="submit" name="generate_pdf" class="btn btn-success" value="Generate Invoice" />  
											</form> 
											<br/>
											<br/>
										</div>
								</table>
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