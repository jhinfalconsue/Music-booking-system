<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['musician']);

$pdo = getDatabaseConnection();
$user = currentUser();
$musician = $pdo->prepare('SELECT * FROM musicians WHERE user_id = :user_id LIMIT 1');
$musician->execute([':user_id' => $user['id']]);
$musician = $musician->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $musician) {
    $day = trim((string) ($_POST['day_of_week'] ?? ''));
    $start = trim((string) ($_POST['start_time'] ?? ''));
    $end = trim((string) ($_POST['end_time'] ?? ''));

    $insert = $pdo->prepare('INSERT INTO availability (id, musician_id, day_of_week, start_time, end_time, is_available, created_at, updated_at) VALUES (:id, :musician_id, :day_of_week, :start_time, :end_time, :is_available, NOW(), NOW())');
    $insert->execute([
        ':id' => 'AVL' . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT),
        ':musician_id' => $musician['id'],
        ':day_of_week' => $day,
        ':start_time' => $start,
        ':end_time' => $end,
        ':is_available' => 1,
    ]);
    header('Location: /musician/availability.php');
    exit;
}

$slots = $pdo->prepare('SELECT * FROM availability WHERE musician_id = :musician_id ORDER BY day_of_week, start_time');
$slots->execute([':musician_id' => $musician['id'] ?? '']);
$slots = $slots->fetchAll();

include __DIR__ . '/../includes/header.php';
?>
<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <h2>Availability</h2>
        <div class="card-panel p-4 mt-3">
            <form method="post" action="/musician/availability.php">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">Day</label>
                        <select class="form-select" name="day_of_week">
                            <option value="Monday">Monday</option>
                            <option value="Tuesday">Tuesday</option>
                            <option value="Wednesday">Wednesday</option>
                            <option value="Thursday">Thursday</option>
                            <option value="Friday">Friday</option>
                            <option value="Saturday">Saturday</option>
                            <option value="Sunday">Sunday</option>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Start time</label>
                        <input class="form-control" type="time" name="start_time" required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">End time</label>
                        <input class="form-control" type="time" name="end_time" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" type="submit">Add</button>
                    </div>
                </div>
            </form>
        </div>

        <div class="card-panel p-3 mt-4">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Day</th>
                            <th>Start</th>
                            <th>End</th>
                            <th>Available</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($slots as $slot): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($slot['day_of_week'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($slot['start_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($slot['end_time'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo (int) $slot['is_available'] ? 'Yes' : 'No'; ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
