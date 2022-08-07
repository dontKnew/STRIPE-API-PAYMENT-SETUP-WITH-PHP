<?php
require '../vendor/autoload.php';

class DBClass{

    public function __construct()
    {
        $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..');
        $dotenv->load();
        
        if(!file_exists(__DIR__ . '/../../.env')) {
            echo "Environment variable file  is missing";
            exit;
        }else if(!$_ENV['STRIPE_SECRET_KEY']) {
            echo "STRIPE Secret key is missing from .env file";   
            exit;
        }
    }
    
    public function connect(){
        $conn = mysqli_connect($_ENV["DB_HOST"], $_ENV["DB_USER"], $_ENV["DB_PASSWORD"] , $_ENV["DB_NAME"]) or die("Connection failed: " . mysqli_connect_error());
        if (mysqli_connect_errno()) {
            return false;
        }
        return $conn;
    }
}

?>