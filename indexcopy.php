<?php 
$connection = mysqli_connect('localhost', 'root','','praktika');
$result = mysqli_query($connection, "SELECT DATE_FORMAT(FROM_UNIXTIME(Date), '%Y/%m/%d') as Date, AVG(Price) AS Price, Quantity FROM `kaukocoinex` GROUP BY DATE_FORMAT(FROM_UNIXTIME(Date), '%Y/%m/%d')");

// if($result) {
//     echo "CONNECTED";
// }

if(isset($_POST['search'])) 
{
  $txtStartDate=$_POST['txtStartDate'];
  $txtEndDate=$_POST['txtEndDate'];
  $query=mysqli_query($conn, "Select Price, Quantity From kaukocoinex Where Date Between '$txtStartDate' and '$txtEndDate' Order by Date");
  $count=mysqli_num_rows($query);
}

?>

<?php
// Initialize the session
session_start();
 
// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}
?>





<html>
<head>
<script src="//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.css" />
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.css">



<style type="text/css"> 
 body{ font: 14px sans-serif; text-align: left; }

 .page-header {
  margin-right: 700px;
 }

 .daterange {
   margin-left: 1000px;
 }
 
</style>


<title>KaunoCoinEx</title>

</head>

<body>
       <div class="page-header">
        <h1>Hi, <b><?php echo htmlspecialchars($_SESSION["username"]); ?></b>. Welcome to kaukocoinex.</h1>
        <div class="logoutbtn">
        <a href="logout.php" class="btn btn-success">Sign Out of Your Account</a>
        </div></div>
  

  <div class="daterange">
  <form method="post">
  <h5> <input type="date" name="txtStartDate">to<input type="date" name="txtEndDate"> </h5>
  <input type="submit" name="search" value="Search Data">
  </div>
  <?php
  if($count == "0")
  {
    echo '<h3>no results</h3>';
  }
  else 
  {
    while($row=mysqli_fetch_array($query))
  {
    $result=$row['Price, Quantity'];
    $output='.$result.';
    echo $output;
  }
  }
  
  
  ?>

</form>

<div id="graphdiv3"
  style="width:1200px; height:400px; padding:20px; backround-color: grey; margin-bottom:20px;">
</div>


<script type="text/javascript">

  g = new Dygraph(
    document.getElementById("graphdiv3"),
    // For possible data formats, see http://dygraphs.com/data.html
    // The x-values could also be dates, e.g. "2012/03/15"
    <?php
    if(mysqli_num_rows($result)>0) {
        echo "[";
         while ($row = mysqli_fetch_array($result)){ 
                    echo '[new Date("'.date('Y-m-d',strtotime(strtr($row['Date'], '/', '-'))).'"), '.(int)$row['Price'].", ".(int)$row['Quantity']."],";
                } 
        echo "]";
            }?>,
            
    {
        
      // options go here. See http://dygraphs.com/options.html
      //DELETE FROM kaukocoinex WHERE DATE_FORMAT(FROM_UNIXTIME(Date), '%Y/%m/%d') = "2018/06/27"
        legend: 'always',
        labels: [ "Date", "Price", "Quantity" ],
        animatedZooms: true,
        title: 'BTC/USD',
        ylabel: 'USD $',
    });
  
</script>



</body>
</html>



 
