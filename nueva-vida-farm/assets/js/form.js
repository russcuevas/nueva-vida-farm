function checkPasswordLength() {
    var passwordField = document.getElementById('password');
    var passwordMessage = document.getElementById('passwordMessage');
    var password = passwordField.value;

    if (password.length >= 8 && password.length <= 12) {
        passwordMessage.textContent = '';
    } else {
        passwordMessage.textContent = 'Password must be 8 - 12 characters';
    }
}