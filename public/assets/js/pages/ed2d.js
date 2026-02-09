document.addEventListener("DOMContentLoaded", function () {
    const packageModal = document.getElementById('packageModal');

    // "Show more" logic for .packageText
    packageModal.addEventListener('shown.bs.modal', function () {
        document.querySelectorAll('.packageText').forEach(function (textElement) {
            const showMoreButton = textElement.nextElementSibling;
            const lineHeight = parseFloat(getComputedStyle(textElement).lineHeight);
            const maxHeight = lineHeight * 2;

            if (textElement.scrollHeight > maxHeight) {
                showMoreButton.style.display = 'inline-block';
                const newButton = showMoreButton.cloneNode(true);
                showMoreButton.parentNode.replaceChild(newButton, showMoreButton);
                newButton.addEventListener('click', function () {
                    if (textElement.classList.contains('expanded')) {
                        textElement.classList.remove('expanded');
                        newButton.textContent = 'Περισσότερα';
                        newButton.classList.remove('expanded');
                    } else {
                        textElement.classList.add('expanded');
                        newButton.textContent = 'Λιγότερα';
                        newButton.classList.add('expanded');
                    }
                });
            } else {
                showMoreButton.style.display = 'none';
            }
        });
    });


    // =========== 1) FETCH E-DIET PACKAGES =============
    const edietPackages = document.getElementById('edietPackages');
    let selectedPackageId = null;
    let selectedPackagePrice = 0.0;
    let selectedPackageTitle = '';

    function fetchEdietPackages() {
        edietPackages.innerHTML = '<p class="text-muted">Loading packages...</p>';
        const url = 'includes/ajax.php';
        const csrf_token = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || '';

        const formData = new FormData();
        formData.append('action', 'fetchEdietPackages'); // <-- your new action
        formData.append('csrf_token', csrf_token);

        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(resp => resp.json())
            .then(data => {
                if (!data.success) {
                    edietPackages.innerHTML = `<p class="text-danger">${data.errors ? data.errors.join('<br>') : 'Failed to load packages.'}</p>`;
                    return;
                }
                renderEdietPackages(data.packages);
            })
            .catch(err => {
                console.error('fetchEdietPackages error:', err);
                edietPackages.innerHTML = `<p class="text-danger">An error occurred while fetching e-diet packages.</p>`;
            });
    }

    function renderEdietPackages(packagesArr) {
        if (!packagesArr || packagesArr.length === 0) {
            edietPackages.innerHTML = `<p>No packages found.</p>`;
            return;
        }

        let html = '';
        packagesArr.forEach(pkg => {
            const rawPrice = pkg.price && parseFloat(pkg.price) > 0 ? parseFloat(pkg.price) : 0.0;
            const priceText = rawPrice > 0 ? `${rawPrice} €` : 'Κατόπιν Συνεννόησης';

            let includesHtml = '';
            if (pkg.includes) {
                try {
                    const arr = JSON.parse(pkg.includes);
                    if (Array.isArray(arr)) {
                        includesHtml = arr.map(i => `
                            <li><i class="bi bi-check text-success me-2"></i>${escapeHtml(i)}</li>
                        `).join('');
                    }
                } catch {
                    includesHtml = `<li><i class="bi bi-check text-success me-2"></i>${escapeHtml(pkg.includes)}</li>`;
                }
            }

            html += `
              <div class="col-12 text-center mb-4">
                <div class="packageCard">
                  <div class="packageHeader">
                    <h4 class="stitle m-0">${escapeHtml(pkg.title)}</h4>
                    <span class="packagePrice">${priceText}</span>
                  </div>
                  <div class="packageBody">
                    <div class="twrap">
                      <div class="packageText">
                        <p class="mainp m-0">Περιλαμβάνει:</p>
                        <ul class="list-unstyled my-2">
                          ${includesHtml || '<li>—</li>'}
                        </ul>
                      </div>
                      <span class="show-more mt-2" style="display: inline-block;">Περισσότερα</span>
                    </div>
                    <div class="packageCta mt-auto">
                      <input type="radio" name="package"
                             id="package${pkg.id}"
                             value="${pkg.id}"
                             data-package-price="${rawPrice}"
                             data-package-title="${escapeHtml(pkg.title)}"
                             class="btn-check" required>
                      <label class="btn btn-main mt-4 mt-md-2" for="package${pkg.id}">
                        Επιλογή
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            `;
        });
        edietPackages.innerHTML = html;
    }

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/[&<>"'/]/g, s => {
            switch (s) {
                case '&':
                    return '&amp;';
                case '<':
                    return '&lt;';
                case '>':
                    return '&gt;';
                case '"':
                    return '&quot;';
                case "'":
                    return '&#039;';
                case '/':
                    return '&#x2F;';
            }
        });
    }

    document.addEventListener('change', function (e) {
        if (e.target && e.target.name === 'package') {
            selectedPackageId = e.target.value;
            selectedPackagePrice = parseFloat(e.target.getAttribute('data-package-price')) || 0.0;
            selectedPackageTitle = e.target.getAttribute('data-package-title') || '';
        }
    });


    // =========== 2) PHONE VALIDATION WITH LIBPHONENUMBER =============
    function getAllCountryCodes() {
        const phoneUtil = libphonenumber.PhoneNumberUtil.getInstance();
        const supportedRegions = phoneUtil.getSupportedRegions();
        let countryCodes = [];
        supportedRegions.forEach(region => {
            const cc = phoneUtil.getCountryCodeForRegion(region);
            countryCodes.push({
                region: region,
                countryCode: cc
            });
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
            if (item.countryCode == "30") {
                option.selected = true;
            }
            select.appendChild(option);
        });
        const phoneNumberParent = document.getElementById('phoneNumber').parentNode;
        phoneNumberParent.prepend(select);
    }

    populateCountryCodesDropdown();

    const inputElement = document.getElementById('phoneNumber');
    if (inputElement) {
        inputElement.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9\+]/g, '');
        });
    }

    function validatePhoneNumber(phoneNumber) {
        const phoneUtil = libphonenumber.PhoneNumberUtil.getInstance();
        try {
            const number = phoneUtil.parse(phoneNumber, 'ZZ');
            return phoneUtil.isValidNumber(number);
        } catch (error) {
            console.error('Phone number validation error:', error);
            return false;
        }
    }


    // =========== 3) MULTI-STEP FORM LOGIC =============
    const steps = document.querySelectorAll('.step');
    const nextBtn = document.querySelector('.next-btn');
    const prevBtn = document.querySelector('.prev-btn');
    const submitBtn = document.querySelector('.submit-btn');
    const form = document.getElementById('eDietForm');
    const progressbar = document.querySelectorAll('.progressbar li');
    const generalError = document.getElementById('generalError');
    let currentStep = 0;
    let isAnimating = false;

    function showStep(index) {
        if (isAnimating) return;
        isAnimating = true;

        // Hide error
        generalError.classList.add('d-none');

        // Basic checks
        // Step 1 -> Step 2: Ensure a package is selected
        if (index === 1) {
            if (!selectedPackageId) {
                generalError.classList.remove('d-none');
                generalError.textContent = 'Παρακαλώ επιλέξτε ένα πακέτο.';
                isAnimating = false;
                return;
            }
        }

        // Step 2 -> Step 3: Validate phone
        if (index === 2) {
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

        // Validate HTML inputs
        if (!validateStep(currentStep)) {
            isAnimating = false;
            return;
        }

        // Move steps
        steps[currentStep].classList.remove('active');
        steps[index].classList.add('active');
        progressbar[currentStep].classList.remove('active');
        progressbar[index].classList.add('active');
        currentStep = index;

        prevBtn.style.display = currentStep === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = currentStep === steps.length - 1 ? 'none' : 'inline-block';
        submitBtn.style.display = currentStep === steps.length - 1 ? 'inline-block' : 'none';

        // If on step 4, fill confirmation details
        if (currentStep === 3) {
            document.getElementById('confirmInfo').style.display = 'block';
            document.getElementById('submitSuccess').classList.add('d-none');

            document.getElementById('confirmPackage').textContent = selectedPackageTitle || '—';
            const firstNameVal = (document.getElementById('firstName').value || '').trim();
            const lastNameVal = (document.getElementById('lastName').value || '').trim();
            const fullName = (firstNameVal + ' ' + lastNameVal).trim();
            document.getElementById('confirmName').textContent = fullName || 'Δεν έχει δοθεί όνομα';
            document.getElementById('confirmEmail').textContent = document.getElementById('email').value || 'No email provided';
            document.getElementById('confirmPhone').textContent = `(+${document.getElementById('countryCodes').value}) ${document.getElementById('phoneNumber').value}` || 'Δεν έχει δοθεί τηλέφωνο';

            // Price
            const confirmPriceElem = document.getElementById('confirmPrice');
            const payNowBtn = document.getElementById('payNowBtn');
            if (selectedPackagePrice > 0) {
                confirmPriceElem.textContent = selectedPackagePrice.toFixed(2) + ' €';
                payNowBtn.style.display = 'inline-block';

                // 1) Convert your numeric price to cents
                const priceCents = Math.round(selectedPackagePrice * 100);

                // 2) Initialize the embedded payform if not already done
                //    (If you want to re-init each time, that's fine. 
                //     Otherwise add a check so it doesn't re-init multiple times.)

                everypay.payform({
                    pk: 'pk_1pRyetzc0CbZBwvNVNXpdFafvspjcnk8', // your real key
                    amount: priceCents, // in cents
                    locale: 'el',
                    txnType: 'tds',
                    theme: 'default',
                    hidden: true, // <--- EMBED the form (not hidden)
                    data: {
                        email: document.getElementById("email").value,
                        // phone: document.getElementById("phoneNumber").value
                    },
                    display: {
                        button: false, // we hide the default EveryPay "Checkout" button
                        billing: true,
                        mobile: true
                    },
                    formOptions: {
                        border: '0',
                        size: 'lg'
                    }
                }, handlePaymentResponse);

                everypay.changeAmount(priceCents);
                everypay.showForm();
            } else {
                confirmPriceElem.textContent = 'Κατόπιν Συνεννόησης';
                payNowBtn.style.display = 'none';
            }
        }

        setTimeout(() => {
            isAnimating = false;
        }, 300);
    }

    function validateStep(stepIndex) {
        let stepValid = true;
        const stepEl = steps[stepIndex];
        if (!stepEl) return true;

        const inputs = stepEl.querySelectorAll('input, select, textarea');
        inputs.forEach(el => el.classList.remove('is-invalid'));

        inputs.forEach(el => {
            if (!el.checkValidity()) {
                stepValid = false;
                el.classList.add('is-invalid');
            }
        });

        if (!stepValid) {
            generalError.classList.remove('d-none');
            generalError.textContent = 'Παρακαλώ συμπληρώστε όλα τα απαιτούμενα πεδία.';
            const firstInvalid = stepEl.querySelector('.is-invalid');
            if (firstInvalid) firstInvalid.focus();
        }
        return stepValid;
    }

    nextBtn?.addEventListener('click', () => {
        if (currentStep < steps.length - 1) {
            showStep(currentStep + 1);
        }
    });
    prevBtn?.addEventListener('click', () => {
        if (currentStep > 0) {
            showStep(currentStep - 1);
        }
    });

    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!validateStep(currentStep)) return;
        console.log('Form submitted (no payment?).');
        // If package is 0 => user might do a "pay later"
        document.getElementById('confirmInfo').style.display = 'none';
        document.getElementById('submitSuccess').classList.remove('d-none');
    });

    // If modal closes, reset everything
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', function () {
            form.reset();
            steps.forEach(st => st.classList.remove('active'));
            steps[0].classList.add('active');
            progressbar.forEach((p, i) => {
                if (i === 0) p.classList.add('active');
                else p.classList.remove('active');
            });
            prevBtn.style.display = 'none';
            nextBtn.style.display = 'inline-block';
            submitBtn.style.display = 'none';
            currentStep = 0;
            generalError.classList.add('d-none');
            selectedPackageId = null;
            selectedPackagePrice = 0.0;
            selectedPackageTitle = '';
        });
    });


    // =========== 4) EVERYPAY EMBEDDED PAYMENT =============
    const payNowBtn = document.getElementById('payNowBtn');
    if (payNowBtn) {
        payNowBtn.addEventListener('click', function () {

            const termsCheckbox = document.getElementById('terms');
            if (!termsCheckbox.checked) {
                alert('Πρέπει να αποδεχτείτε τους όρους χρήσης για να συνεχίσετε.');
                return;
            }

            if (!selectedPackageId || selectedPackagePrice <= 0) {
                alert('No payable package selected.');
                return;
            }

            everypay.onClick()
        });
    }


    function handlePaymentResponse(r) {
        console.log('Payment response =>', r);
        if (r.response === 'success') {
            requestEdietBooking(r.token);
        } else {

        }
    }

    // Example: final booking submission
    function requestEdietBooking(token) {
        // Reference the form element
        const form = document.getElementById('eDietForm');
        if (!form) {
            console.error('Form with id "eDietForm" not found.');
            return;
        }

        // Create a new FormData object from the form
        const formData = new FormData(form);

        // Append additional necessary data
        formData.append('action', 'completeEdietBooking'); // Backend action
        formData.append('csrf_token', document.querySelector('meta[name="csrf_token"]').getAttribute('content') || '');

        // Append payment token from EveryPay
        formData.append('payment_token', token);

        // Append package details
        if (selectedPackageId) {
            formData.append('package_id', selectedPackageId);
            formData.append('price', selectedPackagePrice);
            formData.append('package_title', selectedPackageTitle);
        } else {
            formData.append('package_id', '');
            formData.append('price', '');
            formData.append('package_title', '');
        }

        // Process multiple checkbox values into comma-separated strings
        const femaleConditions = Array.from(form.querySelectorAll('input[name="femaleConditions"]:checked')).map(cb => cb.value);
        formData.set('femaleConditions', femaleConditions.join(',')); // Overwrite previous entries

        const homeMeals = Array.from(form.querySelectorAll('input[name="homeMeals"]:checked')).map(cb => cb.value);
        formData.set('homeMeals', homeMeals.join(',')); // Overwrite previous entries
        showLoader('.modal-body');
        // Send the serialized data to the backend
        fetch('includes/ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(res => {
                if (!res.success) {
                    // Handle errors: Display error messages to the user
                    console.error('e-Diet booking error:', res.errors);
                    document.getElementById('confirmInfo').style.display = 'none';
                    document.getElementById('submitFailed').classList.remove('d-none');
                    document.getElementById('failedMessage').textContent = res.errors ? res.errors.join('\n') : 'Unknown error';
                    hideLoader()
                    return;
                }

                // If success: Show success animation and hide form elements
                hideLoader()
                console.log('e-Diet booking success!', res);
                document.getElementById('confirmInfo').style.display = 'none';
                payNowBtn.disabled = true;
                document.getElementById('submitSuccess').classList.remove('d-none');

                // Optionally hide footer buttons and headers
                let modalFooters = document.getElementsByClassName('modal-footer');
                Array.from(modalFooters).forEach(el => el.classList.add('d-none'));

                let modalHeaders = document.getElementsByClassName('modal-header');
                Array.from(modalHeaders).forEach(el => el.classList.add('d-none'));

                // Hide sticky header
                let bodyHeads = document.getElementsByClassName('bodyhead');
                Array.from(bodyHeads).forEach(el => {
                    if (el.classList.contains('sticky-top')) {
                        el.classList.add('d-none');
                    }
                });

                // Adjust modal body if necessary
                let modalBody = document.querySelector('#packageModal .modal-body');
                if (modalBody) {
                    modalBody.style.maxHeight = '100%';
                }

                // Optionally redirect after a delay
                // setTimeout(() => window.location.href = 'thank-you.php', 4000);
            })
            .catch(err => {
                console.error('Network / fetch error:', err);
                // Optionally display a generic error message
                document.getElementById('confirmInfo').style.display = 'none';
                document.getElementById('submitFailed').classList.remove('d-none');
                document.getElementById('failedMessage').textContent = 'Σφάλμα δικτύου. Παρακαλώ προσπαθήστε ξανά αργότερα.';
            });
    }

    // Finally, **fetch** e-Diet packages on load:
    fetchEdietPackages();

});