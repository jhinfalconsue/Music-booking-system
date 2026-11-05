<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/auth.php';
requireRole(['musician']);

$pdo = getDatabaseConnection();
$user = currentUser();
$musician = $pdo->prepare('SELECT * FROM musicians WHERE user_id = :user_id LIMIT 1');
$musician->execute([':user_id' => $user['id']]);
$musician = $musician->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $stageName = trim((string) ($_POST['stage_name'] ?? ''));
    $bio = trim((string) ($_POST['bio'] ?? ''));
    $experienceYears = (int) ($_POST['experience_years'] ?? 0);
    $hourlyRate = (float) ($_POST['hourly_rate'] ?? 0);
    $city = trim((string) ($_POST['city'] ?? ''));

    if ($musician) {
        $pdo->prepare('UPDATE musicians SET stage_name = :stage_name, bio = :bio, experience_years = :experience_years, hourly_rate = :hourly_rate, city = :city, updated_at = NOW() WHERE id = :id')->execute([
            ':stage_name' => $stageName,
            ':bio' => $bio,
            ':experience_years' => $experienceYears,
            ':hourly_rate' => $hourlyRate,
            ':city' => $city,
            ':id' => $musician['id'],
        ]);
    } else {
        $id = 'MUS' . str_pad((string) (random_int(100000, 999999)), 6, '0', STR_PAD_LEFT);
        $pdo->prepare('INSERT INTO musicians (id, user_id, stage_name, bio, experience_years, hourly_rate, city, is_verified, created_at, updated_at) VALUES (:id, :user_id, :stage_name, :bio, :experience_years, :hourly_rate, :city, 0, NOW(), NOW())')->execute([
            ':id' => $id,
            ':user_id' => $user['id'],
            ':stage_name' => $stageName,
            ':bio' => $bio,
            ':experience_years' => $experienceYears,
            ':hourly_rate' => $hourlyRate,
            ':city' => $city,
        ]);
    }

    header('Location: /musician/profile.php');
    exit;
}

include __DIR__ . '/../includes/header.php';
?>
<div class="dashboard-shell">
    <?php include __DIR__ . '/../includes/sidebar.php'; ?>
    <div class="dashboard-content">
        <h2>Profile</h2>
        <div class="card-panel p-4 mt-3">
            <form method="post" action="/musician/profile.php">
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Stage name</label>
                        <input class="form-control" type="text" name="stage_name" value="<?php echo htmlspecialchars($musician['stage_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Experience years</label>
                        <input class="form-control" type="number" name="experience_years" value="<?php echo htmlspecialchars((string) ($musician['experience_years'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">City</label>
                        <input class="form-control" type="text" name="city" value="<?php echo htmlspecialchars($musician['city'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Hourly rate</label>
                        <input class="form-control" type="number" step="0.01" name="hourly_rate" value="<?php echo htmlspecialchars((string) ($musician['hourly_rate'] ?? 0), ENT_QUOTES, 'UTF-8'); ?>" required>
                    </div>
                    <div class="col-12">
                        <label class="form-label">Bio</label>
                        <textarea class="form-control" rows="5" name="bio" required><?php echo htmlspecialchars($musician['bio'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
                    </div>
                </div>
                <button class="btn btn-primary mt-4" type="submit">Save profile</button>
            </form>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
