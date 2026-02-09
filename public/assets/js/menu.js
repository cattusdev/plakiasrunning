document.addEventListener("DOMContentLoaded", function () {
    const mobileBtn = document.getElementById('mobileMenuTrigger');
    const closeBtn = document.getElementById('closeMenuBtn');
    const overlay = document.getElementById('mobileOverlay');
    const mobServiceTrigger = document.getElementById('mobServiceTrigger');
    const mobServiceList = document.getElementById('mobServiceList');

    // 1. Άνοιγμα Mobile Menu
    mobileBtn.addEventListener('click', () => {
        overlay.classList.add('active');
        document.body.style.overflow = 'hidden'; // Κλειδώνει το scroll στο body
    });

    // 2. Κλείσιμο Mobile Menu
    closeBtn.addEventListener('click', () => {
        overlay.classList.remove('active');
        document.body.style.overflow = 'auto'; // Επαναφέρει το scroll
    });

    // 3. Dropdown στο Mobile (Accordion style)
    if (mobServiceTrigger) {
        mobServiceTrigger.addEventListener('click', (e) => {
            e.preventDefault();
            mobServiceList.classList.toggle('open');
            // Περιστροφή του βέλους
            mobServiceTrigger.querySelector('i').classList.toggle('rotate-icon');
        });
    }

    // 4. Κλείσιμο του μενού αν πατηθεί κάποιο Link (για one-page navigation)
    document.querySelectorAll('.mobile-link:not(#mobServiceTrigger)').forEach(link => {
        link.addEventListener('click', () => {
            overlay.classList.remove('active');
            document.body.style.overflow = 'auto';
        });
    });
});