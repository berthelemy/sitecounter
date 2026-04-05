<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <title>SiteCounter Installation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="d-flex justify-content-end mb-3">
            <?= lang_switcher() ?>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><?= lang('SiteCounter.install.title') ?></h4>
                    </div>
                    <div class="card-body">
                        <p><?= lang('SiteCounter.install.intro') ?></p>

                        <div class="mb-3">
                            <label for="db-driver" class="form-label"><?= lang('SiteCounter.install.db_type_label') ?></label>
                            <select id="db-driver" class="form-select" onchange="toggleDbFields()">
                                <option value="sqlite" selected><?= lang('SiteCounter.install.db_type_sqlite') ?></option>
                                <option value="mysql"><?= lang('SiteCounter.install.db_type_mysql') ?></option>
                                <option value="mariadb"><?= lang('SiteCounter.install.db_type_mariadb') ?></option>
                            </select>
                            <div class="form-text"><?= lang('SiteCounter.install.db_type_help') ?></div>
                        </div>

                        <div id="sqlite-fields" class="mb-3">
                            <label for="sqlite-database" class="form-label"><?= lang('SiteCounter.install.sqlite_db_label') ?></label>
                            <input type="text" id="sqlite-database" class="form-control" value="sitecounter.db" maxlength="255">
                            <div class="form-text"><?= lang('SiteCounter.install.sqlite_db_help') ?></div>
                        </div>

                        <div id="mysql-fields" class="d-none">
                            <div class="mb-3">
                                <label for="db-hostname" class="form-label"><?= lang('SiteCounter.install.db_hostname_label') ?></label>
                                <input type="text" id="db-hostname" class="form-control" value="localhost" maxlength="255">
                            </div>
                            <div class="row">
                                <div class="col-md-8 mb-3">
                                    <label for="db-name" class="form-label"><?= lang('SiteCounter.install.db_name_label') ?></label>
                                    <input type="text" id="db-name" class="form-control" maxlength="255">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label for="db-port" class="form-label"><?= lang('SiteCounter.install.db_port_label') ?></label>
                                    <input type="number" id="db-port" class="form-control" value="3306" min="1" max="65535">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="db-username" class="form-label"><?= lang('SiteCounter.install.db_username_label') ?></label>
                                    <input type="text" id="db-username" class="form-control" maxlength="255">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="db-password" class="form-label"><?= lang('SiteCounter.install.db_password_label') ?></label>
                                    <input type="password" id="db-password" class="form-control" maxlength="255">
                                </div>
                            </div>
                        </div>

                        <div id="install-status" class="alert d-none"></div>

                        <button id="install-btn" class="btn btn-primary btn-lg w-100" onclick="runInstall()">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            <i class="bi bi-download me-1"></i>
                            <?= lang('SiteCounter.install.install_button') ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function toggleDbFields() {
            const driver = document.getElementById('db-driver').value;
            const sqliteFields = document.getElementById('sqlite-fields');
            const mysqlFields = document.getElementById('mysql-fields');

            if (driver === 'sqlite') {
                sqliteFields.classList.remove('d-none');
                mysqlFields.classList.add('d-none');
                return;
            }

            sqliteFields.classList.add('d-none');
            mysqlFields.classList.remove('d-none');
        }

        function getInstallPayload() {
            const driver = document.getElementById('db-driver').value;

            if (driver === 'sqlite') {
                return {
                    db_driver: driver,
                    sqlite_database: document.getElementById('sqlite-database').value.trim()
                };
            }

            return {
                db_driver: driver,
                hostname: document.getElementById('db-hostname').value.trim(),
                database: document.getElementById('db-name').value.trim(),
                username: document.getElementById('db-username').value.trim(),
                password: document.getElementById('db-password').value,
                port: document.getElementById('db-port').value.trim()
            };
        }

        async function runInstall() {
            const btn = document.getElementById('install-btn');
            const spinner = btn.querySelector('.spinner-border');
            const status = document.getElementById('install-status');
            const payload = getInstallPayload();

            // Show loading state
            btn.disabled = true;
            spinner.classList.remove('d-none');
            status.className = 'alert alert-info';
            status.textContent = '<?= esc(lang('SiteCounter.install.installing_message')) ?>';
            status.classList.remove('d-none');

            try {
                const response = await fetch('/install/run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                    ,
                    body: JSON.stringify(payload)
                });

                const result = await response.json();

                if (result.success) {
                    status.className = 'alert alert-success';
                    status.textContent = result.message;
                    setTimeout(() => {
                        window.location.href = '/';
                    }, 2000);
                } else {
                    status.className = 'alert alert-danger';
                    status.textContent = result.message;
                }
            } catch (error) {
                status.className = 'alert alert-danger';
                status.textContent = '<?= esc(lang('SiteCounter.install.install_failed_prefix')) ?>' + error.message;
            } finally {
                btn.disabled = false;
                spinner.classList.add('d-none');
            }
        }

        toggleDbFields();
    </script>
</body>
</html>