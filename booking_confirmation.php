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
    <title>Booking Confirmation</title>
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
            <p>We have received your booking appointment and sent a confirmation email to address: <?php echo $_SESSION['email'] ?>.</p>
            <p>Looking forward to seeing you.</p>
            <p>Health@Mental</p>
        </div>

        <footer>
            <?php include 'footer.php' ?>
        </footer>
    </div>
</body>

</html>
