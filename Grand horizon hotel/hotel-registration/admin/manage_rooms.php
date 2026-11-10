
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

        $action = $_POST['action'] ?? '';

        if ($action === "add" || $action === "update") {

            $room_name = trim($_POST['room_name'] ?? '');
            $room_type = trim($_POST['room_type'] ?? '');
            $description = trim($_POST['description'] ?? '');
            $price = filter_var($_POST['price'] ?? '', FILTER_VALIDATE_FLOAT);
            $capacity = filter_var($_POST['capacity'] ?? '', FILTER_VALIDATE_INT);
            $image = trim($_POST['image'] ?? '');
            $status = $_POST['status'] ?? '';

            if (
                $room_name === "" ||
                $room_type === "" ||
                $description === "" ||
                $price === false || $price <= 0 ||
                $capacity === false || $capacity < 1 ||
                !in_array($status, ['available', 'unavailable'], true)
            ) {
                $error = "Please enter valid information in all required fields.";
            } elseif ($image !== "" && !filter_var($image, FILTER_VALIDATE_URL)) {
                $error = "Please enter a valid image URL.";
            } else {

                if ($action === "add") {

                    $stmt = $conn->prepare("
                        INSERT INTO rooms
                        (room_name, room_type, description, price, capacity, image, status)
                        VALUES (?, ?, ?, ?, ?, ?, ?)
                    ");

                    $stmt->bind_param(
                        "sssdiss",
                        $room_name,
                        $room_type,
                        $description,
                        $price,
                        $capacity,
                        $image,
                        $status
                    );

                    if ($stmt->execute()) {
                        $success = "Room added successfully!";
                    } else {
                        $error = "Unable to add the room.";
                    }

                    $stmt->close();

                } else {

                    $room_id = filter_input(INPUT_POST, 'room_id', FILTER_VALIDATE_INT);

                    if (!$room_id || $room_id < 1) {
                        $error = "Invalid room selected.";
                    } else {
                        $stmt = $conn->prepare("
                            UPDATE rooms
                            SET room_name = ?, room_type = ?, description = ?,
                                price = ?, capacity = ?, image = ?, status = ?
                            WHERE room_id = ?
                        ");

                        $stmt->bind_param(
                            "sssdissi",
                            $room_name,
                            $room_type,
                            $description,
                            $price,
                            $capacity,
                            $image,
                            $status,
                            $room_id
                        );

                        if ($stmt->execute()) {
                            $success = "Room updated successfully!";
                        } else {
                            $error = "Unable to update the room.";
                        }

                        $stmt->close();
                    }
                }
            }
        }

        elseif ($action === "delete") {

            $room_id = filter_var(
                $_POST['room_id'] ?? '',
                FILTER_VALIDATE_INT
            );

            if (!$room_id || $room_id < 1) {
                $error = "Invalid room selected.";
            } else {
                try {
                    $stmt = $conn->prepare(
                        "DELETE FROM rooms WHERE room_id = ?"
                    );

                    $stmt->bind_param("i", $room_id);
                    $stmt->execute();
                    $stmt->close();

                    $success = "Room deleted successfully!";

                } catch (mysqli_sql_exception $e) {
                    $error = "This room has existing bookings and cannot be deleted. You can mark it as unavailable instead.";
                }
            }
        }

        if ($success !== "") {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
    }
}

$edit_room = null;

$edit_id = filter_input(INPUT_GET, 'edit', FILTER_VALIDATE_INT);

if ($edit_id && $edit_id > 0) {
    $stmt = $conn->prepare("SELECT * FROM rooms WHERE room_id = ?");
    $stmt->bind_param("i", $edit_id);
    $stmt->execute();
    $edit_room = $stmt->get_result()->fetch_assoc();
    $stmt->close();
}

$rooms = $conn->query("SELECT * FROM rooms ORDER BY room_id DESC");

require_once "../includes/header.php";
?>

<div class="container py-5">

    <h2 class="mb-4">Manage Hotel Rooms</h2>

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

    <div class="card shadow-sm border-0 mb-5">
        <div class="card-body p-4">

            <h4 class="mb-3">
                <?php echo $edit_room ? "Edit Room" : "Add New Room"; ?>
            </h4>

            <form method="POST" action="manage_rooms.php">

                <input type="hidden" name="csrf_token"
                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                <input type="hidden" name="action"
                       value="<?php echo $edit_room ? 'update' : 'add'; ?>">

                <?php if ($edit_room): ?>
                    <input type="hidden" name="room_id"
                           value="<?php echo (int)$edit_room['room_id']; ?>">
                <?php endif; ?>

                <div class="row">

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Room Name</label>
                        <input type="text" name="room_name"
                               class="form-control" required
                               value="<?php echo htmlspecialchars($edit_room['room_name'] ?? ''); ?>">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Room Type</label>
                        <input type="text" name="room_type"
                               class="form-control" required
                               value="<?php echo htmlspecialchars($edit_room['room_type'] ?? ''); ?>">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Description</label>
                        <textarea name="description" class="form-control"
                                  rows="3" required><?php echo htmlspecialchars($edit_room['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Price Per Night ($)</label>
                        <input type="number" name="price"
                               class="form-control" min="0.01"
                               step="0.01" required
                               value="<?php echo htmlspecialchars((string)($edit_room['price'] ?? '')); ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Maximum Guests</label>
                        <input type="number" name="capacity"
                               class="form-control" min="1"
                               required
                               value="<?php echo htmlspecialchars((string)($edit_room['capacity'] ?? '')); ?>">
                    </div>

                    <div class="col-md-4 mb-3">
                        <label class="form-label">Availability</label>
                        <select name="status" class="form-select" required>
                            <option value="available"
                                <?php echo ($edit_room['status'] ?? 'available') === 'available' ? 'selected' : ''; ?>>
                                Available
                            </option>
                            <option value="unavailable"
                                <?php echo ($edit_room['status'] ?? '') === 'unavailable' ? 'selected' : ''; ?>>
                                Unavailable
                            </option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Room Image URL</label>
                        <input type="url" name="image"
                               class="form-control"
                               placeholder="https://example.com/room.jpg"
                               value="<?php echo htmlspecialchars($edit_room['image'] ?? ''); ?>">
                    </div>

                </div>

                <button type="submit" class="btn btn-dark">
                    <?php echo $edit_room ? "Save Changes" : "Add Room"; ?>
                </button>

                <?php if ($edit_room): ?>
                    <a href="manage_rooms.php" class="btn btn-secondary">
                        Cancel Edit
                    </a>
                <?php endif; ?>

            </form>

        </div>
    </div>

    <h4 class="mb-3">Existing Rooms</h4>

    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">

            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Room</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Capacity</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php while ($room = $rooms->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo (int)$room['room_id']; ?></td>

                        <td><?php echo htmlspecialchars($room['room_name']); ?></td>

                        <td><?php echo htmlspecialchars($room['room_type']); ?></td>

                        <td>$<?php echo number_format((float)$room['price'], 2); ?></td>

                        <td><?php echo (int)$room['capacity']; ?></td>

                        <td>
                            <?php if ($room['status'] === 'available'): ?>
                                <span class="badge bg-success">Available</span>
                            <?php else: ?>
                                <span class="badge bg-danger">Unavailable</span>
                            <?php endif; ?>
                        </td>

                        <td>
                            <a href="manage_rooms.php?edit=<?php echo (int)$room['room_id']; ?>"
                               class="btn btn-sm btn-primary mb-1">
                                Edit
                            </a>

                            <form method="POST" action="manage_rooms.php"
                                  class="d-inline"
                                  onsubmit="return confirm('Are you sure you want to delete this room?');">

                                <input type="hidden" name="csrf_token"
                                       value="<?php echo htmlspecialchars($_SESSION['csrf_token']); ?>">

                                <input type="hidden" name="action" value="delete">

                                <input type="hidden" name="room_id"
                                       value="<?php echo (int)$room['room_id']; ?>">

                                <button type="submit" class="btn btn-sm btn-danger mb-1">
                                    Delete
                                </button>

                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>

            </tbody>
        </table>
    </div>

</div>

<?php require_once "../includes/footer.php"; ?>