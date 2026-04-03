<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= lang('SiteCounter.websites.details_title') ?> - <?= lang('SiteCounter.app.name') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary">
        <div class="container">
            <a class="navbar-brand" href="/dashboard"><?= lang('SiteCounter.app.name') ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard"><i class="bi bi-speedometer2 me-1"></i><?= lang('SiteCounter.nav.dashboard') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard/websites"><i class="bi bi-globe me-1"></i><?= lang('SiteCounter.nav.websites') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/profile"><i class="bi bi-person-circle me-1"></i><?= lang('SiteCounter.nav.profile') ?></a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text me-3"><?= esc(lang('SiteCounter.app.welcome_user', [$user->username])) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout"><i class="bi bi-box-arrow-right me-1"></i><?= lang('SiteCounter.nav.logout') ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3 d-flex justify-content-end">
        <?= lang_switcher() ?>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h1><?= lang('SiteCounter.websites.details_title') ?></h1>
                    <div>
                        <a href="/dashboard/websites/<?= $website['id'] ?>/edit" class="btn btn-warning"><i class="bi bi-pencil me-1"></i><?= lang('SiteCounter.websites.edit') ?></a>
                        <a href="/dashboard/websites/<?= $website['id'] ?>/report" class="btn btn-info"><i class="bi bi-bar-chart-line me-1"></i><?= lang('SiteCounter.websites.view_report') ?></a>
                        <a href="/dashboard/websites" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i><?= lang('SiteCounter.websites.back_to_websites') ?></a>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <h5><?= lang('SiteCounter.websites.information') ?></h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p><strong><?= lang('SiteCounter.websites.name') ?>:</strong> <?= esc($website['name']) ?></p>
                                <p><strong><?= lang('SiteCounter.websites.url') ?>:</strong> <a href="<?= esc($website['url']) ?>" target="_blank"><?= esc($website['url']) ?></a></p>
                            </div>
                            <div class="col-md-6">
                                <p><strong><?= lang('SiteCounter.websites.token') ?>:</strong> <code><?= esc($website['token']) ?></code></p>
                                <p><strong><?= lang('SiteCounter.websites.created') ?>:</strong> <?= date('M j, Y H:i', strtotime($website['created_at'])) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5><?= lang('SiteCounter.websites.tracking_script') ?></h5>
                        <button id="copyButton" class="btn btn-sm btn-outline-primary"><i class="bi bi-clipboard me-1"></i><?= lang('SiteCounter.websites.copy_script') ?></button>
                    </div>
                    <div class="card-body">
                        <p class="text-muted"><?= lang('SiteCounter.websites.tracking_help') ?></p>
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
            this.textContent = <?= json_encode(lang('SiteCounter.websites.copied')) ?>;
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