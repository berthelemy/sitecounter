<!DOCTYPE html>
<html lang="<?= esc(service('request')->getLocale()) ?>">
<head>
    <meta charset="UTF-8">
    <title><?= lang('SiteCounter.profile.title') ?> - <?= lang('SiteCounter.app.name') ?></title>
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
                        <a class="nav-link active" href="/dashboard/profile"><i class="bi bi-person-circle me-1"></i><?= lang('SiteCounter.nav.profile') ?></a>
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
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4><?= lang('SiteCounter.profile.settings_title') ?></h4>
                    </div>
                    <div class="card-body">
                        <?php if (session()->has('success')): ?>
                            <div class="alert alert-success">
                                <?= session('success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->has('password_success')): ?>
                            <div class="alert alert-success">
                                <?= session('password_success') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->has('error')): ?>
                            <div class="alert alert-danger">
                                <?= session('error') ?>
                            </div>
                        <?php endif; ?>

                        <?php if (session()->has('info')): ?>
                            <div class="alert alert-info">
                                <?= session('info') ?>
                            </div>
                        <?php endif; ?>

                        <?php $errors = session('errors') ?? []; ?>
                        <?php if ($errors): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($errors as $error): ?>
                                        <li><?= esc($error) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="/dashboard/profile" method="post" novalidate>
                            <?= csrf_field() ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label"><?= lang('SiteCounter.profile.first_name') ?></label>
                                        <input type="text" class="form-control" id="firstname" name="firstname"
                                               value="<?= old('firstname', $user->firstname ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label"><?= lang('SiteCounter.profile.last_name') ?></label>
                                        <input type="text" class="form-control" id="lastname" name="lastname"
                                               value="<?= old('lastname', $user->lastname ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label"><?= lang('SiteCounter.profile.email') ?></label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= old('email', $user->email) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label"><?= lang('SiteCounter.profile.username') ?></label>
                                <input type="text" class="form-control" value="<?= esc($user->username) ?>" readonly>
                                <div class="form-text"><?= lang('SiteCounter.profile.username_readonly') ?></div>
                            </div>

                            <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i><?= lang('SiteCounter.profile.update_profile') ?></button>
                            <a href="/dashboard" class="btn btn-secondary"><i class="bi bi-arrow-left me-1"></i><?= lang('SiteCounter.app.cancel') ?></a>
                        </form>

                        <hr class="my-4">
                        <h5><?= lang('SiteCounter.profile.change_password') ?></h5>

                        <?php $passwordErrors = session('password_errors') ?? []; ?>
                        <?php if ($passwordErrors): ?>
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    <?php foreach ($passwordErrors as $e): ?>
                                        <li><?= esc($e) ?></li>
                                    <?php endforeach; ?>
                                </ul>
                            </div>
                        <?php endif; ?>

                        <form action="/dashboard/profile/password" method="post" novalidate>
                            <?= csrf_field() ?>
                            <?php if (! $passwordResetMode): ?>
                                <div class="mb-3">
                                    <label for="current_password" class="form-label"><?= lang('SiteCounter.profile.current_password') ?></label>
                                    <input type="password" class="form-control" id="current_password" name="current_password" required>
                                </div>
                            <?php else: ?>
                                <div class="alert alert-info py-2">
                                    <?= lang('SiteCounter.profile.magic_link_reset_hint') ?>
                                </div>
                            <?php endif; ?>
                            <div class="mb-3">
                                <label for="new_password" class="form-label"><?= lang('SiteCounter.profile.new_password') ?></label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                                <div class="form-text"><?= lang('SiteCounter.profile.password_min') ?></div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirm" class="form-label"><?= lang('SiteCounter.profile.confirm_new_password') ?></label>
                                <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" required minlength="8">
                            </div>
                            <button type="submit" class="btn btn-warning"><i class="bi bi-lock me-1"></i><?= lang('SiteCounter.profile.change_password') ?></button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>