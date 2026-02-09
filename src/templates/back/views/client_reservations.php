<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>


<div class="container-fluid mt-5 bg-optional py-3 rounded" id="clientVehiclesView">
    <div class="mini-card bg-success" id="totalRevenueCard">
        <div class="card-content">
            <p>Ραντεβού</p>
            <span id="totalReservations"></span>
        </div>
    </div>
    <div class="mini-card" id="totalRevenueCard">
        <div class="card-content">
            <p>Συνολικά Έσοδα</p>
            <span id="totalRevenue"></span>
        </div>
    </div>
    <div class="mini-card bg-pendingpayment" id="amoundDeposit">
        <div class="card-content">
            <p>Έναντι</p>
            <span id="totalDeposit"></span>
        </div>
    </div>
    <div class="mini-card bg-pendingpayment" id="amoundDueCard">
        <div class="card-content">
            <p>Υπόλοιπο</p>
            <span id="totalAmountDue"></span>
        </div>
    </div>
    <div class="bg-secondary d-flex flex-wrap align-items-center justify-content-between p-2 rounded mb-3">

        <div class="d-flex align-items-center gap-1">
            <input type="text" class="form-control form-control-sm mr-sm-2" placeholder="Αναζήτηση.." name="searchBookings" id="searchBookings">
            <i class="bi bi-search"></i>


        </div>

        <!-- <div class="d-flex">
            <i class="bi bi-printer mx-1 hand optionsPrintD"></i>
            <i class="bi bi-download mx-1 hand optionsExportD"></i>
            <select class="form-select form-select-sm border-0 w-auto mx-1" id="exportOptionss">
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
            </select>
        </div> -->


        <div class="d-flex align-items-center gap-1 flex-wrap flex-md-nowrap my-1">
            <label for="dateRangePicker" class="form-label mb-0 font-sm">Ημ/νία </label>
            <div class="input-group justify-content-center ml-1">

                <input type="text" class="form-control form-control-sm" id="dateRangePicker" name="dateRangePicker">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="clearDates">
                    <i class="bi bi-calendar-x" title="Καθαρισμός"></i>
                </button>
            </div>


            <!-- <input type="text" id="pickupDate" class="form-control form-control-sm" placeholder="Pickup Date"> -->

            <div class="input-group">

                <input type="text" id="pickupDate" class="form-control form-control-sm" placeholder="Ημ/νία Επισκευής">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="clearPDate">
                    <i class="bi bi-calendar-x" title="Καθαρισμός"></i>
                </button>
            </div>

            <div class="input-group">

                <input type="text" id="returnDate" class="form-control form-control-sm" placeholder="Ημ/νία Παράδοσης">
                <button class="btn btn-outline-secondary btn-sm" type="button" id="clearRDate">
                    <i class="bi bi-calendar-x" title="Καθαρισμός"></i>
                </button>
            </div>

        </div>


    </div>


    <table class="table table-hover table-bordered rounded shadow-sm" id="bookings_table">

        <tbody id="bookings_body">

        </tbody>

    </table>
</div>