<?php

if (!isset($_SESSION))  session_start();
$mysqli = new mysqli('localhost', 'f32ee', 'f32ee', 'f32ee');


// Check if service type is selected or not, if not, redirect to select service type first
if (isset($_GET['service'])) {
    $_SESSION['service'] = $_GET['service'];
}

if (!isset($_SESSION['service'])) {
    header("Location: booking_service.php");
    exit();
}

// Get selected date and all booked timeslot for that date
if (isset($_GET['date'])) {
    $_SESSION['selectedDate'] = $_GET['date'];
}

// Default selected date is today
if (!isset($_SESSION['selectedDate'])) {
    $_SESSION['selectedDate'] = date("Y-m-d");
}
$result = $mysqli->query("select * from bookings where date = '" . $_SESSION['selectedDate'] . "' AND service = '" . $_SESSION['service'] . "' ");
$bookings = array();
$num_results = $result->num_rows;
for ($i = 0; $i < $num_results; $i++) {
    $row = $result->fetch_assoc();
    $bookings[] = $row['timeslot'];
}
$result->free();


function build_calendar($month, $year)
{

    @$mysqli = new mysqli('localhost', 'f32ee', 'f32ee', 'f32ee');
    $daysOfWeek = array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
    $firstDayOfMonth = mktime(0, 0, 0, $month, 1, $year);
    $numberDays = date("t", $firstDayOfMonth);
    $dateComponents = getdate($firstDayOfMonth);
    $monthName = $dateComponents["month"];
    $dayOfWeek = $dateComponents["wday"] == 0 ? 6 :  $dateComponents["wday"] - 1;

    $prevMonth = date("m", mktime(0, 0, 0, $month - 1, 1, $year));
    $prevYear = date("Y", mktime(0, 0, 0, $month - 1, 1, $year));
    $nextMonth =  date("m", mktime(0, 0, 0, $month + 1, 1, $year));
    $nextYear = date("Y", mktime(0, 0, 0, $month + 1, 1, $year));

    $calendar = "<table class='table table-bordered'>";
    $calendar .= "<center><h2>$monthName $year</h2>";

    $selectedDate = $_SESSION['selectedDate'];
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . $prevMonth . "&year=" . $prevYear . "&date=" . $selectedDate . "'><button class='month'>Prev Month</button></a>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . date('m') . "&year=" . date("Y") . "&date=" . $selectedDate . "'><button class='month'>Current Month</button></a>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . $nextMonth . "&year=" . $nextYear . "&date=" . $selectedDate . "'><button class='month'>Next Month</button></a></center>";
    $calendar .= "<br><tr>";

    //Display day of week header
    foreach ($daysOfWeek as $day) {
        $calendar .= "<th class='header'>$day</th>";
    }
    $calendar .= "</tr><tr>";

    //Display empty slot for day in week that not in same month
    $currentDay = 1;
    if ($dayOfWeek > 0) {
        for ($k = 0; $k < $dayOfWeek; $k++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
    while ($currentDay <= $numberDays) {
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";

        $dayInWeek = date("l", mktime(0, 0, 0, $month, $currentDayRel, $year));

        $selected = $date == $_SESSION['selectedDate'] ? 'selected' : '';
        if ($date < date('Y-m-d')) {
            $calendar .= "<td class='pastday' > <h4>$currentDay</h4></td>";
        } elseif (isDayOff($dayInWeek, $_SESSION['service']))
            $calendar .= "<td class='pastday' > <h4>$currentDay</h4></td>";
        else {

            $calendar .= "<td class='$selected available-date'  ><a  href='?month=" . $month . "&year=" . $year . "&date=" . $date . "'>
            <h4>$currentDay</h4></a></td>";
        }

        $currentDay++;
        $dayOfWeek++;
    }

    if ($dayOfWeek < 7) {
        $remainingDays = 7 - $dayOfWeek;
        for ($l = 0; $l < $remainingDays; $l++) {
            $calendar .= "<td class='empty'></td>";
        }
    }

    $calendar .= "</tr>";
    $calendar .= "</table>";


    return $calendar;
}


function timeslots($duration, $cleanup, $start, $end)
{
    $start = new DateTime($start);
    $end = new DateTime($end);
    $interval = new DateInterval("PT" . $duration . "M");
    $cleanupInterval = new DateInterval("PT" . $cleanup . "M");
    $slots = array();

    for ($intStart = $start; $intStart < $end; $intStart->add($interval)->add($cleanupInterval)) {
        $endPeriod = clone $intStart;
        $endPeriod->add($interval);
        if ($endPeriod > $end) {
            break;
        }

        $slots[] = $intStart->format("H:i") . "-" . $endPeriod->format("H:i");
    }

    return $slots;
}

function build_timeslot($duration, $cleanup, $start, $end, $bookings)
{
    $selectedDate = isset($_SESSION['selectedDate']) ? date('d/m/Y', strtotime($_SESSION['selectedDate'])) : date("d/m/Y");
    $timeslotHtml = "<form method='POST'>";
    $timeslotHtml .= "<h3 class='text-left'>Book for Date: $selectedDate </h3>";

    $timeslots = timeslots($duration, $cleanup, $start, $end);
    foreach ($timeslots as $ts) {

        $timeslotHtml .=  "<div class='timeslot'>";
        if (in_array($ts, $bookings)) {
            $timeslotHtml .= "<button class='btn btn-booked'>$ts</button>";
        } else {
            $timeslotHtml .= "<button name='book' type='submit' class='btn btn-available' value='$ts'> $ts</button>";
        }
        $timeslotHtml .= "</div>";
    }

    $timeslotHtml .= "</form>";
    return $timeslotHtml;
}


// Check when timeslot is clicked. Check if the selected timeslot is just booked by another user.
if (isset($_POST['book'])) {
    $_SESSION['timeslot'] = $_POST['book'];

    $result = $mysqli->query("select * from bookings where date = '" . $_SESSION['selectedDate'] . "' AND timeslot = '" . $_SESSION['timeslot'] . "' AND service = '" . $_SESSION['service'] . "'");
    $num_results = $result->num_rows;
    if ($num_results > 0) {
        $msg = "<div class='alert alert-danger'>Already Booked, Please Choose Another Date or Time.</div>";
    } else {
        header("Location: booking_form.php?");
        exit();
    }
}
function isDayOff($dateInWeek, $service)
{
    global $mysqli;
    $result = $mysqli->query("select dayoff from service_day_off where service = '" . $service . "' ");
    $num_results = $result->num_rows;
    for ($i = 0; $i < $num_results; $i++) {
        $row = $result->fetch_assoc();
        if ($dateInWeek == $row['dayoff']) {
            return True;
        }
    }
    $result->free();
    return False;
}
function getWorkingHour($service)
{
    global $mysqli;
    $result = $mysqli->query("select working_hour from service_working_hour where service = '" . $service . "' ");
    $row = $result->fetch_assoc();
    $workingHour =  $row['working_hour'];
    $workingHour = explode("-", $workingHour);
    $result->free();
    return $workingHour;
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
    <div class='service_box_booking'>
    <div class='servicebox_booking'> <img src='" . $src . "' alt='" . $imageName . "'>
    <div class='centered_service'>" . $serviceName . "</div></div></div>";
    return $serviceImage;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <title>Booking Calendar</title>
    <meta charset="utf-8">
    <link rel="stylesheet" type="text/css" href="stylesheet.css">
</head>

<body>

    <!-- This script will make the page appear at the same position when reload -->
    <script>
        document.addEventListener("DOMContentLoaded", function(event) {
            var scrollpos = localStorage.getItem('scrollpos');
            if (scrollpos) window.scrollTo(0, scrollpos);
        });

        window.onbeforeunload = function(e) {
            localStorage.setItem('scrollpos', window.scrollY);
            alert(window.scrollY);
        };
    </script>

    <div id="wrapper2">
        <header>
            <?php include 'header.php' ?>
        </header>

        <div class="box_img">
            <img src="assets/booking.jpg">
            <div class="centered">Booking Calender</div>
            <div class="centered_booking">Choose the date of your appointment:</div>
        </div>
            <a href="booking_service.php"><button class="month">Back</button></a>
            <div class="calendar">
                <?php
                $dateComponents = getdate();
                if (isset($_GET['month']) && isset($_GET['year'])) {
                    $month = $_GET["month"];
                    $year = $_GET['year'];
                } else {
                    $month = $dateComponents["mon"];
                    $year = $dateComponents["year"];
                }
                echo build_calendar($month, $year);
                ?>
                <div class="alert-message">
                    <?php echo (isset($msg)) ? $msg : ""; ?>
                    </div><br>
            </div><br>
            <div class="row">
                <div class="side-col">
                    <?php echo displayServiceImage($_SESSION['service']); ?>
                </div>

                <div class="timeslot-group main-col">
                    <?php
                    $workingHour = getWorkingHour($_SESSION["service"]);
                    $start = $workingHour[0];
                    $end = $workingHour[1];
                    $duration = 50;
                    $cleanup = 10;
                    echo build_timeslot($duration, $cleanup, $start, $end, $bookings);
                    ?>
                
                </div>

                
            </div>

            <footer>
                <?php include 'footer.php' ?>
            </footer>
        </div>
</body>

</html>
