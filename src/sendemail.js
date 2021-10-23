function sendEmailDefault(){
    var email = "f32ee@localhost";
    var subject = "Test Email";
    var msgBody = "Thank you for contacting us, we will get back to you in 24 hours!";
    window.open(`mailto:${email}?subject=${subject}&body=${msgBody}`);
  }
