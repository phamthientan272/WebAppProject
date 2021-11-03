
function validateName()
{
    var myName =  document.getElementById("name");
    var pos = myName.value.search(/^[A-Za-z ]+$/);
    const nameError = document.getElementById("nameError");
    if (pos != 0) {
        nameError.classList.add("visible");
        nameError.setAttribute("aria-hidden", false);
        nameError.setAttribute("aria-invalid", true);
        myName.classList.add("invalid");
        myName.focus();
        myName.select();
        return false;
    } else {
        nameError.classList.remove("visible");
        nameError.setAttribute("aria-hidden", true);
        nameError.setAttribute("aria-invalid", false);
        myName.classList.remove("invalid");
        return true;
    }
}

function validateEmail() {
    var email = document.getElementById("email");
    var pos = email.value.search(/^[^\s@]+@[^\s@]+\.[^\s@]+$/);
    const emailError = document.getElementById("emailError");

    if (pos != 0) {
        emailError.classList.add("visible");
        emailError.setAttribute("aria-hidden", false);
        emailError.setAttribute("aria-invalid", true);
        email.classList.add("invalid");
        email.focus();
        email.select();
        return false;
    } else {
        emailError.classList.remove("visible");
        emailError.setAttribute("aria-hidden", true);
        emailError.setAttribute("aria-invalid", false);
        email.classList.remove("invalid");
        return true;
    }
}

function validatePhone()
{
    var phone = document.getElementById("phone");
    const phoneError = document.getElementById("phoneError");

    if (phone.value.match(/\d/g).length != 8) {
        phoneError.classList.add("visible");
        phoneError.setAttribute("aria-hidden", false);
        phoneError.setAttribute("aria-invalid", true);
        phone.classList.add("invalid");
        phone.focus();
        phone.select();
        return false;
    } else {
        phoneError.classList.remove("visible");
        phoneError.setAttribute("aria-hidden", true);
        phoneError.setAttribute("aria-invalid", false);
        phone.classList.remove("invalid");
        return true;
    }
}


function validateForm()
{
    return validateEmail() &&  validateName() && validatePhone();
}
