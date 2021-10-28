<?php


if (!isset($_SESSION))  session_start();

if (!isset($_SESSION['service']) || !isset($_SESSION['timeslot']) || !isset($_SESSION['selectedDate'])) {
    header("Location: booking_calendar.php?");
    exit();
}

$mysqli = new mysqli('localhost', 'f32ee', 'f32ee', 'f32ee');


if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $service = $_SESSION['service'];
    $timeslot = $_SESSION['timeslot'];
    $date = $_SESSION['selectedDate'];

    $email = 'f32ee@localhost';
    sendEmail($name, $email, $service, $timeslot, $date);

    $result = $mysqli->query("select * from bookings where service = '" . $service . "' AND
    date = '" . $date . "' AND timeslot = '" . $timeslot . "'");
    $num_results = $result->num_rows;
    if ($num_results > 0) {
        $msg = "<div class='alert alert-danger'>Already Booked</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Can book</div>";
        $stmt = $mysqli->prepare("INSERT INTO bookings (service, date, timeslot, name, email) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param('sssss', $service, $date, $timeslot, $name, $email);
        $stmt->execute();
        $msg = "<div class='alert alert-success'>Booking Successfull</div>";
        $bookings[] = $timeslot;
        $stmt->close();
        $mysqli->close();
    }
}

function sanitize_my_email($field) {
    $field = filter_var($field, FILTER_SANITIZE_EMAIL);
    if (filter_var($field, FILTER_VALIDATE_EMAIL)) {
        return true;
    } else {
        return false;
    }
}

function sendEmail($recipient, $email, $service, $timeslot, $date)
{
    $date = date('d/m/Y', strtotime($date));
    $to_email = $email;
    $subject = 'Confirmation Email';
    $mail_body =
    "
    Dear ".$recipient.",
    We have received your booking request for service ".$service.".
    Looking forward to serving you on ".$date." at ".$timeslot.".
    If you wish to cancel, please go to this link https://www.codexworld.com/send-beautiful-html-email-using-php/
    Health@Mental
    ";

    // More headers
    $headers = 'From: <f32ee@localhost>' . "\r\n";
    mail($to_email, $subject, $mail_body, $headers);

}

function print_booking_info()
{
    echo $_SESSION['service'];
    echo $_SESSION['timeslot'];
    echo $_SESSION['selectedDate'];
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
          <div class="centered">Booking Summary</div>
        </div>
      </header>

    <div class="container">
        <div class="confirmation-group">
            <a href="booking_calendar.php">Back</a>
            <div class="booking-info">
                <?php
                print_booking_info();
                ?>
            </div>
            <div class="patient-info">

                <form method="post">
                    <label for="name">*Name:</label>
                    <input type="text" name="name" id="name" required placeholder="Enter your name here">

                    <label for="email">*Email:</label>
                    <input type="email" name="email" id="email" required placeholder="Enter your email here">

                    <input type="submit" value="Book Now" id="submit" name="submit" onclick="sendEmailDefault()">
                </form>
            </div>
            <div>
                <?php echo (isset($msg)) ? $msg : ""; ?>
            </div>

        </div>
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
