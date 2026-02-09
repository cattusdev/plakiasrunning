$(window).on('load', function () {
    $('#status').fadeOut('fast');
    // $('#preloader').delay(250).fadeOut();
    $('#preloader').fadeOut('fast');
    $('body').css('overflow', 'auto').fadeIn('fast');
    // setTimeout(function () {
    //     //document.body.style.overflow = "auto";
    //     $('body').css('overflow', 'auto').fadeIn('fast');
    // }, 300)
    $("#manufacturer").select2({
        // placeholder: "Amenities",
        // dropdownParent: $('#bookingsModal .modal-body'),
        theme: 'bootstrap-5'
    });

    $("#manufacturer").trigger('change')

    moment.locale('el')

    
   
    function formatOption(option) {
        if (!option.id) {
            return option.text;
        }
        var name = $(option.element).data('name');
        var price = $(option.element).data('price');
        var model = $(option.element).data('model');
        var condition = $(option.element).data('condition');
        var quantity = $(option.element).data('quantity');

        var template = `
            <div class="d-flex flex-column p-2">
                <div class="font-weight-bold">${name} (Τεμ. ${quantity})</div>
                <div class="text-muted">Model: ${model}</div>
                <div class="text-success font-weight-bold">€${price}</div>
                <div class="text-primary small">${condition}</div>
            </div>
        `;

        return $(template);
    }

    function formatSelection(option) {
        if (!option.id) {
            return option.text;
        }
        var name = $(option.element).data('name');
        return name;
    }

    $('#storageParts').select2({
        templateResult: formatOption,
        templateSelection: formatSelection,
        theme: 'bootstrap-5',
        escapeMarkup: function (markup) { return markup; } // Allow HTML in select2
    });

    $('thead th:visible:last').css('border-top-right-radius', '4px');
})

function setCookie(name, value, daysToExpire) {
    const date = new Date();
    date.setTime(date.getTime() + (daysToExpire * 24 * 60 * 60 * 1000)); // Convert days to milliseconds
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/";
}

function getCookie(name) {
    const cookieName = name + "=";
    const decodedCookie = decodeURIComponent(document.cookie);
    const cookieArray = decodedCookie.split(';');

    for (let i = 0; i < cookieArray.length; i++) {
        let cookie = cookieArray[i].trim();

        if (cookie.indexOf(cookieName) === 0) {
            return cookie.substring(cookieName.length, cookie.length);
        }
    }

    return "";
}

let notifStackCount = 0;
function setNotification(title, message, type = 'info', delay = 3500, time = null) {
    notifStackCount++;
    let icon = 'bi bi-info-circle';
    let color = 'text-white'
    let bg = 'bg-info'
    switch (type) {
        case 'success':
            icon = 'bi bi-check';
            color = 'text-white'
            bg = 'bg-success'
            break;
        case 'info':
            icon = 'bi bi-info-circle';
            color = 'text-white'
            bg = 'bg-info'
            break;
        case 'warning':
            icon = 'bi bi-exclamation-triangle';
            color = 'text-white'
            bg = 'bg-warning'
            break;
        case 'error':
            icon = 'bi bi-exclamation-triangle';
            color = 'text-white'
            bg = 'bg-danger'
            break;

        default:
            icon = 'bi bi-info-circle';
            color = 'text-white'
            bg = 'bg-info'
            break;
    }
    let bodymsg = `
            <div class="toast fade mt-1" data-delay="${delay}" id="${notifStackCount}">
					<div class="toast-header border-0 ${bg}">
						<b>${title}</b>
						<b class="${icon} ms-1 ${color}"></b>
					</div>
					<div class="toast-body bg-secondary">
						${message}
					</div>
				</div>
            `;
    $('#genNotifications').append(bodymsg);
    $("#" + notifStackCount).toast({
        autohide: true
    });
    $("#" + notifStackCount).toast('show');
}


