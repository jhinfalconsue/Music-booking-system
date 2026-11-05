<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

$pdo = getDatabaseConnection();
$payments = $pdo->query('SELECT p.*, u.full_name AS payer_name FROM payments p INNER JOIN users u ON u.id = p.user_id ORDER BY p.paid_at DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <h2>Payments</h2>
        <div class="card-panel p-3 mt-3">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Payer</th>
                            <th>Amount</th>
                            <th>Method</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $payment): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($payment['id'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($payment['payer_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>$<?php echo number_format((float) $payment['amount'], 2); ?></td>
                                <td><?php echo htmlspecialchars($payment['payment_method'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($payment['status'], ENT_QUOTES, 'UTF-8'); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
