<?php
$mess = "";
$num = "";
    if(isset($_POST['cancel'])){
        session_start();
        session_destroy();
        header('location: bank_log_in.php');
    }elseif(isset($_POST['payon'])){
            session_start();
        if($_POST['amount']){
            
            if((int)$_POST['amount'] > (int)$_SESSION['balance']){
                $mess = "Account balance is not sufficient for amount to pay.";
            }else{
                $connection = mysqli_connect("localhost", "root", "", "bankpay");
                    if(mysqli_errno($connection)){
                        echo "Connection Failed: " . mysqli_errno();
                        exit();
                    }else{
                        $query = "SELECT MAX(num) as num FROM nums";
                        if($result = mysqli_query($connection, $query)){
                            $rows =  mysqli_fetch_assoc($result);
                            $num = $rows["num"];
                            
                        }else{
						  $num = 0;
					   }
                    }
                
                $num = (int)$num + 1;
                
                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/RESTs/payment",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => '{"channel_id":"UNDERDOGS","transaction_id":"' . $num . '","source_account":"' . $_SESSION['current-account'] . '","source_currency":"php","biller_id":"SCHOOL","reference1":"000000000A","reference2":"000000000B","reference3":"000000000C","amount":' . $_POST['amount'] . '}',
                  CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    "content-type: application/json",
                    "x-ibm-client-id: 488fe6f9-c20d-4fe0-b544-92377c2db37f",
                    "x-ibm-client-secret: fY4aI3hA4dJ7lQ1rB1hN5uH7mV0iP8jT4iM5vD3hR8tY0jV2gP"
                  ),
                ));
                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                  echo "cURL Error #:" . $err;
                } else {
                    $account = json_decode($response);

                }
                if(mysqli_errno($connection)){
                        echo "Connection Failed: " . mysqli_errno();
                        exit();
                    }else{
                        $query = "INSERT INTO nums VALUES(". $num .")";
                        mysqli_query($connection, $query);
                }
            }
        }else{
            $mess = "Please Enter your amount.";
        }
    }else{
        session_start();

        if(!(isset($_SESSION['current-user-log']))){
            header('location: bank_log_in.php');
        }
    }
?>

<html>
<head>
	<title></title>
</head>
<link rel = "stylesheet" type = "text/css" href = "bank_index.css">
<body>
<div id = "body-container">
	<div id = "content-container" class = "div-major">
        <h1>ONLINE PAYMENT TRANSACTION</h1>
        
		<form action = "bank_index.php" method = "POST">
        <input type="text" placeholder="Amount Pay" name="amount"><br/>
        <br/><?php echo '<span style="color: red;margin-left:500px;">' . $mess . '</span>'?>
        <table width="100%">
            <tr>
                <th colspan="2">TRANSACTION INFORMATION</th>
            </tr>
            <tr>
                <td>Account ID:</td>
                <td><?php echo $_SESSION['current-account'];?></td>
            </tr>
            <tr>
                <td>Account Name:</td>
                <td><?php echo $_SESSION['current-fullname'];?></td>
            </tr>
            <tr>
                <td>Account Balance:</td>
                <td><?php

                $curl = curl_init();

                curl_setopt_array($curl, array(
                  CURLOPT_URL => "https://api.us.apiconnect.ibmcloud.com/ubpapi-dev/sb/api/RESTs/getAccount?account_no=" . $_SESSION['current-account'],
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "GET",
                  CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    "content-type: application/json",
                    "x-ibm-client-id: 488fe6f9-c20d-4fe0-b544-92377c2db37f",
                    "x-ibm-client-secret: fY4aI3hA4dJ7lQ1rB1hN5uH7mV0iP8jT4iM5vD3hR8tY0jV2gP"
                  ),
                ));

                curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
                $response = curl_exec($curl);
                $err = curl_error($curl);

                curl_close($curl);

                if ($err) {
                  echo "cURL Error #:" . $err;
                } else {

                    $account = json_decode($response);

                  echo $account[0]->current_balance;
                    $_SESSION['balance'] = $account[0]->current_balance;
                }
            ?></td>
            </tr>
        </table>
        
        
			<input type = "submit" name = "cancel" class = "btn" value = "Cancel" >
            <input type = "submit" name = "payon" class = "btn" value = "Pay">
		</form>
	</div>
</div>
</body>
</html>