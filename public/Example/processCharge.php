<?php
require_once("./database/TransactionClass.php");
if(!empty($_POST['stripeToken'])){
    $stripeToken  = trim($_POST['stripeToken']);
    $customerName = trim($_POST['customerName']);
    $customerEmail = trim($_POST['emailAddress']);
	
	$customerAddress = trim($_POST['customerAddress']);
	$customerCity = trim($_POST['customerCity']);
	$customerZipcode = trim($_POST['customerZipcode']);
	$customerState = trim($_POST['customerState']);
	$customerCountry = trim($_POST['customerCountry']);

    $cardNumber = trim($_POST['cardNumber']);
    $cardCVC = trim($_POST['cardCVC']);
    $cardExpMonth = trim($_POST['cardExpMonth']);
    $cardExpYear = trim($_POST['cardExpYear']);

    $stripe = new TransactionClass();

    $data  = [
        'name' => $customerName,
        'description' => 'test description',
        'email' => $customerEmail,
        'source'  => $stripeToken,
        "address" => ["city" => $customerCity, "country" => $customerCountry, "line1" => $customerAddress, "line2" => "", "postal_code" => $customerZipcode, "state" => $customerState]
    ];

    // item details for which payment made
	$itemName = $_POST['item_details'];
	$itemNumber = $_POST['item_number'];
	$itemPrice = $_POST['price'];
	$totalAmount = $_POST['total_amount'];
	$currency = $_POST['currency_code'];
	$orderNumber = $_POST['order_number'];   

    // echo $stripe->message[0]['msg'];
    if($stripe->addCustomerToStripe($data)){
        $data =[
            'customer'=>$stripe->customerResponse->id,
            'amount'   => $totalAmount,
            'currency' => $currency,
            'description' => $itemName,
            'metadata' => array(
                'order_id' => $orderNumber
            )
        ];
        if($stripe->addChargeToStripe($data)){
            // echo $stripe->message[1]['msg'];  
            $chargesResponse = $stripe->chargesResponse->jsonSerialize(); 
            $data = [
                "cust_name"=>$customerName, 
                "cust_email"=>$customerEmail,
                "card_number"=>$cardNumber, 
                "card_cvc"=>$cardCVC,
                "card_exp_month"=>$cardExpMonth, 
                "card_exp_year"=>$cardExpYear, 
                "item_name"=>$itemName,
                "item_number"=>$itemNumber,
                "item_price"=>$itemPrice,
                "item_price_currency"=>$currency, 
                "paid_amount"=>$chargesResponse['amount'], 
                "paid_amount_currency"=>$chargesResponse['currency'], 
                "txn_id"=>"", 
                "payment_status"=>$chargesResponse['status'], 
                "created"=>date("Y-m-d H:i:s"),
                "modified"=>date("Y-m-d H:i:s")
            ];
                echo "<pre>";
                print_r($chargesResponse);
                echo "</pre>";
            if($stripe->saveData("transaction",$data)){
                echo $stripe->message[2]['msg'];     // probaly will work on live demo

            }else {
                echo $stripe->message[2]['msg'];    // You cannot accept payments using this API as it is no longer supported in India. Please refer to https://stripe.com/docs/payments for accepting payments.
            }
        }else {
            echo $stripe->message[1]['msg'];

        }
    }else {
        echo $stripe->message[0]['msg'];
        exit;
    }
}else {
    echo "No token found";
}

?>