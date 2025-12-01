<?= $this->extend('layouts/default') ?>

<?= $this->section('more-styles') ?>
<!-- Select2 base CSS -->
<link rel="stylesheet" href="<?= base_url('assets/select2/css/select2.min.css') ?>">
<!-- Bootstrap 5 theme for select2 -->
<link rel="stylesheet" href="<?= base_url('assets/select2/css/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->endsection() ?>

<?= $this->section('content') ?>
    <div class="container pt-5 mt-3 mb-3 text-center text-secondary">
        <h1 class="p-0 m-0 fw-bolder">Company Jobs</h1>
        <img class="mt-4" src="<?= base_url('assets/img/jobSearch.png') ?>" alt="JobSearch Logo" height="50">
    </div>

    <div class="container pt-3 mt-2 mb-4">
        <!-- Buttons Row -->
        <div class="d-flex justify-content-between align-items-center mb-3 mx-3">
            <button class="btn btn-outline-primary shadow-sm" data-bs-toggle="offcanvas" data-bs-target="#filtersOffcanvas">
                <span class="material-symbols-rounded align-bottom">filter_list</span>
                Filters
            </button>

            <button class="btn btn-primary shadow-sm" id="btnNewJob" data-bs-toggle="modal" data-bs-target="#job_modal">
                <span class="material-symbols-rounded align-bottom">add</span>
                New Job
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
                                        <span class="input-group-text">Job Title</span>
                                        <input type="text" class="form-control" name="jobTitle" id="jobTitle" maxlength="200">
                                    </div>
                                </div>
                                <div class="col-12 col-md-4 px-1 pb-2">
                                    <div class="input-group">
                                        <label for="jobBusiness" class="input-group-text shadow-sm">Area</label>
                                        <select class="form-select shadow-sm" id="jobBusiness" name="jobBusiness">
                                            <option selected disabled value="">Choose...</option>
                                            <?php foreach ($jobs as $job) : ?>
                                                <option value="<?= $job['value'] ?>"><?= $job['display'] ?></option>
                                            <?php endforeach ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row justify-content-center pb-2 align-items-center">
                                <div class="col-12 col-md-4 px-1 pb-2">
                                    <div class="input-group">
                                        <label for="jobState" class="input-group-text shadow-sm">State</label>
                                        <select class="form-select shadow-sm" id="jobState" name="jobState">
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

                                <div class="col-12 col-md-4 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">City</span>
                                        <input type="text" class="form-control" name="jobCity" id="jobCity">
                                    </div>
                                </div>

                                <div class="col-12 col-md-2 px-1 pb-2">
                                    <div class="input-group shadow-sm">
                                        <span class="input-group-text">Salary</span>
                                        <input type="number" class="form-control" name="jobSalary" id="jobSalary" min="0">
                                    </div>
                                </div>
                            </div>

                            <div class="row justify-content-center pb-2 align-items-center">
                                <div class="col-12 col-md-10 px-1 pb-2">
                                    <textarea id="jobDescription" name="jobDescription" cols="12" rows="8" class="form-control shadow-sm bg-light" aria-required="false" aria-invalid="false" maxlength="5000" placeholder="Describe the job responsibilities (e.g., Assist in the development of web projects using JSON in the communication protocol at the application transport layer.)"></textarea>
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer bg-light">
                        <button type="button" class="btn btn-outline-primary" id="cancel" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-primary" id="save">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Candidates Modal -->
        <div class="modal fade" id="candidates_modal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-scrollable">
                <div class="modal-content shadow">

                    <div class="modal-header" style="background-color:#4889B4;">
                        <h5 class="modal-title text-white fw-bold">Candidates</h5>
                        <button class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="candidatesContainer" class="row g-3"></div>
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-outline-secondary" data-bs-dismiss="modal">
                            Close
                        </button>
                    </div>

                </div>
            </div>
        </div>
        
        <!-- Candidate Feedback Modal -->
        <div class="modal fade" id="candidateFeedbackModal" tabindex="-1">
            <div class="modal-dialog modal-lg modal-dialog-centered">
                <div class="modal-content shadow-lg">

                    <div class="modal-header" style="background-color:#4889B4;">
                        <h5 class="modal-title text-white fw-bold">Candidate Feedback</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>

                    <div class="modal-body">
                        <div id="cf_candidate_data" class="mb-3"></div>

                        <label class="form-label fw-bold">Feedback for this candidate</label>
                        <textarea id="cf_message" class="form-control shadow-sm" rows="4"
                                placeholder="Write the feedback (10 to 600 characters)..."></textarea>

                        <input type="hidden" id="cf_user_id">
                        <input type="hidden" id="cf_job_id">
                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button class="btn btn-primary" id="cf_send">Send Feedback</button>
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
            $('#jobBusiness, #jobState').val(null).trigger('change');
            $('#jobId').val("");
            $('#job_form')[0].reset();
        }

        function sanitizeSalary(rawValue) {
            if (rawValue === null || rawValue === undefined) return null;

            let value = rawValue.toString().trim();

            if (value === "") return null;

            // Convert comma to dot (ex: 5,5 → 5.5)
            value = value.replace(',', '.');

            let num = parseFloat(value);

            // If not a valid number or equal to 0, return null
            if (isNaN(num) || num === 0) return null;

            // Fix decimal places WITHOUT turning into string
            num = Number(num.toFixed(2));

            return num;
        }

        function getJobFilters() {
            const title = $("#jobTitleFilter").val()?.trim() || "";
            const area = $("#jobBusinessFilter").val() || "";
            const description = $("#jobDescriptionFilter").val()?.trim() || "";
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
                        description: description,
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
                            <button class="btn btn-sm btn-outline-success" onclick="getCandidates(${sub}, ${job.job_id})">
                                <span class="material-symbols-rounded align-bottom">patient_list</span> Candidates
                            </button>
                            <button class="btn btn-sm btn-outline-primary" onclick="editJob(${job.job_id})">
                                <span class="material-symbols-rounded align-bottom">edit</span> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="deleteJob(${job.job_id})">
                                <span class="material-symbols-rounded align-bottom">delete</span> Delete
                            </button>
                        </div>
                    </div>
                </div>`;
                
                container.append(card);
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
                    $("#jobBusiness").val(response.area).trigger("change");
                    $("#jobDescription").val(response.description);
                    $("#jobState").val(response.state).trigger("change");
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

        function renderCandidatesModal(items, jobId) {
            const container = $("#candidatesContainer");
            container.empty();

            if (!items || items.length === 0) {
                container.html(`
                    <div class="alert alert-info text-center shadow-sm">
                        No candidates found for this job.
                    </div>
                `);
                return;
            }

            items.forEach(c => {
                const card = `
                    <div class="card shadow-sm mb-3">
                        <div class="card-body">

                            <h5 class="fw-bold text-primary">${c.name}</h5>

                            <p><strong>Email:</strong> ${c.email}</p>
                            <p><strong>Phone:</strong> ${c.phone}</p>
                            <p><strong>Education:</strong> ${c.education}</p>
                            <p><strong>Experience:</strong> ${c.experience}</p>

                            <div class="text-end">
                                <button class="btn btn-success btn-sm"
                                        onclick='openCandidateFeedback(${JSON.stringify(c.user_id)}, ${JSON.stringify(jobId)}, ${JSON.stringify(c)})'>
                                    <span class="material-symbols-rounded align-bottom">done</span>
                                    Select
                                </button>
                            </div>
                        </div>
                    </div>
                `;

                container.append(card);
            });
        }

        window.openCandidateFeedback = function(user_id, job_id, candidate) {
            $("#cf_user_id").val(user_id);
            $("#cf_job_id").val(job_id);

            $("#cf_candidate_data").html(`
                <div class="border rounded p-3 bg-light">
                    <p><strong>Name:</strong> ${candidate.name}</p>
                    <p><strong>Email:</strong> ${candidate.email}</p>
                    <p><strong>Phone:</strong> ${candidate.phone}</p>
                    <p><strong>Education:</strong> ${candidate.education}</p>
                    <p><strong>Experience:</strong> ${candidate.experience}</p>
                </div>
            `);

            $("#cf_message").val("");

            $("#candidateFeedbackModal").modal("show");
        };

        $("#cf_send").on("click", function () {
            const message = $("#cf_message").val().trim();
            const user_id = parseInt($("#cf_user_id").val());
            const job_id = parseInt($("#cf_job_id").val());

            if (!user_id || !job_id) {
                showMessage("error", "Internal error: missing candidate or job.");
                return;
            }

            if (message.length < 10 || message.length > 600) {
                showMessage("error", "Feedback must contain 10 to 600 characters.");
                return;
            }

            const endpoint = `${serverUrl}/jobs/${job_id}/feedback`;

            showLoading("Sending feedback...");

            $.ajax({
                url: endpoint,
                method: "POST",
                headers: headers,
                contentType: "application/json",
                data: JSON.stringify({
                    user_id: user_id,
                    message: message
                }),
                success: function (response, textStatus, xhr) {
                    hideLoading();
                    $("#candidateFeedbackModal").modal("hide");

                    const status = xhr.status;
                    const msg = response.message || "Feedback sent successfully.";

                    showMessage("success", `(${status}) ${msg}`);
                },
                error: function (xhr) {
                    hideLoading();

                    let response = xhr.responseJSON;
                    if (typeof response === "string") {
                        try { response = JSON.parse(response); }
                        catch { response = {}; }
                    }

                    const msg = response?.message || "Failed to send feedback.";
                    showMessage("error", msg);
                }
            });
        });

        function loadJobs() {
            showJobsLoading(true);

            const payload = getJobFilters();
            const endpoint = serverUrl + "/companies/" + sub + "/jobs";

            $.ajax({
                url: endpoint,
                method: "POST",
                data: JSON.stringify(payload),
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
                        {name: "salary", type: "float"},
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

                    $("#jobsContainer").html(`
                        <div class="col-12">
                            <div class="alert alert-info text-center shadow-sm">
                                No jobs found.
                            </div>
                        </div>
                    `);
                    return;
                }
            });
        }

        function editJob(jobId) {
            const job = loadedJobs.find(j => j.job_id === jobId);
            
            if (!job) {
                showMessage("error", "(Local) Job not found.");
                return;
            }

            $("#jobId").val(job.job_id);
            $("#jobTitle").val(job.title);
            $("#jobBusiness").val(job.area).trigger("change");
            $("#jobDescription").val(job.description);
            $("#jobState").val(job.state).trigger("change");
            $("#jobCity").val(job.city);
            $("#jobSalary").val(job.salary);
            $("#jobContact").val(job.contact);

            $("#job_modal_label").text("Edit Job");
            $("#save").text("Update");

            $("#job_modal").modal("show");
        }
        window.editJob = editJob;

        function getCandidates(companyId, jobId) {
            const endpoint = serverUrl + `/companies/${sub}/jobs/${jobId}`;

            showLoading("Loading candidates...");

            $.ajax({
                url: endpoint,
                method: "GET",
                headers: headers,
                success: function (response) {
                    hideLoading();

                    verifyReceivedJSON(
                        [{ name: "items", type: "array" }],
                        { items: response.items }
                    );

                    renderCandidatesModal(response.items, jobId);
                    $("#candidates_modal").modal("show");
                },
                error: function (xhr) {
                    hideLoading();

                    let response = xhr.responseJSON;
                    if (typeof response === "string") {
                        try { response = JSON.parse(response); }
                        catch { response = {}; }
                    }

                    showMessage("error", response?.message || "Failed to load candidates.");
                }
            });
        }
        window.getCandidates = getCandidates;


        function deleteJob(jobId) {
            Swal.fire({
                title: "Confirm Action",
                text: "Do you really want to delete the current job?",
                icon: "warning",
                showCancelButton: true,
                confirmButtonText: "Delete",
                confirmButtonColor: "#d33",
                cancelButtonText: "Cancel",
                cancelButtonColor: "#3085d6"
            }).then(result => {

                if (result.isConfirmed) {
                    const endpoint = serverUrl + "/jobs/" + jobId;

                    showLoading(`Deleting job #${jobId}...`);

                    $.ajax({
                        url: endpoint,
                        method: "DELETE",
                        headers: headers,
                        contentType: 'application/json',
                        success: function(response, textStatus, xhr) {
                            hideLoading();
                            const status = xhr.status;
                            const message = response.message || "Job deleted successfully.";
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

                            const msg = response?.message || "Failed to delete job.";
                            showMessage('error', `(${status}) ${msg}`);
                        }
                    });
                }
            });
        } window.deleteJob = deleteJob;

        $('#btnNewJob').on('click', function () {
            $('#job_form')[0].reset();
            $('#job_modal_label').text('New Job');
            $('#save').text('Save');
        });

        $('#save').click(function(e) {
            e.preventDefault();

            const jobId = $('#jobId').val(); // Verify if it's editMode.
            const isEditing = jobId && jobId !== "";

            let data = {
                title: $('#jobTitle').val() || "",
                area: $('#jobBusiness').val() || "",
                description: $('#jobDescription').val() || "",
                state: $('#jobState').val() || "",
                city: $('#jobCity').val() || "",
                salary: sanitizeSalary($('#jobSalary').val()),
            };

            const endpoint = isEditing 
                ? `${serverUrl}/jobs/${jobId}` 
                : `${serverUrl}/jobs`;

            const method = isEditing ? "PATCH" : "POST";
            const loadingMsg = isEditing ? "Updating Job..." : "Creating Job...";

            showLoading(loadingMsg);
            $('#job_modal').modal('hide');

            $.ajax({
                url: endpoint,
                method: method,
                headers: headers,
                contentType: 'application/json',
                data: JSON.stringify(data),
                dataType: 'json',
                success: function(response, textStatus, xhr) {
                    hideLoading();
                    const status = xhr.status;
                    const message = response.message || (isEditing ? "Job updated successfully." : "Job created successfully.");
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
                        return;
                    }

                    const msg = response?.message || (isEditing ? "Failed to update job." : "Failed to create job.");

                    $('#job_modal').modal('show');
                    showMessage('error', `(${status}) ${msg}`);
                }
            });
        });


        // SELECT2 - Filters (offcanvas)
        $('#jobBusinessFilter, #jobStateFilter').select2({
            theme: 'bootstrap-5',
            placeholder: 'Choose...',
            allowClear: true,
            dropdownParent: $('#filtersOffcanvas'),
            selectionCssClass: 'shadow-sm'
        });

        // SELECT2 - Modal
        $('#jobBusiness, #jobState').select2({
            theme: 'bootstrap-5',
            placeholder: 'Choose...',
            allowClear: true,
            dropdownParent: $('#job_modal'),
            selectionCssClass: 'shadow-sm'
        });

        document.getElementById('filtersOffcanvas').addEventListener('shown.bs.offcanvas', function () {
            $('#jobTitleFilter').trigger('focus');
        });

        $('#job_form').on('submit', function(e) {
            e.preventDefault();
        });

    });
</script>
<?= $this->endsection() ?>
