<?php


function build_calendar($month, $year)
{
    if (isset($_GET['date'])) {
        $selectedDate = $_GET['date'];
    }

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
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . $prevMonth . "&year=" . $prevYear . "'>Prev Month</a>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . date('m') . "&year=" . date("Y") . "&date=".date("Y-m-d")."'>Current Month</a>";
    $calendar .= "<a class='btn btn-primary btn-xs' href='?month=" . $nextMonth . "&year=" . $nextYear . "'>Next Month</a></center>";
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
        //New row after display 7 days in a week
        if ($dayOfWeek == 7) {
            $dayOfWeek = 0;
            $calendar .= "</tr><tr>";
        }

        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
        $date = "$year-$month-$currentDayRel";

        if (isset($selectedDate)){
            $today = $date == $selectedDate ? 'today' : '';

        } else{
            $today = $date == date("Y-m-d") ? 'today' : '';
        }

        if ($date < date('Y-m-d')) {
            $calendar .= "<td class='pastday'> <h4>$currentDay</h4></td>";
        } else {

            $calendar .= "<td class='$today available-date'><a  href='?month=" . $month . "&year=" . $year . "&date=" . $date . "'>
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


?>


</script>
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
    </div>

    <div class="timeslot-group">
        <h3 class="text-center">Book for Date: <?php echo date('d/m/Y', strtotime($_GET['date'])); ?></h3>
        <?php $timeslots = timeslots($duration, $cleanup, $start, $end);
        foreach ($timeslots as $ts) {
        ?>
            <div class="timeslot">
                <?php if (in_array($ts, $bookings)) { ?>
                    <button class="btn btn-booked"><?php echo $ts; ?></button>
                <?php } else { ?>
                    <button class="btn btn-available" data-timeslot="<?php echo $ts; ?>"><?php echo $ts; ?></button>
                <?php } ?>
            </div>
        <?php } ?>
    </div>
</body>

</html>
