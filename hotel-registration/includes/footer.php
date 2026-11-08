

</main>

<footer class="bg-dark text-white py-4 mt-5">
    <div class="container text-center">

        <h4 class="fw-bold">Grand Horizon Hotel</h4>

        <p class="text-white-50">
            Experience comfort, luxury and unforgettable hospitality.
        </p>

        <div class="mb-3">
            <a href="index.php" class="footer-link">Home</a>
            <a href="rooms.php" class="footer-link">Rooms</a>
            <a href="contact.php" class="footer-link">Contact</a>
        </div>

        <hr>

        <p class="mb-0 text-white-50">
            &copy; <?php echo date("Y"); ?>
            Grand Horizon Hotel. All Rights Reserved.
        </p>

    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<script src="<?php echo strpos($_SERVER['PHP_SELF'], '/admin/') !== false ? '../js/script.js' : 'js/script.js'; ?>"></script>

</body>
</html>
