<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= lang('SiteCounter.websites.title') ?> - <?= lang('SiteCounter.app.name') ?></title>
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
                    <h1><?= lang('SiteCounter.websites.title') ?></h1>
                    <a href="/dashboard/websites/create" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i><?= lang('SiteCounter.websites.add_website') ?></a>
                </div>

                <?php if (session()->has('success')): ?>
                    <div class="alert alert-success">
                        <?= session('success') ?>
                    </div>
                <?php endif; ?>

                <?php if (session()->has('errors')): ?>
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            <?php foreach (session('errors') as $error): ?>
                                <li><?= esc($error) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>

                <div class="card">
                    <div class="card-body">
                        <?php if (empty($websites)): ?>
                            <p class="text-muted"><?= lang('SiteCounter.websites.no_websites') ?> <a href="/dashboard/websites/create"><?= lang('SiteCounter.websites.add_first_website') ?></a>.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th><?= lang('SiteCounter.websites.name') ?></th>
                                            <th><?= lang('SiteCounter.websites.url') ?></th>
                                            <th><?= lang('SiteCounter.websites.created') ?></th>
                                            <th><?= lang('SiteCounter.websites.actions') ?></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($websites as $website): ?>
                                            <tr>
                                                <td><?= esc($website['name']) ?></td>
                                                <td><a href="<?= esc($website['url']) ?>" target="_blank"><?= esc($website['url']) ?></a></td>
                                                <td><?= date('M j, Y', strtotime($website['created_at'])) ?></td>
                                                <td>
                                                    <a href="/dashboard/websites/<?= $website['id'] ?>" class="btn btn-sm btn-info"><i class="bi bi-eye me-1"></i><?= lang('SiteCounter.websites.view') ?></a>
                                                    <a href="/dashboard/websites/<?= $website['id'] ?>/edit" class="btn btn-sm btn-warning"><i class="bi bi-pencil me-1"></i><?= lang('SiteCounter.websites.edit') ?></a>
                                                    <form method="post" action="/dashboard/websites/<?= $website['id'] ?>/delete" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('<?= esc(lang('SiteCounter.websites.delete_confirm')) ?>')"><i class="bi bi-trash me-1"></i><?= lang('SiteCounter.websites.delete') ?></button>
                                                    </form>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>