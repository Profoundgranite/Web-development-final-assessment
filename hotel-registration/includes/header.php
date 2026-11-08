

<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$base_path = strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Grand Horizon Hotel</title>


    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
          rel="stylesheet">


    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <link rel="stylesheet" href="<?php echo $base_path; ?>css/style.css">
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark fixed-top">
    <div class="container">

        <a class="navbar-brand fw-bold" href="<?php echo $base_path; ?>index.php">
            <i class="bi bi-buildings"></i>
            Grand Horizon
        </a>

        <button class="navbar-toggler" type="button"
                data-bs-toggle="collapse"
                data-bs-target="#mainNavbar">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="mainNavbar">

            <ul class="navbar-nav ms-auto align-items-lg-center">

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>index.php">Home</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>rooms.php">Rooms</a>
                </li>

                <li class="nav-item">
                    <a class="nav-link" href="<?php echo $base_path; ?>contact.php">Contact</a>
                </li>

                <?php if (isset($_SESSION['user_id'])): ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>my_bookings.php">
                            My Bookings
                        </a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-outline-light ms-lg-3"
                           href="<?php echo $base_path; ?>logout.php">Logout</a>
                    </li>

                <?php else: ?>

                    <li class="nav-item">
                        <a class="nav-link" href="<?php echo $base_path; ?>login.php">Login</a>
                    </li>

                    <li class="nav-item">
                        <a class="btn btn-warning ms-lg-3"
                           href="<?php echo $base_path; ?>register.php">Register</a>
                    </li>

                <?php endif; ?>
                

            </ul>

        </div>
    </div>
</nav>

<main>
