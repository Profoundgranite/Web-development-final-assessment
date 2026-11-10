
<?php
require_once "includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Make sure the customer is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("
    SELECT
        bookings.booking_id,
        rooms.room_name,
        bookings.check_in,
        bookings.check_out,
        bookings.guests,
        bookings.total_price,
        bookings.status,
        bookings.created_at
    FROM bookings
    INNER JOIN rooms ON bookings.room_id = rooms.room_id
    WHERE bookings.user_id = ?
    ORDER BY bookings.created_at DESC
");

$stmt->bind_param("i", $user_id);
$stmt->execute();

$result = $stmt->get_result();

require_once "includes/header.php";
?>

<div class="container py-5">

    <h2 class="mb-2">My Bookings</h2>

    <p class="text-muted mb-4">
        Welcome, <?php echo htmlspecialchars($_SESSION['full_name'] ?? 'Customer'); ?>.
        Here you can view your reservations.
    </p>

    <?php if ($result->num_rows > 0): ?>

        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">

                <thead class="table-dark">
                    <tr>
                        <th>Booking ID</th>
                        <th>Room</th>
                        <th>Check-in</th>
                        <th>Check-out</th>
                        <th>Guests</th>
                        <th>Total Price</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <?php while ($booking = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($booking['booking_id']); ?></td>
                            <td><?php echo htmlspecialchars($booking['room_name']); ?></td>
                            <td><?php echo htmlspecialchars($booking['check_in']); ?></td>
                            <td><?php echo htmlspecialchars($booking['check_out']); ?></td>
                            <td><?php echo htmlspecialchars($booking['guests']); ?></td>
                            <td>$<?php echo number_format((float)$booking['total_price'], 2); ?></td>
                            <td>
                                <?php
                                $status = $booking['status'];
                                $badge = 'secondary';

                                if ($status === 'confirmed') {
                                    $badge = 'success';
                                } elseif ($status === 'pending') {
                                    $badge = 'warning';
                                } elseif ($status === 'cancelled') {
                                    $badge = 'danger';
                                }
                                ?>

                                <span class="badge bg-<?php echo $badge; ?>">
                                    <?php echo htmlspecialchars(ucfirst($status)); ?>
                                </span>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>

            </table>
        </div>

    <?php else: ?>

        <div class="alert alert-info">
            You haven't made any bookings yet.
            <a href="rooms.php">Browse our rooms</a> to make a reservation.
        </div>

    <?php endif; ?>

</div>

<?php
$stmt->close();
require_once "includes/footer.php";
?>