<?php
if (!isset($_SESSION))  session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['email'])) {
    header("Location: booking_service.php");
    exit();
}

?>

<html lang="en">

<head>
    <title>Cancellation Confirmation</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <div id="wrapper">
        <header>
            <?php include 'header.php' ?>
        </header>

        <div class="box_img">
            <img src="assets/test.jpg" alt="test">
            <div class="centered">Confirmation</div>
        </div>

        <div>
            <p>Dear <?php echo $_SESSION['name'] ?>,</p>
            <p>You have canceled your appointment for <?php echo $_SESSION['service'] ?> at <?php echo $_SESSION['timeslot'] . " on " . date('d/m/Y', strtotime($_SESSION['date'])) ?>, </p>
            <p>Health@Mental</p>

        </div>

        <footer>
            <?php include 'footer.php' ?>
        </footer>
    </div>
</body>

</html>
