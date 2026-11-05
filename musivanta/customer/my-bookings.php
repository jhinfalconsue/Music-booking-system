<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['customer']);

$pdo = getDatabaseConnection();
$currentUser = currentUser();
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $musicianId = $_POST['musician_id'] ?? null;
    $bookingDate = $_POST['booking_date'] ?? date('Y-m-d');
    $startTime = $_POST['start_time'] ?? '18:00';
    $endTime = $_POST['end_time'] ?? '20:00';
    $notes = $_POST['notes'] ?? '';

    if ($musicianId) {
        $bookingId = 'BKG' . str_pad((string) random_int(100000, 999999), 6, '0', STR_PAD_LEFT);
        $pdo->prepare('INSERT INTO bookings (id, customer_id, musician_id, booking_date, start_time, end_time, status, notes, created_at, updated_at) VALUES (:id, :customer_id, :musician_id, :booking_date, :start_time, :end_time, :status, :notes, NOW(), NOW())')->execute([
            ':id' => $bookingId,
            ':customer_id' => $currentUser['id'],
            ':musician_id' => $musicianId,
            ':booking_date' => $bookingDate,
            ':start_time' => $startTime,
            ':end_time' => $endTime,
            ':status' => 'pending',
            ':notes' => $notes,
        ]);
        setFlash('success', 'Your booking request has been submitted successfully.');
    }
}

$bookings = $pdo->prepare('SELECT b.*, m.stage_name FROM bookings b INNER JOIN musicians m ON m.id = b.musician_id WHERE b.customer_id = :customer_id ORDER BY b.created_at DESC');
$bookings->execute([':customer_id' => $currentUser['id']]);
$bookings = $bookings->fetchAll();
include __DIR__ . '/../includes/header.php';
?>
<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <h2>My bookings</h2>
        <?php echo renderFlash(); ?>
        <div class="card-panel p-4 mt-3">
            <form method="post" action="/customer/my-bookings.php">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">Musician ID</label>
                        <input class="form-control" type="text" name="musician_id" placeholder="MUS..." required>
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Booking date</label>
                        <input class="form-control" type="date" name="booking_date" value="<?php echo date('Y-m-d'); ?>" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">Start</label>
                        <input class="form-control" type="time" name="start_time" value="18:00" required>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End</label>
                        <input class="form-control" type="time" name="end_time" value="20:00" required>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button class="btn btn-primary w-100" type="submit">Request</button>
                    </div>
                </div>
                <div class="mt-3">
                    <label class="form-label">Notes</label>
                    <textarea class="form-control" rows="3" name="notes"></textarea>
                </div>
            </form>
        </div>

        <div class="card-panel p-3 mt-4">
            <div class="table-wrap">
                <table class="table">
                    <thead>
                        <tr>
                            <th>Musician</th>
                            <th>Date</th>
                            <th>Time</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $booking): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($booking['stage_name'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['booking_date'], ENT_QUOTES, 'UTF-8'); ?></td>
                                <td><?php echo htmlspecialchars($booking['start_time'] . '-' . $booking['end_time'], ENT_QUOTES, 'UTF-8'); ?></td>
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