function showLoader(container = 'body', status = 'load', succesTime = 1100, errorTime = 1500) {

    $(".postLoader").remove();
    $(container).append(mainPreloader);
    switch (status) {
        case 'load':
            $('#postLoader').fadeIn('fast');
            break;
        case 'success':
            $('#postLoader').css('display', 'block');
            $('#postCheck').fadeIn();
            $('#postCheck').css('animation', 'dash-check 0.9s 0.35s ease-in-out forwards');
            $('#postLoader').css('animation', 'none');
            $('#postLoader').css('stroke-dasharray', '1000');
            $('#postLoader').css('stroke-dashoffset', '0');
            setTimeout(() => {
                hideLoader();
            }, 1100);
            break;
        case 'error':
            $('#postLoader').fadeOut('fast');
            $('#postCheck').fadeOut('fast');
            $('#failLoader').fadeIn();
            $('#failLoader').css('animation', 'none');
            $('#failLoader').css('stroke-dasharray', '1000');
            $('#failLoader').css('stroke-dashoffset', '0');
            $('#failLoaderLine1').css('animation', 'dash-check 0.9s 0.35s ease-in-out forwards');
            $('#failLoaderLine2').css('animation', 'dash-check 0.9s 0.35s ease-in-out forwards');
            $('#failLoaderLine1').fadeIn();
            $('#failLoaderLine2').fadeIn();
            setTimeout(() => {
                hideLoader();
            }, 1500);
            break;
        default:
            break;
    }

}


function hideLoader() {
    $('.postLoader').fadeOut('fast');
}

let mainPreloader = `<div class="postLoader">
        <svg id="svgSuccess" version="1.1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 130.2 130.2">
            <circle id="postLoader" class="path circle" fill="none" stroke="#73AF55" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
            <polyline id="postCheck" class="path check" fill="none" stroke="#73AF55" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" points="100.2,40.2 51.5,88.8 29.8,67.5 " />
            <circle id="failLoader" class="path circle" fill="none" stroke="#D06079" stroke-width="6" stroke-miterlimit="10" cx="65.1" cy="65.1" r="62.1" />
            <line id="failLoaderLine1" class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="34.4" y1="37.9" x2="95.8" y2="92.3" />
            <line id="failLoaderLine2" class="path line" fill="none" stroke="#D06079" stroke-width="6" stroke-linecap="round" stroke-miterlimit="10" x1="95.8" y1="38" x2="34.4" y2="92.2" />
        </svg>
        <p class="response-message mt-3"></p>
    </div>`;

const themeToggle = document.getElementById('theme-toggle');
const body = document.body;
const iconToggle = document.getElementById('icon-toggle');
const iconToggleLight = document.getElementById('icon-toggle-light');

// Function to set the theme based on user preference
function setTheme(theme) {
    body.classList.toggle('dark-theme', theme === 'dark');
    iconToggle.classList.toggle('d-none', theme !== 'dark');
    iconToggleLight.classList.toggle('d-none', theme === 'dark');
}

// Check for saved theme preference

const savedTheme = localStorage.getItem('theme');

if (savedTheme === 'dark') {
    setTheme('dark');
    if (iconToggle && iconToggleLight) {
        themeToggle.checked = false; // Default to light theme
    }

} else {
    setTheme('light'); // Set light theme as the default
    if (iconToggle && iconToggleLight) {
        themeToggle.checked = true;
    }

}

// Toggle theme and save preference when the switch is clicked
themeToggle.addEventListener('change', function () {
    const currentTheme = this.checked ? 'light' : 'dark';
    setTheme(currentTheme);
    localStorage.setItem('theme', currentTheme);
});
// Remove This: 
$("#warningNotif").on('click', function () {
    setNotification('Warning', 'This is a warning message.', 'warning', 3500, Date($.now()));
})
$("#infoNotif").on('click', function () {
    setNotification('Info', 'This is an info message.', 'info', 3500, Date($.now()));
})
$("#errorNotif").on('click', function () {
    setNotification('Error', 'This is an error message.', 'error', 3500, Date($.now()));
})


var scrollTopBtn = document.getElementById("scrollTopBtn");

// When the user scrolls down 20px from the top of the document, show the button
window.onscroll = function () {
    if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
        scrollTopBtn.style.display = "block";
    } else {
        scrollTopBtn.style.display = "none";
    }
};

// When the user clicks on the button, scroll to the top of the document
scrollTopBtn.onclick = function () {
    document.body.scrollTop = 0; // For Safari
    document.documentElement.scrollTop = 0; // For Chrome, Firefox, IE, and Opera
};


