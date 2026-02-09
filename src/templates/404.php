<?php
// Check if the HTTP_REFERER header is set
if (isset($_SERVER['HTTP_REFERER'])) {
    $previousUrl = $_SERVER['HTTP_REFERER'];
} else {
    // Default to a fallback URL if the referer is not set
    $previousUrl = '/';
}
?>
<div class="not-found-container">
    <div class="not-found-text">404</div>
    <div class="not-found-description">Oops! The page you're looking for could not be found.</div>
    <div class="mt-4 w-50 mx-auto">
        <a href="<?php echo $previousUrl; ?>" class="btn btn-secondary mr-2 my-1">Go Back</a>
        <a href="/" class="btn btn-primary my-1">Go to Home</a>
    </div>
</div>

</div>

<footer class="footer mt-auto py-3 bg-light">
    <div class="container">
        <span class="text-muted">Developed By <a href="https://cattus.dev">Cattus</a></span>
    </div>
</footer>

<style>
    .not-found-container {
        text-align: center;
    }

    .not-found-text {
        font-size: 6rem;
        font-weight: bold;
        color: #dc9135;
        animation: shake 2s;
    }

    .not-found-description {
        font-size: 1.5rem;
        color: #6c757d;
        margin-top: 10px;
    }

    @keyframes shake {

        0%,
        100% {
            transform: translateX(0);
        }

        10%,
        30%,
        50%,
        70%,
        90% {
            transform: translateX(-10px);
        }

        20%,
        40%,
        60%,
        80% {
            transform: translateX(10px);
        }
    }
</style>


<!-- Optional: jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>

</body>

</html>