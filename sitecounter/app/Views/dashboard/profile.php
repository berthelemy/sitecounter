<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Profile - SiteCounter</title>
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
                        <a class="nav-link active" href="/dashboard/profile">Profile</a>
                    </li>
                </ul>
                <ul class="navbar-nav">
                    <li class="nav-item me-2">
                        <?= lang_switcher() ?>
                    </li>
                    <li class="nav-item">
                        <span class="navbar-text me-3">Welcome, <?= esc($fullName) ?></span>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="/logout">Logout</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">
                        <h4>Profile Settings</h4>
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

                        <form action="/dashboard/profile" method="post">
                            <?= csrf_field() ?>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="firstname" class="form-label">First Name</label>
                                        <input type="text" class="form-control" id="firstname" name="firstname"
                                               value="<?= old('firstname', $user->firstname ?? '') ?>" required>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label for="lastname" class="form-label">Last Name</label>
                                        <input type="text" class="form-control" id="lastname" name="lastname"
                                               value="<?= old('lastname', $user->lastname ?? '') ?>" required>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email"
                                       value="<?= old('email', $user->email) ?>" required>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Username</label>
                                <input type="text" class="form-control" value="<?= esc($user->username) ?>" readonly>
                                <div class="form-text">Username cannot be changed</div>
                            </div>

                            <button type="submit" class="btn btn-primary">Update Profile</button>
                            <a href="/dashboard" class="btn btn-secondary">Cancel</a>
                        </form>

                        <hr class="my-4">
                        <h5>Change Password</h5>

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

                        <form action="/dashboard/profile/password" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="current_password" class="form-label">Current Password</label>
                                <input type="password" class="form-control" id="current_password" name="current_password" required>
                            </div>
                            <div class="mb-3">
                                <label for="new_password" class="form-label">New Password</label>
                                <input type="password" class="form-control" id="new_password" name="new_password" required minlength="8">
                                <div class="form-text">Minimum 8 characters.</div>
                            </div>
                            <div class="mb-3">
                                <label for="new_password_confirm" class="form-label">Confirm New Password</label>
                                <input type="password" class="form-control" id="new_password_confirm" name="new_password_confirm" required minlength="8">
                            </div>
                            <button type="submit" class="btn btn-warning">Change Password</button>
                        </form>

                        <?php $supported = config('App')->supportedLocales ?? ['en']; ?>
                        <?php if (count($supported) > 1): ?>
                        <hr class="my-4">
                        <h5>Language Preference</h5>
                        <form action="/dashboard/profile/language" method="post">
                            <?= csrf_field() ?>
                            <div class="mb-3">
                                <label for="language" class="form-label">Preferred Language</label>
                                <select class="form-select" id="language" name="language">
                                    <?php foreach ($supported as $locale): ?>
                                        <option value="<?= esc($locale) ?>" <?= (service('request')->getLocale() === $locale) ? 'selected' : '' ?>>
                                            <?= strtoupper(esc($locale)) ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-secondary">Save Language</button>
                        </form>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>