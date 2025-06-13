document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('register-form');
    const fullName = document.getElementById('full_name');
    const username = document.getElementById('username');
    const email = document.getElementById('email');
    const password = document.getElementById('password');
    const confirmPassword = document.getElementById('confirm-password');
    const statusDiv = document.getElementById('registration-status');

    function showError(message) {
        statusDiv.innerHTML = `<p style="color: red;">${message}</p>`;
    }

    function clearError() {
        statusDiv.innerHTML = '';
    }

    function isValidEmail(email) {
        const re = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return re.test(email.toLowerCase());
    }

    form.addEventListener('submit', function (e) {
        clearError();

        // Basic required field validation
        if (!fullName.value.trim() || !username.value.trim() || !email.value.trim() || !password.value || !confirmPassword.value) {
            showError("Please fill in all the fields.");
            e.preventDefault();
            return;
        }

        // Email format validation
        if (!isValidEmail(email.value)) {
            showError("Please enter a valid email address.");
            e.preventDefault();
            return;
        }

        // Password match validation
        if (password.value !== confirmPassword.value) {
            showError("Passwords do not match.");
            e.preventDefault();
            return;
        }

        // Password strength validation (based on criteria you already use)
        const hasLower = /[a-z]/.test(password.value);
        const hasUpper = /[A-Z]/.test(password.value);
        const hasNumber = /\d/.test(password.value);
        const hasSpecial = /[^A-Za-z0-9]/.test(password.value);
        const isLengthValid = password.value.length >= 6;

        if (!(hasLower && hasUpper && hasNumber && hasSpecial && isLengthValid)) {
            showError("Password does not meet strength requirements.");
            e.preventDefault();
            return;
        }

        // Google reCAPTCHA check
        const recaptchaResponse = grecaptcha.getResponse();
        if (recaptchaResponse.length === 0) {
            showError("Please complete the reCAPTCHA.");
            e.preventDefault();
            return;
        }
    });
});
