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

    // echo $stripe->message[0]['cmsg'];
    if($stripe->addCustomerToStripe($data)){
        $data =[
            'automatic_payment_methods' => ['enabled' => true],
            'customer'=>$stripe->customerResponse->id,
            'amount'   => $totalAmount,
            'currency' => $currency,
            'description' => $itemName,
            'metadata' => array(
                'order_id' => $orderNumber
            )
        ];
        if($stripe->addPaymentToStripe($data)){
            // echo $stripe->message[1]['pmsg'];  
            $paymentResponse = $stripe->paymentResponse->jsonSerialize(); 
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
                "paid_amount"=>$paymentResponse['amount'], 
                "paid_amount_currency"=>$paymentResponse['currency'], 
                "txn_id"=>"", 
                "payment_status"=>$paymentResponse['status'], 
                "created"=>date("Y-m-d H:i:s"),
                "modified"=>date("Y-m-d H:i:s")
            ];
                echo "<pre>";
                print_r($paymentResponse);
                echo "</pre>";
            // if($stripe->saveData("transaction",$data)){
            //     echo $stripe->message[2]['smsg'];    
            //     echo "<pre>";
            //     print_r($data);
            //     echo "</pre>";
            // }else {
            //     echo $stripe->message[2]['smsg'];    
            // }
        }else {
            echo $stripe->message[1]['pmsg'];

        }
    }else {
        echo $stripe->message[0]['cmsg'];
        exit;
    }
}else {
    echo "No token found";
}

?>