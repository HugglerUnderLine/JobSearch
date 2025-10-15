<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-5.3.3-dist/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/material-symbols/material-symbols-rounded.css') ?>">

    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <?= $this->renderSection('more-styles') ?>

    <style>
        #loading_overlay {
            z-index: 1060 !important;
        }

        .navbar-brand img {
            height: 30px;
            width: auto;
            transition: transform .1s ease;
        }

        .custom-navbar {
            width: 100%;
            border-radius: 0;
        }

        .navbar-inner {
            padding-left: 2rem;
            padding-right: 2rem;
        }

        .nav-gap .nav-item {
            margin-right: 1rem;
        }

        .nav-hover {
            transition: all .1s ease-in-out;
            border-radius: 6px;
            padding: 6px 12px !important;
        }

        .nav-hover:hover {
            background-color: rgba(255, 255, 255, 0.25);
            transform: scale(1.05);
            font-weight: 500;
        }

        .highlight-on-hover:hover {
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 6px;
            transform: scale(1.05);
            transition: all .1s ease-in-out;
        }

        .navbar-brand img {
            height: 30px;
            width: auto;
            transition: transform .1s ease;
        }

        .navbar-brand img:hover {
            transform: scale(1.05);
        }

        #username {
            font-size: 0.95rem;
            margin-left: 4px;
        }

        .dropdown-menu {
            min-width: 200px;
            border-radius: 10px;
            padding: 10px;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 6px;
            border-radius: 6px;
            transition: all .1s ease-in-out;
        }

        .dropdown-item:last-child {
            margin-bottom: 0;
        }

        .dropdown-item:hover {
            background-color: rgba(213, 216, 218, 0.15);
            font-weight: 500;
            transform: scale(1.02);
        }

        .dropdown-header {
            font-size: 0.85rem;
            font-weight: 600;
            opacity: 0.8;
        }

        .dropdown-header span.material-symbols-rounded {
            font-size: 20px;
            vertical-align: middle;
        }

        .user-dropdown {
            min-width: 220px;
            padding: 0.5rem 0;
        }

        .user-dropdown .dropdown-item {
            display: flex;
            align-items: center;
            justify-content: flex-start;
            gap: 8px;
            transition: background-color 0.2s ease, transform 0.15s ease;
            margin-bottom: 6px;
        }

        .user-dropdown .dropdown-item:hover {
            background-color: rgba(0, 0, 0, 0.05);
            transform: scale(1.01);
        }

        .user-dropdown .material-symbols-rounded {
            font-size: 20px;
            line-height: 1;
            vertical-align: middle;
        }

        .user-dropdown .dropdown-header {
            font-weight: 600;
            font-size: 0.9rem;
        }
    </style>

    <title>Job Search</title>
</head>

<body class="bg-light d-flex flex-column min-vh-100">
<!-- Loading Screen -->
<div id="loading_screen">
    <div class="spinner-border text-primary" role="status">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Alerts -->
<div id="alert-message-container">
    <div id="dynamic-alert" class="alert alert-dismissible fade show" role="alert" style="display:none;">
        <strong id="alert_title"></strong>
        <span id="message_alert" class="text-center"></span>
        <button type="button" class="btn-close" id="close-alert" aria-label="Close"></button>
    </div>
</div>

<div id="loading_overlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); color: #fff; text-align: center; padding-top: 20%; font-size: 1.5em;">
    <div id="loading_message">Loading...</div>
    <div class="spinner-border text-light mt-3" role="status" style="width: 3rem; height: 3rem;">
        <span class="visually-hidden">Loading...</span>
    </div>
</div>

<!-- Delete Account Confirmation Modal -->
<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-danger">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title" id="deleteAccountModalLabel">Delete My Account</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Are you sure you want to delete your account? This action is permanent and cannot be undone.</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" id="confirmDeleteAccount" class="btn btn-danger">Confirm</button>
            </div>
        </div>
    </div>
</div>


