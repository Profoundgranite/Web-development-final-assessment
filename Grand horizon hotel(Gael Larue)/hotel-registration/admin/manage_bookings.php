

<?php
require_once "../includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] ?? '') !== 'admin') {
    header("Location: ../login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $csrf = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $error = "Invalid request. Please refresh the page.";
    } else {

        $booking_id = filter_var(
            $_POST['booking_id'] ?? '',
            FILTER_VALIDATE_INT
        );

        $status = $_POST['status'] ?? '';

        if (
            !$booking_id ||
            $booking_id < 1 ||
            !in_array($status, ['confirmed', 'cancelled'], true)
        ) {
            $error = "Invalid booking information.";
        } else {

            $stmt = $conn->prepare("
                UPDATE bookings
                SET status = ?
                WHERE booking_id = ?
                AND status = 'pending'
            ");

            $stmt->bind_param("si", $status, $booking_id);

            if ($stmt->execute()) {
                if ($stmt->affected_rows > 0) {
                    $success = "Booking status updated successfully!";
                    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
                } else {
                    $error = "Booking was not found or is no longer pending.";
                }
            } else {
                $error = "Unable to update booking status.";
            }

            $stmt->close();
        }
    }
}

$bookings = $conn->query("
    SELECT
        bookings.booking_id,
        users.full_name,
        users.email,
        rooms.room_name,
        bookings.check_in,
        bookings.check_out,
        bookings.guests,
        bookings.total_price,
        bookings.status,
        bookings.created_at
    FROM bookings
    INNER JOIN users
        ON bookings.user_id = users.user_id
    INNER JOIN rooms
        ON bookings.room_id = rooms.room_id
    ORDER BY bookings.created_at DESC
");

require_once "../includes/header.php";
?>

<div class="container py-5">

    <h2 class="mb-2">Manage Customer Bookings</h2>

    <p class="text-muted mb-4">
        View and manage hotel reservations.
    </p>

    <a href="dashboard.php" class="btn btn-outline-dark mb-4">
        Back to Dashboard
    </a>

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

    <div class="card shadow-sm border-0">
        <div class="card-body">

            <div class="table-responsive">

                <table class="table table-bordered table-hover align-middle">

                    <thead class="table-dark">
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Room</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Guests</th>
                            <th>Total Price</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if ($bookings->num_rows > 0): ?>

                            <?php while ($booking = $bookings->fetch_assoc()): ?>

                                <tr>
                                    <td>
                                        <?php echo (int)$booking['booking_id']; ?>
                                    </td>

                                    <td>
                                        <strong>
                                            <?php echo htmlspecialchars($booking['full_name']); ?>
                                        </strong>
                                        <br>
                                        <small class="text-muted">
                                            <?php echo htmlspecialchars($booking['email']); ?>
                                        </small>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($booking['room_name']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($booking['check_in']); ?>
                                    </td>

                                    <td>
                                        <?php echo htmlspecialchars($booking['check_out']); ?>
                                    </td>

                                    <td>
                                        <?php echo (int)$booking['guests']; ?>
                                    </td>

                                    <td>
                                        $<?php echo number_format((float)$booking['total_price'], 2); ?>
                                    </td>

                                    <td>
                                        <?php
                                        $badge = 'secondary';

                                        if ($booking['status'] === 'confirmed') {
                                            $badge = 'success';
                                        } elseif ($booking['status'] === 'pending') {
                                            $badge = 'warning';
                                        } elseif ($booking['status'] === 'cancelled') {
                                            $badge = 'danger';
                                        }
                                        ?>

                                        <span class="badge bg-<?php echo $badge; ?>">
                                            <?php echo htmlspecialchars(ucfirst($booking['status'])); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <?php if ($booking['status'] === 'pending'): ?>

                                            <form method="POST"
                                                  action="manage_bookings.php"
                                                  class="d-flex flex-column gap-2">

                                                <input type="hidden"
                                                       name="csrf_token"
                                                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                                <input type="hidden"
                                                       name="booking_id"
                                                       value="<?php echo (int)$booking['booking_id']; ?>">

                                                <button type="submit"
                                                        name="status"
                                                        value="confirmed"
                                                        class="btn btn-sm btn-success">
                                                    Confirm
                                                </button>

                                                <button type="submit"
                                                        name="status"
                                                        value="cancelled"
                                                        class="btn btn-sm btn-danger"
                                                        onclick="return confirm('Are you sure you want to cancel this booking?');">
                                                    Cancel
                                                </button>

                                            </form>

                                        <?php else: ?>

                                            <span class="text-muted">
                                                No actions available
                                            </span>

                                        <?php endif; ?>
                                    </td>

                                </tr>

                            <?php endwhile; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="9" class="text-center">
                                    No bookings have been made yet.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>

            </div>

        </div>
    </div>

</div>

<?php require_once "../includes/footer.php"; ?>
