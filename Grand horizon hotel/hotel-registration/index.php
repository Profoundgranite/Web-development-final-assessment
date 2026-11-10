

<?php require_once 'includes/header.php'; ?>

<div id="hotelCarousel" class="carousel slide"
     data-bs-ride="carousel">

    <div class="carousel-inner">

        <div class="carousel-item active">

            <img src="https://images.unsplash.com/photo-1571896349842-33c89424de2d"
                 class="d-block w-100 hero-image"
                 alt="Luxury hotel">

            <div class="carousel-caption">
                <h1 style="color: white;">Welcome to Grand Horizon</h1>

                <p class="fs-5">
                    Experience luxury, comfort and relaxation.
                </p>

                <a href="rooms.php"
                   class="btn btn-warning btn-lg">
                    Explore Our Rooms
                </a>
            </div>

        </div>

        <div class="carousel-item">

            <img src="https://images.unsplash.com/photo-1542314831-068cd1dbfeeb"
                 class="d-block w-100 hero-image"
                 alt="Luxury resort">

            <div class="carousel-caption">
                <h1 style="color: white;">Your Perfect Getaway</h1>

                <p class="fs-5">
                    Discover a memorable hotel experience.
                </p>

                <a href="rooms.php"
                   class="btn btn-warning btn-lg">
                    Book Your Stay
                </a>
            </div>

        </div>

    </div>

    <button class="carousel-control-prev"
            type="button"
            data-bs-target="#hotelCarousel"
            data-bs-slide="prev">

        <span class="carousel-control-prev-icon"></span>
    </button>

    <button class="carousel-control-next"
            type="button"
            data-bs-target="#hotelCarousel"
            data-bs-slide="next">

        <span class="carousel-control-next-icon"></span>
    </button>

</div>


<section class="container py-5 text-center">

    <h2 class="section-title">Discover Grand Horizon</h2>

    <p class="text-muted mt-3">
        Grand Horizon Hotel offers exceptional hospitality,
        beautiful accommodation and outstanding facilities.
        Whether you are travelling for business or leisure,
        we provide everything you need for a memorable stay.
    </p>

</section>


<section class="container py-4">

    <div class="text-center mb-4">
        <h2 class="section-title">Our Featured Rooms</h2>

        <p class="text-muted">
            Choose the perfect room for your stay.
        </p>
    </div>

    <div class="row g-4">

        <div class="col-md-4">

            <div class="card room-card shadow-sm h-100">

                <img src="https://images.unsplash.com/photo-1611892440504-42a792e24d32"
                     class="card-img-top"
                     alt="Standard room">

                <div class="card-body">

                    <h5 class="card-title">Standard Room</h5>

                    <p class="text-muted">
                        Comfortable accommodation
                        with modern facilities.
                    </p>

                    <h5 class="text-warning">$80 / night</h5>

                    <a href="rooms.php"
                       class="btn btn-dark w-100 mt-2">
                        View Details
                    </a>

                </div>
            </div>
        </div>


        <div class="col-md-4">

            <div class="card room-card shadow-sm h-100">

                <img src="https://images.unsplash.com/photo-1590490360182-c33d57733427"
                     class="card-img-top"
                     alt="Deluxe room">

                <div class="card-body">

                    <h5 class="card-title">Deluxe Room</h5>

                    <p class="text-muted">
                        Spacious accommodation
                        with premium comfort.
                    </p>

                    <h5 class="text-warning">$150 / night</h5>

                    <a href="rooms.php"
                       class="btn btn-dark w-100 mt-2">
                        View Details
                    </a>

                </div>
            </div>
        </div>

        <div class="col-md-4">

            <div class="card room-card shadow-sm h-100">

                <img src="https://images.unsplash.com/photo-1613490493576-7fde63acd811"
                     class="card-img-top"
                     alt="Luxury suite">

                <div class="card-body">

                    <h5 class="card-title">Luxury Suite</h5>

                    <p class="text-muted">
                        Enjoy an exclusive luxury
                        hotel experience.
                    </p>

                    <h5 class="text-warning">$250 / night</h5>

                    <a href="rooms.php"
                       class="btn btn-dark w-100 mt-2">
                        View Details
                    </a>

                </div>
            </div>
        </div>

    </div>

</section>



<section class="container py-5">

    <div class="text-center mb-5">
        <h2 class="section-title">Our Facilities</h2>
    </div>

    <div class="row text-center g-4">

        <div class="col-6 col-md-3">
            <i class="bi bi-water facility-icon"></i>
            <h5 class="mt-3">Swimming Pool</h5>
        </div>

        <div class="col-6 col-md-3">
            <i class="bi bi-cup-hot facility-icon"></i>
            <h5 class="mt-3">Restaurant</h5>
        </div>

        <div class="col-6 col-md-3">
            <i class="bi bi-wifi facility-icon"></i>
            <h5 class="mt-3">Free Wi-Fi</h5>
        </div>

        <div class="col-6 col-md-3">
            <i class="bi bi-car-front facility-icon"></i>
            <h5 class="mt-3">Free Parking</h5>
        </div>

    </div>

</section>


<?php require_once 'includes/footer.php'; ?>
