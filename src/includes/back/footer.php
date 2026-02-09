<!-- <footer class="footer mt-auto py-3 bg-dark">
    <div class="container">
        <span class="text-muted">Developed By <a href="https://cattus.dev">Cattus</a></span>
    </div>
</footer> -->
<!-- jQuery and Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.5.1.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js" integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js" integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF" crossorigin="anonymous"></script>



<!-- plugins -->
<script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/vendor/plugins/moment/moment.js"></script>
<script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/vendor/plugins/moment/momentL.js"></script>


<!-- Required -->
<script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/back/main.js"></script>
<script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/back/sidebar.js"></script>
<script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/js/back/notificationsN.js"></script>

<style>
    /* Notification Item */
    .notification-item {
        transition: transform 0.2s ease-in-out, background-color 0.3s ease;
        border-left: 4px solid #0d6efd;
    }

    .notification-item:hover {
        transform: translateY(-2px);
        background-color: #f8f9fa;
    }

    /* Unread vs Read */
    .notif-unread {
        border-left-color: #0d6efd;
    }

    .notif-read {
        opacity: 0.8;
        border-left-color: #adb5bd;
    }

    /* Action Buttons */
    .notification-item .btn {
        font-size: 0.8rem;
    }

    .notification-item .btn i {
        /* margin-right: 5px; */
    }

    /* Content Box */
    .notification-content {
        background-color: #f1f3f5;
        transition: background-color 0.3s ease;
    }

    .notification-content:hover {
        background-color: #e2e6ea;
    }

    div#notifcontainer {
        min-width: 290px;
        max-height: 380px;
        overflow: auto;
    }

    div#notifcontainer::-webkit-scrollbar {
        width: 6px;
    }

    div#notifcontainer::-webkit-scrollbar-track {
        background: #f1f1f1;
    }

    div#notifcontainer::-webkit-scrollbar-thumb {
        background: #6c757d;
        border-radius: 3px;
    }

    div#notifcontainer::-webkit-scrollbar-thumb:hover {
        background: #555;
        border-radius: 3px;
    }

      @media (max-width: 767px) {
        div#notifcontainer {
            min-width: 290px;
            max-height: 380px;
            overflow: auto;
        }
      }
</style>

<!-- Extras -->

<!-- Moment  -->
<!-- <script src="<?php echo $GLOBALS['config']['base_url']; ?>assets/vendor/plugins/moment/moment.js"></script> -->

<!-- Datepicker  -->
<script type="text/javascript" src="/assets/vendor/plugins/daterangepicker/daterangepicker.js"></script>
<link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/daterangepicker/daterangepicker.css" />



<!-- Styles -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
<!-- Or for RTL support -->
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.rtl.min.css" />

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
<?php
if (function_exists('hook_end_scripts'))
    hook_end_scripts();
?>

</body>

</html>