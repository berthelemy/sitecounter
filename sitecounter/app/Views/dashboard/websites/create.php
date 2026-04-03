<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= lang('SiteCounter.websites.add_website') ?> - <?= lang('SiteCounter.app.name') ?></title>
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
        <div class="row">
            <div class="col-md-8 offset-md-2">
                <div class="card">
                    <div class="card-header">
                        <h4><?= lang('SiteCounter.websites.add_title') ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->has('errors')): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach (session('errors') as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form method="post" action="/dashboard/websites/store">
                            <?= csrf_field() ?>

                            <div class="mb-3">
                                <label for="name" class="form-label"><?= lang('SiteCounter.websites.name_label') ?></label>
                                <input type="text" class="form-control" id="name" name="name" value="<?= old('name') ?>" required>
                                <div class="form-text"><?= lang('SiteCounter.websites.name_help') ?></div>
                            </div>

                            <div class="mb-3">
                                <label for="url" class="form-label"><?= lang('SiteCounter.websites.url_label') ?></label>
                                <input type="url" class="form-control" id="url" name="url" value="<?= old('url') ?>" required>
                                <div class="form-text"><?= lang('SiteCounter.websites.url_help') ?></div>
                            </div>

                            <div class="d-flex justify-content-between">
                                <a href="/dashboard/websites" class="btn btn-secondary"><?= lang('SiteCounter.app.cancel') ?></a>
                                <button type="submit" class="btn btn-primary"><?= lang('SiteCounter.websites.create_button') ?></button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>