<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container d-flex py-5 my-5 justify-content-center">

    <!-- Card: Server Config -->
    <div id="card-server" class="card shadow-lg p-4 bg-secondary bg-opacity-10 rounded" style="width: 500px; display:block;">
        <div class="mb-3 d-flex align-items-center">
            <h5 class="mb-0 me-2">Destination Server</h5>
            <span class="material-symbols-rounded" 
                data-bs-toggle="tooltip" 
                data-bs-placement="right" 
                title="Provide the IP address and Port of the destination server. Note: This configuration must reflect the server the CLIENT application will reach, not the server itself. Server settings are defined via .env."
                style="cursor: default;">
                help
            </span>
        </div>
        <form id="server_form" class="form px-2">
            <div class="mb-3">
                <label for="server_ip" class="form-label">Server IP *</label>
                <input type="text" class="form-control" id="server_ip" name="server_ip" placeholder="e.g. 192.168.1.10">
            </div>
            <div class="mb-3">
                <label for="server_port" class="form-label">Server Port *</label>
                <input type="text" class="form-control" id="server_port" name="server_port" placeholder="e.g. 9000">
            </div>
            <button type="submit" class="btn btn-primary w-100">Save & Continue</button>
        </form>
    </div>

    <!-- Card: Login -->
    <div id="card-login" class="card shadow-lg p-4 bg-secondary bg-opacity-10 rounded" style="width: 500px; display:none;">
        <img class="mb-3 mx-auto d-block" src="<?= base_url('assets/img/jobSearch.png') ?>" alt="Job Search Logo" style="max-width: 200px;">
        <form method="POST" id="login_form" class="form px-2">
            <div class="mb-3">
                <label for="username" class="form-label">Username *</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Username">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password *</label>
                <input type="password" class="form-control" id="password" name="password" placeholder="Password">
            </div>
            <button class="btn btn-primary w-100" type="submit">Login</button>
        </form>
        <div class="text-center mt-3">
            <span>Don't have an account? </span>
            <a href="#" id="btn-show-register">Click here</a>
        </div>
    </div>

    <!-- Card: Register -->
    <div id="card-register" class="card shadow-lg p-4 bg-secondary bg-opacity-10 rounded mb-3" style="width: 600px; display:none;">
        <h4 class="mb-3 text-center">Create Account</h4>
        <div class="mb-3">
            <label for="account_type" class="form-label">Account Type *</label>
            <select id="account_type" class="form-select">
                <option value="" disabled selected>Select...</option>
                <option value="user">User</option>
                <option value="company">Company</option>
            </select>
        </div>

        <!-- User Form -->
        <form id="register_user_form" class="mb-3" method="POST" style="display:none;">
            <div class="mb-3">
                <label for="name" class="form-label">Full Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Full Name *">
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control" placeholder="Username *">
                </div>
                <div class="col">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Password *">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="email" class="form-label">E-Mail</label>
                    <input type="email" name="email" class="form-control" placeholder="E-Mail">
                </div>
                <div class="col">
                    <label for="phone" class="form-label">Phone</label>
                    <input type="text" name="phone" class="form-control" placeholder="Phone">
                </div>
            </div>

            <div class="mb-3">
                <label for="experience" class="form-label">Professional Experience</label>
                <textarea name="experience" class="form-control" rows="4" placeholder="Describe your professional experience (e.g., 5 years as a software developer, worked with Python, JavaScript, and CI/CD processes)"></textarea>
            </div>

            <div class="mb-3">
                <label for="education" class="form-label">Education</label>
                <textarea name="education" class="form-control" rows="4" placeholder="Provide more details about where you studied, what you graduated from, what courses you took, etc. (e.g., 2021 - 2025: UTFPR - Systems Analysis and Development)"></textarea>
            </div>

            <button type="submit" class="btn btn-success w-100">Register</button>
        </form>

        <!-- Company Form -->
        <form id="register_company_form" class="mb-3" method="POST" style="display:none;">
            <div class="mb-3">
                <label for="name" class="form-label">Company Name *</label>
                <input type="text" name="name" class="form-control" placeholder="Company Name *">
            </div>

            <div class="mb-3">
                <label for="business" class="form-label">Business *</label>
                <input type="text" name="business" class="form-control" placeholder="Business *">
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="username" class="form-label">Username *</label>
                    <input type="text" name="username" class="form-control" placeholder="Username *">
                </div>
                <div class="col">
                    <label for="password" class="form-label">Password *</label>
                    <input type="password" name="password" class="form-control" placeholder="Password *">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="street" class="form-label">Street *</label>
                    <input type="text" name="street" class="form-control" placeholder="Street *">
                </div>
                <div class="col">
                    <label for="number" class="form-label">Number *</label>
                    <input type="text" name="number" class="form-control" placeholder="Number *">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="city" class="form-label">City *</label>
                    <input type="text" name="city" class="form-control" placeholder="City *">
                </div>
                <div class="col">
                    <label for="state" class="form-label">State *</label>
                    <input type="text" name="state" class="form-control" placeholder="State *">
                </div>
            </div>

            <div class="row mb-3">
                <div class="col">
                    <label for="email" class="form-label">E-Mail *</label>
                    <input type="email" name="email" class="form-control" placeholder="E-Mail *">
                </div>
                <div class="col">
                    <label for="phone" class="form-label">Phone *</label>
                    <input type="text" name="phone" class="form-control" placeholder="Phone *">
                </div>
            </div>

            <button type="submit" class="btn btn-success w-100">Register</button>
        </form>


        <div class="text-center">
            <span>or</span>
        </div>

        <!-- Back to Login -->
        <div class="text-center mt-3">
            <button type="button" id="btn-back-login" class="btn btn-secondary d-inline-flex align-items-center gap-2">
                <span class="material-symbols-rounded">undo</span>
                <span>Back to Login</span>
            </button>
        </div>
    </div>

