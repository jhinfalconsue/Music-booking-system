<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once __DIR__ . '/auth.php';

$currentUser = currentUser();
$currentPage = basename($_SERVER['PHP_SELF']);
$appBasePath = preg_replace(
    '~/(admin|customer|musician)$~',
    '',
    rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/')
);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Musivanta helps clients discover and book top-tier musicians for events and performances.">
    <title>Musivanta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/assets/style.css">
</head>
<body class="<?php echo $currentPage === 'index.php' ? 'homepage' : ''; ?>">
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center gap-2" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/index.php">
                <span class="brand-mark">M</span>
                <span>Musivanta</span>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNav" aria-controls="mainNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="mainNav">
                <ul class="navbar-nav mx-auto mb-2 mb-lg-0">
                    <li class="nav-item"><a class="nav-link <?php echo $currentPage === 'index.php' ? 'active' : ''; ?>" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/customer/browse-musicians.php">Browse Musicians</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/index.php#how-it-works">How It Works</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/index.php#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/index.php#contact">Contact</a></li>
                </ul>
                <div class="d-flex align-items-center gap-2">
                    <?php if ($currentUser): ?>
                        <a class="btn btn-outline-light" href="<?php echo $currentUser['role'] === 'admin' ? '/admin/dashboard.php' : ($currentUser['role'] === 'musician' ? '/musician/dashboard.php' : '/customer/dashboard.php'); ?>">Dashboard</a>
                        <a class="btn btn-primary" href="/logout.php">Logout</a>
                    <?php else: ?>
                        <a class="btn btn-outline-light" href="/login.php">Login</a>
                        <a class="btn btn-primary" href="/register.php">Register</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <main>
