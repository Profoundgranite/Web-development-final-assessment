

<?php
require_once 'includes/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$errors = [];
$success = "";

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Collect form information
    $full_name = trim($_POST['full_name'] ?? '');
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $csrf_token = $_POST['csrf_token'] ?? '';

    // Server-side validation
    if (!hash_equals($_SESSION['csrf_token'], $csrf_token)) {
        $errors[] = "Invalid form submission. Please try again.";
    }

    if ($full_name === '') {
        $errors[] = "Full name is required.";
    }

    if (!preg_match('/^[a-zA-Z0-9_]{3,50}$/', $username)) {
        $errors[] = "Username must contain 3–50 letters, numbers or underscores.";
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Please enter a valid email address.";
    }

    if (strlen($password) < 8) {
        $errors[] = "Password must contain at least 8 characters.";
    }

    if ($password !== $confirm_password) {
        $errors[] = "Passwords do not match.";
    }

    // Check whether username or email already exists
    if (empty($errors)) {

        $check = $conn->prepare(
            "SELECT user_id FROM users WHERE username = ? OR email = ?"
        );

        $check->bind_param("ss", $username, $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $errors[] = "Username or email is already registered.";
        }

        $check->close();
    }

    // Save customer information
    if (empty($errors)) {

        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $conn->prepare(
            "INSERT INTO users (full_name, username, email, password, role)
             VALUES (?, ?, ?, ?, 'customer')"
        );

        $stmt->bind_param(
            "ssss",
            $full_name,
            $username,
            $email,
            $hashed_password
        );

        try {
            if ($stmt->execute()) {
                $success = "Registration successful! You can now log in.";
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
        } catch (mysqli_sql_exception $e) {
            $errors[] = "Registration could not be completed. Please try again.";
        }

        $stmt->close();
    }
}

require_once 'includes/header.php';
?>

<section class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-7 col-lg-6">

            <div class="card shadow border-0">

                <div class="card-body p-4 p-md-5">

                    <div class="text-center mb-4">
                        <i class="bi bi-person-plus-fill display-4 text-warning"></i>
                        <h2 class="fw-bold mt-2">Create an Account</h2>
                        <p class="text-muted">
                            Register with Grand Horizon Hotel.
                        </p>
                    </div>

                    <?php if ($success): ?>

                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                            <a href="login.php" class="alert-link">
                                Login here
                            </a>.
                        </div>

                    <?php endif; ?>

                    <?php if (!empty($errors)): ?>

                        <div class="alert alert-danger">
                            <strong>Please correct the following:</strong>
                            <ul class="mb-0 mt-2">
                                <?php foreach ($errors as $error): ?>
                                    <li><?php echo htmlspecialchars($error); ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>

                    <?php endif; ?>

                    <form id="registerForm" method="POST"
                          action="register.php" novalidate>

                        <input type="hidden" name="csrf_token"
                               value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input type="text" name="full_name"
                                   class="form-control"
                                   placeholder="Enter your full name"
                                   value="<?php echo htmlspecialchars($full_name ?? ''); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Username</label>
                            <input type="text" name="username"
                                   class="form-control"
                                   placeholder="Choose a username"
                                   value="<?php echo htmlspecialchars($username ?? ''); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input type="email" name="email"
                                   class="form-control"
                                   placeholder="example@email.com"
                                   value="<?php echo htmlspecialchars($email ?? ''); ?>"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password"
                                   class="form-control"
                                   placeholder="Minimum 8 characters"
                                   required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Confirm Password</label>
                            <input type="password" name="confirm_password"
                                   class="form-control"
                                   placeholder="Re-enter your password"
                                   required>
                        </div>

                        <button type="submit"
                                class="btn btn-warning w-100 py-2 fw-bold">
                            Create Account
                        </button>

                    </form>

                    <p class="text-center mt-4 mb-0">
                        Already have an account?
                        <a href="login.php">Login</a>
                    </p>

                </div>
            </div>

        </div>
    </div>
</section>

<?php require_once 'includes/footer.php'; ?>