//Currency
var currencyInput = document.querySelectorAll('input[type="currency"]');
for (var i = 0; i < currencyInput.length; i++) {

    var currency = 'EUR'
    // onBlur({
    // 	target: currencyInput[i]
    // })

    currencyInput[i].addEventListener('focus', onFocus)
    currencyInput[i].addEventListener('blur', onBlur)

    function localStringToNumber(s) {
        return Number(String(s).replace(/[^0-9.-]+/g, ""))
    }

    function onFocus(e) {
        var value = e.target.value;
        e.target.value = value ? localStringToNumber(value) : ''
    }

    function onBlur(e) {
        var value = e.target.value

        var options = {
            //maximumFractionDigits: 2,
            currency: currency,
            style: "currency",
            currencyDisplay: "symbol"
        }
        e.target.value = (value || value === 0) ?
            localStringToNumber(value).toLocaleString('en-US', options) :
            ''


    }
}

function localStringToNumber(s) {
    return Number(String(s).replace(/[^0-9.-]+/g, ""))
}



function addTask() {
    const title = document.getElementById('taskTitle').value.trim();
    const price = document.getElementById('taskPrice').value.trim();
    if (!title || !price || isNaN(price.replace('€', ''))) {
        alert("Παρακαλώ εισάγετε έναν έγκυρο τίτλο και τιμή για την εργασία.");
        return; // Prevent the addition of the row if inputs are invalid
    }

    const table = document.getElementById('tasksTable').getElementsByTagName('tbody')[0];
    const row = table.insertRow();
    const titleCell = row.insertCell(0);
    const priceCell = row.insertCell(1);
    const actionsCell = row.insertCell(2);

    titleCell.textContent = title;
    priceCell.textContent = price;
    actionsCell.classList.add('d-flex', 'gap-1');
    actionsCell.innerHTML = '<button type="button" class="btn btn-info btn-sm" onclick="editTask(this)">Επεξεργασία</button> <button type="button" class="btn btn-danger btn-sm" onclick="removeTask(this)">Διαγραφή</button>';

    // Clear inputs after adding the task
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskPrice').value = '';
    updateTotalPrice();
}

function addPresetTask() {
    const presetTasks = document.getElementById('presetService');
    const selectedTask = presetTasks.options[presetTasks.selectedIndex];

    const title = selectedTask.getAttribute('data-name').trim();
    const price = selectedTask.getAttribute('data-price').trim();
    if (!title || !price || isNaN(price.replace('€', ''))) {
        alert("Παρακαλώ εισάγετε έναν έγκυρο τίτλο και τιμή για την εργασία.");
        return; // Prevent the addition of the row if inputs are invalid
    }

    const table = document.getElementById('tasksTable').getElementsByTagName('tbody')[0];
    const row = table.insertRow();
    const titleCell = row.insertCell(0);
    const priceCell = row.insertCell(1);
    const actionsCell = row.insertCell(2);

    titleCell.textContent = title;
    priceCell.textContent = price;
    actionsCell.classList.add('d-flex', 'gap-1');
    actionsCell.innerHTML = '<button type="button" class="btn btn-info btn-sm" onclick="editTask(this)">Επεξεργασία</button> <button type="button" class="btn btn-danger btn-sm" onclick="removeTask(this)">Διαγραφή</button>';

    // Clear inputs after adding the task
    document.getElementById('taskTitle').value = '';
    document.getElementById('taskPrice').value = '';
    $("#presetTasks").trigger('change');
    updateTotalPrice();
}

function addPresetPart() {
    const presetParts = document.getElementById('presetParts');
    const selectedPart = presetParts.options[presetParts.selectedIndex];

    const title = selectedPart.getAttribute('data-name').trim();
    const price = selectedPart.getAttribute('data-price').trim();
    if (!title || !price || isNaN(price.replace('€', ''))) {
        alert("Παρακαλώ εισάγετε έναν έγκυρο τίτλο και τιμή.");
        return; // Prevent the addition of the row if inputs are invalid
    }

    const table = document.getElementById('partsTable').getElementsByTagName('tbody')[0];
    const row = table.insertRow();
    const titleCell = row.insertCell(0);
    const priceCell = row.insertCell(1);
    const actionsCell = row.insertCell(2);

    titleCell.textContent = title;
    priceCell.textContent = price;
    actionsCell.classList.add('d-flex', 'gap-1');
    actionsCell.innerHTML = '<button type="button" class="btn btn-info btn-sm" onclick="editPart(this)">Επεξεργασία</button> <button type="button" class="btn btn-danger btn-sm" onclick="removePart(this)">Διαγραφή</button>';

    // Clear inputs after adding the task
    document.getElementById('partName').value = '';
    document.getElementById('partPrice').value = '';
    $("#presetParts").trigger('change');
    updateTotalPrice();
}

