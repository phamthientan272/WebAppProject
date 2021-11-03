<?php
echo uniqid();
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
            <div class="topnav">
                <a class="imgbutton" href="index.html"><img class="logo" src="assets/logo4.png" href="#home"></a>
                <a href="services.html">Services</a>
                <a href="team.html">Team</a>
                <a href="about.html">About</a>
                <a href="booking_service.php"><button class="top_btn"><strong>BOOK NOW</strong></button></a>
            </div>
            <div class="box_img">
                <img src="assets/test.jpg" alt="test">
                <div class="centered">Confirmation</div>
            </div>
        </header>

        <div >
            <p>Dear <?php echo $_SESSION['name'] ?>,</p>
            <p>We have received your booking apointment and sent a confimation email to address <?php echo $_SESSION['email'] ?>.</p>
            <p>Looking forward to seeing you.</p>
            <p>Health@Mental</p>

        </div>

        <footer>
            <div class="botnav">
                <a class="imgbutton" href="index.html"><img class="logo2" src="assets/logo4.png" href="#home"></a>
                <div class="bottombox"><small><i>Copyright &copy; 2021 Health@Mental</i></small></div>
            </div>
        </footer>
    </div>
</body>

</html>
