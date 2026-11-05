<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['admin']);

$pdo = getDatabaseConnection();
$musicians = $pdo->query('SELECT m.*, u.full_name, u.email FROM musicians m INNER JOIN users u ON u.id = m.user_id ORDER BY m.created_at DESC')->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <h2>Musicians</h2>
        <div class="card-panel p-3 mt-3">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Stage name</th>
                            <th>Owner</th>
                            <th>Location</th>
                            <th>Rate</th>
                            <th>Verified</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($musicians as $musician): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($musician['stage_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($musician['full_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($musician['city'] ?? 'N/A', ENT_QUOTES, 'UTF-8'); ?></td>
                                <td>$<?php echo htmlspecialchars($musician['hourly_rate'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo $musician['is_verified'] ? 'Yes' : 'No'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
