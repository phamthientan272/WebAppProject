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
        sendEmail($name, $email, $service, $timeslot, $date);

        $msg = "<div class='alert alert-success'>Booking Successfull</div>";
        $bookings[] = $timeslot;
        $stmt->close();
        $mysqli->close();
    }
}

function sanitize_my_email($field)
{
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
    Dear " . $recipient . ",
    We have received your booking request for service " . $service . ".
    Looking forward to serving you on " . $date . " at " . $timeslot . ".
    If you wish to cancel, please go to this link https://www.codexworld.com/send-beautiful-html-email-using-php/
    Health@Mental
    ";

    // More headers
    $headers = 'From: <f32ee@localhost>' . "\r\n";
    mail($to_email, $subject, $mail_body, $headers);
}

function displayBookingInfo()
{
    $date = date('d/m/Y', strtotime($_SESSION['selectedDate']));
    $bookingInfo = "<p>Service: " . $_SESSION['service'] . "</p>";
    $bookingInfo .= "<p>Date: " . $date . "</p>";
    $bookingInfo .= "<p>Timeslot: " . $_SESSION['timeslot'] . "</p>";

    return $bookingInfo;
}


function displayServiceImage($service)
{
    $imageName = "";
    $serviceName = "";
    if ($service == "Therapy") {
        $imageName = "coffee";
        $serviceName = "Therapy & Counseling";
    } elseif ($service == "Massage") {
        $imageName = "massage";
        $serviceName = "Massage";
    } elseif ($service == "Yoga") {
        $imageName = "yoga";
        $serviceName = "Yoga";
    }

    $src  = "assets/" . $imageName . ".jpg";

    $serviceImage = "
    <div class='service_box'>
    <div class='servicebox1'> <img src='" . $src . "' alt='" . $imageName . "'>
    <div class='centered_service'>" . $serviceName . "</div></div></div>";
    return $serviceImage;
}

?>


<html lang="en">

<head>
    <title>Booking Confirmation</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylesheet.css">
    <script type="text/javascript" src="validator.js"></script>
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

        <a href="booking_calendar.php">Back</a>
        <div class="row">
            <div class="side-col">
                <?php echo displayServiceImage($_SESSION['service']); ?>
            </div>

            <div class="confirmation-group main-col">

                <div class="patient-info">
                    <?php echo displayBookingInfo() ?>
                    <form id="form" method="post" onsubmit="return validateForm()" >
                        <label for="name">*Name:
                            <span role="alert" id="nameError" class="error" aria-hidden="true">
                                Please enter a valid name
                            </span>
                            <input type="text" name="name" id="name" required placeholder="Enter your name here">
                        </label>
                        <label for="email">*Email:
                            <span role="alert" id="emailError" class="error" aria-hidden="true">
                                Please enter a valid email
                            </span>
                            <input type="email" name="email" id="email" required placeholder="Enter your email here">
                        </label>

                        <input type="submit" value="Book Now" id="submit" name="submit" >
                    </form>
                    <script type="text/javascript" src="validator.js"></script>

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
