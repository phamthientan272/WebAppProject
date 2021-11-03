<?php
if (!isset($_SESSION))  session_start();
if (!isset($_SESSION['name']) || !isset($_SESSION['email'])) {
    header("Location: booking_service.php");
    exit();
}

?>

<html lang="en">

<head>
    <title>Booking Confirmation</title>
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
            <p>We have received your booking apointment and sent a confimation email to address <?php echo $_SESSION['email'] ?>.</p>
            <p>Looking forward to seeing you.</p>
            <p>Health@Mental</p>

        </div>

        <footer>
            <?php include 'footer.php' ?>
        </footer>
    </div>
</body>

</html>
