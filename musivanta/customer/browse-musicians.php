<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';

$genreFilter = trim((string) ($_GET['genre'] ?? ''));
$locationFilter = trim((string) ($_GET['location'] ?? ''));
$musicians = [];
$browseError = false;

try {
    $pdo = getDatabaseConnection();
    $sql = 'SELECT m.*, u.full_name, GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ", ") AS genre_names FROM musicians m INNER JOIN users u ON u.id = m.user_id LEFT JOIN musicians_genres mg ON mg.musician_id = m.id LEFT JOIN genres g ON g.id = mg.genre_id GROUP BY m.id';
    $params = [];
    $conditions = [];
    if ($genreFilter !== '') {
        $conditions[] = 'genre_names LIKE :genre';
        $params[':genre'] = '%' . $genreFilter . '%';
    }
    if ($locationFilter !== '') {
        $conditions[] = 'm.city LIKE :location';
        $params[':location'] = '%' . $locationFilter . '%';
    }
    if (!empty($conditions)) {
        $sql .= ' HAVING ' . implode(' AND ', $conditions);
    }
    $sql .= ' ORDER BY m.created_at DESC';
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $musicians = $stmt->fetchAll();
} catch (Throwable $e) {
    error_log('Unable to load browse musicians: ' . $e->getMessage());
    $browseError = true;
}

include __DIR__ . '/../includes/header.php';
?>
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <span class="tag">Browse artists</span>
            <h2 class="section-title mt-2">Find the perfect performance</h2>
        </div>
    </div>

    <form method="get" class="card-panel p-3 mb-4">
        <div class="row g-3 align-items-end">
            <div class="col-md-4">
                <label class="form-label">Genre</label>
                <input class="form-control" type="text" name="genre" value="<?php echo htmlspecialchars($genreFilter, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-4">
                <label class="form-label">Location</label>
                <input class="form-control" type="text" name="location" value="<?php echo htmlspecialchars($locationFilter, ENT_QUOTES, 'UTF-8'); ?>">
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary w-100" type="submit">Filter</button>
            </div>
        </div>
    </form>

    <?php if ($browseError): ?>
        <div class="alert alert-warning" role="alert">
            Musician listings are unavailable right now. Please try again later.
        </div>
    <?php elseif (empty($musicians)): ?>
        <div class="card-panel p-4 text-center">
            <p class="text-muted mb-0">No musicians match your search yet.</p>
        </div>
    <?php else: ?>
    <div class="row g-4">
        <?php foreach ($musicians as $musician): ?>
            <div class="col-lg-4 col-md-6">
                <div class="card-panel musician-card h-100 p-3">
                    <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80" alt="<?php echo htmlspecialchars($musician['stage_name'], ENT_QUOTES, 'UTF-8'); ?>" class="rounded-4 mb-3" style="height: 220px; object-fit: cover; width: 100%;">
                    <div class="d-flex justify-content-between">
                        <h5><?php echo htmlspecialchars($musician['stage_name'] ?? $musician['full_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <span class="badge bg-success">Available</span>
                    </div>
                    <p class="text-muted mb-2"><?php echo htmlspecialchars($musician['city'] ?? 'Unknown', ENT_QUOTES, 'UTF-8'); ?></p>
                    <div class="mb-3">
                        <?php $genres = $musician['genre_names'] ? explode(', ', $musician['genre_names']) : []; ?>
                        <?php foreach (array_slice($genres, 0, 3) as $genre): ?>
                            <span class="genre-badge"><?php echo htmlspecialchars($genre, ENT_QUOTES, 'UTF-8'); ?></span>
                        <?php endforeach; ?>
                    </div>
                    <div class="d-flex justify-content-between align-items-center">
                        <strong>$<?php echo htmlspecialchars((string) ($musician['hourly_rate'] ?? '0'), ENT_QUOTES, 'UTF-8'); ?>/hr</strong>
                        <a class="btn btn-primary btn-sm" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/customer/my-bookings.php?musician_id=<?php echo rawurlencode((string) $musician['id']); ?>">Book</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
