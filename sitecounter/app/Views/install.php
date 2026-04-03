<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>SiteCounter Installation</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card shadow">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0">SiteCounter Installation</h4>
                    </div>
                    <div class="card-body">
                        <p>Welcome to SiteCounter installation wizard. This will set up the database and prepare your application.</p>

                        <div id="install-status" class="alert d-none"></div>

                        <button id="install-btn" class="btn btn-primary btn-lg w-100" onclick="runInstall()">
                            <span class="spinner-border spinner-border-sm d-none" role="status"></span>
                            Install SiteCounter
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        async function runInstall() {
            const btn = document.getElementById('install-btn');
            const spinner = btn.querySelector('.spinner-border');
            const status = document.getElementById('install-status');

            // Show loading state
            btn.disabled = true;
            spinner.classList.remove('d-none');
            status.className = 'alert alert-info';
            status.textContent = 'Installing... Please wait.';
            status.classList.remove('d-none');

            try {
                const response = await fetch('/install/run', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    }
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
                status.textContent = 'Installation failed: ' + error.message;
            } finally {
                btn.disabled = false;
                spinner.classList.add('d-none');
            }
        }
    </script>
</body>
</html>