<?= $this->extend('layouts/default') ?>

<?= $this->section('more-styles') ?>
<!-- Select2 base CSS -->
<link rel="stylesheet" href="<?= base_url('assets/select2/css/select2.min.css') ?>">
<!-- Bootstrap 5 theme for select2 -->
<link rel="stylesheet" href="<?= base_url('assets/select2/css/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->endsection() ?>

<?= $this->section('content') ?>
    <div class="container pt-5 mt-3 mb-3 text-center text-secondary">
        <h1 class="p-0 m-0 fw-bolder">Available Jobs</h1>
        <img class="mt-4" src="<?= base_url('assets/img/jobSearch.png') ?>" alt="JobSearch Logo" height="50">
    </div>

    <div class="container pt-3 mt-2 mb-4">
        <!-- Buttons Row -->
        <div class="d-flex justify-content-between align-items-center mb-3 mx-3">
            <button class="btn btn-outline-primary shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
                <span class="material-symbols-rounded align-bottom">filter_list</span>
                Filters
            </button>
        </div>

        <!-- Offcanvas - Filters panel (default hidden) -->
        <div class="offcanvas offcanvas-start" tabindex="-1" id="filtersOffcanvas" aria-labelledby="filtersOffcanvasLabel">
            <div class="offcanvas-header">
                <h5 class="offcanvas-title" id="filtersOffcanvasLabel">Filters</h5>
                <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
            </div>

            <div class="offcanvas-body">
                <form id="job_search" class="pb-3" method="post" accept-charset="utf-8">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Job Title</label>
                        <input type="text" class="form-control shadow-sm" id="jobTitleFilter" name="jobTitleFilter" maxlength="150">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Area</label>
                        <select id="jobBusinessFilter" name="jobBusinessFilter" class="form-select shadow-sm">
                            <option selected disabled value="">Choose...</option>
                            <?php foreach ($jobs as $job) : ?>
                                <option value="<?= $job['value'] ?>"><?= $job['display'] ?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Company</label>
                        <input type="text" class="form-control shadow-sm" id="jobCompanyFilter" name="jobCompanyFilter">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Job Description</label>
                        <input type="text" class="form-control shadow-sm" id="jobDescriptionFilter" name="jobDescriptionFilter" maxlength="5000">
                    </div>
                    <div class="row">
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label fw-semibold">City</label>
                            <input type="text" class="form-control shadow-sm" id="jobCityFilter" name="jobCityFilter">
                        </div>
                        <div class="col-12 col-md-6 mb-3">
                            <label class="form-label fw-semibold">State</label>
                            <select id="jobStateFilter" name="jobStateFilter" class="form-select shadow-sm">
                                <option selected disabled value="">Choose...</option>
                                <option value="AC">Acre (AC)</option>
                                <option value="AL">Alagoas (AL)</option>
                                <option value="AP">Amapá (AP)</option>
                                <option value="AM">Amazonas (AM)</option>
                                <option value="BA">Bahia (BA)</option>
                                <option value="CE">Ceará (CE)</option>
                                <option value="DF">Distrito Federal (DF)</option>
                                <option value="ES">Espírito Santo (ES)</option>
                                <option value="GO">Goiás (GO)</option>
                                <option value="MA">Maranhão (MA)</option>
                                <option value="MT">Mato Grosso (MT)</option>
                                <option value="MS">Mato Grosso do Sul (MS)</option>
                                <option value="MG">Minas Gerais (MG)</option>
                                <option value="PA">Pará (PA)</option>
                                <option value="PB">Paraíba (PB)</option>
                                <option value="PR">Paraná (PR)</option>
                                <option value="PE">Pernambuco (PE)</option>
                                <option value="PI">Piauí (PI)</option>
                                <option value="RJ">Rio de Janeiro (RJ)</option>
                                <option value="RN">Rio Grande do Norte (RN)</option>
                                <option value="RS">Rio Grande do Sul (RS)</option>
                                <option value="RO">Rondônia (RO)</option>
                                <option value="RR">Roraima (RR)</option>
                                <option value="SC">Santa Catarina (SC)</option>
                                <option value="SP">São Paulo (SP)</option>
                                <option value="SE">Sergipe (SE)</option>
                                <option value="TO">Tocantins (TO)</option>
                            </select>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6 mb-2">
                            <label class="form-label fw-semibold">Min Salary</label>
                            <input type="number" class="form-control shadow-sm" id="jobMinSalaryFilter" name="jobMinSalaryFilter" min="0">
                        </div>
                        <div class="col-6 mb-2">
                            <label class="form-label fw-semibold">Max Salary</label>
                            <input type="number" class="form-control shadow-sm" id="jobMaxSalaryFilter" name="jobMaxSalaryFilter" min="0">
                        </div>
                    </div>
                    <div class="d-grid gap-2">
                        <button class="btn btn-primary" id="filter" type="button">
                            <span class="material-symbols-rounded align-bottom">filter_alt</span> Buscar
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="clear">
                            <span class="material-symbols-rounded align-bottom">filter_alt_off</span> Limpar
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <!-- End Offcanvas -->

        <!-- Modal Form -->
        <div class="modal fade" id="job_modal" tabindex="-1" aria-labelledby="job_modal_label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="job_modal_label"></h5>
                    </div>
                    <div class="modal-body bg-light">
                        <form id="job_form">
                            <input type="hidden" id="jobId">
                            <div class="row justify-content-center pb-3 align-items-center">
                                <div class="col-12 col-md-6 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Company</span>
                                        <input type="text" class="form-control" name="jobCompany" id="jobCompany" readonly>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Contact</span>
                                        <input type="text" class="form-control" name="jobContact" id="jobContact" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center pb-3 align-items-center">
                                <div class="col-12 col-md-6 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Job Title</span>
                                        <input type="text" class="form-control" name="jobTitle" id="jobTitle" readonly>
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Business</span>
                                        <input type="text" class="form-control" name="jobBusiness" id="jobBusiness" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center pb-2 align-items-center">
                                <div class="col-12 col-md-4 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">State</span>
                                        <input type="text" class="form-control" name="jobState" id="jobState" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-md-4 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">City</span>
                                        <input type="text" class="form-control" name="jobCity" id="jobCity" readonly>
                                    </div>
                                </div>

                                <div class="col-12 col-md-2 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Salary</span>
                                        <input type="number" class="form-control" name="jobSalary" id="jobSalary" readonly>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center pb-2 align-items-center">
                                <div class="col-12 col-md-10 px-1 pb-2">
                                    <textarea id="jobDescription" name="jobDescription" cols="12" rows="8" class="form-control shadow-sm bg-light" aria-required="false" aria-invalid="false"  placeholder="Describe the job responsibilities (e.g., Assist in the development of web projects using JSON in the communication protocol at the application transport layer.)" readonly></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-primary" id="close" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" id="goToProfile">I'm interested</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Profile Modal Form -->
        <div class="modal fade" id="profile_modal" tabindex="-1" aria-labelledby="profile_modal_label" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-xl">
                <div class="modal-content">
                    <div class="modal-header bg-light">
                        <h5 class="modal-title" id="profile_modal_label">Confirm information before continue:</h5>
                    </div>
                    <div class="modal-body bg-light">
                        <form id="profile_form">
                            <div class="row justify-content-center pb-3 align-items-center">
                                <div class="col-12 col-md-4 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Full Name *</span>
                                        <input type="text" class="form-control" name="fullName" id="fullName">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Email</span>
                                        <input type="email" class="form-control" name="email" id="email">
                                    </div>
                                </div>
                                <div class="col-12 col-md-3 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Phone</span>
                                        <input type="text" class="form-control" name="phone" id="phone">
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center pb-2 align-items-center">
                                <div class="col-12 col-md-10 px-1">
                                    <span class="input-group-text d-flex justify-content-center bg-secondary bg-opacity-10">Professional Experience *</span>
                                </div>
                            </div>
                            <div class="row justify-content-center pb-2 align-items-center">
                                <div class="col-12 col-md-10 px-1 pb-2">
                                    <textarea id="experience" name="experience" cols="12" rows="8" class="form-control shadow-sm bg-light" aria-required="false" aria-invalid="false" placeholder="Describe the job responsibilities (e.g., Assist in the development of web projects using JSON in the communication protocol at the application transport layer.)"></textarea>
                                </div>
                            </div>
                            <div class="row justify-content-center align-items-center">
                                <div class="col-12 col-md-10 px-1 pb-2">
                                    <span class="input-group-text d-flex justify-content-center bg-secondary bg-opacity-10">Education *</span>
                                </div>
                            </div>
                            <div class="row justify-content-center pb-2 align-items-center">
                                <div class="col-12 col-md-10 px-1">
                                    <textarea id="education" name="education" cols="12" rows="8" class="form-control shadow-sm bg-light" aria-required="false" aria-invalid="false" placeholder="Provide more details about where you studied, what you graduated from, what courses you took, etc. (e.g., 2021 - 2025: UTFPR - Systems Analysis and Development)"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-primary" id="close" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-success" id="applyToJob">
                            <span class="material-symbols-rounded align-bottom">approval_delegation</span> Send my application
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="container mt-4">
            <!-- Job Container -->
            <div id="jobsContainerWrapper" class="position-relative" style="min-height: 200px;">
                <!-- Loading overlay (shown as default) -->
                <div id="jobsLoading" class="position-absolute top-0 start-0 w-100 h-100 d-flex 
                    justify-content-center align-items-center bg-white bg-opacity-75" 
                    style="z-index: 10;">
                    <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                        <span class="visually-hidden">Loading...</span>
                    </div>
                </div>
                <!-- Cards -->
                <div id="jobsContainer" class="row g-3"></div>
            </div>
        </div>
    </div> <!-- End main container -->

