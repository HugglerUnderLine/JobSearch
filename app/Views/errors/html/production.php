<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="<?= base_url('assets/bootstrap-5.3.3-dist/css/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/material-symbols/material-symbols-rounded.css') ?>">

    <title><?= lang('Errors.whoops') ?></title>
</head>
<body>
    <div class="container py-5 my-5 text-secondary">
        <div class="row justify-content-center">
            <div class="col-6 text-center">
                <h2 class="mb-4">
                    Whoops! Page Not Found :(
                </h2>
                <p class="mb-3">
                    The page you are looking for does not exist or may have been moved. Please check the URL for errors and try again.
                </p>
                <p class="mb-5">
                    If you believe this is a mistake, please contact our support team for assistance.
                </p>
                <a href="<?= base_url('/about') ?>" class="btn btn-primary">
                    Go to Homepage
                    <span class="material-symbols-rounded align-middle">home</span>
                </a>
            </div>
        </div>
    </div>
</body>

</html>
