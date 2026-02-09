document.addEventListener("DOMContentLoaded", (event) => {
    const sessionModal = document.getElementById('sessionModal');

    function disableBodyScroll() {
        document.body.style.overflow = 'hidden';
        document.documentElement.style.overflow = 'hidden';
    }

    // Function to enable body scroll
    function enableBodyScroll() {
        document.body.style.overflow = 'auto';
        document.documentElement.style.overflow = 'auto';
    }

    // Add event listener for when the modal is fully shown
    sessionModal.addEventListener('shown.bs.modal', function () {
        disableBodyScroll();
        // Query and process the `.packageText` elements
        document.querySelectorAll('.packageText').forEach(function (textElement) {
            const showMoreButton = textElement.nextElementSibling;

            // Check if content exceeds 3 lines
            const lineHeight = parseFloat(getComputedStyle(textElement).lineHeight);
            const maxHeight = lineHeight * 2;

            if (textElement.scrollHeight > maxHeight) {
                showMoreButton.style.display = 'inline-block'; // Show the button if content overflows

                // Remove existing event listeners to prevent duplicates
                const newButton = showMoreButton.cloneNode(true);
                showMoreButton.parentNode.replaceChild(newButton, showMoreButton);

                // Add event listener for toggle functionality
                newButton.addEventListener('click', function () {
                    if (textElement.classList.contains('expanded')) {
                        textElement.classList.remove('expanded');
                        newButton.textContent = 'Περισσότερα';
                        newButton.classList.remove('expanded'); // Remove expanded class for chevron
                    } else {
                        textElement.classList.add('expanded');
                        newButton.textContent = 'Λιγότερα';
                        newButton.classList.add('expanded'); // Add expanded class for chevron
                    }
                });
            } else {
                showMoreButton.style.display = 'none'; // Hide the button if content fits
            }
        });
    });

    sessionModal.addEventListener('hidden.bs.modal', function () {
        enableBodyScroll();
    });



    const onlineTab = document.getElementById('onlineTab');
    const inPersonTab = document.getElementById('inPersonTab');
    const onlineContent = document.getElementById('onlineContent');
    const inPersonContent = document.getElementById('inPersonContent');
    const sessionsCards = document.getElementById('sessionsCards');

    let selectedPackageId = null;
    let selectedPackagePrice = 0.0;
    let selectedPackageTitle = '';
    let selectedSlotType = null;

    onlineTab.addEventListener('click', () => {
        onlineContent.style.display = 'block';
        inPersonContent.style.display = 'none';
        inPersonTab.classList.remove('btn-active');
        onlineTab.classList.add('btn-active');
        selectedSlotType = 'online';
        fetchAndRenderPackages('online');
    });

    inPersonTab.addEventListener('click', () => {
        onlineContent.style.display = 'none';
        inPersonContent.style.display = 'block';
        onlineTab.classList.remove('btn-active');
        inPersonTab.classList.add('btn-active');
        selectedSlotType = 'inPerson';
        fetchAndRenderPackages('inPerson');
    });

    document.addEventListener("DOMContentLoaded", () => {
        // Get the current URL hash
        const urlHash = window.location.hash;

        // Check the hash and select the appropriate button
        if (urlHash === "#online") {
            onlineTab.classList.add("btn-active");
            onlineTab.click();
        } else if (urlHash === "#inPerson") {
            inPersonTab.classList.add("btn-active");
            inPersonTab.click();
        }


    });

    function fetchAndRenderPackages(type) {
        sessionsCards.innerHTML = '<p class="text-muted">Loading packages...</p>';
        const url = 'includes/ajax.php';
        const csrf_token = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || '';

        const formData = new FormData();
        formData.append('action', 'fetchPackagesByType');
        formData.append('csrf_token', csrf_token);
        formData.append('type', type);

        fetch(url, {
            method: 'POST',
            body: formData
        })
            .then(resp => resp.json())
            .then(data => {
                if (data.success) {
                    renderPackages(data.packages);
                } else {
                    sessionsCards.innerHTML = `<p class="text-danger">${data.errors.join('<br>')}</p>`;
                }
            })
            .catch(err => {
                console.error('Fetch error:', err);
                sessionsCards.innerHTML = `<p class="text-danger">An error occurred while fetching packages.</p>`;
            });
    }

    function renderPackages(packagesArr) {
        if (!packagesArr || packagesArr.length === 0) {
            sessionsCards.innerHTML = `<p>No packages found.</p>`;
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
                        includesHtml = arr.map(i => `<li><i class="bi bi-check text-success me-2"></i>${escapeHtml(i)}</li>`).join('');
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
        sessionsCards.innerHTML = html;
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


    function handlePaymentResponse(r) {
        console.log('Payment response => ', r);
        if (r.response === 'success') {
            // Payment authorized => call your final AJAX or function:
            requestReservation(r.token);
        } else { }
    }

    function requestReservation(token) {
        // 1) Gather the data from your form:
        const csrf_token = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || '';

        // Example: from your step 2 fields
        const firstName = document.getElementById('firstName').value.trim();
        const lastName = document.getElementById('lastName').value.trim();
        const phone = document.getElementById('phoneNumber').value.trim();
        const email = document.getElementById('email').value.trim();

        // Example: from your step 3 selection
        // You might store the selected slot ID or date/time in `selectedTimeSlot`.
        // The backend might require a `slot_id`.
        const slotId = selectedTimeSlot ? selectedTimeSlot.slot_id : null;

        // Example: from step 1 (package selection)
        // You already track selectedPackageId, selectedPackagePrice, etc.
        const packageId = selectedPackageId;

        // 2) Prepare data to send
        const fd = new FormData();
        fd.append('action', 'completeBooking'); // We'll create a "completeBooking" case on the backend
        fd.append('csrf_token', csrf_token);

        fd.append('fname', firstName);
        fd.append('lname', lastName);
        fd.append('phone', phone);
        fd.append('email', email);

        fd.append('slot_id', slotId);
        fd.append('package_id', packageId);

        // The EveryPay token from payment success:
        fd.append('payment_token', token);

        // The price (in cents or euros), if needed. 
        // Or let the backend recalculate to avoid tampering.
        // fd.append('price', selectedPackagePrice);
        showLoader('.modal-body');
        // 3) Post to your backend
        fetch('includes/ajax.php', {
            method: 'POST',
            body: fd
        })
            .then(resp => resp.json())
            .then(res => {
                if (!res.success) {
                    // Show error(s) to the user
                    let prevBtns = document.querySelectorAll('.prev-btn');
                    prevBtns.forEach(el => el.disabled = true);

                    // Hide all modal footers
                    let modalFooters = document.getElementsByClassName('modal-footer');
                    Array.from(modalFooters).forEach(el => el.classList.add('d-none'));

                    let modalHeaders = document.getElementsByClassName('modal-header');
                    Array.from(modalHeaders).forEach(el => el.classList.add('d-none'));

                    // Hide all bodyhead sticky-top elements
                    let bodyHeads = document.getElementsByClassName('bodyhead');
                    Array.from(bodyHeads).forEach(el => {
                        if (el.classList.contains('sticky-top')) {
                            el.classList.add('d-none');
                        }
                    });

                    // Set max-height to auto for #sessionModal .modal-body
                    let modalBody = document.querySelector('#sessionModal .modal-body');
                    if (modalBody) {
                        modalBody.style.maxHeight = '100%';
                    }
                    hideLoader();

                    document.getElementById('payNowBtn').disabled = true;
                    console.error('Booking creation error:', res.errors);
                    document.getElementById('confirmInfo').style.display = 'none';
                    document.getElementById('submitFailed').classList.remove('d-none');
                    document.getElementById('failedMessage').textContent = (res.errors ? res.errors.join('\n') : 'Unknown error');
                    return;
                }

                // If success => show success animation
                document.getElementById('confirmInfo').style.display = 'none';
                document.getElementById('payNowBtn').disabled = true;

                let prevBtns = document.querySelectorAll('.prev-btn');
                prevBtns.forEach(el => el.disabled = true);

                // Hide all modal footers
                let modalFooters = document.getElementsByClassName('modal-footer');
                Array.from(modalFooters).forEach(el => el.classList.add('d-none'));

                let modalHeaders = document.getElementsByClassName('modal-header');
                Array.from(modalHeaders).forEach(el => el.classList.add('d-none'));

                // Hide all bodyhead sticky-top elements
                let bodyHeads = document.getElementsByClassName('bodyhead');
                Array.from(bodyHeads).forEach(el => {
                    if (el.classList.contains('sticky-top')) {
                        el.classList.add('d-none');
                    }
                });

                // Set max-height to auto for #sessionModal .modal-body
                let modalBody = document.querySelector('#sessionModal .modal-body');
                if (modalBody) {
                    modalBody.style.maxHeight = '100%';
                }
                hideLoader();
                document.getElementById('submitSuccess').classList.remove('d-none');
                console.log('Booking success! Server response:', res);

                // Optional redirect after delay
                // setTimeout(() => window.location.href = 'thank-you.php', 4000);
            })
            .catch(err => {
                console.error('Network / fetch error:', err);
            });
    }

    function retCents(amount) {
        let str = amount.replace(',', '').replace('.', '')
        return str;
    }


    // --------------------------------
    //    MULTISTEP FORM LOGIC
    // --------------------------------

    // Populate Country Codes Dropdown
    function getAllCountryCodes() {
        var phoneUtil = libphonenumber.PhoneNumberUtil.getInstance();
        var supportedRegions = phoneUtil.getSupportedRegions();
        var countryCodes = [];

        supportedRegions.forEach(function (region) {
            var countryCode = phoneUtil.getCountryCodeForRegion(region);
            countryCodes.push({
                region: region,
                countryCode: countryCode
            });
        });

        return countryCodes;
    }

    function populateCountryCodesDropdown() {
        var countryCodes = getAllCountryCodes();
        var select = $('<select id="countryCodes" class="form-select m-0" style="width:auto;"></select>');

        countryCodes.forEach(function (item) {
            var option = $('<option></option>')
                .attr('value', item.countryCode)
                .text(`${item.region} +(${item.countryCode})`);

            if (item.countryCode == "30") { // Default to Greece
                option.attr('selected', 'selected');
            }
            select.append(option);
        });
        $('#phoneNumber').parent().prepend(select);
    }

    populateCountryCodesDropdown();

    // Restrict Phone Number Input
    var inputElement = document.getElementById('phoneNumber');
    if (inputElement) {
        inputElement.addEventListener('input', function () {
            this.value = this.value.replace(/[^0-9\+]/g, '');
        });
    }

    // Phone Number Validation
    function validatePhoneNumber(phoneNumber) {
        var phoneUtil = libphonenumber.PhoneNumberUtil.getInstance();
        try {
            var number = phoneUtil.parse(phoneNumber, 'ZZ');
            var isValid = phoneUtil.isValidNumber(number);
            console.log(`Phone Number: ${phoneNumber} | Valid: ${isValid}`);
            return isValid;
        } catch (error) {
            console.error('Phone number validation error:', error);
            return false;
        }
    }

    let selectedTimeSlot = null;
    const steps = document.querySelectorAll('.step');
    const nextBtn = document.querySelector('.next-btn');
    const prevBtn = document.querySelector('.prev-btn');
    const submitBtn = document.querySelector('.submit-btn');
    const form = document.getElementById('eSessions');
    const progressbar = document.querySelectorAll('.progressbar li');
    const generalError = document.getElementById('generalError');
    let currentStep = 0;
    let isAnimating = false;

    function showStep(index) {
        // Additional checks:
        // If going to step=1 => means user is leaving step 0; we skip that because step 0 is the default
        if (index === 1) {
            if (!selectedPackageId) {
                generalError.classList.remove('d-none');
                generalError.textContent = 'Παρακαλώ επιλέξτε ένα πακέτο.';
                return;
            }
        }

        if (index === 2) {
            var countryCode = $('#countryCodes').val();
            var phoneNumberInput = $('#phoneNumber').val().trim();
            var phoneNumber = '+' + countryCode + phoneNumberInput;

            if (!validatePhoneNumber(phoneNumber)) {
                generalError.classList.remove('d-none');
                generalError.textContent = 'Παρακαλώ εισάγετε έναν έγκυρο αριθμό τηλεφώνου.';
                return;
            } else {
                generalError.classList.add('d-none');
            }

            // Fetch slots for the selected package
            const selectedPackageInput = document.querySelector('input[name="package"]:checked');
            if (selectedPackageInput) {
                const packageId = selectedPackageInput.value;
                fetchSlotsAndRender(packageId);
            }
        }

        // If going to step=2 => personal info (we can do phone validation, etc. or rely on validateStep)
        // If going to step=3 => must have selected time slot
        if (index === 3) {
            if (!selectedTimeSlot) {
                generalError.classList.remove('d-none');
                generalError.textContent = 'Παρακαλώ επιλέξτε μια χρονική στιγμή.';
                return;
            }
        }

        // Also do basic step validation
        if (!validateStep(currentStep)) {
            return;
        }

        if (isAnimating) return;
        isAnimating = true;
        generalError.classList.add('d-none');

        steps[currentStep].classList.remove('active');
        steps[index].classList.add('active');
        progressbar[currentStep].classList.remove('active');
        progressbar[index].classList.add('active');
        currentStep = index;

        prevBtn.style.display = currentStep === 0 ? 'none' : 'inline-block';
        nextBtn.style.display = currentStep === steps.length - 1 ? 'none' : 'inline-block';
        submitBtn.style.display = currentStep === steps.length - 1 ? 'inline-block' : 'none';

        // If on step 4: Show final info + Payment button
        if (currentStep === 3) {
            // Show final details
            document.getElementById('confirmInfo').style.display = 'block';
            document.getElementById('submitSuccess').classList.add('d-none');

            document.getElementById('confirmPackage').textContent = selectedPackageTitle || '—';
            const firstNameVal = (document.getElementById('firstName').value || '').trim();
            const lastNameVal = (document.getElementById('lastName').value || '').trim();
            const fullName = (firstNameVal + ' ' + lastNameVal).trim();
            document.getElementById('confirmName').textContent = fullName || 'Δεν έχει δοθεί όνομα';
            document.getElementById('confirmEmail').textContent = document.getElementById('email').value || 'Δεν έχει δοθεί email';
            document.getElementById('confirmPhone').textContent = `(+${document.getElementById('countryCodes').value}) ${document.getElementById('phoneNumber').value}` || 'Δεν έχει δοθεί τηλέφωνο';
            document.getElementById('confirmDate').textContent = `${selectedTimeSlot.date.toDateString()} στις ${selectedTimeSlot.time}` || 'Δεν έχει δοθεί ημερομινία';

            // Price
            const priceSpan = document.getElementById('confirmPrice');
            priceSpan.textContent = (selectedPackagePrice > 0) ?
                selectedPackagePrice.toFixed(2) + ' €' :
                'Κατόπιν Συνεννόησης';

            // Now initialize or show the embedded form if price > 0
            const payNowBtn = document.getElementById('payNowBtn');
            if (selectedPackagePrice > 0) {
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

                // everypay.changeAmount(priceCents);
                everypay.showForm();

            } else {
                // Price is 0 => user won't pay online
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

        // Basic HTML5 validation on required fields
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

    // If "submit" is used as fallback
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        if (!validateStep(currentStep)) return;
        // If you want to do something if there's no payment...
        console.log('Form submitted without payment?');
    });

    // If modal closes, reset everything
    document.querySelectorAll('.close-modal').forEach(btn => {
        btn.addEventListener('click', () => {
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
            selectedTimeSlot = null;
        });
    });



    // ------------------------------------------------
    //           CALENDAR & SLOTS LOGIC
    // ------------------------------------------------

    let currentDate = new Date();
    let weekDates = [];
    let selectedDateIndex = 0;
    const today = new Date();
    let timeSlotsData = {};
    let slotIdsByDateLabel = {};
    let maxSlotDate = null;

    function getWeekDates(date) {
        const week = [];
        const firstDayOfWeek = new Date(date);
        firstDayOfWeek.setDate(date.getDate() - date.getDay());
        for (let i = 0; i < 7; i++) {
            const wd = new Date(firstDayOfWeek);
            wd.setDate(firstDayOfWeek.getDate() + i);
            week.push(wd);
        }
        return week;
    }

    // 1)  RENDER DATE NAV
    function renderDateNav() {
        const dateNav = document.getElementById('dateNav');
        dateNav.innerHTML = '';

        weekDates.forEach((date, index) => {
            const dateStr = date.toISOString().split('T')[0];
            const btn = document.createElement('button');
            btn.classList.add('btn', 'btn-outline-secondary');
            btn.setAttribute('type', 'button');

            const day = date.toDateString().slice(0, 3);
            const monthDate = date.toDateString().slice(4, 10);
            btn.innerHTML = `${day}<br>${monthDate}`;

            // Disable past days
            if (date < new Date().setHours(0, 0, 0, 0)) {
                btn.disabled = true;
            }

            if (index === selectedDateIndex) {
                btn.classList.add('active');
            }
            btn.addEventListener('click', () => selectDate(index));

            const daySlots = timeSlotsData[dateStr];
            let hasFutureSlots = false;

            if (daySlots) {
                // Filter out times in the past
                const futureMorning = daySlots.morning?.filter(t => !isTimeInPast(date, t)) || [];
                const futureAfternoon = daySlots.afternoon?.filter(t => !isTimeInPast(date, t)) || [];
                hasFutureSlots = (futureMorning.length > 0 || futureAfternoon.length > 0);
            }

            // If either the date has no slot object or there are zero future slots, disable the button:
            if (!hasFutureSlots) {
                btn.disabled = true;
            }

            dateNav.appendChild(btn);
        });

        // If at the current week, disable "Prev"
        const prevWeekBtn = document.getElementById('prevWeekBtn');
        if (isCurrentWeek()) {
            prevWeekBtn.disabled = true;
        } else {
            prevWeekBtn.disabled = false;
        }

        // If beyond the last available week, also disable "Next" 
        // (meaning if first day of current week is >= the last possible week).
        // We'll do that check in `toggleNextWeekButton()`.
        toggleNextWeekButton();
    }

    const maxBrowseDate = new Date();
    maxBrowseDate.setMonth(maxBrowseDate.getMonth() + 2);

    function toggleNextWeekButton() {
        const nextWeekBtn = document.getElementById('nextWeekBtn');
        if (!nextWeekBtn) return;

        if (!maxBrowseDate) {
            nextWeekBtn.disabled = false;
            return;
        }

        const nextWeekStart = new Date(weekDates[0]);
        nextWeekStart.setDate(nextWeekStart.getDate() + 7);

        if (nextWeekStart > maxBrowseDate) {
            nextWeekBtn.disabled = true;
        } else {
            nextWeekBtn.disabled = false;
        }
    }

    function isCurrentWeek() {
        const firstDayOfCurrentWeek = new Date();
        firstDayOfCurrentWeek.setDate(firstDayOfCurrentWeek.getDate() - firstDayOfCurrentWeek.getDay());
        firstDayOfCurrentWeek.setHours(0, 0, 0, 0);

        const firstDayOfWeekDates = new Date(weekDates[0]);
        firstDayOfWeekDates.setHours(0, 0, 0, 0);

        return firstDayOfWeekDates.getTime() === firstDayOfCurrentWeek.getTime();
    }

    // 2)  SELECT A DATE
    function selectDate(index) {
        selectedDateIndex = index;
        renderDateNav();
        const selectedDate = weekDates[index];
        renderTimeSlots(selectedDate);
        updateSelectedSlotLabel();
    }

    // 3)  RENDER TIME SLOTS
    function renderTimeSlots(date) {
        const dateStr = date.toISOString().split('T')[0];
        const slots = timeSlotsData[dateStr];

        console.log(timeSlotsData);
        const morningSlots = document.getElementById('morningSlots');
        const afternoonSlots = document.getElementById('afternoonSlots');
        morningSlots.innerHTML = '';
        afternoonSlots.innerHTML = '';

        // We'll skip displaying times that are in the past
        // so we only show future times for today.
        if (slots) {
            // MORNING
            if (slots.morning && slots.morning.length > 0) {
                // Filter out times that are already in the past
                const futureMorning = slots.morning.filter(t => !isTimeInPast(date, t));

                if (futureMorning.length > 0) {
                    futureMorning.forEach(time => {
                        const col = document.createElement('div');
                        col.classList.add('col-3');
                        const btn = document.createElement('button');
                        btn.classList.add('btn', 'btn-outline-secondary', 'w-100', 'mb-2');
                        btn.setAttribute('type', 'button');
                        btn.setAttribute('data-slotid', 'button');
                        btn.textContent = time;

                        const slotId = slotIdsByDateLabel[dateStr][time];
                        if (selectedTimeSlot &&
                            selectedTimeSlot.date.toDateString() === date.toDateString() &&
                            selectedTimeSlot.time === time) {
                            btn.classList.remove('btn-outline-secondary');
                            btn.classList.add('btn-success');
                            selectedTimeSlot.button = btn;
                        }
                        btn.addEventListener('click', () => selectTimeSlot(btn, time, slotId));
                        // btn.addEventListener('click', () => selectTimeSlot(btn, time));
                        col.appendChild(btn);
                        morningSlots.appendChild(col);
                    });
                } else {
                    const noSlotsMsg = document.createElement('p');
                    noSlotsMsg.textContent = 'Δεν υπάρχουν διαθέσιμα πρωινά ραντεβού στο μέλλον.';
                    morningSlots.appendChild(noSlotsMsg);
                }
            } else {
                const noSlotsMsg = document.createElement('p');
                noSlotsMsg.textContent = 'Δεν υπάρχουν διαθέσιμα πρωινά ραντεβού.';
                morningSlots.appendChild(noSlotsMsg);
            }

            // AFTERNOON
            if (slots.afternoon && slots.afternoon.length > 0) {
                const futureAfternoon = slots.afternoon.filter(t => !isTimeInPast(date, t));
                if (futureAfternoon.length > 0) {
                    futureAfternoon.forEach(time => {
                        const col = document.createElement('div');
                        col.classList.add('col-3');
                        const btn = document.createElement('button');
                        btn.setAttribute('type', 'button');
                        btn.classList.add('btn', 'btn-outline-secondary', 'w-100', 'mb-2');
                        btn.textContent = time;
                        const slotId = slotIdsByDateLabel[dateStr][time];
                        if (selectedTimeSlot &&
                            selectedTimeSlot.date.toDateString() === date.toDateString() &&
                            selectedTimeSlot.time === time) {
                            btn.classList.remove('btn-outline-secondary');
                            btn.classList.add('btn-success');
                            selectedTimeSlot.button = btn;
                        }
                        btn.addEventListener('click', () => selectTimeSlot(btn, time, slotId));
                        // btn.addEventListener('click', () => selectTimeSlot(btn, time));
                        col.appendChild(btn);
                        afternoonSlots.appendChild(col);
                    });
                } else {
                    const noSlotsMsg = document.createElement('p');
                    noSlotsMsg.textContent = 'Δεν υπάρχουν διαθέσιμα ραντεβού για το απόγευμα στο μέλλον.';
                    afternoonSlots.appendChild(noSlotsMsg);
                }
            } else {
                const noSlotsMsg = document.createElement('p');
                noSlotsMsg.textContent = 'Δεν υπάρχουν διαθέσιμα απογευματινά ραντεβού.';
                afternoonSlots.appendChild(noSlotsMsg);
            }
        } else {
            // No slots for this date
            const noSlotsMsg = document.createElement('p');
            noSlotsMsg.textContent = 'Δεν υπάρχουν διαθέσιμα ραντεβού για αυτή την ημερομηνία.';
            morningSlots.appendChild(noSlotsMsg.cloneNode(true));
            afternoonSlots.appendChild(noSlotsMsg.cloneNode(true));
        }
    }

    // 3a)  Check if time is in the past
    function isTimeInPast(dateObj, timeStr) {
        // dateObj is the full date (Date object)
        // timeStr is "HH:MM AM/PM"
        // Construct a new Date from these
        const [time, modifier] = timeStr.split(' ');
        let [hours, minutes] = time.split(':').map(Number);

        if (modifier === 'PM' && hours !== 12) {
            hours += 12;
        } else if (modifier === 'AM' && hours === 12) {
            hours = 0;
        }

        const slotTime = new Date(dateObj.getFullYear(), dateObj.getMonth(), dateObj.getDate(), hours, minutes, 0, 0);

        // If slotTime < "now", it's in the past
        return slotTime < new Date();
    }

    // 4)  SELECT A TIME SLOT
    function selectTimeSlot(btn, label, slotId) {
        // Deselect previous
        if (selectedTimeSlot && selectedTimeSlot.button) {
            selectedTimeSlot.button.classList.remove('btn-success');
            selectedTimeSlot.button.classList.add('btn-outline-secondary');
        }
        btn.classList.remove('btn-outline-secondary');
        btn.classList.add('btn-success');

        selectedTimeSlot = {
            button: btn,
            slot_id: slotId,
            time: label,
            date: weekDates[selectedDateIndex],
            dateIndex: selectedDateIndex
        };
        updateSelectedSlotLabel();
    }

    function updateSelectedSlotLabel() {
        const labelContainer = document.getElementById('selectedSlotLabelContainer');
        const label = document.getElementById('selectedSlotLabel');

        if (
            selectedTimeSlot &&
            selectedTimeSlot.date.toDateString() !== weekDates[selectedDateIndex].toDateString()
        ) {
            labelContainer.style.display = 'block';
            label.textContent = `Επιλεγμένη ημ/νία: ${selectedTimeSlot.date.toDateString()} at ${selectedTimeSlot.time}`;
            label.onclick = () => {
                // If user clicks, jump to that date
                currentDate = new Date(selectedTimeSlot.date);
                weekDates = getWeekDates(currentDate);
                selectedDateIndex = weekDates.findIndex(
                    date => date.toDateString() === selectedTimeSlot.date.toDateString()
                );
                renderDateNav();
                selectDate(selectedDateIndex);
            };
        } else {
            labelContainer.style.display = 'none';
            label.onclick = null;
        }
    }

    // 5)  CHANGE WEEK
    function changeWeek(offset) {
        const newDate = new Date(currentDate);
        newDate.setDate(newDate.getDate() + offset * 7);

        // Evaluate first day of new week
        const firstDayOfNewWeek = new Date(newDate);
        firstDayOfNewWeek.setDate(firstDayOfNewWeek.getDate() - firstDayOfNewWeek.getDay());
        firstDayOfNewWeek.setHours(0, 0, 0, 0);

        const firstDayOfCurrentWeek = new Date();
        firstDayOfCurrentWeek.setDate(firstDayOfCurrentWeek.getDate() - firstDayOfCurrentWeek.getDay());
        firstDayOfCurrentWeek.setHours(0, 0, 0, 0);

        // Don’t go before the current week
        if (firstDayOfNewWeek >= firstDayOfCurrentWeek) {
            currentDate = newDate;
            weekDates = getWeekDates(currentDate);
            selectedDateIndex = 0;

            // Possibly re-fetch for the new week if needed:
            if (selectedPackageId) {
                fetchSlotsAndRender(selectedPackageId);
            } else {
                renderDateNav();
                selectDate(selectedDateIndex);
            }
        }
    }

    function goToToday() {
        currentDate = new Date();
        weekDates = getWeekDates(currentDate);
        selectedDateIndex = weekDates.findIndex(date => date.toDateString() === today.toDateString());
        if (selectedDateIndex === -1) selectedDateIndex = 0;
        if (selectedPackageId) {
            fetchSlotsAndRender(selectedPackageId);
        } else {
            renderDateNav();
            selectDate(selectedDateIndex);
        }
    }

    function clearSelection() {
        if (selectedTimeSlot && selectedTimeSlot.button) {
            selectedTimeSlot.button.classList.remove('btn-success');
            selectedTimeSlot.button.classList.add('btn-outline-secondary');
            selectedTimeSlot = null;
            updateSelectedSlotLabel();
        }
    }

    function initSwipeNavigation() {
        const dateNav = document.getElementById('dateNav');
        let startX = 0;
        let endX = 0;
        let translateX = 0;

        dateNav.addEventListener('touchstart', (e) => {
            startX = e.touches[0].clientX;
            dateNav.style.transition = 'none';
        });

        dateNav.addEventListener('touchmove', (e) => {
            endX = e.touches[0].clientX;
            translateX = endX - startX;
            dateNav.style.transform = `translateX(${translateX}px)`;
            dateNav.style.scale = 0.7;
        });

        dateNav.addEventListener('touchend', () => {
            dateNav.style.transition = 'transform 0.3s, scale 0.3s';
            if (translateX < -50) {
                changeWeek(1);
            } else if (translateX > 50) {
                changeWeek(-1);
            }
            dateNav.style.scale = 1;
            dateNav.style.transform = 'translateX(0)';
        });
    }

    document.getElementById('prevWeekBtn').addEventListener('click', () => {
        changeWeek(-1);
    });
    document.getElementById('nextWeekBtn').addEventListener('click', () => {
        changeWeek(1);
    });
    document.getElementById('todayBtn').addEventListener('click', goToToday);
    document.getElementById('clearBtn').addEventListener('click', clearSelection);

    // Initialize the calendar with the current week's dates
    weekDates = getWeekDates(currentDate);
    selectedDateIndex = weekDates.findIndex(date => date.toDateString() === today.toDateString());
    if (selectedDateIndex === -1) selectedDateIndex = 0;
    renderDateNav();
    selectDate(selectedDateIndex);
    initSwipeNavigation();


    // ------------------------------------------------
    //           ACTUAL FETCH FOR TIME SLOTS
    // ------------------------------------------------
    // let selectedPackageId = null;
    document.addEventListener('change', function (e) {
        if (e.target && e.target.name === 'package') {
            selectedPackageId = e.target.value;
            // Immediately fetch the slots for the new selection
            fetchSlotsAndRender(selectedPackageId);
        }
    });

    function fetchSlotsAndRender(packageId) {
        if (!packageId) return;
        const csrf_token = document.querySelector('meta[name="csrf_token"]')?.getAttribute('content') || '';
        const start = formatDateTime(weekDates[0], 'start');
        const end = formatDateTime(weekDates[6], 'end');

        const fd = new FormData();
        fd.append('action', 'getCalendarSlotsRange');
        fd.append('csrf_token', csrf_token);
        fd.append('start', start);
        fd.append('end', end);
        fd.append('package_id', packageId);
        fd.append('type', selectedSlotType);

        fetch('includes/ajax.php', {
            method: 'POST',
            body: fd
        })
            .then(r => r.json())
            .then(res => {
                if (!res.success) {
                    console.warn('Slots fetch error', res.errors);
                    timeSlotsData = {};
                    renderDateNav();
                    selectDate(selectedDateIndex);
                    return;
                }
                buildTimeSlotsData(res.data);
                const foundIndex = findFirstAvailableIndex();
                if (foundIndex !== -1) selectedDateIndex = foundIndex;
                renderDateNav();
                selectDate(selectedDateIndex);
            })
            .catch(err => {
                console.error('Error fetching slots', err);
                timeSlotsData = {};
                renderDateNav();
                selectDate(selectedDateIndex);
            });
    }

    function buildTimeSlotsData(slotsArray) {
        timeSlotsData = {};
        maxSlotDate = null;

        slotsArray.forEach(s => {
            const dt = parseDateLocal(s.start_datetime);
            const dateStr = formatDateLocalDateStr(dt);

            if (!maxSlotDate || dt > maxSlotDate) maxSlotDate = dt;
            if (!timeSlotsData[dateStr]) {
                timeSlotsData[dateStr] = {
                    morning: [],
                    afternoon: []
                };
            }

            // Ensure we have a sub-dictionary for dateStr in slotIdsByDateLabel
            if (!slotIdsByDateLabel[dateStr]) {
                slotIdsByDateLabel[dateStr] = {};
            }

            const label = formatTimeAMPM(dt); // e.g. "10:00 AM"

            // Updated logic: Morning until 4:59 PM, Afternoon from 5 PM onwards
            if (dt.getHours() < 17) {
                timeSlotsData[dateStr].morning.push(label);
            } else {
                timeSlotsData[dateStr].afternoon.push(label);
            }

            // Store slot ID in the dictionary
            slotIdsByDateLabel[dateStr][label] = s.id;
        });
    }


    function parseDateLocal(dateStr) {
        const [datePart, timePart] = dateStr.split(' ');
        const [year, month, day] = datePart.split('-').map(Number);
        const [hour, minute, second] = timePart.split(':').map(Number);
        return new Date(year, month - 1, day, hour, minute, second);
    }

    function formatDateLocalDateStr(date) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        return `${y}-${m}-${d}`;
    }

    function formatDateTime(date, boundary) {
        const y = date.getFullYear();
        const m = String(date.getMonth() + 1).padStart(2, '0');
        const d = String(date.getDate()).padStart(2, '0');
        let hh = "00",
            mm = "00",
            ss = "00";
        if (boundary === "end") {
            hh = "23";
            mm = "59";
            ss = "59";
        }
        return `${y}-${m}-${d} ${hh}:${mm}:${ss}`;
    }

    function formatTimeAMPM(dateObj) {
        let hours = dateObj.getHours();
        const minutes = String(dateObj.getMinutes()).padStart(2, '0');
        const ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        if (hours === 0) hours = 12;
        return `${hours}:${minutes} ${ampm}`;
    }

    function findFirstAvailableIndex() {
        // Return the index of the earliest day in the week that has at least one future slot
        for (let i = 0; i < weekDates.length; i++) {
            const dateStr = weekDates[i].toISOString().split('T')[0];
            const daySlots = timeSlotsData[dateStr];
            if (daySlots) {
                const futureMorning = daySlots.morning?.filter(t => !isTimeInPast(weekDates[i], t)) || [];
                const futureAfternoon = daySlots.afternoon?.filter(t => !isTimeInPast(weekDates[i], t)) || [];
                if (futureMorning.length > 0 || futureAfternoon.length > 0) {
                    return i;
                }
            }
        }
        return -1;
    }


    const payNowBtn = document.getElementById('payNowBtn');
    if (payNowBtn) {
        payNowBtn.addEventListener('click', function () {
            const termsCheckbox = document.getElementById('terms');
            if (!termsCheckbox.checked) {
                alert('Πρέπει να αποδεχτείτε τους όρους χρήσης για να συνεχίσετε.');
                return;
            }

            if (!selectedPackageId || selectedPackagePrice <= 0) {
                alert('Πρέπει να επιλέξετε πακέτο για να συνεχίσετε.');
                return;
            }

            everypay.onClick()
        });
    }



});