<?= $this->endSection() ?>

<?= $this->section('more-scripts') ?>
<script src="<?= base_url("assets/select2/js/select2.min.js") ?>"></script>
<script src="<?= base_url('assets/sweetalert2/sweet.min.js') ?>"></script>

<script type="text/javascript">
    $(document).ready(function() {
        const token = localStorage.getItem('token');
        const role = localStorage.getItem('role');
        const sub = localStorage.getItem('sub');
        const serverUrl = localStorage.getItem('server_url');

        const headers = { 
            "Accept": "application/json", 
            "Authorization": "Bearer " + token 
        };

        if (!token || !role || !serverUrl) {
            showMessage('error', 'Missing authentication data. Please log in again.');
            setTimeout(() => window.location.href = "<?= base_url('login') ?>", 2000);
            return;
        }

        var expected = []; // Expected returned fields from server
        var returned = []; // Returned fields from server

        let loadedJobs = [];

        let endpoint = null;

        loadJobs();

        $("#filter").on("click", function () {
            loadJobs();
        });

        $('#clear').on('click', function(e) {
            e.preventDefault();
            $('#job_search')[0].reset();
            $('#jobBusinessFilter, #jobStateFilter').val(null).trigger('change');
            loadJobs();
        });

        function showJobsLoading(show = true) {
            if (show) {
                $("#jobsLoading").removeClass("d-none");
            } else {
                $("#jobsLoading").addClass("d-none");
            }
        }

        function clearModalInputs() {
            $('#jobId').val("");
            $('#job_form')[0].reset();
            $('#profile_form')[0].reset();
        }

        function getJobFilters() {
            const title = $("#jobTitleFilter").val()?.trim() || "";
            const area = $("#jobBusinessFilter").val() || "";
            const company = $("#jobCompanyFilter").val() || "";
            const city = $("#jobCityFilter").val()?.trim() || "";
            const state = $("#jobStateFilter").val() || "";

            // Salary range
            let min = $("#jobMinSalaryFilter").val();
            let max = $("#jobMaxSalaryFilter").val();

            min = min ? parseFloat(min) : null;
            max = max ? parseFloat(max) : null;

            if (min === 0) min = null;
            if (max === 0) max = null;

            return {
                filters: [
                    {
                        title: title,
                        area: area,
                        company: company,
                        city: city,
                        state: state,
                        salary_range: {
                            min: min,
                            max: max
                        }
                    }
                ]
            };
        }

        function loadUserProfile() {
            $.ajax({
                url: serverUrl + `/users/${sub}`,
                method: 'GET',
                headers: headers,
                contentType: "application/json; charset=UTF-8",
                dataType: 'json',
                success: function(response) {
                    $('#fullName').val(response.name);
                    $('#email').val(response.email);
                    $('#phone').val(response.phone);
                    $('#experience').val(response.experience);
                    $('#education').val(response.education);
                    $('#profile_modal').modal('show');
                },
                error: function(xhr) {
                    const status = xhr.status;

                    const msg = xhr.responseJSON?.message || "Failed to load profile.";
                    showMessage('error', `(${status}) ${msg}`);
                }
            });
        } window.loadUserProfile = loadUserProfile;

        function getProfileData() {
            const name = $("#fullName").val()?.trim() || "";
            const email = $("#email").val() || "";
            const phone = $("#phone").val() || "";
            const education = $("#education").val()?.trim() || "";
            const experience = $("#experience").val() || "";

            return {
                name: name,
                email: email,
                phone: phone,
                education: education,
                experience: experience,
            }
        }

        function renderJobCards(items) {
            const container = $("#jobsContainer");
            container.empty();

            items.forEach(job => {
                const card = `
                <div class="col-12 col-md-6">
                    <div class="card shadow-sm h-100">
                        <div class="card-header text-white fw-bold" style="background-color: #4889B4;">
                            #${job.job_id} | ${job.title}
                        </div>

                        <div class="card-body">
                            <p><strong>Business:</strong> ${job.area}</p>
                            <p><strong>Company:</strong> ${job.company}</p>
                            <p><strong>Description:</strong> ${job.description}</p>
                            <p><strong>Locale:</strong> ${job.city} - ${job.state}</p>
                            <p><strong>Salary:</strong> 
                                ${job.salary ? "R$ " + Number(job.salary).toFixed(2).replace(".", ",") : "Not informed."}
                            </p>
                            <p><strong>Contact:</strong> ${job.contact}</p>
                        </div>

                        <div class="card-footer d-flex justify-content-between">
                            <button class="btn btn-sm btn-outline-primary" onclick="readJobDetails(${job.job_id})">
                                <span class="material-symbols-rounded align-bottom">visibility</span> View Details
                            </button>
                            <button class="btn btn-sm btn-success" onclick="prepareJobApplication(${job.job_id})">
                                <span class="material-symbols-rounded align-bottom">approval_delegation</span> Apply to Job
                            </button>
                        </div>
                    </div>
                </div>`;
                
                container.append(card);
            });
        }

        function prepareJobApplication(jobId) {
            if (!jobId) {
                showMessage('error', 'Job ID not provided.');
                return;
            }

            // Preenche o hidden input
            $("#jobId").val(jobId);

            // Carrega informações da vaga
            readJobDetails(jobId);

            // Após carregar a vaga, abre o modal de perfil
            loadUserProfile();
        }
        window.prepareJobApplication = prepareJobApplication;


        function loadJobs() {
            showJobsLoading(true);

            const filters_payload = getJobFilters();
            endpoint = serverUrl + "/jobs/search";

            $.ajax({
                url: endpoint,
                method: "POST",
                data: JSON.stringify(filters_payload),
                contentType: "application/json; charset=UTF-8",
                dataType: "json",
                headers: headers,
                success: function(response) {
                    expected = [
                        { name: "items", type: "array" }
                    ];

                    returned = {
                        items: response.items
                    };

                    verifyReceivedJSON(expected, returned);

                    loadedJobs = response.items || [];
                    if (!response.items || response.items.length === 0) {
                        $("#jobsContainer").html(`
                            <div class="col-12">
                                <div class="alert alert-info text-center shadow-sm">
                                    No jobs found.
                                </div>
                            </div>
                        `);
                        showJobsLoading(false);
                        return;
                    }

                    const expectedJobFields = [
                        {name: "job_id", type: "int"},
                        {name: "title", type: "string"},
                        {name: "area", type: "string"},
                        {name: "company", type: "string"},
                        {name: "description", type: "string"},
                        {name: "state", type: "string"},
                        {name: "city", type: "string"},
                        {name: "salary", type: "float", allowEmpty: true},
                        {name: "contact", type: "string"},
                    ];

                    verifyArrayOfObjects(expectedJobFields, response.items);

                    renderJobCards(response.items);
                    showJobsLoading(false);
                },
                error: function(xhr) {
                    const status = xhr.status;
                    let response = xhr.responseJSON;
                    
                    showJobsLoading(false);

                    if (typeof response === "string") {
                        try { response = JSON.parse(response); }
                        catch { response = {}; }
                    }

                    const msg = response?.message || "Failed to load jobs.";
                    showMessage('error', `(${status}) ${msg}`);
                }
            });
        }

        function readJobDetails(jobId) {
            endpoint = serverUrl + "/jobs/" + jobId;

            $.ajax({
                url: endpoint,
                method: "GET",
                contentType: "application/json; charset=UTF-8",
                headers: headers,
                success: function(response) {
                    expected = [
                        {name: "job_id", type: "number"},
                        {name: "title", type: "string"},
                        {name: "area", type: "string"},
                        {name: "company", type: "string"},
                        {name: "description", type: "string"},
                        {name: "state", type: "string"},
                        {name: "city", type: "string"},
                        {name: "salary", type: "number", allowEmpty: true},
                        {name: "contact", type: "string"},
                    ];

                    verifyReceivedJSON(expected, response);

                    $("#jobId").val(response.job_id);
                    $("#jobCompany").val(response.company);
                    $("#jobContact").val(response.contact);
                    $("#jobTitle").val(response.title);
                    $("#jobBusiness").val(response.area);
                    $("#jobDescription").val(response.description);
                    $("#jobState").val(response.state);
                    $("#jobCity").val(response.city);
                    $("#jobSalary").val(response.salary);

                    $("#job_modal_label").text(`#${response.job_id} | ${response.title}`);
                    $("#job_modal").modal("show");
                },
                error: function(xhr) {
                    const status = xhr.status;
                    let response = xhr.responseJSON;

                    if (typeof response === "string") {
                        try { response = JSON.parse(response); }
                        catch { response = {}; }
                    }

                    const msg = response?.message || "Failed to load job.";
                    showMessage('error', `(${status}) ${msg}`);
                }
            });
        }
        window.readJobDetails = readJobDetails;

        function applyToJob(jobId) {
            endpoint = serverUrl + "/jobs/" + jobId;
            const payload = getProfileData();

            showLoading(`Sending application to job #${jobId}...`);

            let data = {
                name: $('#name').val() || "",
                email: $('#email').val() || "",
                phone: $('#phone').val() || "",
                education: $('#education').val() || "",
                experience: $('#experience').val() || "",
            };

            $('#profile_modal').modal('hide');
            $('#job_modal').modal('hide');

            $.ajax({
                url: endpoint,
                method: "POST",
                headers: headers,
                contentType: 'application/json',
                data: JSON.stringify(payload),
                dataType: 'json',
                success: function(response, textStatus, xhr) {
                    hideLoading();
                    const status = xhr.status;
                    const message = response.message || `Applied to job #${jobId} successfully.`;
                    showMessage("success", `(${status}) ${message}`);
                    clearModalInputs();
                    loadJobs();
                },
                error: function(xhr) {
                    hideLoading();

                    const status = xhr.status;
                    let response = xhr.responseJSON;

                    if (typeof response === "string") {
                        try { response = JSON.parse(response); }
                        catch { response = {}; }
                    }

                    if (status === 422 && response.details) {
                        let errorMsg = `<strong>(${status}) ${response.message || "Validation Error"}</strong><ul>`;
                        response.details.forEach(item => {
                            errorMsg += `<li>${item.field}: ${item.error}</li>`;
                        });
                        errorMsg += "</ul>";
                        showMessage("error", errorMsg);
                        $('#job_modal').modal('show');
                        $('#profile_modal').modal('show');
                        return;
                    }

                    const msg = response?.message || `Unable to apply to job #${jobId}.`;

                    $('#job_modal').modal('show');
                    $('#profile_modal').modal('show');
                    showMessage('error', `(${status}) ${msg}`);
                }
            });
        } window.applyToJob = applyToJob;

        $('#applyToJob').click(function(e) {
            applyToJob($('#jobId').val());
        });

        // SELECT2 - Filters (offcanvas)
        $('#jobBusinessFilter, #jobStateFilter').select2({
            theme: 'bootstrap-5',
            placeholder: 'Choose...',
            allowClear: true,
            dropdownParent: $('#filtersOffcanvas'),
            selectionCssClass: 'shadow-sm'
        });

        document.getElementById('filtersOffcanvas').addEventListener('shown.bs.offcanvas', function () {
            $('#jobTitleFilter').trigger('focus');
        });

        $('#job_form').on('submit', function(e) {
            e.preventDefault();
        });

        $('#goToProfile').on('click', function(e) {
            loadUserProfile();
        });

    });
</script>
<?= $this->endsection() ?>
