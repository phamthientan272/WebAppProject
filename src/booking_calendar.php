<?php
if (!isset($_SESSION))  session_start();

// Check if service type is selected or not, if not, redirect to select service type first
if (isset($_GET['service'])){
    $_SESSION['service'] = $_GET['service'];
}

if (!isset($_SESSION['service'])){
    header("Location: booking_service.php");
    exit();
}

// ----------------------




if (isset($_GET['date'])) {
    $_SESSION['selectedDate'] = $_GET['date'];

    $bookings = array();
}

if (!isset($_SESSION['selectedDate'])) {
    $_SESSION['selectedDate'] = date("Y-m-d");
}


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
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . $prevMonth . "&year=" . $prevYear . "&date=".$selectedDate."'>Prev Month</a>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . date('m') . "&year=" . date("Y") . "&date=".$selectedDate."'>Current Month</a>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . $nextMonth . "&year=" . $nextYear . "&date=".$selectedDate."'>Next Month</a></center>";
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

        $selected = $date == $_SESSION['selectedDate'] ? 'selected' : '';
        if ($date < date('Y-m-d')) {
            $calendar .= "<td class='pastday' > <h4>$currentDay</h4></td>";
        } else {

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

$duration = 50;
$cleanup = 10;
$start = "09:00";
$end = "20:00";

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

        $slots[] = $intStart->format("H:iA") . "-" . $endPeriod->format("H:iA");
    }

    return $slots;
}

function build_timeslot($duration, $cleanup, $start, $end, $bookings)
{
    $selectedDate = isset($_SESSION['selectedDate']) ? date('d/m/Y', strtotime($_SESSION['selectedDate'])) : date("d/m/Y");
    $timeslotHtml = "<form method='POST'>";
    $timeslotHtml .= "<h3 class='text-center'>Book for Date: $selectedDate </h3>";

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


if (isset($_POST['book'])) {
    $_SESSION['timeslot'] = $_POST['book'];

    //TODO: Check timeslot available in database or not before moving on

    header("Location: booking_confirmation.php?");
    exit();
}

?>



<html lang="en">

<head>
    <title>Booking Calendar</title>
    <meta charset="utf-8">
    <link rel="stylesheet" href="main.css">

</head>

<body>
    <div class="container">
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
        </div>

        <div class="timeslot-group">
            <?php
            echo build_timeslot($duration, $cleanup, $start, $end, $bookings);
            ?>
        </div>
    </div>


</body>

</html>
