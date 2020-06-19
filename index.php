<?php 
$connection = mysqli_connect('localhost', 'root','','praktika');
$result = mysqli_query($connection, "SELECT DATE_FORMAT(FROM_UNIXTIME(Date), '%Y/%m/%d') as Date, AVG(Price) AS Price FROM `kaukocoinex` GROUP BY FROM_UNIXTIME(Date, '%Y/%m/%d')");
?>

<?php
// Initialize the session
session_start();

// Check if the user is logged in, if not then redirect him to login page
if(!isset($_SESSION["loggedin"]) || $_SESSION["loggedin"] !== true){
    header("location: login.php");
    exit;
}

$getMoney = mysqli_query($connection, "SELECT money, kce FROM `users` WHERE id = ".$_SESSION["id"]);
$row = mysqli_fetch_array($getMoney);
$money = $row['money'];
$kce = $row['kce'];

$result2 = mysqli_query($connection, "SELECT Price FROM kaukocoinex ORDER BY Date DESC LIMIT 1");
$row2 = mysqli_fetch_array($result2);
$rate = $row2['Price'];

?>



<html>
<head>

<script src="//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.js"></script>
<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/dygraph/2.1.0/dygraph.min.css" />
<link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">

<script src="https://kit.fontawesome.com/adaad8c57d.js" crossorigin="anonymous"></script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js" integrity="sha384-ApNbgh9B+Y1QKtv3Rn7W3mgPxhU9K/ScQsAP7hUibX39j7fakFPskvXusvfa0b4Q" crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>


<style type="text/css"> 
  body{ font: 14px sans-serif; text-align: left; background-color: #101015; }



  .form-group {
    margin-left: 900px;
  }

  #graphdiv3 .dygraph-axis-label { 
    color: white; 
  }

  .dygraph-legend { 
    text-align: right; 
  }

  #graphdiv3 .dygraph-legend { 
    background-color: #101015; 
  }

</style>


<title>KaunoCoinEx</title>

</head>

<body>
<div class="container">
    <div class="text-center text-white">

        <div class="row mt-3">
          <div class="col-9"> </div>
          <div class="col-3 float-right"> 
            <a href="logout.php" class="btn btn-success">Sign Out <i class="fas fa-sign-out-alt"></i></a> 
          </div>
        </div>

        <h3 class="text-white">Hi, <?php echo ucfirst(htmlspecialchars($_SESSION["username"])); ?> <br> Welcome to KaukoCoinEx!</h3>

        <div class="row mt-3">

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4 mx-auto">
              <div class="card">
                <div class="card-body p-0 m-0">
                  <div class="row no-gutters align-items-center">
                    <div class="col">
                      <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">USD in wallet</div>
                      <div class="h5 mb-0 font-weight-bold text-secondary">$<span id="currentUSD"><?php echo $money; ?></span></div>
                    </div>
                    <div class="col-auto mr-2">
                      <i class="fas fa-dollar-sign fa-2x text-dark"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

            <!-- Earnings (Monthly) Card Example -->
            <div class="col-xl-3 col-md-6 mb-4 mx-auto">
              <div class="card">
                <div class="card-body p-0 m-0">
                  <div class="row no-gutters align-items-center">
                    <div class="col">
                      <div class="text-xs font-weight-bold text-success text-uppercase mb-1">KCE in wallet</div>
                      <div class="h5 mb-0 font-weight-bold text-secondary"><span id="currentKCE"><?php echo number_format($kce, 6); ?></span> KCE</div>
                    </div>
                    <div class="col-auto mr-2">
                      <i class="fab fa-bitcoin fa-2x text-dark"></i>
                    </div>
                  </div>
                </div>
              </div>
            </div>

          </div>

      <button type="button" class="btn btn-dark m-2" data-toggle="modal" data-target="#buyModal">
        Buy <span class="badge badge-light"> <?php echo $rate + 34.52; ?> $</span>
      </button>

      <button type="button" class="btn btn-secondary m-2" data-toggle="modal" data-target="#sellModal">
        Sell <span class="badge badge-light"><?php echo $rate - 34.52; ?> $</span>
      </button>

    </div>


      <!-- BUY Modal -->
      <div class="modal fade" id="buyModal" tabindex="-1" role="dialog" aria-labelledby="buyModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="buyModalLabel">Buy KCE</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="buyForm" method="post">
                <h5>You have: <span id="currentUSDmodal"><?php echo $money; ?></span> $ </h5>
                <h5>KCE buy price: <?php echo $rate + 34.52; ?> $ </h5>
          
                For how much you want to buy? 
                <input id="usdInputBuy" name="buyKCEwithUSD" type="text" required><br>
                <input type="hidden" name="user_id" value="<?php echo $_SESSION["id"]; ?>"><br>
                 You would get <span id="KCE_buy">0</span> KCE
                <input type="hidden" id="KCEfromUSD_buy" name="KCEfromUSD_buy" >

            </div>
            <div class="modal-footer">
              <button class="btn btn-success btn-block mt-3" type="submit">Buy</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    

      <!-- SELL Modal -->
      <div class="modal fade" id="sellModal" tabindex="-1" role="dialog" aria-labelledby="sellModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="sellModalLabel">SELL KCE</h5>
              <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                <span aria-hidden="true">&times;</span>
              </button>
            </div>
            <div class="modal-body">
              <form id="sellForm" method="post">
                <h5>You have: <span id="currentKCEmodal"><?php echo $kce; ?></span> KCE </h5>
                <h5>KCE sell price: <?php echo $rate - 34.52; ?> $ </h5>
          
                How much you want to sell? 
                <input id="kceInputSell" name="buyUSDwithKCE" type="text" required><br>
                <input type="hidden" name="user_id" value="<?php echo $_SESSION["id"]; ?>"><br>
                You would get <span id="usd_sell">0</span> $
                <input type="hidden" id="KCEtoUSD_sell" name="KCEtoUSD_sell" >

            </div>
            <div class="modal-footer">
              <button class="btn btn-success btn-block mt-3" type="submit">Sell</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    

      <!-- <form method="post">
      <h5> 
        <input type="date" name="txtStartDate"> <span class="text-white">to</span> <input type="date" name="txtEndDate">
        <button type="button"value="Search Data" class="btn btn-secondary btn-lg m-2">Search Data...</button> 
      </h5>
      </form> -->

  

      <div class="row">
        <div class="col-12 mx-auto">
          <div id="graphdiv3" style="width:100%; height:400px; background-color: #101015; color: white;"></div>
        </div>
      </div>
