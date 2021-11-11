<?php

if (!isset($_SESSION))  session_start();
if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit();
}

$mysqli = new mysqli('localhost', 'f32ee', 'f32ee', 'f32ee');

$result = $mysqli->query("select * from bookings where id = '" . $_GET['id'] . "'");
$num_results = $result->num_rows;
if ($num_results > 0) {
    $row = $result->fetch_assoc();
    $_SESSION["service"] = $row["service"];
    $_SESSION["timeslot"] = $row["timeslot"];
    $_SESSION["date"] = $row["date"];
    $_SESSION["name"] = $row["name"];
    $_SESSION["email"] = $row["email"];
    $_SESSION["phone"] = $row["phone"];
}

if (isset($_POST['submit'])) {
    $phone = $_POST['phone'];
    if ($phone == $_SESSION["phone"]) {
        $result = $mysqli->query("delete from bookings where id = '" . $_GET['id'] . "' ");

        $email = 'f32ee@localhost';
        sendEmail($_SESSION["name"], $email, $_SESSION["service"], $_SESSION["timeslot"], $_SESSION["date"]);
        header("Location: cancel_confirmation.php");
        exit();
    } else {
        $msg = "<div class='error visible'>This phone number is not as same as the registered one.</div>";
    }
}

function sendEmail($recipient, $email, $service, $timeslot, $date)
{
    $date = date('d/m/Y', strtotime($date));
    $to_email = $email;
    $subject = 'Cancellation Confirmation';
    $mail_body =
        "
    Dear " . $recipient . ",

    We have cancelled your booking appointment for:\n    ". $service . " at " . $timeslot . " on " . $date . ".

    Best Regards,
    Health@Mental
    ";

    // More headers
    $headers = 'From: <f32ee@localhost>' . "\r\n";
    mail($to_email, $subject, $mail_body, $headers);
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>Booking Cancellation</title>
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
            <img src="assets/booking.jpg" alt="test">
            <div class="centered">Cancellation</div>
        </div>

        <div class="confirm">
            <p>Dear <?php echo $_SESSION['name'] ?>,</p>
            <p>If you would like to cancel your appointment for <?php echo $_SESSION['service'] ?> at <?php echo $_SESSION['timeslot'] . " on " . date('d/m/Y', strtotime($_SESSION['date'])) ?>, </p>
            <p>Please key in your phone number that you have registered with us.</p>

            <form id="form" method="post" onsubmit="return validatePhone()">
                <label for="phone">Phone Number:
                    <span role="alert" id="phoneError" class="error" aria-hidden="true">
                        Please enter a valid 8-digit phone number.
                    </span>
                    <div>
                        <?php echo (isset($msg)) ? $msg : ""; ?>
                    </div>
                    <input type="number" name="phone" id="phone" required onkeyup="if(this.value<0){this.value= this.value * -1}">
                </label>
                <input class="month2" type="submit" value="Cancel Appointment" id="submit" name="submit">
            </form>
            <script type="text/javascript" src="validator.js"></script>

        </div>
        <footer>
            <?php include 'footer.php' ?>
        </footer>
    </div>
</body>
</html>
