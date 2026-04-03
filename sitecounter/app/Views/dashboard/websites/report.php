<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= lang('SiteCounter.report.title_prefix') ?>: <?= esc($website['name']) ?> - <?= lang('SiteCounter.app.name') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
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
                        <a class="nav-link" href="/dashboard"><?= lang('SiteCounter.nav.dashboard') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="/dashboard/websites"><?= lang('SiteCounter.nav.websites') ?></a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/dashboard/profile"><?= lang('SiteCounter.nav.profile') ?></a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item">
                        <span class="navbar-text me-3"><?= esc(lang('SiteCounter.app.welcome_user', [$user->username])) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout"><?= lang('SiteCounter.nav.logout') ?></a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-3 d-flex justify-content-end">
        <?= lang_switcher() ?>
    </div>

    <div class="container mt-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="mb-0"><?= esc($website['name']) ?></h1>
                <small class="text-muted">
                    <a href="<?= esc($website['url']) ?>" target="_blank"><?= esc($website['url']) ?></a>
                    &mdash; <?= lang('SiteCounter.report.last_30_days') ?> (<?= esc(date('d M', strtotime($startDate))) ?> &ndash; <?= esc(date('d M Y', strtotime($endDate))) ?>)
                </small>
            </div>
            <a href="/dashboard/websites/<?= $website['id'] ?>" class="btn btn-secondary"><?= lang('SiteCounter.report.back_to_website') ?></a>
        </div>

        <!-- Summary cards -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted"><?= lang('SiteCounter.report.total_page_views') ?></h6>
                        <p class="display-5 mb-0 fw-bold"><?= number_format($totalVisits) ?></p>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center h-100">
                    <div class="card-body">
                        <h6 class="card-subtitle mb-2 text-muted"><?= lang('SiteCounter.report.unique_visitors') ?></h6>
                        <p class="display-5 mb-0 fw-bold"><?= number_format($uniqueVisitors) ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daily timeline chart -->
        <div class="card mb-4">
            <div class="card-header"><h5 class="mb-0"><?= lang('SiteCounter.report.daily_page_views') ?></h5></div>
            <div class="card-body">
                <canvas id="dailyChart" height="100"></canvas>
            </div>
        </div>

        <!-- Top / bottom page tables -->
        <div class="row mb-4">
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="mb-0"><?= lang('SiteCounter.report.top_10_pages') ?></h5></div>
                    <div class="card-body p-0">
                        <?php if (empty($topPages)): ?>
                            <p class="p-3 text-muted mb-0"><?= lang('SiteCounter.report.no_data') ?></p>
                        <?php else: ?>
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><?= lang('SiteCounter.report.page') ?></th>
                                        <th class="text-end"><?= lang('SiteCounter.report.views') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($topPages as $page): ?>
                                        <tr>
                                            <td class="text-truncate" style="max-width:300px;" title="<?= esc($page['url']) ?>"><?= esc($page['url']) ?></td>
                                            <td class="text-end"><?= number_format($page['visits']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card h-100">
                    <div class="card-header"><h5 class="mb-0"><?= lang('SiteCounter.report.bottom_10_pages') ?></h5></div>
                    <div class="card-body p-0">
                        <?php if (empty($bottomPages)): ?>
                            <p class="p-3 text-muted mb-0"><?= lang('SiteCounter.report.no_data') ?></p>
                        <?php else: ?>
                            <table class="table table-sm table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th><?= lang('SiteCounter.report.page') ?></th>
                                        <th class="text-end"><?= lang('SiteCounter.report.views') ?></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($bottomPages as $page): ?>
                                        <tr>
                                            <td class="text-truncate" style="max-width:300px;" title="<?= esc($page['url']) ?>"><?= esc($page['url']) ?></td>
                                            <td class="text-end"><?= number_format($page['visits']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
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
        var raw = <?= json_encode($dailyVisits) ?>;

        // Build a complete 30-day label array with zeros for missing days.
        var labels = [];
        var counts = {};
        for (var i = 29; i >= 0; i--) {
            var d = new Date();
            d.setDate(d.getDate() - i);
            var key = d.toISOString().slice(0, 10);
            labels.push(key);
            counts[key] = 0;
        }
        for (var j = 0; j < raw.length; j++) {
            var day = raw[j].date ? raw[j].date.slice(0, 10) : null;
            if (day && counts.hasOwnProperty(day)) {
                counts[day] = parseInt(raw[j].visits, 10);
            }
        }
        var data = labels.map(function (l) { return counts[l]; });

        new Chart(document.getElementById('dailyChart'), {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: <?= json_encode(lang('SiteCounter.report.dataset_label')) ?>,
                    data: data,
                    backgroundColor: 'rgba(13, 110, 253, 0.6)',
                    borderColor:     'rgba(13, 110, 253, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: {
                    x: { ticks: { maxTicksLimit: 10 } },
                    y: { beginAtZero: true, ticks: { precision: 0 } }
                }
            }
        });
    })();
    </script>
</body>
</html>
