

<?php
require_once 'includes/db.php';
require_once 'includes/header.php';

// Retrieve available rooms from MySQL
$sql = "SELECT * FROM rooms WHERE status = 'available' ORDER BY price ASC";
$result = $conn->query($sql);
?>

<section class="container py-5">

    <div class="text-center mb-5">
        <h1 class="section-title">Our Rooms</h1>
        <p class="text-muted">
            Discover comfortable accommodation
            designed for your perfect stay.
        </p>
    </div>

    <div class="row g-4">

        <?php if ($result && $result->num_rows > 0): ?>

            <?php while ($room = $result->fetch_assoc()): ?>

                <div class="col-md-6 col-lg-4">

                    <div class="card room-card shadow-sm h-100">

                        <img
                            src="<?php echo htmlspecialchars($room['image'] ?? ''); ?>"
                            class="card-img-top"
                            alt="<?php echo htmlspecialchars($room['room_name']); ?>"
                        >

                        <div class="card-body d-flex flex-column">

                            <span class="badge bg-success mb-2 align-self-start">
                                Available
                            </span>

                            <h5 class="card-title">
                                <?php echo htmlspecialchars($room['room_name']); ?>
                            </h5>

                            <p class="text-muted">
                                <?php echo htmlspecialchars($room['description']); ?>
                            </p>

                            <p>
                                <i class="bi bi-people"></i>
                                Capacity:
                                <?php echo (int)$room['capacity']; ?> guests
                            </p>

                            <h5 class="text-warning">
                                $<?php echo number_format((float)$room['price'], 2); ?>
                                / night
                            </h5>

                            <a
                                href="booking.php?room_id=<?php echo (int)$room['room_id']; ?>"
                                class="btn btn-dark w-100 mt-auto"
                            >
                                Book This Room
                            </a>

                        </div>

                    </div>

                </div>

            <?php endwhile; ?>

        <?php else: ?>

            <div class="col-12 text-center">
                <div class="alert alert-info">
                    No rooms are currently available.
                </div>
            </div>

        <?php endif; ?>

    </div>

</section>

<?php require_once 'includes/footer.php'; ?>
