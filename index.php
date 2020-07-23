<?php
//this line makes PHP behave in a more strict way
declare(strict_types=1);

//we are going to use session variables so we need to enable sessions
session_start();

function whatIsHappening()
{
    echo '<h2>$_GET</h2>';
    var_dump($_GET);
    echo '<h2>$_POST</h2>';
    var_dump($_POST);
    echo '<h2>$_COOKIE</h2>';
    var_dump($_COOKIE);
    echo '<h2>$_SESSION</h2>';
    var_dump($_SESSION);
}
//your products with their price.

require 'form-view.php';


function checkProducts() {
    $products0 = [
        ['name' => 'Club Ham', 'price' => 3.20],
        ['name' => 'Club Cheese', 'price' => 3],
        ['name' => 'Club Cheese & Ham', 'price' => 4],
        ['name' => 'Club Chicken', 'price' => 4],
        ['name' => 'Club Salmon', 'price' => 5]
    ];

    $products1 = [
        ['name' => 'Cola', 'price' => 2],
        ['name' => 'Fanta', 'price' => 2],
        ['name' => 'Sprite', 'price' => 2],
        ['name' => 'Ice-tea', 'price' => 3],
    ];

    $products = $products0;
    $type = $_GET["type"];
    if ($type == 0) {
        $products = $products0;
    } else if($type == 1) {
        $products = $products1;
    }
    return $products;
}

function calculatePrice($array) {
    if(isset($_COOKIE['total_spent'])) {
        $totalSpent = $_COOKIE['total_spent'];
    } else {
        $totalSpent = 0;
    }

    foreach($array as $key => $product) {
        if($_POST['products'][$key] == '1') {
            $totalSpent += $product['price'];
        }
    }
    if($_POST['express_delivery'] == '5') {
        $totalSpent += 5;
    }
    $_COOKIE['total_spent'] = $totalSpent; // only necessary if you want to use cookie on the same page
    setcookie('total_spent',(string)$totalSpent, time() + 3600); // won't work if cookie already exists
    return $_COOKIE['total_spent'];
}

function calculateDelivery(){
    date_default_timezone_set('Europe/Brussels');
    $currentTime = date('H:i:s GMT+1');

    if($_POST['express_delivery'] == '5') {
        $currentTime = date('H:i', strtotime($currentTime. ' + 45 minutes'));

    } else {
        $currentTime = date('H:i', strtotime($currentTime. ' + 2 hours'));
    }
    $_SESSION['delivery_time'] = $currentTime;
    echo 'Your delivery will arrive around ' . $currentTime . '.';
}

function sendEmail() {
    $to = $_SESSION['email'] . ", vinnie@outpost.be";
    $orderDate = date('D jS F, H:i');
    $subject = 'Order confirmation - ' . $orderDate;
    $totalPrice = $_COOKIE['total_spent'];
    $deliveryTime = $_SESSION['delivery_time'];
    $message = "Dear customer, " . "\r\n\n" .
        "Through this email we would like to confirm your order from " .
        $orderDate . " of a total price " . $totalPrice . " euros." . "\r\n\n" .
        "It is estimated to arrive at " .
        $deliveryTime . "." . "\r\n\n" . "Kind regards," .
        "\n" . "The Personal Ham Processors";
     mail($to, $subject, $message);

}

function postFormvariables() {
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $_SESSION['error-array'] = [];

        $email = $_POST['email'];
        $_SESSION['email'] = $_POST['email'];
        if (empty($email)) {
            $emailErr = "An email adress is required!";
            array_push($_SESSION['error-array'], $emailErr);
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
            $emailErr = "Invalid email format";
            array_push($_SESSION['error-array'], $emailErr);
        }

        $street = $_POST['street'];
        $_SESSION['street'] = $_POST['street'];
        if (empty($street)) {
            $streetErr = "A street name is required!";
            array_push($_SESSION['error-array'], $streetErr);
        }
        if (!empty($street) && !preg_match("/^[a-zA-Z ]*$/", $street)) {
            $streetErr = "Street name: only letters and white space allowed";
            $_SESSION['street-error'] = $streetErr;
            array_push($_SESSION['error-array'], $streetErr);
        }


        $streetNumber = $_POST['streetnumber'];
        $_SESSION['streetnumber'] = $_POST['streetnumber'];
        if (empty($streetNumber)) {
            $streetnumberErr = "A street number is required!";
            array_push($_SESSION['error-array'], $streetnumberErr);
        }
        if (!empty($streetNumber && !preg_match("/^[0-9]*$/", $streetNumber))) {
            $streetnumberErr = "Street number: only numbers allowed";
            array_push($_SESSION['error-array'], $streetnumberErr);
        }

        $city = $_POST['city'];
        $_SESSION['city'] = $_POST['city'];
        if (empty($city)) {
            $cityErr = "A city name is required!";
            array_push($_SESSION['error-array'], $cityErr);
        }
        if (!empty($city) && !preg_match("/^[a-zA-Z ]*$/", $city)) {
            $cityErr = "City name: only letters and white space allowed";
            array_push($_SESSION['error-array'], $cityErr);
        }

        $zip = $_POST['zipcode'];
        $_SESSION['zipcode'] = $_POST['zipcode'];
        if (empty($zip)) {
            $zipErr = "A zip code is required!";
            array_push($_SESSION['error-array'], $zipErr);
        }
        if (!empty($zip) && !preg_match("/^[0-9]*$/", $zip)) {
            $zipErr = "Zip code: only numbers are allowed";
            array_push($_SESSION['error-array'], $zipErr);
        }

        $_SESSION['message'] = '';
        if (count($_SESSION['error-array']) > 0) {
            $_SESSION['message'] .= 'It seems you have made a few mistakes in your form. Please check below for more info:' . '<br/>';
            foreach ($_SESSION['error-array'] as $error)
            {
                $_SESSION['message'] .= $error . '<br/>';
                echo $_SESSION['message'];
                session_unset();
            }
        } else {
            echo "All information correctly put in, thank you! We will contact you further via email.";
            calculateDelivery();
            calculatePrice(checkProducts());
            sendEmail();

        }
    }
}




