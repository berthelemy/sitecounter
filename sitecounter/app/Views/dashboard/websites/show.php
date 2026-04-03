<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Website Details - SiteCounter</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/dashboard">SiteCounter</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard/websites">Websites</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/profile">Profile</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item me-2">
                        <?= lang_switcher() ?>
                    </li>
                    <li class="nav-item">
                        <span class="navbar-text me-3">Welcome, <?= esc($user->username) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1>Website Details</h1>
                    <div>
                        <a href="/dashboard/websites/<?= $website['id'] ?>/edit" class="btn btn-warning">Edit</a>
                        <a href="/dashboard/websites/<?= $website['id'] ?>/report" class="btn btn-info">View Report</a>
                        <a href="/dashboard/websites" class="btn btn-secondary">Back to Websites</a>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5>Website Information</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong>Name:</strong> <?= esc($website['name']) ?></p>
                                <p><strong>URL:</strong> <a href="<?= esc($website['url']) ?>" target="_blank"><?= esc($website['url']) ?></a></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong>Token:</strong> <code><?= esc($website['token']) ?></code></p>
                                <p><strong>Created:</strong> <?= date('M j, Y H:i', strtotime($website['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5>Tracking Script</h5>
                        <button id="copyButton" class="btn btn-sm btn-outline-primary">Copy Script</button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted">Copy and paste this script into the <code>&lt;head&gt;</code> section of your website's HTML.</p>
                        <pre id="trackingScript" class="bg-light p-3 rounded"><code><?= htmlspecialchars($trackingScript) ?></code></pre>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('copyButton').addEventListener('click', function() {
            const scriptElement = document.getElementById('trackingScript');
            const textArea = document.createElement('textarea');
            textArea.value = scriptElement.textContent;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);

            // Change button text temporarily
            const originalText = this.textContent;
            this.textContent = 'Copied!';
            this.classList.remove('btn-outline-primary');
            this.classList.add('btn-success');
            setTimeout(() => {
                this.textContent = originalText;
                this.classList.remove('btn-success');
                this.classList.add('btn-outline-primary');
            }, 2000);
        });
    </script>
</body>
</html>