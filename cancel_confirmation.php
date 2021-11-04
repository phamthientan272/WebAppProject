<?php
if (!isset($_SESSION))  session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['email'])) {
    header("Location: booking_service.php");
    exit();
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Cancellation Confirmation</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
    <div id="wrapper2">
        <header>
            <?php include 'header.php' ?>
        </header>

        <div class="box_img">
            <img src="assets/booking.jpg" alt="test">
            <div class="centered">Confirmation</div>
        </div>

        <div class="confirm">
            <p>Dear <?php echo $_SESSION['name'] ?>,</p>
            <p>You have cancelled your appointment for <?php echo $_SESSION['service'] ?> at <?php echo $_SESSION['timeslot'] . " on " . date('d/m/Y', strtotime($_SESSION['date'])) ?>, </p>
            <p>Health@Mental</p>
        <a href="index.php"><button class="month2">Return To Homepage</button></a>
        </div>

        <footer>
            <?php include 'footer.php' ?>
        </footer>
    </div>
</body>

</html>
