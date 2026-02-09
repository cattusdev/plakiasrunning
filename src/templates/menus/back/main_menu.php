<div class="container-fluid p-0">
    <header class="c-header shadow-sm d-flex justify-content-end">
        <nav class="navbar py-auto px-2 w-100 justify-content-start">
            <div class="c-header-icon js-hamburger d-flex">
                <div class="hamburger-toggle"><span class="bar-top"></span><span class="bar-mid"></span><span class="bar-bot"></span></div>
            </div>
            <div class="c-header-icon d-flex">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle notification-ui_icon" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell-fill fs-5"></i>
                        <span id="notifbadge" class="badge rounded-pill bg-danger">
                            
                            <span class="visually-hidden">unread notifications</span>
                        </span>
                    </a>
                    <div id="notifcontainer" class="dropdown-menu notifications-main p-0 bg-primary" aria-labelledby="navbarDropdown">

                    </div>

                </li>
            </div>

            <div class="form-check form-switch ms-auto me-2 d-none">
                <input class="form-check-input" type="checkbox" id="theme-toggle" checked>
                <label class="form-check-label" for="theme-toggle">
                    <span id="icon-toggle" class="bi bi-moon"></span>
                    <span id="icon-toggle-light" class="bi bi-sun d-none" style="color: gray;"></span>
                </label>
            </div>

            <li class="dropdown ms-auto" style="list-style: none;">
                <a class="dropdown-toggle d-flex align-items-center" href="#" id="usermemu" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <!-- <div class="company-circular mx-auto" aria-describedby="profileimage" referrerpolicy="no-referrer" style="background-image: url(/assets/images/app_logo.svg)"></div> -->
                    <div class="company-circular mx-auto" aria-describedby="profileimage" referrerpolicy="no-referrer">
                        <?php echo strtoupper($mainUser->data()->firstName[0]); ?>
                    </div>
                    <style>
                        .company-circular {
                            width: 40px;
                            height: 40px;
                            border-radius: 50%;
                            background-size: cover;
                            background-position: center;
                            display: flex;
                            align-items: center;
                            justify-content: center;
                            font-size: 25px;
                            color: var(--a-color);
                            font-weight: bold;
                            background-color: var(--bg-color-primary-faded);
                        }
                    </style>
                </a>
                <div class="dropdown-menu dropdown-menu-right m-0" aria-labelledby="usermemu">
                    <a class="dropdown-item" href="/profile"><i class="bi bi-person-square"></i> <?php echo $mainUser->data()->firstName; ?></a>
                    <a class="dropdown-item" href="/logout"><i class="bi bi-box-arrow-right"></i> Αποσύνδεση</a>
                </div>
            </li>

        </nav>
    </header>
</div>
<div style="position: absolute;min-height: 300px;right:0; bottom:0;">
    <div style="position: fixed;bottom: 50px;right: 35px;min-width: 300px;" id="genNotifications">

    </div>
</div>



<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die(header('location:  /'));

$menuManager = new BackMenuRenderer($GLOBALS['config']);
echo $menuManager->render($pagesList, 'backEnd');
