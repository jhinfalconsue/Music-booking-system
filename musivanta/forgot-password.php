<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim((string) ($_POST['email'] ?? ''));
    $token = trim((string) ($_GET['token'] ?? ''));
    $newPassword = (string) ($_POST['new_password'] ?? '');
    $confirmPassword = (string) ($_POST['confirm_password'] ?? '');

    if ($token !== '' && $newPassword !== '') {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->query('SELECT * FROM users WHERE reset_token_hash IS NOT NULL AND reset_token_expires_at > NOW()');
            $users = $stmt->fetchAll();
            $matchFound = false;

            foreach ($users as $user) {
                if (hash_equals($user['reset_token_hash'] ?? '', hash_hmac('sha256', $token, 'musivanta_reset_secret'))) {
                    $matchFound = true;
                    if (strlen($newPassword) >= 8 && $newPassword === $confirmPassword) {
                        $update = $pdo->prepare('UPDATE users SET password_hash = :password_hash, reset_token_hash = NULL, reset_token_expires_at = NULL, updated_at = NOW() WHERE id = :id');
                        $update->execute([
                            ':password_hash' => password_hash($newPassword, PASSWORD_DEFAULT),
                            ':id' => $user['id'],
                        ]);
                        setFlash('success', 'Your password has been reset successfully.');
                        header('Location: /login.php');
                        exit;
                    }
                    setFlash('danger', 'The reset token is invalid or the password does not meet requirements.');
                    break;
                }
            }

            if (!$matchFound) {
                setFlash('danger', 'The reset token is invalid or has expired.');
            }
        } catch (Throwable $e) {
            setFlash('danger', 'Unable to process the password reset right now.');
        }
    }

    if ($email !== '' && empty($token)) {
        try {
            $pdo = getDatabaseConnection();
            $stmt = $pdo->prepare('SELECT * FROM users WHERE email = :email LIMIT 1');
            $stmt->execute([':email' => $email]);
            $user = $stmt->fetch();

            if ($user) {
                $resetToken = bin2hex(random_bytes(32));
                $resetTokenHash = hash_hmac('sha256', $resetToken, 'musivanta_reset_secret');
                $expiry = date('Y-m-d H:i:s', time() + 3600);

                $update = $pdo->prepare('UPDATE users SET reset_token_hash = :hash, reset_token_expires_at = :expires, updated_at = NOW() WHERE id = :id');
                $update->execute([
                    ':hash' => $resetTokenHash,
                    ':expires' => $expiry,
                    ':id' => $user['id'],
                ]);

                $resetLink = 'http://localhost/forgot-password.php?token=' . urlencode($resetToken);
                setFlash('info', 'For development, the reset link is: ' . $resetLink . ' (email delivery is not configured in this local setup).');
            } else {
                setFlash('info', 'If an account exists for that email, a password reset link will be generated. No email is sent in this development setup.');
            }
        } catch (Throwable $e) {
            setFlash('danger', 'Unable to create a reset request right now.');
        }
    }
}

include __DIR__ . '/includes/header.php';
?>

<section class="auth-shell">
    <div class="auth-box">
        <div class="text-center mb-4">
            <span class="brand-mark">M</span>
            <h2 class="mt-3">Reset your password</h2>
            <p class="text-muted mb-0">Use this flow to securely recover access to your account.</p>
        </div>

        <?php echo renderFlash(); ?>

        <?php if (isset($_GET['token']) && $_GET['token'] !== ''): ?>
            <form method="post" action="/forgot-password.php?token=<?php echo urlencode((string) $_GET['token']); ?>">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="mb-3">
                    <label for="new_password" class="form-label">New password</label>
                    <input type="password" class="form-control" id="new_password" name="new_password" required>
                </div>
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm new password</label>
                    <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Update password</button>
            </form>
        <?php else: ?>
            <form method="post" action="/forgot-password.php">
                <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars(csrfToken(), ENT_QUOTES, 'UTF-8'); ?>">
                <div class="mb-3">
                    <label for="email" class="form-label">Email address</label>
                    <input type="email" class="form-control" id="email" name="email" placeholder="you@example.com" required>
                </div>
                <button class="btn btn-primary w-100" type="submit">Send reset link</button>
                <div class="mt-3 text-center text-muted small">
                    <a href="/login.php">Back to login</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
