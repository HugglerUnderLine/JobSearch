<?= $this->extend('layouts/default') ?>

<?= $this->section('more-styles') ?>
<style>
    #logged_users_table td, 
    #logged_users_table th {
        vertical-align: middle;
    }

    .loader-row {
        text-align: center;
        font-size: 1.1rem;
        color: #6c757d;
    }
</style>
<?= $this->endsection() ?>

<?= $this->section('content') ?>

<div class="container pt-5 mt-3 mb-3 text-center text-secondary">
    <h1 class="p-0 m-0 fw-bolder">Logged Users</h1>
    <img class="mt-4" src="<?= base_url('assets/img/jobSearch.png') ?>" alt="JobSearch Logo" height="50">
</div>

<div class="container mt-4 mb-5">

    <div class="card shadow-sm rounded-3">
        <div class="card-body">

            <table class="table table-striped table-hover" id="logged_users_table">
                <thead class="table-dark">
                    <tr>
                        <th style="width: 80px;">ID</th>
                        <th>Username</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th style="width: 150px;">Role</th>
                        <th style="width: 140px;">IP</th>
                    </tr>
                </thead>
                <tbody id="logged_body">
                    <tr class="loader-row">
                        <td colspan="6">Carregando usuários...</td>
                    </tr>
                </tbody>
            </table>

        </div>
    </div>

</div>

<?= $this->endSection() ?>

<?= $this->section('more-scripts') ?>
<script type="text/javascript">

// -------------------------------
// Atualiza tabela a cada 5s
// -------------------------------
setInterval(loadLoggedUsers, 5000);
document.addEventListener("DOMContentLoaded", loadLoggedUsers);


// -------------------------------
// Função principal de busca
// -------------------------------
function loadLoggedUsers() {
    $.ajax({
        url: "<?= base_url('/logged-users/fetch') ?>",
        method: "GET",
        dataType: "json",

        success: function(response) {
            const tbody = $("#logged_body");

            if (!response.success || !response.users) {
                tbody.html(`
                    <tr class="loader-row">
                        <td colspan="6">Nenhum usuário logado encontrado.</td>
                    </tr>
                `);
                return;
            }

            // Monta tabela
            let html = "";

            if (response.users.length === 0) {
                html = `
                    <tr class="loader-row">
                        <td colspan="6">Nenhum usuário logado.</td>
                    </tr>
                `;
            } else {
                response.users.forEach(u => {
                    html += `
                        <tr>
                            <td>${u.user_id}</td>
                            <td>${u.username}</td>
                            <td>${u.name}</td>
                            <td>${u.email}</td>
                            <td>${u.account_role}</td>
                            <td>${u.ip}</td>
                        </tr>
                    `;
                });
            }

            tbody.html(html);
        },

        error: function() {
            $("#logged_body").html(`
                <tr class="loader-row">
                    <td colspan="6">Erro ao buscar dados.</td>
                </tr>
            `);
        }
    });
}

</script>
<?= $this->endsection() ?>
