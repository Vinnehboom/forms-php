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
$products = [
    ['name' => 'Club Ham', 'price' => 3.20],
    ['name' => 'Club Cheese', 'price' => 3],
    ['name' => 'Club Cheese & Ham', 'price' => 4],
    ['name' => 'Club Chicken', 'price' => 4],
    ['name' => 'Club Salmon', 'price' => 5]
];

$products = [
    ['name' => 'Cola', 'price' => 2],
    ['name' => 'Fanta', 'price' => 2],
    ['name' => 'Sprite', 'price' => 2],
    ['name' => 'Ice-tea', 'price' => 3],
];

$totalValue = 0;

require 'form-view.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $_SESSION['error-array'] = [];

    $email = $_POST['email'];
    if (empty($email)) {
        $emailErr = "An email adress is required!";
        $_SESSION['email-error'] = $emailErr;
        array_push($_SESSION['error-array'], $emailErr);


    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($email)) {
        $emailErr = "Invalid email format";
        $_SESSION['email-error'] = $emailErr;
        array_push($_SESSION['error-array'], $emailErr);

    }

    $street = $_POST['street'];
    if (empty($street)) {
        $streetErr = "A street name is required!";
        $_SESSION['street-error'] = $streetErr;
        array_push($_SESSION['error-array'], $streetErr);

    }
    if (!empty($street) && !preg_match("/^[a-zA-Z ]*$/", $street)) {
        $streetErr = "Street name: only letters and white space allowed";
        $_SESSION['street-error'] = $streetErr;
        array_push($_SESSION['error-array'], $streetErr);
    }


    $streetNumber = $_POST['streetnumber'];
    if (empty($streetNumber)) {
        $streetnumberErr = "A street number is required!";
        $_SESSION['number-error'] = $streetnumberErr;
        array_push($_SESSION['error-array'], $streetnumberErr);

    }
    if (!empty($streetNumber && !preg_match("/^[0-9]*$/", $streetNumber))) {
        $streetnumberErr = "Street number: only numbers allowed";
        $_SESSION['number-error'] = $streetnumberErr;
        array_push($_SESSION['error-array'], $streetnumberErr);
    }

    $city = $_POST['city'];
    if (empty($city)) {
        $cityErr = "A city name is required!";
        $_SESSION['city-error'] = $cityErr;
        array_push($_SESSION['error-array'], $cityErr);

    }
    if (!empty($city) && !preg_match("/^[a-zA-Z ]*$/", $city)) {
        $cityErr = "City name: only letters and white space allowed";
        $_SESSION['city-error'] = $cityErr;
        array_push($_SESSION['error-array'], $cityErr);
    }



    $zip = $_POST['zipcode'];
    if (empty($zip)) {
        $zipErr = "A zip code is required!";
        $_SESSION['zip-error'] = $zipErr;
        array_push($_SESSION['error-array'], $zipErr);

    }
    if (!empty($zip) && !preg_match("/^[0-9]*$/", $zip)) {
        $zipErr = "Zip code: only numbers are allowed";
        $_SESSION['zip-error'] = $zipErr;
        array_push($_SESSION['error-array'], $zipErr);

    }

    $_SESSION['message'] = '';
    if (count($_SESSION['error-array']) > 0) {
        $_SESSION['message'] .= 'It seems you have made a few mistakes in your form. Please check below for more info:' . '<br/>';
        foreach ($_SESSION['error-array'] as $error)
        {
            $_SESSION['message'] .= $error . '<br/>';
        }
    } else {
        $_SESSION['message'] = "All information correctly put in, thank you! We will contact you further via email.";
    }
}

