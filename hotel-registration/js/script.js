

function welcomeMessage() {
    console.log("Welcome to Grand Horizon Hotel!");
}

welcomeMessage();


function validateRegistrationForm() {

    const form = document.getElementById("registerForm");

    if (!form) return;

    form.addEventListener("submit", function(event) {

        const fullName = form.querySelector('[name="full_name"]').value.trim();
        const username = form.querySelector('[name="username"]').value.trim();
        const email = form.querySelector('[name="email"]').value.trim();
        const password = form.querySelector('[name="password"]').value;
        const confirmPassword = form.querySelector('[name="confirm_password"]').value;

        let errors = [];

        if (fullName === "") {
            errors.push("Please enter your full name.");
        }

        if (!/^[a-zA-Z0-9_]{3,50}$/.test(username)) {
            errors.push("Username must be 3–50 characters and contain only letters, numbers or underscores.");
        }

        if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
            errors.push("Please enter a valid email address.");
        }

        if (password.length < 8) {
            errors.push("Password must contain at least 8 characters.");
        }

        if (password !== confirmPassword) {
            errors.push("Passwords do not match.");
        }

        if (errors.length > 0) {

            event.preventDefault();

            alert(errors.join("\n"));
        }

    });
}

validateRegistrationForm();
