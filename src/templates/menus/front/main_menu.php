<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die(header('location:  /'));
?>
<nav class="alma-navbar">
        <div class="nav-container">

                <div class="nav-left">
                        <a href="/" class="brand-logo">
                                <div class="logo-icon-wrapper">
                                        <img src="/assets/images/logo/logo.png" alt="Alma Icon" class="logo-img">
                                </div>
                                <span class="brand-text">alma</span>
                        </a>
                </div>

                <div class="nav-center d-none d-xl-block">
                        <ul class="nav-links">
                                <li><a href="/#about" class="nav-link-custom">Η Ομάδα</a></li>

                                <li class="dropdown-wrapper">
                                        <a href="/services" class="nav-link-custom">Υπηρεσίες <i class="bi bi-chevron-down ms-1" style="font-size: 0.8em;"></i></a>
                                        <div class="custom-dropdown">
                                                <a href="/services#adults">Ατομική Συνεδρία</a>
                                                <a href="/services#parents">Γονείς & Οικογένεια</a>
                                                <a href="/services#kids">Παιδιά & Έφηβοι</a>
                                                <a href="/services#psychosexual">Ψυχοσεξουαλική Υγεία</a>
                                        </div>
                                </li>

                                <li><a href="/about" class="nav-link-custom">Σχετικά</a></li>
                                <li><a href="/contact" class="nav-link-custom">Επικοινωνία</a></li>
                        </ul>
                </div>

                <div class="nav-right">
                        <a href="/services" class="btn-alma-pill d-none d-sm-flex">
                                <span>Κλείστε Ραντεβού</span>
                                <div class="arrow-circle">→</div>
                        </a>

                        <button class="burger-btn d-xl-none" id="mobileMenuBtn">
                                <span></span>
                                <span></span>
                                <span></span>
                        </button>
                </div>
        </div>
</nav>

<div class="mobile-menu-overlay" id="mobileOverlay">
        <div class="mobile-menu-container">
                <button class="close-menu-btn" id="closeMenuBtn">&times;</button>

                <div class="text-center mb-5">
                        <img src="/assets/images/logo/logo.png" alt="Alma" style="height: 110px;">
                </div>

                <ul class="mobile-nav-list">
                        <li><a href="/" class="mobile-link">Αρχική</a></li>
                        <li><a href="/#about" class="mobile-link">Η Ομάδα</a></li>

                        <li class="mobile-dropdown-wrapper">
                                <div class="d-flex justify-content-between align-items-center mobile-link" id="mobServiceTrigger">
                                        <span>Υπηρεσίες</span>
                                        <i class="bi bi-chevron-down transition-icon"></i>
                                </div>
                                <ul class="mobile-sub-menu" id="mobServiceList">
                                        <li><a href="/services#adults">Ατομική Συνεδρία</a></li>
                                        <li><a href="/services#parents">Γονείς & Οικογένεια</a></li>
                                        <li><a href="/services#kids">Παιδιά & Έφηβοι</a></li>
                                        <li><a href="/services#psychosexual">Ψυχοσεξουαλική Υγεία</a></li>
                                </ul>
                        </li>

                        <li><a href="/about" class="mobile-link">Σχετικά</a></li>
                        <li><a href="/contact" class="mobile-link">Επικοινωνία</a></li>
                </ul>

                <div class="mt-5 px-4">
                        <a href="/services" class="btn-alma-pill w-100 justify-content-center py-3">Κλείστε Ραντεβού</a>
                </div>
        </div>
</div>

<script>
        document.addEventListener("DOMContentLoaded", function() {
                const burgerBtn = document.getElementById('mobileMenuBtn');
                const closeBtn = document.getElementById('closeMenuBtn');
                const overlay = document.getElementById('mobileOverlay');
                const mobServiceTrigger = document.getElementById('mobServiceTrigger');
                const mobServiceList = document.getElementById('mobServiceList');
                const mobileLinks = document.querySelectorAll('.mobile-link:not(#mobServiceTrigger), .mobile-sub-menu a'); // Επιλογή όλων των links εκτός από το trigger

                // --- FUNCTIONS ---

                const openMenu = () => {
                        overlay.classList.add('active');
                        document.body.style.overflow = 'hidden';
                        document.documentElement.style.overflow = 'hidden';
                };

                const closeMenu = () => {
                        overlay.classList.remove('active');
                        document.body.style.overflow = '';
                        document.documentElement.style.overflow = '';
                };

                // --- EVENT LISTENERS ---

                if (burgerBtn) burgerBtn.addEventListener('click', openMenu);
                if (closeBtn) closeBtn.addEventListener('click', closeMenu);

                // Accordion Logic
                if (mobServiceTrigger) {
                        mobServiceTrigger.addEventListener('click', () => {
                                mobServiceList.classList.toggle('open');
                                const icon = mobServiceTrigger.querySelector('.transition-icon');
                                if (icon) icon.classList.toggle('rotate-icon');
                        });
                }

                // Click Link -> Close Menu Logic
                // Αυτό είναι σημαντικό: Όταν πατήσει κάποιο link, να κλείνει το μενού
                mobileLinks.forEach(link => {
                        link.addEventListener('click', closeMenu);
                });

                // Resize Handler
                window.addEventListener('resize', () => {
                        if (window.innerWidth >= 1200) {
                                if (overlay.classList.contains('active')) {
                                        closeMenu();
                                }
                        }
                });
        });
</script>