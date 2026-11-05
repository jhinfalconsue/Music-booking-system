<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['musician']);

$pdo = getDatabaseConnection();
$user = currentUser();
$musician = $pdo->prepare('SELECT * FROM musicians WHERE user_id = :user_id LIMIT 1');
$musician->execute([':user_id' => $user['id']]);
$musician = $musician->fetch();

$bookings = $pdo->prepare('SELECT COUNT(*) FROM bookings WHERE musician_id = :musician_id');
$bookings->execute([':musician_id' => $musician['id'] ?? '']);
$bookingCount = (int) $bookings->fetchColumn();

$earnings = $pdo->prepare('SELECT COALESCE(SUM(amount), 0) FROM payments WHERE user_id = :user_id');
$earnings->execute([':user_id' => $user['id']]);
$earningTotal = (float) $earnings->fetchColumn();

include __DIR__ . '/../includes/header.php';
?>
<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <h2>Musician dashboard</h2>
        <p class="text-muted">Manage your profile, bookings, availability, and income.</p>
        <div class="row g-4 mt-2">
            <div class="col-md-6">
                <div class="stat-card">
                    <span class="text-muted">Bookings</span>
                    <div class="metric-value"><?php echo $bookingCount; ?></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="stat-card">
                    <span class="text-muted">Earnings</span>
                    <div class="metric-value">$<?php echo number_format($earningTotal, 2); ?></div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
