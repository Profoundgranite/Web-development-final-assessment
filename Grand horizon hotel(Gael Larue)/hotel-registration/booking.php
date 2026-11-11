

<?php
require_once "includes/db.php";

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

$error = "";
$success = "";

$room_id = filter_input(INPUT_GET, 'room_id', FILTER_VALIDATE_INT);

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);
}

$room = null;

if ($room_id && $room_id > 0) {
    $stmt = $conn->prepare(
        "SELECT * FROM rooms WHERE room_id = ? LIMIT 1"
    );
    $stmt->bind_param("i", $room_id);
    $stmt->execute();
    $room = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

if (!$room) {
    $error = "The selected room could not be found.";
} elseif ($_SERVER["REQUEST_METHOD"] === "POST") {

    $csrf = $_POST['csrf_token'] ?? '';
    $check_in = $_POST['check_in'] ?? '';
    $check_out = $_POST['check_out'] ?? '';
    $guests = filter_input(INPUT_POST, 'guests', FILTER_VALIDATE_INT);

    if (!hash_equals($_SESSION['csrf_token'], $csrf)) {
        $error = "Invalid request. Please try again.";
    } elseif ($room['status'] !== 'available') {
        $error = "This room is currently unavailable.";
    } elseif (!$check_in || !$check_out || !$guests || $guests < 1) {
        $error = "Please complete all booking fields correctly.";
    } else {
        $start = DateTime::createFromFormat('!Y-m-d', $check_in);
        $end = DateTime::createFromFormat('!Y-m-d', $check_out);
        $today = new DateTime('today');

        if (
            !$start || !$end ||
            $start->format('Y-m-d') !== $check_in ||
            $end->format('Y-m-d') !== $check_out
        ) {
            $error = "Please enter valid dates.";
        } elseif ($start < $today) {
            $error = "Check-in cannot be in the past.";
        } elseif ($end <= $start) {
            $error = "Check-out must be after check-in.";
        } elseif ($guests > (int)$room['capacity']) {
            $error = "This room can accommodate a maximum of "
                . (int)$room['capacity'] . " guests.";
        } else {
            $availability = $conn->prepare("
                SELECT booking_id
                FROM bookings
                WHERE room_id = ?
                AND status IN ('pending', 'confirmed')
                AND check_in < ?
                AND check_out > ?
                LIMIT 1
            ");

            $availability->bind_param(
                "iss",
                $room_id,
                $check_out,
                $check_in
            );

            $availability->execute();
            $unavailable = $availability->get_result()->num_rows > 0;
            $availability->close();

            if ($unavailable) {
                $error = "This room is already booked for those dates.";
            } else {
                $nights = $start->diff($end)->days;
                $total_price = $nights * (float)$room['price'];
                $user_id = (int)$_SESSION['user_id'];

                $insert = $conn->prepare("
                    INSERT INTO bookings
                    (user_id, room_id, check_in, check_out, guests, total_price, status)
                    VALUES (?, ?, ?, ?, ?, ?, 'pending')
                ");

                $insert->bind_param(
                    "iissid",
                    $user_id,
                    $room_id,
                    $check_in,
                    $check_out,
                    $guests,
                    $total_price
                );

                if ($insert->execute()) {
                    $success = "Your booking has been submitted successfully!";
                } else {
                    $error = "Unable to complete your booking. Please try again.";
                }

                $insert->close();
            }
        }
    }
}

require_once "includes/header.php";
?>

<div class="container py-5">

    <h2 class="mb-4">Room Reservation</h2>

    <?php if ($error !== ""): ?>
        <div class="alert alert-danger">
            <?php echo htmlspecialchars($error); ?>
        </div>
    <?php endif; ?>

    <?php if ($success !== ""): ?>
        <div class="alert alert-success">
            <?php echo htmlspecialchars($success); ?>
            <br>
            <a href="my_bookings.php">View My Bookings</a>
        </div>
    <?php endif; ?>

    <?php if ($room): ?>

        <div class="row justify-content-center">
            <div class="col-lg-7">

                <div class="card shadow border-0">
                    <div class="card-body p-4">

                        <h3>
                            <?php echo htmlspecialchars($room['room_name']); ?>
                        </h3>

                        <p class="text-muted">
                            <?php echo htmlspecialchars($room['description']); ?>
                        </p>

                        <p>
                            <strong>Price per night:</strong>
                            $<?php echo number_format((float)$room['price'], 2); ?>
                        </p>

                        <p>
                            <strong>Maximum guests:</strong>
                            <?php echo (int)$room['capacity']; ?>
                        </p>

                        <?php if ($success === ""): ?>

                            <form method="POST" action="booking.php">

                                <input type="hidden" name="room_id"
                                       value="<?php echo (int)$room['room_id']; ?>">

                                <input type="hidden" name="csrf_token"
                                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                <div class="mb-3">
                                    <label class="form-label">Check-in Date</label>
                                    <input type="date"
                                           name="check_in"
                                           class="form-control"
                                           min="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo htmlspecialchars($_POST['check_in'] ?? ''); ?>"
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Check-out Date</label>
                                    <input type="date"
                                           name="check_out"
                                           class="form-control"
                                           min="<?php echo date('Y-m-d'); ?>"
                                           value="<?php echo htmlspecialchars($_POST['check_out'] ?? ''); ?>"
                                           required>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Number of Guests</label>
                                    <input type="number"
                                           name="guests"
                                           class="form-control"
                                           min="1"
                                           max="<?php echo (int)$room['capacity']; ?>"
                                           value="<?php echo htmlspecialchars($_POST['guests'] ?? '1'); ?>"
                                           required>
                                </div>

                                <button type="submit" class="btn btn-dark w-100">
                                    Confirm Reservation
                                </button>

                            </form>

                        <?php endif; ?>

                    </div>
                </div>

            </div>
        </div>

    <?php endif; ?>

</div>

<?php require_once "includes/footer.php"; ?>