<main class="flex-grow-1">
    <?php if (!isset($no_banner) || $no_banner == false): ?>
    <nav class="navbar navbar-expand-lg bg-secondary bg-opacity-25 custom-navbar">
        <div class="container-fluid navbar-inner">
            <!-- Logo -->
            <a href="<?= base_url('about') ?>" class="navbar-brand m-0 me-5 ms-5 highlight-on-hover">
                <img class="m-2" src="<?= base_url('assets/img/jobSearch.png') ?>" alt="JobSearch Logo">
            </a>

            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarText" aria-controls="navbarText" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarText">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 nav-gap">
                    <li class="nav-item">
                        <a class="nav-link nav-hover" aria-current="page" href="<?= base_url('about') ?>">About</a>
                    </li>
                    <!-- <li class="nav-item">
                        <a class="nav-link nav-hover" href="#">Jobs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link nav-hover" href="#"></a>
                    </li> -->
                </ul>

                <div class="d-flex align-items-center me-5 ms-5">
                    <!-- Dropdown User -->
                    <div class="dropdown me-3">
                        <button class="btn btn-link nav-link dropdown-toggle d-flex align-items-center text-decoration-none nav-hover"
                                type="button" id="userDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            <span id="user-icon" class="material-symbols-rounded align-bottom me-1">account_circle</span>
                            <b id="username">User</b>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow user-dropdown" aria-labelledby="userDropdown">
                            <li>
                                <h6 class="dropdown-header text-secondary d-flex align-items-center mb-2">
                                    <span class="material-symbols-rounded align-bottom me-2">settings_account_box</span>
                                    Account
                                </h6>
                            </li>
                            <li><hr class="dropdown-divider"></li>
                            <li class="mb-2">
                                <a class="dropdown-item d-flex align-items-center" href="<?= base_url('profile') ?>">
                                    <span class="material-symbols-rounded me-2">account_box</span>
                                    Profile
                                </a>
                            </li>
                            <li class="mb-2">
                                <a class="dropdown-item d-flex align-items-center" href="#">
                                    <span class="material-symbols-rounded me-2" style="color: red;">delete</span>
                                    Delete my account
                                </a>
                            </li>
                        </ul>
                    </div>

                    <!-- Logout -->
                    <button id="btn-logout" class="btn btn-link nav-link highlight-on-hover d-inline-flex align-items-center gap-1 nav-hover">
                        <span class="material-symbols-rounded align-bottom mr-1">logout</span>
                        <span class="me-1">Logout</span>
                    </button>
                </div>
            </div>
        </div>
    </nav>

    <?php endif; ?>
            
    <?= $this->renderSection('content') ?>

</main>

<?php if (!isset($no_footer) || $no_footer == false): ?>
    <footer class="footer mt-auto bg-secondary bg-opacity-25">
        <div class="container d-flex justify-content-center align-items-center small py-1">
            <a href="<?= base_url('about') ?>">
                <img src="<?= base_url('assets/img/jobSearchIcon.png') ?>" class="footer-icon" style="width: 25px; height: auto; margin-right: 10px;" alt="JobSearch Logo">
            </a>
            <div class="text-center text-secondary mt-1">
                <p class="m-0" ><strong>&copy; Vitor Huggler &ndash; <?= date('Y') ?></strong></p>
                <p class="pb-1 m-0">Job Search &ndash; Version <?= APP_VERSION ?></p>
            </div>
        </div>
    </footer>
<?php endif ?>

