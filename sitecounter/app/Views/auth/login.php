<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= lang('SiteCounter.login.title') ?> - <?= lang('SiteCounter.app.name') ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .login-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        }
        .login-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="container">
            <div class="d-flex justify-content-end mb-3">
                <?= lang_switcher() ?>
            </div>
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <div class="card login-card">
                        <div class="card-body p-5">
                            <div class="text-center mb-4">
                                <h2 class="fw-bold"><?= lang('SiteCounter.app.name') ?></h2>
                                <p class="text-muted"><?= lang('SiteCounter.login.subtitle') ?></p>
                            </div>

                            <?php if (session()->has('error')): ?>
                                <div class="alert alert-danger">
                                    <?= session('error') ?>
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

                            <form action="/login" method="post">
                                <?= csrf_field() ?>

                                <div class="mb-3">
                                    <label for="email" class="form-label"><?= lang('SiteCounter.login.email_address') ?></label>
                                    <input type="email" class="form-control" id="email" name="email"
                                           value="<?= old('email') ?>" required>
                                </div>

                                <div class="mb-3">
                                    <label for="password" class="form-label"><?= lang('SiteCounter.login.password') ?></label>
                                    <input type="password" class="form-control" id="password" name="password" required>
                                    <?php if (isset($loginButton) && $loginButton === 'submit'): ?>
                                        <div class="form-text">
                                            <?= lang('SiteCounter.validation.password_min_length') ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <div class="mb-3 form-check">
                                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                                    <label class="form-check-label" for="remember"><?= lang('SiteCounter.login.remember_me') ?></label>
                                </div>

                                <button type="submit" class="btn btn-primary w-100 py-2">
                                    <?= lang('SiteCounter.login.login_button') ?>
                                </button>
                            </form>

                            <div class="text-center mt-4">
                                <p class="mb-1">
                                    <a href="/login/magic-link" class="text-decoration-none"><?= lang('SiteCounter.login.forgot_password_long') ?></a>
                                </p>
                                <p class="mb-0">
                                    <a href="/" class="text-decoration-none">&larr; <?= lang('SiteCounter.app.back_to_home') ?></a>
                                </p>
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