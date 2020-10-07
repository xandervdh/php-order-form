<?php
//this line makes PHP behave in a more strict way
declare(strict_types=1);

//error handling
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

//we are going to use session variables so we need to enable sessions
session_start();

//function to var dump the global variables
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
class menu{
    public $name = array();
    public $price = array();
}

/*$food = [
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
];*/

//declaring the variables
$products = new menu();
$succes = "";
$express = "";
$total = 0;
$email = $street = $streetNumber = $city = $zipCode = "";
$emailError = $streetError = $streetNumberError = $cityError = $zipCodeError = "";
$emailClass = $streetClass = $streetNumberClass = $cityClass = $zipCodeClass = "";

//error style variable
$error = 'style="border-color: red"';

//check if there is a cookie set
if (isset($_COOKIE['totalValue'])){
    $totalValue = $_COOKIE['totalValue'];
} else {
    $totalValue = 0;
}

//check whitch products where selected
if (!isset($_SESSION['products'])){
    array_push($products->name, 'Club Ham', 'Club Cheese', 'Club Cheese & Ham', 'Club Chicken', 'Club Salmon');
    array_push($products->price, 3.20, 3, 4, 4, 5);
} else {
    $products = $_SESSION['products'];
}

//make $products the chosen products (food or drinks)
if (isset($_GET['food'])){
    if ($_GET['food'] == "1"){
        $products = new menu();
    array_push($products->name, 'Club Ham', 'Club Cheese', 'Club Cheese & Ham', 'Club Chicken', 'Club Salmon');
    array_push($products->price, 3.20, 3, 4, 4, 5);
        $_SESSION['products'] = $products;
    } else {
        $products = new menu();
        array_push($products->name, 'Cola', 'Fanta', 'Sprite', 'Ice-tea');
        array_push($products->price, 2, 2, 2, 3);
        $_SESSION['products'] = $products;
    }
}

//change the variables to the session if the session is set
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

//do things if there is a POST request
if ($_SERVER["REQUEST_METHOD"] == "POST"){
    if (empty($_POST["email"])) { //check if input is empty
        $email = "";
    } else {
        $email = check_input($_POST["email"]);
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) { //check if input has a valid email
            $emailClass = $error; //give error style
            $emailError = "* Invalid email format"; //give error message
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
        } else {$_SESSION['street'] = $street;} //if there are no errors put $street in a session
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

    //do if there are no errors
    if ($emailError == "" && $streetError == "" && $streetNumberError == "" && $cityError == "" && $zipCodeError == ""){
        date_default_timezone_set('Europe/Brussels'); //set timezone
        if (isset($_POST['express_delivery'])){ //if express delivery is checked
            $new_time = date("H:i", strtotime('+45 minutes')); //current time + 45 minutes
            $delivery = 'Your order will be delivered at ' . $new_time;
            $total += 5;
            $express = "5";
        } else {
            $new_time = date("H:i", strtotime('+2 hours')); //current time + 2 hours
            $delivery = 'Your order will be delivered at ' . $new_time;
        }

        //create the succes message
        $succes = '<div class="alert alert-success" role="alert">
             Order send. ' . $delivery .
            '</div>';

        $order = array(); //declare order array
        for ($i = 0; $i < count($products->name); $i++){
            if (isset($_POST['products'][$i])){ //check if the product is checked
                array_push($order, $products->name[$i]); //push checked product to order array
                $total += $products->price[$i]; //add price of checked product to total
            }
        }
        $totalValue += $total; //add the total to the total ever spend
        setcookie("totalValue", strval($totalValue), time() + (86400 * 30), "/"); //set coockie for total value
        //creating the email message
        $message = "Your order is sent to:\nStreet: "  . $street . ' Street number: ' . $streetNumber . "\nCity: " . $city . ' Zipcode: ' . $zipCode . "\nYou ordered:\n";
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
        //creating the email headers
        $headers = "";
        $headers .= "Organization: Sender Organization\r\n";
        $headers .= "MIME-Version: 1.0\r\n";
        $headers .= "Content-type: text/plain; charset=iso-8859-1\r\n";
        $headers .= "X-MSMail-Priority: High\r\n";
        $headers .= "X-Mailer: PHP". phpversion() ."\r\n";
        //mail($email, 'Your order', $message, $headers); //send email to client
        //mail(owner@sandwish-shop.be, 'Your order', $message . ' new order!!', $headers); //send email to shop
    }
}

function check_input($data) {
    $data = trim($data); //remove whitespace from beginning and end of input
    $data = stripslashes($data); //remove slashes
    $data = htmlspecialchars($data); //changes html elements to characters
    return $data;
}

require 'form-view.php';