<script src="<?= base_url('assets/bootstrap-5.3.3-dist/js/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/jQuery-3.7.0/jquery-3.7.0.min.js') ?>"></script>
<script>

    // Intercept both requests and responses
    $(document).ajaxSend(function(event, jqXHR, settings) {
        console.groupCollapsed('%c[AJAX REQUEST]', 'color:#007BFF;font-weight:bold;');
        console.log('📡 URL:', settings.url);
        console.log('📤 Method:', settings.type || settings.method);
        console.log('📦 Data:', settings.data ? JSON.parse(JSON.stringify(settings.data)) : '(none)');
        console.log('📋 Headers:', jqXHR.requestHeaders || '(not yet available)');
        console.groupEnd();
    });


    // Capture responses as well
    $(document).ajaxComplete(function(event, xhr, settings) {
        console.groupCollapsed('%c[AJAX RESPONSE]', 'color:#28A745;font-weight:bold;');
        console.log('📡 URL:', settings.url);
        console.log('📥 Status:', xhr.status);
        try {
            console.log('📄 Response JSON:', JSON.parse(xhr.responseText));
        } catch (e) {
            console.log('📄 Response Text:', xhr.responseText);
        }
        console.groupEnd();
    });


    // Intercept headers before sending (works with override)
    (function($) {
        const oldAjax = $.ajax;
        $.ajax = function(settings) {
            // Log detailed headers and body before sending
            const headers = settings.headers || {};
            console.groupCollapsed('%c[AJAX DEBUG]', 'color:#FF8C00;font-weight:bold;');
            console.log('🔗 URL:', settings.url);
            console.log('📬 Method:', settings.type || settings.method);
            console.log('📦 Body:', settings.data || '(none)');
            console.log('📋 Headers:', headers);
            console.groupEnd();

            return oldAjax.apply(this, arguments);
        };
    })(jQuery);



    // Ensure that the whole content are loaded before showing the current page
    window.onload = function () {
        // Check if 'loading_screen' exists
        const loadingScreen = document.getElementById('loading_screen');
        if (loadingScreen) {
            loadingScreen.style.display = 'none';
        }
    };


    function decodeJWT(token) {
        if (!token) return null;

        try {
            const payloadPart = token.split('.')[1]; // JWT: header.payload.signature
            const base64 = payloadPart.replace(/-/g, '+').replace(/_/g, '/'); // Base64URL -> Base64
            const jsonPayload = decodeURIComponent(atob(base64).split('').map(function(c) {
                return '%' + ('00' + c.charCodeAt(0).toString(16)).slice(-2);
            }).join(''));

            return JSON.parse(jsonPayload);
        } catch (e) {
            console.error('Invalid JWT token', e);
            return null;
        }
    }


    // Show alert Messages
    var alertTimeout; // Global var for timer control
    function showMessage(type, message) {
        var alertElement = $('#dynamic-alert');
        var alertTitleElement = $('#alert_title');
        var messageAlertElement = $('#message_alert');

        // Clear any pending animation or timeout
        alertElement.stop(true, true).hide();
        clearTimeout(alertTimeout);

        // Reset classes
        alertElement.removeClass('alert-success alert-danger');

        // Message Content
        alertTitleElement.text(type === 'success' ? 'Success: ' : 'Error: ');
        messageAlertElement.html(message);

        // Visual Class
        if (type === 'success') {
            alertElement.addClass('alert-success');
        } else {
            alertElement.addClass('alert-danger');
        }

        // Show the alert
        alertElement.slideDown(300);

        // Slideup timer
        alertTimeout = setTimeout(function() {
            alertElement.slideUp(300);
        }, 10000);
    }


    // Close button
    $('#close-alert').on('click', function() {
        $('#dynamic-alert').slideUp(300);
    });


    document.addEventListener('DOMContentLoaded', function() {
        // Get username from localStorage
        const username = localStorage.getItem('username') || 'User';

        // Get the <b> element
        const usernameElement = document.getElementById('username');

        // Set the username text
        usernameElement.textContent = username;
    });


    $(document).ready(function() {

        // Display username from localStorage
        const username = localStorage.getItem('username') || 'Unknown User';
        const role = localStorage.getItem('role');
        $('#username').text(username);
        $('#user-icon').text(role == 'company' ? 'business_center' : 'person');

        // AJAX logout
        $('#btn-logout').on('click', function(e) {
            e.preventDefault();

            const $button = $(this);
            const token = localStorage.getItem('token');

            if (!token) {
                showMessage('error', 'No token found. Unable to logout.');
                return;
            }

            // Show spinner & disable button
            setLoadingButton($button, true);

            $.ajax({
                url: localStorage.getItem('server_url') + '/logout',
                method: 'POST',
                headers: {
                    'Authorization': 'Bearer ' + token,
                    'Accept': 'application/json'
                },
                dataType: 'json',
                success: function(response) {
                    showMessage('success', response.message || 'Logout successful.');
                    // Clear localStorage
                    localStorage.removeItem('token');
                    localStorage.removeItem('username');
                    // Redirect to login
                    setTimeout(() => window.location.href = "<?= base_url('login') ?>", 1000);
                },
                error: function(xhr) {
                    const response = xhr.responseJSON || {};
                    showMessage('error', response.message || 'Logout error.');
                    setLoadingButton($button, false);
                }
            });
        });


        function setLoadingButton($button, isLoading) {
            if (isLoading) {
                const originalText = $button.html();
                $button.data('original-text', originalText);
                $button.prop('disabled', true);
                $button.html(`<span class="spinner-border spinner-border-sm me-2" style="width: 1rem; height: 1rem;"></span>Logging out...`);
            } else {
                const originalText = $button.data('original-text') || 'Logout';
                $button.prop('disabled', false);
                $button.html(originalText);
            }
        }
    });


    // Open delete account modal
    $('.dropdown-item:contains("Delete my account")').on('click', function(e) {
        e.preventDefault();
        const deleteModal = new bootstrap.Modal(document.getElementById('deleteAccountModal'));
        deleteModal.show();
    });

    // Action when user clicks "Confirm" in modal
    $('#confirmDeleteAccount').on('click', function() {
        // Hide modal immediately
        const deleteModalEl = document.getElementById('deleteAccountModal');
        const modalInstance = bootstrap.Modal.getInstance(deleteModalEl);
        modalInstance.hide();

        deleteAccount();
    });

    // Placeholder for AJAX delete function
    function deleteAccount() {
        const token = localStorage.getItem('token');
        const role = localStorage.getItem('role');
        const sub = localStorage.getItem('sub');
        const serverUrl = localStorage.getItem('server_url');
        const headers = { 
            "Accept": "application/json", 
            "Authorization": "Bearer " + token 
        };

        let endpoint = role === 'company' ? `/companies/${sub}` : `/users/${sub}`;

        $.ajax({
            url: serverUrl + endpoint,
            method: 'DELETE',
            headers: headers,
            complete: function(xhr) {
                const status = xhr.status;
                const response = xhr.responseJSON || {};
                const message = response.message || "No message returned.";

                if (status === 200) {
                    showMessage("success", `(${status}) ${message || 'Account deleted successfully.'}`);
                } else {
                    showMessage("error", `(${status}) ${message || "Error on account delete."}`);
                }
            },
            error: function(xhr) {
                const msg = xhr.responseJSON?.message || "Error on account delete.";
                showMessage('error', msg);
            }
        });
    }


    // Bootstrap Tooltips
    $(document).ready(function () {
        const tooltips = document.querySelectorAll('.tt')
        tooltips.forEach(t => {
            new bootstrap.Tooltip(t)
        })
    });


    // Aux functions to hide / show the loading overlay
    /*
    - Sync bootstrap modal events to avoid race conditions
    - Queues attempts to open a modal if a hide is in progress
    - Ensures that the loading overlay does not cause the Bootstrap backdrop to be lost
    - Does not create or remove .modal-backdrop; Only handles its own overlay
    */

    (function ($) {
        // Internal State
        var modalIsHiding = false;          // True while bootstrap modal is in hide process
        var loadingPending = false;         // if showLoading was requested during a hide, wait until hidden
        var loadingPendingMessage = null;
        var overlayVisible = false;         // current overlay visibility
        var queuedShowQueue = [];           // Queue of modals that tried to open while hide was in progress

        // Setup
        var OVERLAY_Z = 1045; // between backdrop (1040) and modal (1050). Never EVER above the modal.

        // Internal function to show overlay
        function _doShowLoading(message) {
            $('#loading_message').text(message);
            $('#loading_overlay').css('z-index', OVERLAY_Z).stop(true, true).fadeIn(120, function () {
                overlayVisible = true;
            });
        }

        // Bootstrap Events (for sync)
        $(document).on('hide.bs.modal', '.modal', function () {
            modalIsHiding = true;
        });

        $(document).on('hidden.bs.modal', '.modal', function () {
            modalIsHiding = false;

            // If there is any queued modals awaiting to open, just open the next one immediately
            // setTimeout to 0 to ensure that the bootstrap flux finish completely
            if (queuedShowQueue.length > 0) {
                var nextModal = queuedShowQueue.shift();
                setTimeout(function () {
                    $(nextModal).modal('show');
                }, 0);
                // Don't show loading cause we'll open a new modal immediately
                return;
            }

            // If there was any request for showLoading while it was closing, run it NOW
            if (loadingPending) {
                var msg = loadingPendingMessage;
                loadingPending = false;
                loadingPendingMessage = null;
                _doShowLoading(msg);
            }
        });


        $(document).on('show.bs.modal', '.modal', function (e) {
            // If there is an running hide(), we prevent the show() and put the new request in queue
            if (modalIsHiding) {
                e.preventDefault();
                // Store the modal DOM to open it later
                queuedShowQueue.push(e.target);
                return;
            }

            // If there is any visible loading overlay, we hide it immediately
            // to avoid double dark layer when the modal opens
            if (overlayVisible) {
                $('#loading_overlay').stop(true, true).fadeOut(80, function () {
                    overlayVisible = false;
                });
            }
        });


        // Expose the global functions according to his actual usage
        window.showLoading = function (message) {
            // If a modal is in the middle proccess of closing, wait for hidden.bs.modal
            // to avoid race conditions. The request still as "pending".
            if (modalIsHiding) {
                loadingPending = true;
                loadingPendingMessage = message;
                return;
            }

            // If there are already have modals queued to open, it is assumed that there will be a visible modal soon;
            // So, we consider the loading as "pending" until the queue is fully processed
            if (queuedShowQueue.length > 0) {
                loadingPending = true;
                loadingPendingMessage = message;
                return;
            }

            // Normal case: Show Now()
            _doShowLoading(message);
        };


        window.hideLoading = function () {
            // Cancel pending request (if any)
            loadingPending = false;
            loadingPendingMessage = null;

            $('#loading_overlay').stop(true, true).fadeOut(120, function () {
                overlayVisible = false;
            });
        };


    })(jQuery);

    
</script>

<?= $this->renderSection('more-scripts') ?>

</body>

</html>
