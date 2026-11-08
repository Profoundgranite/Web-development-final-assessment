

<?php
require_once "includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

$full_name = "";
$email = "";
$subject = "";
$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $full_name = trim($_POST['full_name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $subject = trim($_POST['subject'] ?? '');
    $message = trim($_POST['message'] ?? '');
    $csrf = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $error = "Invalid request. Please refresh the page and try again.";
    } elseif (
        $full_name === "" ||
        !filter_var($email, FILTER_VALIDATE_EMAIL) ||
        $subject === "" ||
        $message === ""
    ) {
        $error = "Please complete all fields and enter a valid email address.";
    } else {
        $stmt = $conn->prepare("
            INSERT INTO contacts (full_name, email, subject, message)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->bind_param(
            "ssss",
            $full_name,
            $email,
            $subject,
            $message
        );

        if ($stmt->execute()) {
            $success = "Your message has been sent successfully!";

            $full_name = "";
            $email = "";
            $subject = "";
            $message = "";

            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        } else {
            $error = "Unable to send your message. Please try again.";
        }

        $stmt->close();
    }
}

require_once "includes/header.php";
?>

<div class="container py-5">

    <div class="text-center mb-4">
        <h2>Contact Us</h2>
        <p class="text-muted">
            Have a question? Send us a message.
        </p>
    </div>

    <div class="row justify-content-center">
        <div class="col-lg-7">

            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <?php if ($error !== ""): ?>
                        <div class="alert alert-danger">
                            <?php echo htmlspecialchars($error); ?>
                        </div>
                    <?php endif; ?>

                    <?php if ($success !== ""): ?>
                        <div class="alert alert-success">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="contact.php">

                        <input
                            type="hidden"
                            name="csrf_token"
                            value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>"
                        >

                        <div class="mb-3">
                            <label class="form-label">Full Name</label>
                            <input
                                type="text"
                                name="full_name"
                                class="form-control"
                                value="<?php echo htmlspecialchars($full_name); ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email Address</label>
                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                value="<?php echo htmlspecialchars($email); ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Subject</label>
                            <input
                                type="text"
                                name="subject"
                                class="form-control"
                                value="<?php echo htmlspecialchars($subject); ?>"
                                required
                            >
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Message</label>
                            <textarea
                                name="message"
                                class="form-control"
                                rows="5"
                                required
                            ><?php echo htmlspecialchars($message); ?></textarea>
                        </div>

                        <button type="submit" class="btn btn-dark w-100">
                            Send Message
                        </button>

                    </form>

                </div>
            </div>

        </div>
    </div>

</div>

<?php require_once "includes/footer.php"; ?>
