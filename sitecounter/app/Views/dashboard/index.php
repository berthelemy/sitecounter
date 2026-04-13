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
                        <p class="mb-4"><?= lang('SiteCounter.dashboard.intro') ?></p>

                        <?php if (empty($dashboardWebsites)): ?>
                            <div class="alert alert-info mb-0">
                                <?= lang('SiteCounter.dashboard.no_websites_metrics') ?>
                                <a href="/dashboard/websites/create" class="alert-link"><?= lang('SiteCounter.websites.add_first_website') ?></a>.
                            </div>
                        <?php else: ?>
                            <div class="row g-4">
                                <?php foreach ($dashboardWebsites as $item): ?>
                                    <?php $website = $item['website']; ?>
                                    <div class="col-12 col-lg-6">
                                        <div class="card h-100">
                                            <div class="card-header d-flex justify-content-between align-items-center">
                                                <div>
                                                    <h5 class="mb-0"><?= esc($website['name']) ?></h5>
                                                    <small class="text-muted"><?= esc($website['url']) ?></small>
                                                </div>
                                                <a href="/dashboard/websites/<?= (int) $website['id'] ?>/report" class="btn btn-sm btn-outline-primary">
                                                    <i class="bi bi-bar-chart-line me-1"></i><?= lang('SiteCounter.dashboard.view_report') ?>
                                                </a>
                                            </div>
                                            <div class="card-body">
                                                <div class="row g-3 mb-4">
                                                    <div class="col-4">
                                                        <div class="border rounded p-2 h-100">
                                                            <small class="text-muted d-block"><?= lang('SiteCounter.dashboard.total_unique_visitors') ?></small>
                                                            <strong><?= number_format((int) $item['totalUniqueVisitors']) ?></strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="border rounded p-2 h-100">
                                                            <small class="text-muted d-block"><?= lang('SiteCounter.dashboard.avg_unique_visitors_per_month') ?></small>
                                                            <strong><?= number_format((float) $item['averageUniqueVisitorsPerMonth'], 1) ?></strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="border rounded p-2 h-100">
                                                            <small class="text-muted d-block"><?= lang('SiteCounter.dashboard.unique_visitors_last_month') ?></small>
                                                            <strong><?= number_format((int) $item['uniqueVisitorsLastMonth']) ?></strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="border rounded p-2 h-100">
                                                            <small class="text-muted d-block"><?= lang('SiteCounter.dashboard.total_visits') ?></small>
                                                            <strong><?= number_format((int) $item['totalVisits']) ?></strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="border rounded p-2 h-100">
                                                            <small class="text-muted d-block"><?= lang('SiteCounter.dashboard.avg_visits_per_month') ?></small>
                                                            <strong><?= number_format((float) $item['averageVisitsPerMonth'], 1) ?></strong>
                                                        </div>
                                                    </div>
                                                    <div class="col-4">
                                                        <div class="border rounded p-2 h-100">
                                                            <small class="text-muted d-block"><?= lang('SiteCounter.dashboard.visits_last_month') ?></small>
                                                            <strong><?= number_format((int) $item['visitsLastMonth']) ?></strong>
                                                        </div>
                                                    </div>
                                                </div>

                                                <canvas
                                                    class="website-visits-chart"
                                                    height="90"
                                                    data-labels='<?= esc(json_encode($item['chart']['labels']), 'attr') ?>'
                                                    data-values='<?= esc(json_encode($item['chart']['values']), 'attr') ?>'
                                                ></canvas>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
    (function () {
        var charts = document.querySelectorAll('.website-visits-chart');

        charts.forEach(function (canvas) {
            var labels = JSON.parse(canvas.getAttribute('data-labels') || '[]');
            var values = JSON.parse(canvas.getAttribute('data-values') || '[]');

            new Chart(canvas, {
                type: 'bar',
                data: {
                    labels: labels,
                    datasets: [{
                        data: values,
                        backgroundColor: 'rgba(13, 110, 253, 0.55)',
                        borderColor: 'rgba(13, 110, 253, 1)',
                        borderWidth: 1,
                    }],
                },
                options: {
                    responsive: true,
                    plugins: {
                        legend: {
                            display: false,
                        },
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: { precision: 0 },
                        },
                    },
                },
            });
        });
    })();
    </script>
</body>
</html>