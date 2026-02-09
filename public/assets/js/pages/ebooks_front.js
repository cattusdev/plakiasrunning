document.addEventListener("DOMContentLoaded", function () {
    const listContainer = document.getElementById('ebooksList');
    const modalElement = document.getElementById('ebookModal');

    if (!modalElement) return;

    const modal = new bootstrap.Modal(modalElement);
    const form = document.getElementById('ebookForm');

    // Selectors EXACTLY like ed2d.js
    const steps = modalElement.querySelectorAll('.step');
    const progressbar = modalElement.querySelectorAll('.progressbar li');
    const nextBtn = modalElement.querySelector('.next-btn');
    const prevBtn = modalElement.querySelector('.prev-btn');
    const payNowBtn = document.getElementById('payNowBtn');
    const generalError = document.getElementById('generalError');

    let currentStep = 0;
    let currentProduct = null;
    let isAnimating = false;

    // =========== 1) LIBPHONENUMBER =============
    function getAllCountryCodes() {
        const phoneUtil = libphonenumber.PhoneNumberUtil.getInstance();
        const supportedRegions = phoneUtil.getSupportedRegions();
        let countryCodes = [];
        supportedRegions.forEach(region => {
            const cc = phoneUtil.getCountryCodeForRegion(region);
            countryCodes.push({ region: region, countryCode: cc });
        });
        return countryCodes;
    }

    function populateCountryCodesDropdown() {
        const countryCodes = getAllCountryCodes();
        const select = document.createElement('select');
        select.id = 'countryCodes';
        select.classList.add('form-select', 'm-0');
        select.style.width = 'auto';

        countryCodes.forEach(item => {
            const option = document.createElement('option');
            option.value = item.countryCode;
            option.textContent = `${item.region} +(${item.countryCode})`;
            if (item.countryCode == "30") option.selected = true;
            select.appendChild(option);
        });

        const phoneInput = document.getElementById('phoneNumber');
        if (phoneInput && phoneInput.parentNode) {
            phoneInput.parentNode.prepend(select);
        }
    }

    populateCountryCodesDropdown();

    const phoneInput = document.getElementById('phoneNumber');
    if (phoneInput) {
        phoneInput.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9\+]/g, '');
        });
    }

    function validatePhoneNumber(phoneNumber) {
        const phoneUtil = libphonenumber.PhoneNumberUtil.getInstance();
        try {
            const number = phoneUtil.parse(phoneNumber, 'ZZ');
            return phoneUtil.isValidNumber(number);
        } catch (error) {
            return false;
        }
    }

    // =========== 2) GLOBAL OPENER =============
    window.openPurchaseModal = function (product) {
        currentProduct = product;
        form.reset();

        // Reset Logic - Manually reset UI
        currentStep = 0;
        generalError.classList.add('d-none');

        steps.forEach((el, i) => {
            el.classList.remove('active');
            if (i === 0) el.classList.add('active');
        });
        progressbar.forEach((el, i) => {
            el.classList.remove('active');
            if (i === 0) el.classList.add('active');
        });

        // Reset Buttons visibility for step 0
        prevBtn.style.display = 'none';
        nextBtn.style.display = 'inline-block';
        if (payNowBtn) payNowBtn.style.display = 'none';

        // Fill Info
        const titleEl = modalElement.querySelector('#selectedTitle');
        const priceEl = modalElement.querySelector('#selectedPrice');
        const priceEl2 = modalElement.querySelector('#selectedPrice2');
        if (titleEl) titleEl.textContent = product.title;
        if (priceEl) priceEl.textContent = parseFloat(product.price).toFixed(2) + ' €';
        if (priceEl2) priceEl2.textContent = parseFloat(product.price).toFixed(2) + ' €';

        modal.show();
    }

    // =========== 3) STEP LOGIC (Identical to ed2d.js) =============

    function showStep(index) {
        if (isAnimating) return;
        isAnimating = true;

        generalError.classList.add('d-none');

        // Validate if moving forward
        if (index > currentStep) {
            // Step 0 check (Info + Phone)
            if (currentStep === 0) {
                if (!validateStep(0)) {
                    isAnimating = false;
                    return;
                }
                const ccVal = document.getElementById('countryCodes').value;
                const phoneVal = document.getElementById('phoneNumber').value.trim();
                const phoneFull = `+${ccVal}${phoneVal}`;

                if (!validatePhoneNumber(phoneFull)) {
                    generalError.classList.remove('d-none');
                    generalError.textContent = 'Παρακαλώ εισάγετε έναν έγκυρο αριθμό τηλεφώνου.';
                    isAnimating = false;
                    return;
                }
            }
        }

        // Toggle Steps
        steps[currentStep].classList.remove('active');
        steps[index].classList.add('active');
        progressbar[currentStep].classList.remove('active');
        progressbar[index].classList.add('active');

        currentStep = index;

        // Button Visibility Logic (Matched exactly to ed2d.js)
        prevBtn.style.display = currentStep === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = currentStep === steps.length - 1 ? 'none' : 'inline-block';

        // Show Pay Button ONLY on last step (which is index 1 here)
        if (currentStep === steps.length - 1) {
            if (payNowBtn) payNowBtn.style.display = 'inline-block';
            // Init EveryPay
            initEveryPay(currentProduct.price);
        } else {
            if (payNowBtn) payNowBtn.style.display = 'none';
        }

        setTimeout(() => { isAnimating = false; }, 300);
    }

    function validateStep(index) {
        let isValid = true;
        const currentEl = steps[index];
        const inputs = currentEl.querySelectorAll('input, select');
        inputs.forEach(el => el.classList.remove('is-invalid'));

        inputs.forEach(el => {
            if (!el.checkValidity()) {
                isValid = false;
                el.classList.add('is-invalid');
            }
        });

        if (!isValid) {
            generalError.classList.remove('d-none');
            generalError.textContent = 'Παρακαλώ συμπληρώστε όλα τα πεδία.';
        }
        return isValid;
    }

    if (nextBtn) {
        nextBtn.addEventListener('click', function () {
            if (currentStep < steps.length - 1) {
                showStep(currentStep + 1);
            }
        });
    }
    if (prevBtn) {
        prevBtn.addEventListener('click', function () {
            if (currentStep > 0) {
                showStep(currentStep - 1);
            }
        });
    }

    modalElement.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            form.reset();
            // Full Reset UI
            steps.forEach(s => s.classList.remove('active'));
            steps[0].classList.add('active');
            progressbar.forEach(p => p.classList.remove('active'));
            progressbar[0].classList.add('active');
            currentStep = 0;

            prevBtn.style.display = 'none';
            nextBtn.style.display = 'inline-block';
            if (payNowBtn) payNowBtn.style.display = 'none';

            // Revert Success UI (Show headers/footers again)
            let modalFooters = document.getElementsByClassName('modal-footer');
            Array.from(modalFooters).forEach(el => el.classList.remove('d-none'));
            let modalHeaders = document.getElementsByClassName('modal-header');
            Array.from(modalHeaders).forEach(el => el.classList.remove('d-none'));
            let bodyHeads = document.getElementsByClassName('bodyhead');
            Array.from(bodyHeads).forEach(el => el.classList.remove('d-none'));

            document.getElementById('confirmInfo').style.display = 'block';
            document.getElementById('submitSuccess').classList.add('d-none');
        });
    });

    // =========== 4) EVERYPAY LOGIC =============

    function initEveryPay(amount) {
        const container = document.getElementById('pay-form');
        if (!container) return;
        container.innerHTML = '';

        const amountCents = Math.round(amount * 100);

        everypay.payform({
            pk: 'pk_1pRyetzc0CbZBwvNVNXpdFafvspjcnk8',
            amount: amountCents,
            locale: 'el',
            txnType: 'tds',
            theme: 'default',
            hidden: true,
            display: { button: false, billing: true, mobile: true },
            formOptions: { border: '0', size: 'md' },
            data: { email: document.getElementById("email").value }
        }, handlePaymentResponse);

        everypay.changeAmount(amountCents);
        everypay.showForm();
    }

    if (payNowBtn) {
        payNowBtn.addEventListener('click', function () {
            const terms = document.getElementById('terms');
            if (terms && !terms.checked) {
                alert("Πρέπει να αποδεχτείτε τους όρους χρήσης.");
                return;
            }
            everypay.onClick();
        });
    }

    function handlePaymentResponse(r) {
        if (r.response === 'success') {
            completePurchase(r.token);
        }
    }

    function completePurchase(token) {
        if (payNowBtn) {
            payNowBtn.disabled = true;
            payNowBtn.textContent = 'Επεξεργασία...';
        }

        // SHOW LOADER
        if (typeof showLoader === 'function') showLoader('.modal-body');

        const formData = new FormData(form);
        formData.append('action', 'purchaseDigitalProduct');
        const metaToken = document.querySelector('meta[name="csrf_token"]');
        if (metaToken) formData.append('csrf_token', metaToken.getAttribute('content'));

        formData.append('payment_token', token);
        formData.append('product_id', currentProduct.id);

        fetch('includes/ajax.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(res => {
                // HIDE LOADER
                if (typeof hideLoader === 'function') hideLoader();

                if (res.success) {
                    // 1. Κρύβουμε την αρχική φόρμα
                    document.getElementById('confirmInfo').style.display = 'none';
                    if (payNowBtn) payNowBtn.disabled = true;

                    // 2. Εμφανίζουμε το Success Div (που περιέχει το κουμπί Reload)
                    document.getElementById('submitSuccess').classList.remove('d-none');

                    // 3. Κρύβουμε Headers/Footers (ώστε να μην μπορεί να κλείσει αλλιώς)
                    let modalFooters = document.getElementsByClassName('modal-footer');
                    Array.from(modalFooters).forEach(el => el.classList.add('d-none'));

                    let modalHeaders = document.getElementsByClassName('modal-header');
                    Array.from(modalHeaders).forEach(el => el.classList.add('d-none'));

                    let bodyHeads = document.getElementsByClassName('bodyhead');
                    Array.from(bodyHeads).forEach(el => {
                        if (el.classList.contains('sticky-top')) el.classList.add('d-none');
                    });

                    // 4. Προσαρμογή ύψους
                    let modalBody = document.querySelector('#ebookModal .modal-body');
                    if (modalBody) modalBody.style.maxHeight = '100%';

                } else {
                    alert('Σφάλμα: ' + (res.errors ? res.errors.join(', ') : 'Άγνωστο σφάλμα'));
                    if (payNowBtn) {
                        payNowBtn.disabled = false;
                        payNowBtn.textContent = 'Πληρωμή';
                    }
                }
            })
            .catch(err => {
                if (typeof hideLoader === 'function') hideLoader();
                console.error(err);
                alert("Σφάλμα δικτύου.");
                if (payNowBtn) {
                    payNowBtn.disabled = false;
                    payNowBtn.textContent = 'Πληρωμή';
                }
            });
    }

    // =========== 5) FETCH PRODUCTS =============
    function loadProducts() {
        if (!listContainer) return;
        const formData = new FormData();
        formData.append('action', 'fetchPublicDigitalProducts');
        const metaToken = document.querySelector('meta[name="csrf_token"]');
        if (metaToken) formData.append('csrf_token', metaToken.getAttribute('content'));

        fetch('includes/ajax.php', { method: 'POST', body: formData })
            .then(r => r.json())
            .then(data => {
                if (data.success && data.products.length > 0) {
                    renderGrid(data.products);
                } else {
                    listContainer.innerHTML = '<div class="col-12 text-center">Δεν βρέθηκαν eBooks.</div>';
                }
            })
            .catch(err => console.error(err));
    }

    function renderGrid(products) {
        let html = '';
        products.forEach(prod => {
            let imgHtml = prod.cover_image
                ? `<img src="/${prod.cover_image}" class="card-img-top" alt="${prod.title}">`
                : `<i class="bi bi-book fa-5x text-secondary opacity-25" style="position:relative; z-index:1;"></i>`;

            let descHtml = prod.description
                ? `<p class="text-muted small mb-3" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 2.6em; line-height: 1.3;">${prod.description}</p>`
                : `<p class="mb-3" style="height: 2.6em;"></p>`;

            html += `
            <div class="col-md-6 col-lg-4 col-xl-3 mb-4">
                <div class="ebook-card">
                    
                    <div class="card-img-wrapper">
                        ${imgHtml}
                    </div>

                    <div class="card-body-custom">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <span class="badge bg-light text-secondary rounded-pill px-3 py-1 fw-normal" style="font-size: 0.75rem;">E-BOOK</span>
                        </div>

                        <h5 class="card-title text-truncate-2" style="min-height: 2.5em; margin-bottom: 0.5rem;">${prod.title}</h5>
                        
                        ${descHtml}
                        
                        <div class="mt-auto pt-2 d-flex align-items-center justify-content-between">
                            <div class="d-flex flex-column">
                                <span class="text-muted small" style="font-size: 0.8rem;">Τιμή</span>
                                <span class="price-tag">${parseFloat(prod.price).toFixed(2)} €</span>
                            </div>

                            <button class="btn-card-action shadow-sm" onclick='window.openPurchaseModal(${JSON.stringify(prod)})'>
                                <span>Αγορά</span>
                                <i class="bi bi-bag"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>`;
        });
        listContainer.innerHTML = html;
    }
    loadProducts();
});