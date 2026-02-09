<?php
if (!isset($GLOBAL_INCLUDE_CHECK)) die("Access Denied");
?>


<div class="container-fluid mt-5">
    <h3><i class="bi bi-people"></i> Χρήστες</h3>

    <div class="bg-primary d-flex align-items-center justify-content-between p-2 rounded mb-3 gap-4">

        <div class="d-flex align-items-center gap-1">
            <input type="text" class="form-control mr-sm-2" placeholder="Αναζήτηση.." name="searchUsers" id="searchUsers">
            <button class="m-0 btn btn-primary" type="button" title="Add new user" data-bs-toggle="modal" data-bs-target="#usersModal" id="addNewUser">
                <i class="bi bi-plus-lg font-md hand"></i>
            </button>
        </div>

        <div class="d-flex align-items-center">


            <i class="bi bi-printer mx-1 hand optionsPrint"></i>
            <i class="bi bi-download mx-1 hand optionsExport"></i>
            <select class="form-select form-select-sm border-0 w-auto mx-1" id="exportOptions">
                <option value="excel">Excel</option>
                <option value="csv">CSV</option>
                <option value="pdf">PDF</option>
            </select>
        </div>

    </div>

    <table class="table table-hover table-bordered rounded shadow-sm" id="users_table">

        <tbody id="users_body">

        </tbody>

    </table>
</div>


<div class="modal fade" id="usersModal" aria-labelledby="usersModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content bg-primary">
            <div class="modal-header border-0 bg-secondary">
                <h1 class="modal-title fs-5" id="usersModalLongTitle">Χρήστες
                    <span id="userCreated" class="float-end font-xs"></span>
                </h1>
                <button type="button" class="btn-close text-white m-1" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>


            <div class="modal-body">
                <div class="d-flex justify-content-start align-items-center"> <small id="userCreated" class="pull-right"></small></div>

                <form action="" id="usersForm">
                    <div class="row">
                        <div class="col-md-12 my-1">
                            <label for="registerEmail" class="form-label">Email</label>
                            <input type="email" class="form-control" id="registerEmail" name="registerEmail" placeholder="Εισάγετε το email σας" required>
                        </div>
                        <div class="col-md-12 my-1">
                            <label for="registerFirstName" class="form-label">Όνομα</label>
                            <input type="text" class="form-control" id="registerFirstName" name="registerFirstName" placeholder="Εισάγετε το όνομά σας" required>
                        </div>
                        <div class="col-md-12 my-1">
                            <label for="registerLastName" class="form-label">Επώνυμο</label>
                            <input type="text" class="form-control" id="registerLastName" name="registerLastName" placeholder="Εισάγετε το επώνυμό σας" required>
                        </div>
                        <div class="col-md-12 my-1">
                            <label for="registerPassword" class="form-label">Νέος Κωδικός</label>
                            <input type="password" class="form-control is-pass" id="registerPassword" name="registerPassword" placeholder="Εισάγετε τον νέο κωδικό σας" required>
                            <i class="toggle-password bi bi-eye" title="Show Password" onclick="showpwd()"></i>
                        </div>
                        <div class="col-md-12 my-1">
                            <label for="confirmPassword" class="form-label">Επιβεβαίωση Νέου Κωδικού</label>
                            <input type="password" class="form-control is-pass" id="confirmPassword" name="confirmPassword" placeholder="Επιβεβαιώστε τον νέο κωδικό σας" required>
                        </div>


                        <div class="col-md-12 my-1">
                            <label for="userRole">Ρόλος</label>
                            <select class="form-control mb-2 mr-sm-2" name="userRole" id="userRole">
                                <option value="1">Διαχειριστής</option>
                                <option value="2">Υπάλληλος</option>
                            </select>
                        </div>

                    </div>

                    <div class="d-flex justify-content-end align-items-center"> <small id="userUpdated" class="pull-right"></small></div>
            </div>

            <div class="modal-footer border-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Κλείσιμο</button>
                <button type="button" class="btn btn-primary" value="Add" id="usersActionBtn" data-action="addUser" data-key="users">Αποθήκευση</button>
            </div>
        </div>
    </div>
</div>

<?php
function hook_end_scripts()
{
?>
    <link rel="stylesheet" type="text/css" href="/assets/vendor/plugins/datatables/datatables.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/back/table.css">
    <script src="/assets/vendor/plugins/datatables/datatables.js"></script>

    <!-- <script src="js/genFunctions.js"></script> -->

    <script src="/assets/js/back/users.js"></script>

<?php
}
?>