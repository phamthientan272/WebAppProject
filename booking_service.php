<html lang="en">

<head>
  <title>Booking Service</title>
  <meta charset="utf-8">
  <link rel="stylesheet" href="stylesheet.css">
</head>

<body>
  <div id="wrapper">
    <header>
      <?php include 'header.php' ?>
    </header>

    <div class="box_img">
      <img src="assets/test.jpg" alt="test">
      <div class="centered">Booking</div>
    </div>

    <div class="container">
      <br>
      <h2>Select the service you wish to book:</h2><br>
      <div class="service_box">
        <div class="servicebox1">
          <a href="booking_calendar.php?service=Therapy"><img src="assets/coffee.jpg" alt="coffee"></a>
          <div class="centered_service">Therapy & Counseling</div>
        </div>
        <div class="servicebox2">
          <a href="booking_calendar.php?service=Yoga"><img src="assets/yoga.jpg" alt="yoga"></a>
          <div class="centered_service">Yoga</div>
        </div>
        <div class="servicebox3">
          <a href="booking_calendar.php?service=Massage"><img src="assets/massage.jpg" alt="massage"></a>
          <div class="centered_service">Massage</div>
        </div>
      </div>
    </div>

    <footer>
      <?php include 'footer.php' ?>
    </footer>
  </div>
</body>

</html>
