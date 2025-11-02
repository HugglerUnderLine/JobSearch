<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-5 my-5 d-flex justify-content-center">
    <div id="card-profile" class="card shadow-lg p-4 bg-secondary bg-opacity-10 rounded" style="width: 600px;">
        <h4 id="profile-title" class="mb-4 text-center"></h4>

        <!-- User Profile Form -->
        <form id="profile_user_form" style="display: none;">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" id="user_username" name="user_username" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input type="text" id="name" name="name" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">E-Mail</label>
                <input type="email" id="email" name="email" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="password" name="password" class="form-control" disabled placeholder="Leave blank to keep current">
            </div>
            <div class="mb-3">
                <label class="form-label">Phone</label>
                <input type="text" id="phone" name="phone" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Professional Experience</label>
                <textarea id="experience" name="experience" class="form-control" rows="3" disabled></textarea>
            </div>
            <div class="mb-3">
                <label class="form-label">Education</label>
                <textarea id="education" name="education" class="form-control" rows="3" disabled></textarea>
            </div>
        </form>

        <!-- Company Profile Form -->
        <form id="profile_company_form" style="display: none;">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" id="company_username" name="company_username" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Company Name</label>
                <input type="text" id="company_name" name="company_name" class="form-control" disabled>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input type="password" id="password_company" name="password" class="form-control" disabled placeholder="Leave blank to keep current">
            </div>
            <div class="mb-3">
                <label class="form-label">Business</label>
                <input type="text" id="business" name="business" class="form-control" disabled>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">Street</label>
                    <input type="text" id="street" name="street" class="form-control" disabled>
                </div>
                <div class="col">
                    <label class="form-label">Number</label>
                    <input type="text" id="number" name="number" class="form-control" disabled>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">City</label>
                    <input type="text" id="city" name="city" class="form-control" disabled>
                </div>
                <div class="col">
                    <label class="form-label">State</label>
                    <input type="text" id="state" name="state" class="form-control" disabled>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col">
                    <label class="form-label">E-Mail</label>
                    <input type="email" id="company_email" name="company_email" class="form-control" disabled>
                </div>
                <div class="col">
                    <label class="form-label">Phone</label>
                    <input type="text" id="company_phone" name="company_phone" class="form-control" disabled>
                </div>
            </div>
        </form>

        <!-- Buttons -->
        <div class="text-center mt-3">
            <button id="btn_edit" class="btn btn-primary">Edit</button>
            <button id="btn_save" class="btn btn-success" style="display:none;">Save</button>
            <button id="btn_cancel" class="btn btn-secondary" style="display:none;">Cancel</button>
        </div>

        <div id="profile_loader" class="text-center my-3">
            <div class="spinner-border text-primary" role="status"></div>
            <div>Loading profile...</div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('more-scripts') ?>
<script>
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        const role = localStorage.getItem('role');
        const sub = localStorage.getItem('sub');
        const serverUrl = localStorage.getItem('server_url');

        if (!token || !role || !serverUrl) {
            showMessage('error', 'Missing authentication data. Please log in again.');
            setTimeout(() => window.location.href = "<?= base_url('login') ?>", 2000);
            return;
        }

        const headers = { 
            "Accept": "application/json", 
            "Authorization": "Bearer " + token 
        };
        let endpoint = role === 'company' ? `/companies/${sub}` : `/users/${sub}`;

        function enableEdit(form) {
            $(form).find('input, textarea').not('[id$="username"]').prop('disabled', false);
            $('#btn_edit').hide();
            $('#btn_save, #btn_cancel').show();
        }

        function disableEdit(form) {
            $(form).find('input, textarea').prop('disabled', true);
            $('#btn_edit').show();
            $('#btn_save, #btn_cancel').hide();
        }

        function loadProfile() {
            $.ajax({
                url: serverUrl + endpoint,
                method: 'GET',
                headers: headers,
                contentType: "application/json; charset=UTF-8",
                dataType: 'json',
                success: function(response) {
                    $('#profile_loader').hide();
                    if (role === 'company') {
                        $('#profile_company_form').show();
                        $('#company_username').val(response.username);
                        $('#company_name').val(response.name);
                        $('#business').val(response.business);
                        $('#street').val(response.street);
                        $('#number').val(response.number);
                        $('#city').val(response.city);
                        $('#state').val(response.state);
                        $('#company_email').val(response.email);
                        $('#company_phone').val(response.phone);
                    } else {
                        $('#profile_user_form').show();
                        $('#user_username').val(response.username);
                        $('#name').val(response.name);
                        $('#email').val(response.email);
                        $('#phone').val(response.phone);
                        $('#experience').val(response.experience);
                        $('#education').val(response.education);
                    }
                },
                error: function(xhr) {
                    $('#profile_loader').hide();
                    const msg = xhr.responseJSON?.message || "Failed to load profile.";
                    showMessage('error', msg);
                }
            });
        }

        // Update title based on role
        const titleElement = $('#profile-title');
        if (role === 'company') {
            titleElement.text('Company Profile');
        } else {
            titleElement.text('User Profile');
        }


        loadProfile();

        $('#btn_edit').click(function(e) {
            e.preventDefault();
            if (role === 'company') enableEdit('#profile_company_form');
            else enableEdit('#profile_user_form');
        });

        $('#btn_cancel').click(function(e) {
            e.preventDefault();
            if (role === 'company') disableEdit('#profile_company_form');
            else disableEdit('#profile_user_form');
            loadProfile(); // restore original values
        });

        $('#btn_save').click(function(e) {
            e.preventDefault();
            let data = {};
            if (role === 'company') {
                data = {
                    name: $('#company_name').val() || "",
                    password: $('#password_company').val() || "",
                    business: $('#business').val() || "",
                    street: $('#street').val() || "",
                    number: $('#number').val() || "",
                    city: $('#city').val() || "",
                    state: $('#state').val() || "",
                    email: $('#company_email').val() || "",
                    phone: $('#company_phone').val() || ""
                };
            } else {
                data = {
                    name: $('#name').val() || "",
                    password: $('#password').val() || "",
                    email: $('#email').val() || "",
                    phone: $('#phone').val() || "",
                    experience: $('#experience').val() || "",
                    education: $('#education').val() || ""
                };
            }

            $.ajax({
                url: serverUrl + endpoint,
                method: 'PATCH',
                headers: headers,
                contentType: 'application/json',
                data: JSON.stringify(data),
                dataType: 'json',
                complete: function(xhr) {
                    const status = xhr.status;
                    const response = xhr.responseJSON || {};
                    const message = response.message || "No message returned.";

                    if (status === 200) {
                        showMessage("success", `(${status}) ${message || 'Profile updated successfully.'}`);
                        if (role === 'company') disableEdit('#profile_company_form');
                        else disableEdit('#profile_user_form');
                        loadProfile();
                    } else {
                        if (status === 422 && response.details) {
                            let errorMsg = `<strong>${response.message || "Validation Error"}</strong><ul>`;
                            response.details.forEach(item => {
                                errorMsg += `<li>${item.field}: ${item.error}</li>`;
                            });
                            errorMsg += "</ul>";
                            showMessage("error", errorMsg);
                        } else {
                            showMessage("error", `(${status}) ${message || "Failed to save profile"}`);
                        }
                    }
                },
                error: function(xhr) {
                    const msg = xhr.responseJSON?.message || "Failed to save profile.";
                    showMessage('error', msg);
                }
            });
        });
    });
</script>
<?= $this->endSection() ?>
