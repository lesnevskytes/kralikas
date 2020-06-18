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


    if($currentKCE - $userInputValue >= -1){

        //delete money 
        $sql = "UPDATE users SET money=($money + $USD_got), kce=($currentKCE - $userInputValue) WHERE id=$userId";

        if ($conn->query($sql) === TRUE) {
            echo json_encode('Congrats! You sold '. $userInputValue .' KCE for'.$USD_got );
        } else {
            echo json_encode('Error updating record');
        }
    }
    else{
        echo json_encode('Insufficient balance');
    }


    $conn->close();

}else{
    echo json_encode('Wrong input');
}