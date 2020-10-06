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

if (isset($_COOKIE['totalValue'])){
    $totalValue = $_COOKIE['totalValue'];
} else {
    $totalValue = 0;
}

$error = 'style="border-color: red"';
$succes = "";
$express = "";
$email = $street = $streetNumber = $city = $zipCode = "";
$emailError = $streetError = $streetNumberError = $cityError = $zipCodeError = "";
$emailClass = $streetClass = $streetNumberClass = $cityClass = $zipCodeClass = "";

if (!isset($_SESSION['products'])){
    $products = $food;
} else {
    $products = $_SESSION['products'];
}

if (isset($_GET['food'])){
    if ($_GET['food'] == "1"){
        $products = $food;
        $_SESSION['products'] = $food;
    } else {
        $products = $drinks;
        $_SESSION['products'] = $drinks;
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
    date_default_timezone_set('Europe/Brussels');
    if (isset($_POST['express_delivery']) && $_POST['express_delivery'] == "5"){
        $new_time = date("H:i", strtotime('+45 minutes'));
        $delivery = 'Your order will be delivered at ' . $new_time;
        $express = "5";
    } else {
        $new_time = date("H:i", strtotime('+2 hours'));
        $delivery = 'Your order will be delivered at ' . $new_time;
    }

    if ($emailError == "" && $streetError == "" && $streetNumberError == "" && $cityError == "" && $zipCodeError == ""){
        $succes = '<div class="alert alert-success" role="alert">
             Order send. ' . $delivery .
            '</div>';
        $order = array();
        for ($i = 0; $i < count($products); $i++){
            if (isset($_POST['products'][$i])){
                array_push($order, $products[$i]['name']);
                $totalValue += $products[$i]['price'];

                setcookie("totalValue", strval($totalValue), time() + (86400 * 30), "/");
            }
        }
        $message = "Your order is sent to:\n"  . $street . ' ' . $streetNumber . "\n" . $city . ' ' . $zipCode . "\nYou ordered:\n";
        for ($i = 0; $i < count($order); $i++){
            $message .= $order[$i] . "\n";
        }
        $message .= "Total: €" . $totalValue . "\nexpress delivery: ";
        if ($express == "5"){
            $message .= 'Yes €5';
        } else {
            $message .= 'No';
        }
        $message .= "\nYour order will arive at " . $new_time;
        $headers = "";
        $headers .= "Organization: Sender Organization\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
        $headers .= "X-MSMail-Priority: High\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
        //mail($email, 'Your order', $message, $headers);
        //mail(owner@sandwish-shop.be, 'Your order', $message . ' new order', $headers);
    }
}

function check_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

require 'form-view.php';