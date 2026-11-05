<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

$pdo = getDatabaseConnection();
$stats = [
    'users' => $pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'musicians' => $pdo->query('SELECT COUNT(*) FROM musicians')->fetchColumn(),
    'bookings' => $pdo->query('SELECT COUNT(*) FROM bookings')->fetchColumn(),
    'payments' => $pdo->query('SELECT COALESCE(SUM(amount), 0) FROM payments')->fetchColumn(),
];
$recentUsers = $pdo->query('SELECT * FROM users ORDER BY created_at DESC LIMIT 5')->fetchAll();

$currentUser = currentUser();
include __DIR__ . '/../includes/header.php';
?>

<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h2>Admin dashboard</h2>
                <p class="text-muted mb-0">Welcome back, <?php echo htmlspecialchars($currentUser['full_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?>.</p>
            </div>
        </div>

        <div class="row g-4 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <span class="text-muted">Total Users</span>
                    <div class="metric-value"><?php echo (int) $stats['users']; ?></div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <span class="text-muted">Musicians</span>
                    <div class="metric-value"><?php echo (int) $stats['musicians']; ?></div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <span class="text-muted">Bookings</span>
                    <div class="metric-value"><?php echo (int) $stats['bookings']; ?></div>
                </div>
            </div>
            <div class="col-md-6 col-xl-3">
                <div class="stat-card">
                    <span class="text-muted">Revenue</span>
                    <div class="metric-value">$<?php echo number_format((float) $stats['payments'], 2); ?></div>
                </div>
            </div>
        </div>

        <div class="card-panel p-3">
            <h4>Recent users</h4>
            <div class="table-wrap mt-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($recentUsers as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user['email'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user['role'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($user['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>
