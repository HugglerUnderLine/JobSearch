<?= $this->extend('layouts/default') ?>

<?= $this->section('more-styles') ?>
<!-- Select2 base CSS -->
<link rel="stylesheet" href="<?= base_url('assets/select2/css/select2.min.css') ?>">
<!-- Bootstrap 5 theme for select2 -->
<link rel="stylesheet" href="<?= base_url('assets/select2/css/select2-bootstrap-5-theme.min.css') ?>">
<?= $this->endsection() ?>

<?= $this->section('content') ?>
    <div class="container pt-5 mt-3 mb-3 text-center text-secondary">
        <h1 class="p-0 m-0 fw-bolder">My Applications</h1>
        <img class="mt-4" src="<?= base_url('assets/img/jobSearch.png') ?>" alt="JobSearch Logo" height="50">
    </div>

    <div class="container pt-3 mt-2 mb-4">
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

        function showJobsLoading(show = true) {
            if (show) {
                $("#jobsLoading").removeClass("d-none");
            } else {
                $("#jobsLoading").addClass("d-none");
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
                            <p><strong>Feedback:</strong> ${job.feedback || "No feedbacks in meantime."}</p>
                        </div>
                    </div>
                </div>`;
                
                container.append(card);
            });
        }

        function loadJobs() {
            showJobsLoading(true);
            endpoint = serverUrl + `/users/${sub}/jobs`;

            $.ajax({
                url: endpoint,
                method: "GET",
                contentType: "application/json; charset=UTF-8",
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
                        {name: "feedback", type: "string", allowEmpty: true},
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
                    showJobsLoading(false);
                    return;
                }
            });
        }
    });
</script>
<?= $this->endsection() ?>
