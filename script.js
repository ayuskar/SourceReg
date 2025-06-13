// Generate CAPTCHA code

let passwordStrengthScore = 0;  // Global variable to track password strength

document.getElementById("password").addEventListener("input", function() {
    const password = this.value;
    const strengthBar = document.getElementById("strength-fill");
    const strengthText = document.getElementById("password-strength");
    const requirements = {
        length: document.getElementById("length"),
        uppercase: document.getElementById("uppercase"),
        number: document.getElementById("number"),
        special: document.getElementById("special")
    };

    let strength = 0;

    if (password.length >= 8) {
        strength += 1;
        requirements.length.querySelector('.requirement-status').className = 'requirement-status valid';
    } else {
        requirements.length.querySelector('.requirement-status').className = 'requirement-status invalid';
    }

    if (/[a-z]/.test(password) && /[A-Z]/.test(password)) {
        strength += 1;
        requirements.uppercase.querySelector('.requirement-status').className = 'requirement-status valid';
    } else {
        requirements.uppercase.querySelector('.requirement-status').className = 'requirement-status invalid';
    }

    if (/\d/.test(password)) {
        strength += 1;
        requirements.number.querySelector('.requirement-status').className = 'requirement-status valid';
    } else {
        requirements.number.querySelector('.requirement-status').className = 'requirement-status invalid';
    }

    if (/[^A-Za-z0-9]/.test(password)) {
        strength += 1;
        requirements.special.querySelector('.requirement-status').className = 'requirement-status valid';
    } else {
        requirements.special.querySelector('.requirement-status').className = 'requirement-status invalid';
    }

    // Save the score globally
    passwordStrengthScore = strength;

    const percentage = (strength / 4) * 100;
    strengthBar.style.width = percentage + '%';

    switch(strength) {
        case 0:
            strengthBar.style.backgroundColor = '#ff4d4d';
            strengthText.textContent = 'Very Weak';
            strengthText.style.color = '#ff4d4d';
            break;
        case 1:
            strengthBar.style.backgroundColor = '#ff6b6b';
            strengthText.textContent = 'Weak';
            strengthText.style.color = '#ff6b6b';
            break;
        case 2:
            strengthBar.style.backgroundColor = '#feca57';
            strengthText.textContent = 'Fair';
            strengthText.style.color = '#feca57';
            break;
        case 3:
            strengthBar.style.backgroundColor = '#48dbfb';
            strengthText.textContent = 'Good';
            strengthText.style.color = '#48dbfb';
            break;
        case 4:
            strengthBar.style.backgroundColor = '#1dd1a1';
            strengthText.textContent = 'Strong';
            strengthText.style.color = '#1dd1a1';
            break;
    }

    checkPasswordMatch();
});


// Real-time password match checking
document.getElementById("confirm-password").addEventListener("input", checkPasswordMatch);

function checkPasswordMatch() {
    const password = document.getElementById("password").value;
    const confirmPassword = document.getElementById("confirm-password").value;
    const confirmField = document.getElementById("confirm-password");
    
    if (confirmPassword === '') return;
    
    if (password === confirmPassword) {
        confirmField.style.borderColor = '#1dd1a1';
    } else {
        confirmField.style.borderColor = '#ff6b6b';
    }
}


// Function to update password strength text
function updatePasswordStrengthText(strength, passwordStrengthText) {
    if (strength === 0) {
        passwordStrengthText.innerText = "Weak";
        passwordStrengthText.style.color = "red";
    } else if (strength === 1) {
        passwordStrengthText.innerText = "Fair (1/4 requirements met)";
        passwordStrengthText.style.color = "orange";
    } else if (strength === 2) {
        passwordStrengthText.innerText = "Fair (2/4 requirements met)";
        passwordStrengthText.style.color = "orange";
    } else if (strength === 3) {
        passwordStrengthText.innerText = "Good (3/4 requirements met)";
        passwordStrengthText.style.color = "gold";
    } else if (strength === 4) {
        passwordStrengthText.innerText = "Strong (All requirements met)";
        passwordStrengthText.style.color = "green";
    }
}

// ...

// ... (rest of the code remains the same)

