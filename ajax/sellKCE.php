<?php
if (isset($_POST['buyUSDwithKCE']) && isset($_POST['user_id']) && isset($_POST['KCEtoUSD_sell'])) {

    $userInputValue = $_POST['buyUSDwithKCE'];
    $userId = $_POST['user_id'];
    $USD_got = $_POST['KCEtoUSD_sell'];



    $conn = new mysqli('localhost', 'root','','praktika');

    //get users money 
    $getMoney_sql = "SELECT money, kce FROM users WHERE id=$userId";
    $result = $conn->query($getMoney_sql);
    $row = $result->fetch_assoc();
    $money = $row['money'];
    $currentKCE = $row['kce'];


    if($currentKCE - $userInputValue >= 0){

        //delete money 
        $sql = "UPDATE users SET money=($money + $USD_got), kce=($currentKCE - $userInputValue) WHERE id=$userId";

        if ($conn->query($sql) === TRUE) {
            echo json_encode(array('message' => 'Congrats! You sold '. $userInputValue .' KCE for'.$USD_got, 'success' => true));
        } else {
            echo json_encode(array('message' => "Updating balance ERROR", 'success' => false));
        }
    }
    else{
        echo json_encode(array('message' => "Insufficient balance", 'success' => false));
    }


    $conn->close();

}else{
    echo json_encode(array('message' => "Wrong input", 'success' => false));
}