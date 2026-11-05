<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirectToUserDashboard(currentUser());
}

$errors = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');

    if ($email === '' || $password === '') {
        $errors[] = 'Email and password are required.';
    } else {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user'] = [
                    'id' => $user['id'],
                    'full_name' => $user['full_name'],
                    'email' => $user['email'],
                    'role' => $user['role'],
                ];
                redirectToUserDashboard($_SESSION['user']);
            }

            $errors[] = 'Invalid email or password.';
        } catch (Throwable $e) {
            $errors[] = 'Unable to log in right now. Please try again later.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-shell">
    <div class="auth-box">
        <div class="text-center mb-4">
            <span class="brand-mark">M</span>
            <h2 class="mt-3">Welcome back</h2>
            <p class="text-muted mb-0">Log in to access your Musivanta dashboard.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <form method="post" action="/login.php">
            <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
            <div class="mb-3">
                <label for="email" class="form-label">Email address</label>
                <input type="email" class="form-control" id="email" name="email" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="d-grid gap-2">
                <button class="btn btn-primary" type="submit">Login</button>
            </div>
            <div class="mt-3 d-flex justify-content-between small text-muted">
                <a href="/forgot-password.php">Forgot password?</a>
                <a href="/register.php">Create account</a>
            </div>
        </form>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
