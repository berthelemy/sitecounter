<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Websites - SiteCounter</title>
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
                    <h1>Websites</h1>
                    <a href="/dashboard/websites/create" class="btn btn-primary">Add Website</a>
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
                            <p class="text-muted">No websites found. <a href="/dashboard/websites/create">Add your first website</a>.</p>
                        <?php else: ?>
                            <div class="table-responsive">
                                <table class="table table-striped">
                                    <thead>
                                        <tr>
                                            <th>Name</th>
                                            <th>URL</th>
                                            <th>Created</th>
                                            <th>Actions</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($websites as $website): ?>
                                            <tr>
                                                <td><?= esc($website['name']) ?></td>
                                                <td><a href="<?= esc($website['url']) ?>" target="_blank"><?= esc($website['url']) ?></a></td>
                                                <td><?= date('M j, Y', strtotime($website['created_at'])) ?></td>
                                                <td>
                                                    <a href="/dashboard/websites/<?= $website['id'] ?>" class="btn btn-sm btn-info">View</a>
                                                    <a href="/dashboard/websites/<?= $website['id'] ?>/edit" class="btn btn-sm btn-warning">Edit</a>
                                                    <form method="post" action="/dashboard/websites/<?= $website['id'] ?>/delete" style="display: inline;">
                                                        <?= csrf_field() ?>
                                                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this website?')">Delete</button>
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