</div>



<script type="text/javascript">

  const currentUSD = document.getElementById('currentUSD');
  const currentKCE = document.getElementById('currentKCE');
  const currentUSDmodal = document.getElementById('currentUSDmodal');
  const currentKCEmodal = document.getElementById('currentKCEmodal');

  //========For buying
  const usdInputBuy = document.getElementById('usdInputBuy');
  const KCE = document.getElementById('KCEfromUSD_buy'); //hidden
  const goingToBuyKCE = document.getElementById('KCE_buy'); //showing

  let usdSpent = 0;
  let kceBought = 0;

  usdInputBuy.addEventListener('input', function(){
    KCE.value = usdInputBuy.value / <?php echo $rate + 34.52; ?>;
    usdSpent = parseFloat(usdInputBuy.value);
    kceBought = parseFloat(KCE.value);
    goingToBuyKCE.innerHTML = KCE.value;
  });

  //========For selling
  const kceInputSell = document.getElementById('kceInputSell');
  const USD = document.getElementById('KCEtoUSD_sell'); //hidden
  const goingToSellKCE = document.getElementById('usd_sell'); //showing

  let kceSpent = 0;
  let usdBought = 0;

  kceInputSell.addEventListener('input', function(){
    USD.value = kceInputSell.value * <?php echo $rate - 34.52; ?>;
    kceSpent = parseFloat(kceInputSell.value);
    usdBought = parseFloat(USD.value);
    goingToSellKCE.innerHTML = USD.value;
  });






  $(document).ready(function() {

    $('#buyForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "ajax/buyKCE.php",
            type: "post",
            data: $(this).serialize() ,
            success: function (response) {
              const obj = JSON.parse(response);
              if(obj.success){
                currentUSD.innerHTML = (parseFloat(currentUSD.innerHTML) - usdSpent).toFixed(2);
                currentKCE.innerHTML = (parseFloat(currentKCE.innerHTML) + kceBought).toFixed(6);
                currentUSDmodal.innerHTML = currentUSD.innerHTML;
                currentKCEmodal.innerHTML = currentKCE.innerHTML;
                $('#buyModal').modal('toggle');
                alert(obj.message);
              }else{
                alert(obj.message);
              }


            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

    $('#sellForm').submit(function(e) {
        e.preventDefault();
        $.ajax({
            url: "ajax/sellKCE.php",
            type: "post",
            data: $(this).serialize() ,
            success: function (response) {
              const obj = JSON.parse(response);
              if(obj.success){
                currentUSD.innerHTML = (parseFloat(currentUSD.innerHTML) + usdBought).toFixed(2);
                currentKCE.innerHTML = (parseFloat(currentKCE.innerHTML) - kceSpent).toFixed(6);
                currentUSDmodal.innerHTML = currentUSD.innerHTML;
                currentKCEmodal.innerHTML = currentKCE.innerHTML;
                $('#sellModal').modal('toggle');
                alert(obj.message);
              }else{
                alert(obj.message);
              }   
            },
            error: function(jqXHR, textStatus, errorThrown) {
                console.log(textStatus, errorThrown);
            }
        });
    });

  });

  g = new Dygraph(
    document.getElementById("graphdiv3"),
    // For possible data formats, see http://dygraphs.com/data.html
    // The x-values could also be dates, e.g. "2012/03/15"
    <?php
    if(mysqli_num_rows($result)>0) {
        echo "[";
         while ($row = mysqli_fetch_array($result)){ 
                    echo '[new Date("'.date('Y-m-d',strtotime(strtr($row['Date'], '/', '-'))).'"), '.(int)$row['Price']."],";
                } 
        echo "]";
            }?>,
            
    {
        
      // options go here. See http://dygraphs.com/options.html
      //DELETE FROM kaukocoinex WHERE DATE_FORMAT(FROM_UNIXTIME(Date), '%Y/%m/%d') = "2018/06/27"
        legend: 'always',
        labels: [ "Date", "Price"],
        animatedZooms: true,
        title: 'KCE/USD',
        ylabel: 'USD $',
        showRangeSelector: true,
        rangeSelectorPlotFillColor: 'MediumSlateBlue',
        rangeSelectorPlotFillGradientColor: 'rgba(123, 104, 238, 0)',
        colorValue: 0.9,
        fillAlpha: 0.4
    });
  
</script>



</body>
</html>



 