function addStoragePart() {
    const storageParts = document.getElementById('storageParts');
    const selectedPart = storageParts.options[storageParts.selectedIndex];

    const title = selectedPart.getAttribute('data-name').trim();
    const price = selectedPart.getAttribute('data-price').trim();
    const partID = selectedPart.value;

    if (!title || !price || isNaN(price.replace('€', ''))) {
        alert("Παρακαλώ εισάγετε έναν έγκυρο τίτλο και τιμή.");
        return; // Prevent the addition of the row if inputs are invalid
    }

    const table = document.getElementById('partsTable').getElementsByTagName('tbody')[0];
    const row = table.insertRow();
    const titleCell = row.insertCell(0);
    const priceCell = row.insertCell(1);
    const actionsCell = row.insertCell(2);

    row.setAttribute('data-storageID', partID);

    titleCell.textContent = title;
    priceCell.textContent = price;
    actionsCell.classList.add('d-flex', 'gap-1');
    actionsCell.innerHTML = '<button type="button" class="btn btn-info btn-sm" onclick="editPart(this)">Επεξεργασία</button> <button type="button" class="btn btn-danger btn-sm" onclick="removePart(this)">Διαγραφή</button>';

    // Clear inputs after adding the task
    document.getElementById('partName').value = '';
    document.getElementById('partPrice').value = '';
    $("#storageParts").trigger('change');
    updateTotalPrice();
}


function addPart() {
    const name = document.getElementById('partName').value.trim();
    const price = document.getElementById('partPrice').value.trim();
    if (!name || !price || isNaN(price.replace('€', ''))) {
        alert("Παρακαλώ εισάγετε ένα έγκυρο όνομα και τιμή για το εξάρτημα.");
        return; // Prevent the addition of the row if inputs are invalid
    }

    const table = document.getElementById('partsTable').getElementsByTagName('tbody')[0];
    const row = table.insertRow();
    const nameCell = row.insertCell(0);
    const priceCell = row.insertCell(1);
    const actionsCell = row.insertCell(2);

    nameCell.textContent = name;
    priceCell.textContent = price;
    actionsCell.classList.add('d-flex', 'gap-1');
    actionsCell.innerHTML = '<button type="button" class="btn btn-info btn-sm" onclick="editPart(this)">Επεξεργασία</button> <button type="button" class="btn btn-danger btn-sm" onclick="removePart(this)">Διαγραφή</button>';

    // Clear inputs after adding the part
    document.getElementById('partName').value = '';
    document.getElementById('partPrice').value = '';
    updateTotalPrice();
}

function removeTask(element) {
    const row = element.parentNode.parentNode;
    row.parentNode.removeChild(row);
    updateTotalPrice();
}

function removePart(element) {
    const row = element.parentNode.parentNode;
    row.parentNode.removeChild(row);
    updateTotalPrice();
}