// Form submission handler
document.getElementById("register-form").addEventListener("submit", function(event) {
    event.preventDefault(); // Prevent default form submission
    
    var full_name = document.getElementById("full_name").value;
    var username = document.getElementById("username").value;
    var email = document.getElementById("email").value;
    var password = document.getElementById("password").value;
    var confirmPassword = document.getElementById("confirm-password").value;

    // Register user function
    registerUser(full_name, username, email, password, confirmPassword);
});

// Register user function with enhanced error logging
function registerUser(full_name, username, email, password, confirmPassword) {
    // Simple password match checking
    if (password !== confirmPassword) {
        document.getElementById("registration-status").textContent = "Passwords do not match.";
        return;
    }
    if (passwordStrengthScore < 4) {
        document.getElementById("registration-status").textContent = "Password is too weak. Please meet all requirements.";
        return;
    }

    // Verify reCAPTCHA response before registration
    grecaptcha.execute().then(token => {
        verifyRecaptcha(token);
        // Proceed with registration after successful reCAPTCHA verification
        registerUserAfterRecaptcha(full_name, username, email, password, token);
    });
}

// Function to handle registration after successful reCAPTCHA verification
function registerUserAfterRecaptcha(full_name, username, email, password, token) {
    // Using the Fetch API with secure protocol (HTTPS assumed for newuser.php)
    fetch("newuser.php", {
        method: "POST",
        headers: {
            "Content-Type": "application/x-www-form-urlencoded"
        },
        body: `username=${encodeURIComponent(username)}&password=${encodeURIComponent(password)}&full_name=${encodeURIComponent(full_name)}&email=${encodeURIComponent(email)}&token=${encodeURIComponent(token)}`
    })
    .then(response => {
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        return response.json();
    })
    .then(data => {
        const statusDiv = document.getElementById("registration-status");

        if (data.status === "error") {
            if (data.message.includes("Missing requirements:")) {
                const requirements = data.message.replace("Password is too weak. Missing requirements: ", "").split(", ");
                statusDiv.innerHTML = `
                    <p>Password is too weak. Please meet the following requirements:</p>
                    <ul style="padding-left: 20px;">
                        ${requirements.map(item => `<li>${item}</li>`).join("")}
                    </ul>
                `;
            } else if (Array.isArray(data.messages)) {
                statusDiv.innerHTML = `
                    <p>${data.message}</p>
                    <ul style="padding-left: 20px;">
                        ${data.messages.map(item => `<li>${item}</li>`).join("")}
                    </ul>
                `;
            } else {
                statusDiv.textContent = data.message; // Use textContent for security
            }
        } else if (data.status === "success") {
            var redirectTo = 'userlogin.php';
            var params = new URLSearchParams({
                'registered': 'true',
                'message': 'Registration Successful, now you can login!'
            });
            var fullPath = `${redirectTo}?${params.toString()}`;
            window.location.href = fullPath;
        }
    })
    .catch(error => {
        console.error('Error registering user:', error); // Log the error to the console
        document.getElementById("registration-status").textContent = `An error occurred: ${error.message}. Please try again later.`; // Display a more informative error message
    });
}

// ... (rest of the code remains the same)



// Function to redirect to index.html on OK button click
function redirectToIndex() {
    window.location.href = "userlogin.php";
}
function togglePassword(inputId, iconElement) {
  const passwordField = document.getElementById(inputId);

  if (passwordField.type === "password") {
    passwordField.type = "text";
    iconElement.textContent = "🙈";
  } else {
    passwordField.type = "password";
    iconElement.textContent = "👁️";
  }
}

// Verify reCAPTCHA response
function verifyRecaptcha(token) {
    // Send token to your server for verification
    // For demonstration purposes, we'll assume it's verified
    document.getElementById("recaptcha-error").innerText = "";
    // Proceed with form submission
    var xhr = new XMLHttpRequest();
    xhr.open("POST", "newuser.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.send("username=" + document.getElementById("username").value + "&password=" + document.getElementById("password").value);
    xhr.onload = function() {
        if (xhr.status === 200) {
            document.getElementById("registration-status").innerText = "Registration successful!";
        } else {
            document.getElementById("registration-status").innerText = "Error registering user.";
        }
    };
}





