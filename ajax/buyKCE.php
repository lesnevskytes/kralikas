<?php
if (isset($_POST['buyKCEwithUSD']) && isset($_POST['user_id']) && isset($_POST['KCEfromUSD_buy'])) {

    $userInputValue = $_POST['buyKCEwithUSD'];
    $userId = $_POST['user_id'];
    $KCE_bought = $_POST['KCEfromUSD_buy'];



    $conn = new mysqli('localhost', 'root','','praktika');

    //get users money 
    $getMoney_sql = "SELECT money, kce FROM users WHERE id=$userId";
    $result = $conn->query($getMoney_sql);
    $row = $result->fetch_assoc();
    $money = $row['money'];
    $currentKCE = $row['kce'];

    //delete money 
    $sql = "UPDATE users SET money=($money - $userInputValue), kce=($currentKCE + $KCE_bought) WHERE id=$userId";

    
    if($money - $userInputValue >= 0){
        if ($conn->query($sql) === TRUE) {
            echo json_encode(array('message' => 'Congrats! You bought '. $KCE_bought .' KCE', 'success' => true));
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