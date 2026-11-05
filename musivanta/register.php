<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if (isLoggedIn()) {
    redirectToUserDashboard(currentUser());
}

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = trim((string) ($_POST['full_name'] ?? ''));
    $email = trim((string) ($_POST['email'] ?? ''));
    $phone = trim((string) ($_POST['phone'] ?? ''));
    $password = (string) ($_POST['password'] ?? '');
    $confirm = (string) ($_POST['confirm_password'] ?? '');

    if ($fullName === '' || $email === '' || $phone === '' || $password === '' || $confirm === '') {
        $errors[] = 'All fields are required.';
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Please enter a valid email address.';
    }

    if ($password !== $confirm) {
        $errors[] = 'Passwords do not match.';
    }

    if (strlen($password) < 8) {
        $errors[] = 'Password must be at least 8 characters long.';
    }

    if (empty($errors)) {
        try {
            $pdo = getDatabaseConnection();
            $check = $pdo->prepare('SELECT id FROM users WHERE email = :email LIMIT 1');
            $check->execute([':email' => $email]);
            if ($check->fetch()) {
                $errors[] = 'A user with this email already exists.';
            } else {
                $stmt = $pdo->prepare('INSERT INTO users (id, full_name, email, phone, password_hash, role, status, created_at, updated_at) VALUES (:id, :full_name, :email, :phone, :password_hash, :role, :status, NOW(), NOW())');
                $userId = 'USR' . str_pad((string) (random_int(100000, 999999)), 6, '0', STR_PAD_LEFT);
                $stmt->execute([
                    ':id' => $userId,
                    ':full_name' => $fullName,
                    ':email' => $email,
                    ':phone' => $phone,
                    ':password_hash' => password_hash($password, PASSWORD_DEFAULT),
                    ':role' => 'customer',
                    ':status' => 'active',
                ]);
                $success = true;
            }
        } catch (Throwable $e) {
            $errors[] = 'Unable to create your account right now. Please try again later.';
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-shell">
    <div class="auth-box">
        <div class="text-center mb-4">
            <span class="brand-mark">M</span>
            <h2 class="mt-3">Create your account</h2>
            <p class="text-muted mb-0">Join Musivanta to book your next unforgettable performance.</p>
        </div>

        <?php if (!empty($errors)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars(implode(' ', $errors), ENT_QUOTES, 'UTF-8'); ?></div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="alert alert-success">Your account has been created successfully. <a href="/login.php">Login here</a>.</div>
        <?php else: ?>
            <form method="post" action="/register.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" required>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" required>
                </div>
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone number</label>
                    <input type="tel" class="form-control" id="phone" name="phone" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <input type="password" class="form-control" id="password" name="password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <div class="d-grid gap-2">
                    <button class="btn btn-primary" type="submit">Create account</button>
                </div>
                <div class="mt-3 text-center text-muted small">
                    Already have an account? <a href="/login.php">Sign in</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
