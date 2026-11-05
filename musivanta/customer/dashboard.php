<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['customer']);

$pdo = getDatabaseConnection();
$currentUser = currentUser();
$bookings = $pdo->prepare('SELECT b.*, m.stage_name FROM bookings b INNER JOIN musicians m ON m.id = b.musician_id WHERE b.customer_id = :customer_id ORDER BY b.created_at DESC LIMIT 5');
$bookings->execute([':customer_id' => $currentUser['id']]);
$bookings = $bookings->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <h2>Customer dashboard</h2>
        <p class="text-muted">Track your bookings and upcoming performances.</p>
        <div class="card-panel p-3 mt-4">
            <h4>Recent bookings</h4>
            <div class="table-wrap mt-3">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Musician</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['stage_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
