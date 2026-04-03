<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= lang('SiteCounter.dashboard.title') ?> - <?= lang('SiteCounter.app.name') ?></title>
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
                        <a class="nav-link" href="/dashboard/websites"><i class="bi bi-globe me-1"></i><?= lang('SiteCounter.nav.websites') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/profile"><i class="bi bi-person-circle me-1"></i><?= lang('SiteCounter.nav.profile') ?></a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text me-3"><?= esc(lang('SiteCounter.app.welcome_user', [$fullName])) ?></span>
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
                <div class="card">
                    <div class="card-header">
                        <h4><?= lang('SiteCounter.dashboard.title') ?></h4>
                    </div>
                    <div class="card-body">
                        <p><?= lang('SiteCounter.dashboard.intro') ?></p>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-globe display-4 text-primary mb-3 d-block"></i>
                                        <h5><?= lang('SiteCounter.dashboard.websites_title') ?></h5>
                                        <p class="text-muted"><?= lang('SiteCounter.dashboard.websites_desc') ?></p>
                                        <a href="/dashboard/websites" class="btn btn-primary"><i class="bi bi-globe me-1"></i><?= lang('SiteCounter.dashboard.websites_cta') ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-bar-chart-line display-4 text-primary mb-3 d-block"></i>
                                        <h5><?= lang('SiteCounter.dashboard.reports_title') ?></h5>
                                        <p class="text-muted"><?= lang('SiteCounter.dashboard.reports_desc') ?></p>
                                        <a href="/dashboard/websites" class="btn btn-primary"><i class="bi bi-bar-chart-line me-1"></i><?= lang('SiteCounter.dashboard.reports_cta') ?></a>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center">
                                        <i class="bi bi-gear display-4 text-primary mb-3 d-block"></i>
                                        <h5><?= lang('SiteCounter.dashboard.settings_title') ?></h5>
                                        <p class="text-muted"><?= lang('SiteCounter.dashboard.settings_desc') ?></p>
                                        <a href="/dashboard/profile" class="btn btn-primary"><i class="bi bi-gear me-1"></i><?= lang('SiteCounter.dashboard.settings_cta') ?></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>