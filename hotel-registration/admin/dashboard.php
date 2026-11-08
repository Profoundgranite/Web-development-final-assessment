

<?php
require_once "../includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}

$total_users = $conn->query(
    "SELECT COUNT(*) AS total FROM users WHERE role = 'customer'"
)->fetch_assoc()['total'];

$total_rooms = $conn->query(
    "SELECT COUNT(*) AS total FROM rooms"
)->fetch_assoc()['total'];

$total_bookings = $conn->query(
    "SELECT COUNT(*) AS total FROM bookings"
)->fetch_assoc()['total'];

$pending_bookings = $conn->query(
    "SELECT COUNT(*) AS total FROM bookings WHERE status = 'pending'"
)->fetch_assoc()['total'];

$total_messages = $conn->query(
    "SELECT COUNT(*) AS total FROM contacts"
)->fetch_assoc()['total'];

require_once "../includes/header.php";
?>

<div class="container py-5">

    <h2 class="mb-2">Administrator Dashboard</h2>

    <p class="text-muted mb-4">
        Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Administrator'); ?>.
        Manage your hotel from here.
    </p>

    <div class="row g-4 mb-5">

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Registered Customers</h6>
                    <h2><?php echo $total_users; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Rooms</h6>
                    <h2><?php echo $total_rooms; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Total Bookings</h6>
                    <h2><?php echo $total_bookings; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Pending Bookings</h6>
                    <h2><?php echo $pending_bookings; ?></h2>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card shadow-sm border-0">
                <div class="card-body">
                    <h6 class="text-muted">Contact Messages</h6>
                    <h2><?php echo $total_messages; ?></h2>
                </div>
            </div>
        </div>

    </div>

    <h4 class="mb-3">Management</h4>

    <div class="row g-3">

        <div class="col-md-4">
            <a href="manage_rooms.php" class="btn btn-dark w-100 p-3">
                <i class="bi bi-building"></i>
                Manage Rooms
            </a>
        </div>

        <div class="col-md-4">
            <a href="manage_bookings.php" class="btn btn-dark w-100 p-3">
                <i class="bi bi-calendar-check"></i>
                Manage Bookings
            </a>
        </div>

        <div class="col-md-4">
            <a href="manage_messages.php" class="btn btn-dark w-100 p-3">
                <i class="bi bi-envelope"></i>
                Manage Messages
            </a>
        </div>

    </div>

</div>

<?php require_once "../includes/footer.php"; ?>