</div>
<?= $this->endSection() ?>

<!-- *********************************************** -->
<?= $this->section('more-scripts') ?>

<script src="<?= base_url("/assets/jquery-validation-1.19.5/jquery.validate.min.js") ?>"></script>
<script src="<?= base_url("/assets/jquery-validation-1.19.5/additional-methods.min.js") ?>"></script>
<script type="text/javascript">
    $(document).ready(function () {

        $('[data-bs-toggle="tooltip"]').each(function () {
            new bootstrap.Tooltip(this);
        });


        let serverBaseUrl = ""; // will store http://IP:PORT


        // Helper: Switch cards with animation
        function switchCard(hideSelector, showSelector) {
            $(hideSelector).fadeOut(300, function () {
                $(showSelector).fadeIn(300);
            });
        }


        // Universal loading spinner for buttons
        function setLoadingButton($button, isLoading) {
            if (isLoading) {
                const originalText = $button.html();
                $button.data('original-text', originalText);
                $button.prop('disabled', true);
                $button.html(
                    `<span class="spinner-border spinner-border-sm me-2" style="width: 1rem; height: 1rem;"></span>Processing...`
                );
            } else {
                const originalText = $button.data('original-text') || 'Submit';
                $button.prop('disabled', false);
                $button.html(originalText);
            }
        }


        // Save Server IP + Port
        $('#server_form').validate({
            rules: {
                server_ip: { required: true },
                server_port: { required: true, number: true, min: 1, max: 65535 }
            },
            messages: {
                server_ip: { required: "You must provide the server destination IP Address." },
                server_port: {
                    required: "You must provide the server destination port.",
                    number: "The port must be a number.",
                    min: "The port must be at least 1.",
                    max: "The port must be at most 65535"
                }
            },
            invalidHandler: function(e, validator) {
                let errorMsg = "<ul>";
                validator.errorList.forEach(item => {
                    errorMsg += `<li>${item.message}</li>`;
                });
                errorMsg += "</ul>";
                showMessage('error', errorMsg);
            },
            errorPlacement: function () {},
            highlight: function (element) { $(element).addClass('is-invalid'); },
            unhighlight: function (element) { $(element).removeClass('is-invalid'); },
            submitHandler: function (form) {
                const ip = $('#server_ip').val().trim() != '127.0.0.1' ? $('#server_ip').val().trim() : 'localhost';

                const port = $('#server_port').val().trim();

                serverBaseUrl = `http://${ip}:${port}`;
                switchCard("#card-server", "#card-login");
                return false;
            }
        });


        // AJAX form submit helper
        function ajaxFormSubmit($form, endpoint, successMsg, onSuccess, isUserForm = false) {
            $form.on('submit', function(e) {
                e.preventDefault();

                if (!serverBaseUrl) {
                    return showMessage("error", "Server not configured.");
                }

                const $button = $(this).find('button[type="submit"]');
                setLoadingButton($button, true);

                $.ajax({
                    url: serverBaseUrl + endpoint,
                    method: "POST",
                    data: JSON.stringify($form.serializeArray().reduce((obj, item) => {
                        obj[item.name] = item.value;
                        return obj;
                    }, {})),
                    contentType: "application/json; charset=UTF-8",
                    dataType: "json",
                    headers: { "Accept": "application/json" },
                    complete: function(xhr) {
                        const status = xhr.status;
                        const response = xhr.responseJSON || {};
                        const message = response.message || xhr.statusText || "No message returned.";

                        if (status === 200 || status === 201) {
                            showMessage("success", `(${status}) ${message || successMsg}`);
                            if (typeof onSuccess === 'function') onSuccess(response);
                        } else {
                            if (isUserForm && status === 422 && response.details) {
                                let errorMsg = `<strong>${response.message || "Validation Error"}</strong><ul>`;
                                response.details.forEach(item => {
                                    errorMsg += `<li>${item.field}: ${item.error}</li>`;
                                });
                                errorMsg += "</ul>";
                                showMessage("error", errorMsg);
                            } else {
                                showMessage("error", `(${status}) ${message}`);
                            }
                        }
                    },
                    error: function() {
                        setLoadingButton($button, false);
                        showMessage("error", "Connection error or invalid response.");
                    }
                });
            });
        }


        // Login
        ajaxFormSubmit(
            $('#login_form'),
            '/login',
            'Login successful.',
            function(response) {
                if (response && response.token) {
                    const payload = decodeJWT(response.token);

                    if (payload) {
                        localStorage.clear();
                        localStorage.setItem('server_url', serverBaseUrl);
                        localStorage.setItem('sub', payload.sub);
                        localStorage.setItem('token', response.token);
                        localStorage.setItem('username', payload.username.toLowerCase());
                        localStorage.setItem('role', payload.role.toLowerCase());
                        localStorage.setItem('expires_in', payload.exp);

                        setTimeout(() => window.location.href = "<?= base_url('about') ?>", 1000);
                    } else {
                        showMessage('error', 'Failed to decode token payload.');
                    }
                }
            }
        );


        // User Registration
        ajaxFormSubmit(
            $('#register_user_form'),
            '/users',
            'User created successfully.',
            function() { $('#btn-back-login').trigger('click'); },
            true
        );


        // Company Registration
        ajaxFormSubmit(
            $('#register_company_form'),
            '/companies',
            'Company registered successfully.',
            function() { $('#btn-back-login').trigger('click'); }
        );


        // Account type selector
        $("#account_type").change(function(){
            const val = $(this).val();
            const $current = (val === "user") ? $("#register_user_form") : $("#register_company_form");
            const $other = (val === "user") ? $("#register_company_form") : $("#register_user_form");
            if($other.is(":visible")){
                $other.fadeOut(200, function(){ $current.fadeIn(200); });
            } else { $current.fadeIn(200); }
        });


        // Go to register
        $("#btn-show-register").click(function(e){
            e.preventDefault();
            switchCard("#card-login", "#card-register");
        });


        // Back to login
        $("#btn-back-login").click(function(e){
            e.preventDefault();
            switchCard("#card-register", "#card-login");
            $("#register_user_form, #register_company_form").hide();
            $("#account_type").val("");
        });

        
    });

</script>

<?= $this->endsection() ?>
