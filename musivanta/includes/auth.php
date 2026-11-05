<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function currentUser(): ?array
{
    return $_SESSION['user'] ?? null;
}

function isLoggedIn(): bool
{
    return !empty($_SESSION['user']['id']);
}

function requireLogin(): void
{
    if (!isLoggedIn()) {
        $_SESSION['flash'] = ['type' => 'warning', 'message' => 'Please login to continue.'];
        header('Location: /login.php');
        exit;
    }
}

function requireRole(array $allowedRoles): void
{
    requireLogin();

    $user = currentUser();
    if (!$user || !in_array($user['role'], $allowedRoles, true)) {
        $_SESSION['flash'] = ['type' => 'danger', 'message' => 'You do not have permission to access this page.'];
        header('Location: /index.php');
        exit;
    }
}

function redirectToUserDashboard(array $user): void
{
    $dashboardMap = [
        'admin' => '/admin/dashboard.php',
        'musician' => '/musician/dashboard.php',
        'customer' => '/customer/dashboard.php',
    ];

    $target = $dashboardMap[$user['role']] ?? '/index.php';
    header('Location: ' . $target);
    exit;
}

function setFlash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function renderFlash(): string
{
    if (empty($_SESSION['flash'])) {
        return '';
    }

    $flash = $_SESSION['flash'];
    unset($_SESSION['flash']);

    return '<div class="alert alert-' . htmlspecialchars($flash['type'], ENT_QUOTES, 'UTF-8') . ' alert-dismissible fade show" role="alert">'
        . htmlspecialchars($flash['message'], ENT_QUOTES, 'UTF-8')
        . '<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>'
        . '</div>';
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }

    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('CSRF validation failed.');
    }
}
