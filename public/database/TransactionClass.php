<?php

class TransactionClass
{

    public $customerResponse;
    public $paymentResponse;
    public $chargesResponse;
    public $lastSaveId;
    public $message = array();
    public $conn;

    public function __construct()
    {
        require_once 'DBClass.php';
        $db = new DBClass();
        $this->conn = $db->connect();
        $this->message = array();
    }

    public function setStripeAPI()
    {
        $stripe = new \Stripe\StripeClient([
            'api_key' => $_ENV['STRIPE_SECRET_KEY'],
            'stripe_version' => '2020-08-27',
        ]);
        return $stripe;
    }

    public function addCustomerToStripe($data)
    {

        $stripe = $this->setStripeAPI();
        try {
            $this->customerResponse = $stripe->customers->create($data);
            array_push($this->message, array("msg" => "Customer Added to Stripe Website succeed!"));
            return true;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            http_response_code(400);
            error_log($e->getError()->message);
            array_push($this->message, array("msg" => $e->getError()->message));
            return false;
        } catch (Exception $e) {
            error_log($e);
            array_push($this->message, array("msg" => $e));
            return false;
        }
    }

    public function addPaymentToStripe($data)
    {
        $stripe = $this->setStripeAPI();
        try {
            $this->paymentResponse = $stripe->paymentIntents->create($data);
            array_push($this->message, array("msg" => "Payment Added to Stripe Website succeed!"));
            return true;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            http_response_code(400);
            error_log($e->getError()->message);
            array_push($this->message, array("msg" => $e->getError()->message));
            return false;
        } catch (Exception $e) {
            error_log($e);
            array_push($this->message, array("msg" => $e));
            return false;
        }
    }

    public function addChargeToStripe($data)
    {
        $stripe = $this->setStripeAPI();
        try {
            $this->chargeResponse = $stripe->charges->create($data);
            array_push($this->message, array("msg" => "Charges Added to Stripe Website succeed!"));
            return true;
        } catch (\Stripe\Exception\ApiErrorException $e) {
            http_response_code(400);
            error_log($e->getError()->message);
            array_push($this->message, array("msg" => $e->getError()->message));
            return false;
        } catch (Exception $e) {
            error_log($e);
            array_push($this->message, array("msg" => $e));
            return false;
        }
    }

    public function saveData($table, $params=array())
    {
        $table_key = implode(' , ', array_keys($params));
        $table_value = implode("', '", $params);
        try {
            $sql = "INSERT INTO $table ($table_key) VALUES ('$table_value')";
            mysqli_query($this->conn, $sql);
            $this->lastSaveId = mysqli_insert_id($this->conn);
            array_push($this->message, array("msg" => "data has been saved"));
            return true;

        } catch (Exception $e) {
            array_push($this->message, array("msg" => $e));
            return false;

        }
    }
}
