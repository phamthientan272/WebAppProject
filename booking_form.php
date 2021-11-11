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
    $phone = $_POST['phone'];
    $service = $_SESSION['service'];
    $timeslot = $_SESSION['timeslot'];
    $date = $_SESSION['selectedDate'];
    $email = 'f32ee@localhost';

    $_SESSION['name'] = $_POST['name'];
    $_SESSION['email'] = $_POST['email'];
    $_SESSION['phone'] = $_POST['phone'];


    $result = $mysqli->query("select * from bookings where service = '" . $service . "' AND
    date = '" . $date . "' AND timeslot = '" . $timeslot . "'");
    $num_results = $result->num_rows;
    if ($num_results > 0) {
        $msg = "<div class='error visible'>This time slot is already booked. Please choose another timeslot.</div>";
    } else {
        $id = uniqid();
        $stmt = $mysqli->prepare("INSERT INTO bookings (id, service, date, timeslot, name, email, phone) VALUES (?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param('sssssss', $id, $service, $date, $timeslot, $name, $email, $phone);
        $stmt->execute();
        sendEmail($name, $email, $service, $timeslot, $date, $id);

        header("Location: booking_confirmation.php");
        exit();
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

function sendEmail($recipient, $email, $service, $timeslot, $date, $id)
{
    $date = date('d/m/Y', strtotime($date));
    $to_email = $email;
    $subject = 'Confirmation Email';
    $mail_body =
        "
    Dear " . $recipient . ",

    We have received your booking request for service " . $service . ".
    Looking forward to serving you on " . $date . " at " . $timeslot . ".

    If you wish to cancel, please go to this link http://192.168.56.2/f32ee/WebAppProject/cancel.php?id=".$id.".

    Best Regards,
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
    <div class='service_box_booking1'>
    <div class='servicebox_booking'> <img src='" . $src . "' alt='" . $imageName . "'>
    <div class='centered_service'>" . $serviceName . "</div></div></div>";
    return $serviceImage;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Booking Form</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="stylesheet.css">
    <script type="text/javascript" src="validator.js"></script>
</head>

<body>
    <div id="wrapper2">
        <header>
            <?php include 'header.php' ?>
        </header>

        <div class="box_img">
            <img src="assets/booking.jpg">
            <div class="centered">Booking Summary</div>
        </div>

        <a href="booking_calendar.php"><button class="month">Back</button></a>
        <div class="row2">
            <div class="side-col2">
                <?php echo displayServiceImage($_SESSION['service']); ?>
            </div>

            <div class="confirmation-group main-col">

                <div class="patient-info">
                    <?php echo displayBookingInfo() ?>
                    <form id="form" method="post" onsubmit="return validateForm()">
                        <label for="name">*Name:
                            <span role="alert" id="nameError" class="error" aria-hidden="true">
                                Please enter a valid name.
                            </span>
                            <input type="text" name="name" id="name" required placeholder="Enter your name here">
                        </label>
                        <label for="email">*Email:
                            <span role="alert" id="emailError" class="error" aria-hidden="true">
                                Please enter a valid email.
                            </span>
                            <input type="email" name="email" id="email" required placeholder="Enter your email here">
                        </label>
                        <label for="phone">*Phone Number:
                            <span role="alert" id="phoneError" class="error" aria-hidden="true">
                                Please enter a valid 8-digit phone number.
                            </span>
                            <input type="number" name="phone" id="phone" required onkeyup="if(this.value<0){this.value= this.value * -1}">
                        </label>

                        <input class="month2" type="submit" value="Book Now" id="submit" name="submit">
                    </form>
                    <script type="text/javascript" src="validator.js"></script>

                </div>

                <div>
                    <?php echo (isset($msg)) ? $msg : ""; ?>
                </div>

            </div>
        </div>
        <footer>
            <?php include 'footer.php' ?>
        </footer>
    </div>
</body>
</html>
