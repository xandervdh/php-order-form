<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css" type="text/css"
          rel="stylesheet"/>
    <title>Order food & drinks</title>
</head>
<body>
<div class="container">
    <h1>Order food in restaurant "the Personal Ham Processors"</h1>
    <nav>
        <ul class="nav">
            <li class="nav-item">
                <a class="nav-link active" href="?food=1">Order food</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?food=0">Order drinks</a>
            </li>
        </ul>
    </nav>
    <?php
    echo $succes; //show the succes message
    ?>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <div class="form-row">
            <div class="form-group col-md-6">
                <label for="email">E-mail:</label>
                <input type="text" id="email" name="email" class="form-control"
                       value="<?php echo $email ?>" <?php echo $emailClass ?>> <!-- autofill the value and add a error styling -->
                <span class="text-danger"><?php echo $emailError; ?></span> <!-- error placement -->
            </div>
            <div></div>
        </div>

        <fieldset>
            <legend>Address</legend>

            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="street">Street:</label>
                    <input type="text" name="street" id="street" class="form-control"
                           value="<?php echo $street ?>" <?php echo $streetClass ?>>
                    <span class="text-danger"><?php echo $streetError; ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label for="streetnumber">Street number:</label>
                    <input type="text" id="streetnumber" name="streetnumber" class="form-control"
                           value="<?php echo $streetNumber ?>" <?php echo $streetNumberClass ?>>
                    <span class="text-danger"><?php echo $streetNumberError; ?></span>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="city">City:</label>
                    <input type="text" id="city" name="city" class="form-control"
                           value="<?php echo $city ?>" <?php echo $cityClass ?>>
                    <span class="text-danger"><?php echo $cityError; ?></span>
                </div>
                <div class="form-group col-md-6">
                    <label for="zipcode">Zipcode</label>
                    <input type="text" id="zipcode" name="zipcode" class="form-control"
                           value="<?php echo $zipCode ?>" <?php echo $zipCodeClass ?>>
                    <span class="text-danger"><?php echo $zipCodeError; ?></span>
                </div>
            </div>
        </fieldset>

        <fieldset>
            <legend>Products</legend>
            <?php for ($i = 0; $i < count($products->name); $i++){ ?>
                <label>
                    <input type="text" value="0" name="products[<?php echo $i ?>]"/> <?php echo $products->name[$i] ?>
                    -
                    &euro; <?php echo number_format($products->price[$i], 2) ?></label><br/>
            <?php } ?>
        </fieldset>

        <label>
            <input type="checkbox" name="express_delivery" value="5"/>
            Express delivery (+ 5 EUR)
        </label>

        <input type="submit" class="btn btn-primary" name="add" value="Add to cart">
        <input type="submit" class="btn btn-primary" name="order" value="Order!">
    </form>
    <?php echo $cart; ?>

    <div>Total: <strong>€ <?php echo $total; ?> </strong></div><br>
    <footer>You already ordered <strong>&euro; <?php echo $totalValue; ?></strong> in food and drinks.</footer>
</div>

<style>
    footer {
        text-align: center;
    }
</style>
</body>
</html>
