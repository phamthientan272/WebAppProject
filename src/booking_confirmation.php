<?php
if (!isset($_SESSION))  session_start();

if (!isset($_SESSION['service']) || !isset($_SESSION['timeslot']) || !isset($_SESSION['selectedDate'])) {
    header("Location: booking_calendar.php?");
    exit();
}

$mysqli = new mysqli('localhost', 'f32ee', 'f32ee', 'f32ee');
if ($mysqli->connect_error) {
    echo "Database is not online";
    exit;
    // above 2 statments same as die() //
} else
    echo "Congratulations...  MySql is working..";

if (isset($_POST['submit'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $service = $_SESSION['service'];
    $timeslot = $_SESSION['timeslot'];
    $date = $_SESSION['selectedDate'];


    $result = $mysqli->query("select * from bookings where service = '" . $service . "' AND
    date = '" . $date . "' AND timeslot = '" . $timeslot . "'");
    $num_results = $result->num_rows;
    echo "Hello";
    echo $num_results;
    if ($num_results > 0) {
        $msg = "<div class='alert alert-danger'>Already Booked</div>";
    } else {
        $msg = "<div class='alert alert-danger'>Can book</div>";
          $stmt = $mysqli->prepare("INSERT INTO bookings (service, date, timeslot, name, email) VALUES (?, ?, ?, ?, ?)");
          $stmt->bind_param('sssss', $service, $date, $timeslot, $name, $email);
          $stmt->execute();
          $msg = "<div class='alert alert-success'>Booking Successfull</div>";
          $bookings[] = $timeslot ;
          $stmt->close();
          $mysqli->close();
    }
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
    <link rel="stylesheet" href="main.css">

</head>

<body>
    <div class="container">
        <div class="confirmation-group">
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

                    <input type="submit" value="Book Now" id="submit" name="submit">
                </form>
            </div>
            <div>
                <?php echo (isset($msg)) ? $msg : ""; ?>
            </div>

        </div>
    </div>
</body>

</html>
