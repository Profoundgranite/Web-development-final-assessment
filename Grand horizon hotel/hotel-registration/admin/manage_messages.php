

<?php
require_once "../includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$messages = $conn->query("
    SELECT *
    FROM contacts
    ORDER BY created_at DESC
");

require_once "../includes/header.php";
?>

<div class="container py-5">

    <h2 class="mb-2">Customer Messages</h2>

    <p class="text-muted mb-4">
        View messages submitted through the Contact Us page.
    </p>

    <a href="dashboard.php" class="btn btn-outline-dark mb-4">
        Back to Dashboard
    </a>

    <?php if ($messages->num_rows > 0): ?>

        <div class="row g-4">

            <?php while ($message = $messages->fetch_assoc()): ?>

                <div class="col-lg-6">

                    <div class="card shadow-sm border-0 h-100">

                        <div class="card-body p-4">

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($message['subject']); ?>
                            </h5>

                            <hr>

                            <p class="mb-2">
                                <strong>Name:</strong>
                                <?php echo htmlspecialchars($message['full_name']); ?>
                            </p>

                            <p class="mb-2">
                                <strong>Email:</strong>
                                <?php echo htmlspecialchars($message['email']); ?>
                            </p>

                            <p class="mb-3">
                                <strong>Date:</strong>
                                <?php echo htmlspecialchars($message['created_at']); ?>
                            </p>

                            <div class="bg-light rounded p-3">
                                <strong>Message:</strong>

                                <p class="mb-0 mt-2" style="white-space: pre-wrap;"><?php echo htmlspecialchars($message['message']); ?></p>
                            </div>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        </div>

    <?php else: ?>

        <div class="alert alert-info">
            No customer messages have been received yet.
        </div>

    <?php endif; ?>

</div>

<?php require_once "../includes/footer.php"; ?>