function editTask(element) {
    const row = element.parentNode.parentNode;
    const title = row.cells[0].textContent;
    const price = row.cells[1].textContent;

    document.getElementById('editName').value = title;
    document.getElementById('editPrice').value = price;
    document.getElementById('editIndex').value = row.rowIndex;
    document.getElementById('editType').value = 'task';

    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

function editPart(element) {
    const row = element.parentNode.parentNode;
    const name = row.cells[0].textContent;
    const price = row.cells[1].textContent;

    document.getElementById('editName').value = name;
    document.getElementById('editPrice').value = price;
    document.getElementById('editIndex').value = row.rowIndex;
    document.getElementById('editType').value = 'part';

    var editModal = new bootstrap.Modal(document.getElementById('editModal'));
    editModal.show();
}

function saveEdit() {
    const index = document.getElementById('editIndex').value;
    const type = document.getElementById('editType').value;
    const name = document.getElementById('editName').value;
    const price = document.getElementById('editPrice').value;

    if (type === 'task') {
        const table = document.getElementById('tasksTable').getElementsByTagName('tbody')[0];
        const row = table.rows[index - 1];
        row.cells[0].textContent = name;
        row.cells[1].textContent = price;
    } else if (type === 'part') {
        const table = document.getElementById('partsTable').getElementsByTagName('tbody')[0];
        const row = table.rows[index - 1];
        row.cells[0].textContent = name;
        row.cells[1].textContent = price;
    }

    var editModal = bootstrap.Modal.getInstance(document.getElementById('editModal'));
    editModal.hide();
    updateTotalPrice();
}


function updateTotalPrice() {
    let total = 0;
    // Calculate total for tasks
    const tasksTable = document.getElementById('tasksTable').getElementsByTagName('tbody')[0];
    for (let row of tasksTable.rows) {
        let price = parseFloat(row.cells[1].textContent.replace('€', ''));
        if (!isNaN(price)) {
            total += price;
        }
    }

    // Calculate total for parts
    const partsTable = document.getElementById('partsTable').getElementsByTagName('tbody')[0];
    for (let row of partsTable.rows) {
        let price = parseFloat(row.cells[1].textContent.replace('€', ''));
        if (!isNaN(price)) {
            total += price;
        }
    }

    // Update the total price input
    document.getElementById('totalPrice').value = `€${total.toFixed(2)}`;
}


function serializeTableData(tableId) {
    let dataArray = [];
    let table = document.getElementById(tableId);
    for (let row of table.getElementsByTagName('tbody')[0].rows) {
        let rowData = {
            title: row.cells[0].textContent,
            price: row.cells[1].textContent.replace('€', '').trim(),
            storageID: row.getAttribute('data-storageID') || ''
        };
        dataArray.push(rowData);
    }
    return JSON.stringify(dataArray);
}

function addTaskRow(title, price) {
    let table = $('#tasksTable tbody');
    let row = $('<tr></tr>');
    row.append(`<td>${title}</td>`);
    row.append(`<td>${price}</td>`);
    row.append(`<td class="d-flex gap-2"><button type="button" class="btn btn-info btn-sm" onclick="editTask(this)">Επεξεργασία</button> <button type="button" class="btn btn-danger btn-sm" onclick="removeTask(this)">Διαγραφή</button></td>`);
    table.append(row);
}

function addPartRow(name, price, storageID = null) {
    let table = $('#partsTable tbody');
    let row = $('<tr></tr>');
    if (storageID) {
        row = $('<tr data-storageid="' + storageID + '"></tr>');
    }
    row.append(`<td>${name}</td>`);
    row.append(`<td>${price}</td>`);
    row.append(`<td class="d-flex gap-2"><button type="button" class="btn btn-info btn-sm" onclick="editPart(this)">Επεξεργασία</button> <button type="button" class="btn btn-danger btn-sm" onclick="removePart(this)">Διαγραφή</button></td>`);
    table.append(row);
}


function printBookingDetails() {
    const vehicle = $('#vehicle option:selected').text();
    const kmService = $('#kmService').val();
    const serviceTitle = $('#serviceTitle').val();
    const nextService = $('#nextService').val();
    const operatorName = $('#operatorID option:selected').text();
    const bookingStatus = $('#bookingStatus option:selected').text();
    //<p><strong>Κατάσταση:</strong> ${bookingStatus}</p>

    const totalPrice = $('#totalPrice').val() + "€";
    const amountDue = $('#amountDue').val() + "€";
    const bookingNote = $('#bookingNote').val();
    let webbase = window.location.origin;
    let printContents = `
        <html>
        <head>
            <title>${serviceTitle}</title>
            <style>
                body { font-family: 'Arial', sans-serif; }
                header { text-align: left; border-bottom: 2px solid #333; padding-bottom: 10px; margin-bottom: 20px; }
                h1, h2 { color: #0056b3; font-weight: bold; }
                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
                th { background-color: #f0f0f0; color: #333; }
                p { font-size: 14px; }
            </style>
        </head>
        <body>
            <header>
                
                <img src="${webbase}/assets/images/logo/app_logo.svg" style="width:110px;height:auto;" alt="Λογότυπο Εταιρείας">
                <p>
                    <strong>MotoSync</strong>
                    <br>
                    <strong>Αρ. Επικοινωνίας: (+30) 690 0000 000</strong>
                    <br>
                    <strong>Διεύθυνση: Xxxx, Xxx Xxx, Ελλάδα</strong>
                    </p>
            </header>
            <h1>Στοιχεία Service</h1>
            <p><strong>Όχημα:</strong> ${vehicle}</p>
            <p><strong>Τρέχοντα Χιλιόμετρα:</strong> ${kmService} km</p>
            <p><strong>Επόμενο Service :</strong> ${nextService} km</p>
            <p><strong>Υπ. Μηχανικός  :</strong> ${operatorName}</p>
            
            <h2>Εργασίες</h2>
            <table>
                <thead>
                    <tr>
                        <th>Τίτλος</th>
                       
                    </tr>
                </thead>
                <tbody>`;
    // <th>Τιμή</th>
    // Append each task row
    $('#tasksTable tbody tr').each(function () {
        const title = $(this).find('td:nth-child(1)').text();
        const price = $(this).find('td:nth-child(2)').text() + "€";
        //<td>${price}</td>
        printContents += `<tr><td>${title}</td></tr>`;
    });

    //<th>Τιμή</th>
    printContents += `</tbody></table><h2>Ανταλλακτικά</h2><table><thead><tr><th>Ανταλλακτικό</th></tr></thead><tbody>`;

    // Append each part row
    $('#partsTable tbody tr').each(function () {
        const partName = $(this).find('td:nth-child(1)').text();
        const partPrice = $(this).find('td:nth-child(2)').text() + "€";
        //<td>${partPrice}</td>
        printContents += `<tr><td>${partName}</td></tr>`;
    });

    // <p><strong>Συνολική Τιμή:</strong> ${totalPrice}</p>
    // <p><strong>Υπόλοιπο:</strong> ${amountDue}</p>
    printContents += `</tbody></table>
        <p><strong>Σημειώσεις:</strong> ${bookingNote}</p>
        </body>
        </html>`;

    // Open a new window and print the content
    let printWindow = window.open('', '', 'height=600,width=800');
    printWindow.document.write(printContents);
    printWindow.document.close();
    printWindow.focus();
    printWindow.print();
    printWindow.close();
}



function showConfirmationModal(title, message) {
    return new Promise((resolve, reject) => {
        // Create modal element dynamically
        const modalElement = document.createElement('div');
        modalElement.classList.add('modal', 'fade');
        modalElement.id = 'dynamicConfirmationModal';
        modalElement.setAttribute('tabindex', '-1');
        modalElement.setAttribute('aria-labelledby', 'dynamicModalLabel');
        modalElement.setAttribute('aria-hidden', 'true');
        modalElement.innerHTML = `
            <div class="modal-dialog">
                <div class="modal-content bg-primary">
                    <div class="modal-header border-0 bg-secondary">
                        <h5 class="modal-title fs-5" id="dynamicModalLabel">${title}</h5>
                        <button type="button" class="btn-close text-white m-1" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">${message}</div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Οχι</button>
                        <button type="button" class="btn btn-primary" id="confirmBtn">Ναι</button>
                    </div>
                </div>
            </div>
        `;

        // Append to body
        document.body.appendChild(modalElement);
        const modal = new bootstrap.Modal(modalElement);

        // Focus on yes button by default
        modalElement.addEventListener('shown.bs.modal', () => {
            document.getElementById('confirmBtn').focus();
        });

        // Resolve the promise when Yes is clicked
        document.getElementById('confirmBtn').onclick = function () {
            resolve(true);
            modal.hide();
        };

        // Clean up the modal after it's hidden
        modalElement.addEventListener('hidden.bs.modal', () => {
            modal.dispose();
            modalElement.remove();
            resolve(false);
        });

        // Show the modal
        modal.show();
    });
}

async function handleNotificationAction(title = null, message = null) {
    const confirmation = await showConfirmationModal(title, message);
    if (confirmation) {
        console.log('User confirmed the action');
        return true;
    } else {
        console.log('User did not confirm the action');
        return false;
    }
}