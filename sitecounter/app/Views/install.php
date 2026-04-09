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

                        <div id="sqlite-fields" class="mb-3">
                            <label for="sqlite-database" class="form-label"><?= lang('SiteCounter.install.sqlite_db_label') ?></label>
                            <input type="text" id="sqlite-database" class="form-control" value="writable/database/sitecounter.db" maxlength="255">
                            <div class="form-text"><?= lang('SiteCounter.install.sqlite_db_help') ?></div>
                        </div>

                        <hr class="my-4">

                        <h5 class="mb-3"><?= lang('SiteCounter.install.admin_section_title') ?></h5>

                        <div class="mb-3">
                            <label for="admin-email" class="form-label"><?= lang('SiteCounter.install.admin_email_label') ?></label>
                            <input type="email" id="admin-email" class="form-control" value="admin@sitecounter.local" maxlength="255" required>
                        </div>

                        <div class="mb-3">
                            <label for="admin-password" class="form-label"><?= lang('SiteCounter.install.admin_password_label') ?></label>
                            <input type="password" id="admin-password" class="form-control" minlength="8" maxlength="255" required>
                            <div class="form-text"><?= lang('SiteCounter.install.admin_password_help') ?></div>
                        </div>

                        <div class="row g-2 mb-3">
                            <div class="col-md-6">
                                <label for="admin-firstname" class="form-label"><?= lang('SiteCounter.install.admin_firstname_label') ?></label>
                                <input type="text" id="admin-firstname" class="form-control" value="Site" maxlength="50">
                            </div>
                            <div class="col-md-6">
                                <label for="admin-lastname" class="form-label"><?= lang('SiteCounter.install.admin_lastname_label') ?></label>
                                <input type="text" id="admin-lastname" class="form-control" value="Admin" maxlength="50">
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
        const installRunUrl = <?= json_encode(site_url('install/run'), JSON_UNESCAPED_SLASHES) ?>;
        const homeUrl = <?= json_encode(site_url('/'), JSON_UNESCAPED_SLASHES) ?>;

        function getInstallPayload() {
            return {
                db_driver: 'sqlite',
                sqlite_database: document.getElementById('sqlite-database').value.trim(),
                admin_email: document.getElementById('admin-email').value.trim(),
                admin_password: document.getElementById('admin-password').value,
                admin_firstname: document.getElementById('admin-firstname').value.trim(),
                admin_lastname: document.getElementById('admin-lastname').value.trim()
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
                const response = await fetch(installRunUrl, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
                    ,
                    body: JSON.stringify(payload)
                });

                const contentType = response.headers.get('content-type') || '';

                if (!contentType.includes('application/json')) {
                    const bodyText = await response.text();
                    const fallbackMessage = '<?= esc(lang('SiteCounter.install.unexpected_response')) ?>';
                    const serverSnippet = bodyText.replace(/\s+/g, ' ').trim().slice(0, 160);
                    throw new Error(serverSnippet ? `${fallbackMessage} ${serverSnippet}` : fallbackMessage);
                }

                const result = await response.json();

                if (result.success) {
                    status.className = 'alert alert-success';
                    status.textContent = result.message;
                    setTimeout(() => {
                        window.location.href = homeUrl;
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

    </script>
</body>
</html>