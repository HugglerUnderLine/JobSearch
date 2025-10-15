<?= $this->extend('layouts/default') ?>

<?= $this->section('content') ?>
<div class="container py-5 mb-3 mt-2 text-secondary">
    <div class="row justify-content-center">
        <div class="col-8 text-center">
            <h2 class="mb-5">Welcome!</h2>

            <p>
                <strong>Job Search</strong> is a system developed as part of the evaluation process for the 
                <em>Client-Server Technology</em> course, taught at UTFPR - PG for the Systems Analysis and Development program.
            </p>

            <p>
                The project consists of a platform for searching and offering job opportunities, where users can find and apply for positions of interest, 
                while companies can post vacancies to reach professionals with the desired profile.
            </p>

            <p>
                Currently, the system is at <strong>version <?= APP_VERSION ?></strong> and aims to meet the development requirements proposed throughout the course.
            </p>

            <p>
                We hope you have a great experience using the system. <br>
                Kind regards,<br><br>
                <strong>Vitor Huggler - a2384329</strong>
            </p>
        </div>
    </div>
</div>
<?= $this->endSection() ?>

<?= $this->section('more-scripts') ?>

<script>
    $(document).ready(function() {
        console.log("Base URL: " + localStorage.getItem('server_url'));
        console.log("Sub: " + localStorage.getItem('sub'));
        console.log("Token: " + localStorage.getItem('token'));
        console.log("Username: " + localStorage.getItem('username'));
        console.log("Role: " + localStorage.getItem('role'));
        console.log("Expires In: " + localStorage.getItem('expires_in'));
    });
</script>

<?= $this->endSection() ?>
