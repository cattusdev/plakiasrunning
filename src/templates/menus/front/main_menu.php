<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die(header('location: /'));
?>
<nav class="modern-nav" id="mainNav">
        <div class="nav-content">
                <a href="/" class="brand-wrapper">
                        <img src="/assets/images/logo/favicon.png" alt="Plakias Running">
                        <span>Plakias<span class="brand-highlight">Running</span></span>
                </a>

                <div class="desktop-menu">
                        <a href="/" class="nav-item">Home</a>

                        <div class="dropdown-wrapper">
                                <a href="/routes" class="nav-item">
                                        Routes <i class="bi bi-chevron-down" style="font-size: 0.7em;"></i>
                                </a>
                                <div class="custom-dropdown">
                                        <a href="/routes#seaside">Seaside Run (5k)</a>
                                        <a href="/routes#trail">Mountain Trail</a>
                                        <a href="/routes#sunset">Sunset Experience</a>
                                </div>
                        </div>

                        <a href="/coaching" class="nav-item">Coaching</a>
                        <a href="/about" class="nav-item">Story</a>
                        <a href="/contact" class="nav-item">Contact</a>
                        <a href="/book" class="cta-btn">Book Now</a>
                </div>

                <button class="mobile-toggle" id="mobileTrigger">
                        <span class="bar"></span>
                        <span class="bar"></span>
                        <span class="bar"></span>
                </button>
        </div>
</nav>

<div class="mobile-overlay" id="mobileMenu">
        <div class="mobile-links">
                <a href="/" class="mobile-link">Home</a>

                <div class="mobile-link" id="mobRoutesTrigger">
                        Routes <i class="bi bi-chevron-down" style="font-size: 0.8em; transition: 0.3s;"></i>
                </div>
                <div class="mobile-sub-menu" id="mobRoutesList">
                        <a href="/routes#seaside">Seaside Run (5k)</a>
                        <a href="/routes#trail">Mountain Trail</a>
                        <a href="/routes#sunset">Sunset Experience</a>
                </div>

                <a href="/coaching" class="mobile-link">Coaching</a>
                <a href="/about" class="mobile-link">Story</a>
                <a href="/contact" class="mobile-link">Contact</a>
                <a href="/book" class="cta-btn mt-4">Book a Run</a>
        </div>
</div>
<script>
        document.addEventListener("DOMContentLoaded", () => {
                // Selectors
                const nav = document.getElementById('mainNav');
                const mobileTrigger = document.getElementById('mobileTrigger');
                const mobileMenu = document.getElementById('mobileMenu');
                // Select links BUT exclude the Routes Trigger so it doesn't close the menu
                const mobileLinks = document.querySelectorAll('.mobile-link:not(#mobRoutesTrigger), .mobile-links .cta-btn, .mobile-sub-menu a');

                const mobRoutesTrigger = document.getElementById('mobRoutesTrigger');
                const mobRoutesList = document.getElementById('mobRoutesList');

                // 1. Scroll Effect (Glassmorphism)
                window.addEventListener('scroll', () => {
                        if (window.scrollY > 50) {
                                nav.classList.add('scrolled');
                        } else {
                                nav.classList.remove('scrolled');
                        }
                });

                // 2. Mobile Menu Toggle
                mobileTrigger.addEventListener('click', () => {
                        mobileMenu.classList.toggle('is-active');
                        const bars = mobileTrigger.querySelectorAll('.bar');

                        if (mobileMenu.classList.contains('is-active')) {
                                bars[0].style.transform = 'translateY(8px) rotate(45deg)';
                                bars[1].style.opacity = '0';
                                bars[2].style.transform = 'translateY(-8px) rotate(-45deg)';
                                document.body.style.overflow = 'hidden';
                        } else {
                                bars[0].style.transform = 'none';
                                bars[1].style.opacity = '1';
                                bars[2].style.transform = 'none';
                                document.body.style.overflow = '';
                        }
                });

                // 3. Mobile Dropdown Accordion Logic
                if (mobRoutesTrigger) {
                        mobRoutesTrigger.addEventListener('click', () => {
                                mobRoutesList.classList.toggle('open');
                                const icon = mobRoutesTrigger.querySelector('i');
                                if (icon) icon.classList.toggle('rotate-icon');
                        });
                }

                // 4. Close Menu when clicking a link
                mobileLinks.forEach(link => {
                        link.addEventListener('click', () => {
                                mobileMenu.classList.remove('is-active');

                                // Reset Burger Icon
                                const bars = mobileTrigger.querySelectorAll('.bar');
                                bars[0].style.transform = 'none';
                                bars[1].style.opacity = '1';
                                bars[2].style.transform = 'none';
                                document.body.style.overflow = '';
                        });
                });
        });
</script>