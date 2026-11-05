<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['musician']);

$pdo = getDatabaseConnection();
$user = currentUser();
$musician = $pdo->prepare('SELECT * FROM musicians WHERE user_id = :user_id LIMIT 1');
$musician->execute([':user_id' => $user['id']]);
$musician = $musician->fetch();

$bookings = $pdo->prepare('SELECT b.*, u.full_name AS customer_name FROM bookings b INNER JOIN users u ON u.id = b.customer_id WHERE b.musician_id = :musician_id ORDER BY b.booking_date DESC');
$bookings->execute([':musician_id' => $musician['id'] ?? '']);
$bookings = $bookings->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <h2>Bookings</h2>
        <div class="card-panel p-3 mt-3">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Booking ID</th>
                            <th>Customer</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['customer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
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
