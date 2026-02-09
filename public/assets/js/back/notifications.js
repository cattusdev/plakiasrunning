$(window).on('load', function () {
    var notifications = document.querySelectorAll('#notifcontainer .newNotif');
    var notifBadge = document.getElementById('notifbadge');
    notifBadge.textContent = notifications.length == 0 ? '' : notifications.length;

    $('body').on('click', '.notificationSeen', async function () {
        let notoficationID = $(this).attr('data-notoficationID');
        let notifDiv = $(this).closest('.notificationCard');

        let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');
        try {
            const actionConfirmed = await handleNotificationAction('Επιβεβαίωση Ενέργειας', 'Επισήμανση ως αναγνωσμένο;');
            if (actionConfirmed) {
                $.ajax({
                    type: 'post',
                    url: 'includes/admin/ajax.php',
                    data: {
                        action: "markNotification",
                        csrf_token: csrf_token,
                        mainID: notoficationID,
                    },
                    success: function (response) {
                        if (response.success) {
                            notifDiv.toggleClass('bg-primary bg-secondary');
                            notifDiv.removeClass('newNotif');

                            var earlierToday = document.querySelectorAll('#notifcontainer .earlierToday');
                            if (earlierToday.length == 0) {
                                $("#notifcontainer").append('<div class="pt-2 pb-1 text-center earlierToday"><small class="m-0">Νωρίτερα σήμερα</small></div>');
                            }
                            notifDiv.find(".card-header").toggleClass('bg-secondary bg-optional');

                            notifDiv.find(".bi-eye").removeClass('notificationSeen');
                            notifDiv.find(".bi-eye").addClass('notificationUnSeen');
                            notifDiv.find(".bi-eye").attr('title', 'Επισήμανση ως μή αναγνωσμένο');
                            notifDiv.find(".bi-eye").toggleClass('bi-eye bi-eye-slash');

                            $("#notifcontainer").append(notifDiv);

                            var notificationsR = document.querySelectorAll('#notifcontainer .newNotif');
                            var notifBadgeR = document.getElementById('notifbadge');
                            notifBadgeR.textContent = notificationsR.length == 0 ? '' : notificationsR.length;
                            if (!notificationsR.length) {
                                $("#notifcontainer").prepend(`
                            <div class="card m-1 p-0 bg-primary emptyNotifCard" style="width: 18rem;">
                                            <div class="card-header bg-secondary text-white font-sm">
                                                <i class="bi bi-bell float-end"></i>
                                            </div>
                                            <div class="card-body p-2">
                                                <p class="card-text text-center font-sm py-1 px-1 mb-1">Δεν βρέθηκαν νέες ειδοποιήσεις.</p>
                                            </div>
                                        </div>`);
                            }
                            setNotification('Action', response.message, 'success');

                        } else {
                            var errorMessage = '';
                            $.each(response.errors, function (index, error) {
                                errorMessage += error + "<br>";
                            });
                            setNotification('Warning', errorMessage, 'warning');
                        }
                    },
                    error: function (error) {
                        var errorMessage = '';
                        $.each(error.errors, function (index, error) {
                            errorMessage += error + "<br>";
                        });
                        setNotification('Error', errorMessage, 'error');
                    },

                });
            }
        } catch (error) {
            console.error('Error handling action:', error);
            alert('An error occurred');
        }

    });

    $('body').on('click', '.notificationUnSeen', async function () {
        let notoficationID = $(this).attr('data-notoficationID');
        let notifDiv = $(this).closest('.notificationCard');


        let csrf_token = document.getElementsByName('csrf_token')[0].getAttribute('content');

        try {
            const actionConfirmed = await handleNotificationAction('Επιβεβαίωση Ενέργειας', 'Επισήμανση ως μη αναγνωσμένο;');
            if (actionConfirmed) {
                $.ajax({
                    type: 'post',
                    url: 'includes/admin/ajax.php',
                    data: {
                        action: "unMarkNotification",
                        csrf_token: csrf_token,
                        mainID: notoficationID,
                    },
                    success: function (response) {
                        if (response.success) {
                            notifDiv.toggleClass('bg-secondary bg-primary');
                            notifDiv.addClass('newNotif');
                            notifDiv.find(".card-header").toggleClass('bg-optional bg-secondary');
                            notifDiv.find(".bi-eye-slash").toggleClass('bi-eye-slash bi-eye');
                            notifDiv.find(".bi-eye").toggleClass('notificationUnSeen notificationSeen');
                            notifDiv.find(".bi-eye").attr('title', 'Επισήμανση ως αναγνωσμένο');

                            $("#notifcontainer").prepend(notifDiv);

                            var notificationsR = document.querySelectorAll('#notifcontainer .newNotif');
                            var notificationsSeen = document.querySelectorAll('#notifcontainer .notificationUnSeen');
                            var notifBadgeR = document.getElementById('notifbadge');
                            notifBadgeR.textContent = notificationsR.length == 0 ? '' : notificationsR.length;

                            if (notificationsSeen.length == 0) {
                                $(".earlierToday").remove();
                            }
                            if (notificationsR.length != 0) {
                                $(".emptyNotifCard").remove();
                            }

                            setNotification('Action', response.message, 'success');

                        } else {
                            var errorMessage = '';
                            $.each(response.errors, function (index, error) {
                                errorMessage += error + "<br>";
                            });
                            setNotification('Warning', errorMessage, 'warning');
                        }
                    },
                    error: function (error) {
                        var errorMessage = '';
                        $.each(error.errors, function (index, error) {
                            errorMessage += error + "<br>";
                        });
                        setNotification('Error', errorMessage, 'error');
                    },

                });
            }
        } catch (error) {
            console.error('Error handling action:', error);
            alert('An error occurred');
        }

    });
});
