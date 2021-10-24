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
          $bookings[] = $timeslot ;
          $stmt->close();
          $mysqli->close();
    }
}


function print_booking_info()
{
    echo "Service: " .' '. $_SESSION['service'];
    echo "<br>";
    echo "Time: " .' '.  $_SESSION['timeslot'];
    echo "<br>";
    echo "Date: " .' '.  $_SESSION['selectedDate'];
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

                    <input type="submit" value="Book Now" id="submit" name="submit">
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
