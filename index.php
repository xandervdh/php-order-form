<?php
//this line makes PHP behave in a more strict way
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

//we are going to use session variables so we need to enable sessions
session_start();

function whatIsHappening() {
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
$food = [
    ['name' => 'Club Ham', 'price' => 3.20],
    ['name' => 'Club Cheese', 'price' => 3],
    ['name' => 'Club Cheese & Ham', 'price' => 4],
    ['name' => 'Club Chicken', 'price' => 4],
    ['name' => 'Club Salmon', 'price' => 5]
];

$drinks = [
    ['name' => 'Cola', 'price' => 2],
    ['name' => 'Fanta', 'price' => 2],
    ['name' => 'Sprite', 'price' => 2],
    ['name' => 'Ice-tea', 'price' => 3],
];
$error = 'style="border-color: red"';
$succes = "";
$email = $street = $streetNumber = $city = $zipCode = "";
$emailError = $streetError = $streetNumberError = $cityError = $zipCodeError = "";
$emailClass = $streetClass = $streetNumberClass = $cityClass = $zipCodeClass = "";

$products = $food;
if (isset($_GET['food'])){
    if ($_GET['food'] == "1"){
        $products = $food;
    } else {
        $products = $drinks;
    }
}

if (!empty($_SESSION['street'])){
    $street = $_SESSION['street'];
}
if (!empty($_SESSION['streetNumber'])){
    $streetNumber = $_SESSION['streetNumber'];
}
if (!empty($_SESSION['city'])){
    $city = $_SESSION['city'];
}
if (!empty($_SESSION['zipCode'])){
    $zipCode = $_SESSION['zipCode'];
}

if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (empty($_POST["email"])) {
        $email = "";
    } else {
        $email = check_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $emailClass = $error;
            $emailError = "* Invalid email format";
        }
    }
    if (empty($_POST["street"])) {
        $streetClass = $error;
        $streetError = "* Street is required";
    } else {
        $street = check_input($_POST["street"]);
        if (!preg_match("/^[a-zA-Z-' ]*$/",$street)) {
            $streetClass = $error;
            $streetError = "* Only letters and white space allowed";
        } else {$_SESSION['street'] = $street;}
    }
    if (empty($_POST["streetnumber"])) {
        $streetNumberClass = $error;
        $streetNumberError = "* Streetnumber is required";
    } else {
        $streetNumber = check_input($_POST["streetnumber"]);
        if (!is_numeric($streetNumber)){
            $streetNumberClass = $error;
            $streetNumberError = "* Only numbers allowed";
        } else {$_SESSION['streetNumber'] = $streetNumber;}
    }
    if (empty($_POST["city"])) {
        $cityClass = $error;
        $cityError = "* City is required";
    } else {
        $city = check_input($_POST["city"]);
        if (!preg_match_all('/^[A-Za-z\\-]{1,}$/i',$city)){
            $cityClass = $error;
            $cityError = '* Only letters and dashes allowed';
        } else {$_SESSION['city'] = $city;}
    }
    if (empty($_POST["zipcode"])) {
        $zipCodeClass = $error;
        $zipCodeError = "* Zipcode is required";
    } else {
        $zipCode = check_input($_POST["zipcode"]);
        if (!is_numeric($zipCode)){
            $zipCodeClass = $error;
            $zipCodeError = "* Only numbers allowed";
        } else {$_SESSION['zipCode'] = $zipCode;}
    }
    if ($emailError == "" && $streetError == "" && $streetNumberError == "" && $cityError == "" && $zipCodeError == ""){
        $succes = '<div class="alert alert-success" role="alert">
            Your order has been send
            </div>';
    }
}

function check_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

$totalValue = 0;

require 'form-view.php';