<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home</title>

    <!-- Bootstrap Link-->
    <!-- <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous"> -->
    <!-- Style sheet Link-->
    <!-- <link rel="stylesheet" href="style.css"> -->
</head>

<body>
<?php include("header.php"); ?>

    <div>
        <h2>Welcome to the IT College</h2>
        <p>Hello, <strong><?= htmlspecialchars($userFullName) ?></strong><br>You are successfully logged in!</p>
        <h3>ABOUT US</h3>
        <p>This is the IT college  room booking system </p>
         <p> you can explore our rooms in the ROOMS page </p>
        
    </div>

    <?php include("footer.php"); ?>
</body>

</html>






