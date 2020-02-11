<?php
include('include/config.php');
if(!empty($_POST["subcat_id"])) 
{
 $id=intval($_POST['subcat_id']);
$query=mysqli_query($con,"SELECT * FROM products WHERE subCategory=$id");
?>
<option value="">Select Product</option>
<?php
 while($row=mysqli_fetch_array($query))
 {
  ?>
  <option value="<?php echo htmlentities($row['id']); ?>"><?php echo htmlentities($row['productName']); ?></option>
  <?php
 }
}
?>