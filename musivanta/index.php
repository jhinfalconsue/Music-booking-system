<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/auth.php';

try {
    $pdo = getDatabaseConnection();
    $featuredQuery = $pdo->query(
        'SELECT m.*, u.full_name, GROUP_CONCAT(g.name ORDER BY g.name SEPARATOR ", ") AS genre_names
         FROM musicians m
         INNER JOIN users u ON u.id = m.user_id
         LEFT JOIN musicians_genres mg ON mg.musician_id = m.id
         LEFT JOIN genres g ON g.id = mg.genre_id
         GROUP BY m.id
         ORDER BY m.created_at DESC
         LIMIT 6'
    );
    $featuredMusicians = $featuredQuery->fetchAll();
} catch (Throwable $e) {
    $featuredMusicians = [];
}

include __DIR__ . '/includes/header.php';
?>

<section class="hero">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-7">
                <span class="tag">Premium live music bookings</span>
                <h1 class="section-title mt-3">Find the Perfect <span class="text-gradient">Musician</span> for Your Special Event.</h1>
                <p class="text-muted fs-5">Book talented solo artists, bands, and DJs for weddings, parties, corporate functions, and private celebrations. Musivanta makes the process effortless and secure.</p>
                <div class="d-flex gap-3 flex-wrap mt-4">
                    <a class="btn btn-primary" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/customer/browse-musicians.php">Browse Musicians</a>
                    <a class="btn btn-outline-light" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/index.php#how-it-works">How It Works</a>
                </div>
                <div class="row g-3 mt-4">
                    <div class="col-md-4">
                        <div class="card-panel p-3">
                            <strong>1.2K+</strong><br>
                            <span class="text-muted">Bookings completed</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-panel p-3">
                            <strong>250+</strong><br>
                            <span class="text-muted">Verified musicians</span>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card-panel p-3">
                            <strong>4.9/5</strong><br>
                            <span class="text-muted">Average score</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-5">
                <div class="hero-card">
                    <div class="text-center mb-3">
                        <span class="tag">Featured this week</span>
                    </div>
                    <img src="https://images.unsplash.com/photo-1516280440614-37939bbacd81?auto=format&fit=crop&w=900&q=80" alt="Musicians performing" class="rounded-4 mb-3" style="height: 360px; object-fit: cover; width: 100%;">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h5 class="mb-1">Luna Vibes</h5>
                            <p class="text-muted mb-0">Acoustic • Soul • Wedding Sets</p>
                        </div>
                        <span class="badge bg-success">Available</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <span class="tag">Featured artists</span>
                <h2 class="section-title mt-2">Top musicians near you</h2>
            </div>
            <a href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/customer/browse-musicians.php" class="btn btn-outline-light">View all</a>
        </div>
        <div class="row g-4">
            <?php if (empty($featuredMusicians)): ?>
                <div class="col-12">
                    <div class="card-panel p-4 text-center">
                        <p class="mb-0 text-muted">Featured musician data will appear here when the database is connected and populated with artists.</p>
                    </div>
                </div>
            <?php else: ?>
                <?php foreach ($featuredMusicians as $musician): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card-panel musician-card h-100 p-3">
                            <div class="mb-3">
                                <img src="https://images.unsplash.com/photo-1500648767791-00dcc994a43e?auto=format&fit=crop&w=900&q=80" alt="<?php echo htmlspecialchars($musician['stage_name'] ?? $musician['full_name'], ENT_QUOTES, 'UTF-8'); ?>" class="rounded-4" style="height: 220px; object-fit: cover; width: 100%;">
                            </div>
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <h5 class="mb-0"><?php echo htmlspecialchars($musician['stage_name'] ?? $musician['full_name'], ENT_QUOTES, 'UTF-8'); ?></h5>
                                <span class="badge bg-success">Available</span>
                            </div>
                            <p class="text-muted mb-2"><?php echo htmlspecialchars($musician['city'] ?? 'City', ENT_QUOTES, 'UTF-8'); ?></p>
                            <div class="mb-3">
                                <?php $genres = $musician['genre_names'] ? explode(', ', $musician['genre_names']) : []; ?>
                                <?php foreach (array_slice($genres, 0, 3) as $genre): ?>
                                    <span class="genre-badge"><?php echo htmlspecialchars($genre, ENT_QUOTES, 'UTF-8'); ?></span>
                                <?php endforeach; ?>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <strong>$<?php echo htmlspecialchars($musician['hourly_rate'] ?? '0', ENT_QUOTES, 'UTF-8'); ?>/hour</strong>
                                <a class="btn btn-sm btn-primary" href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/customer/browse-musicians.php?musician=<?php echo (int) ($musician['id'] ?? 0); ?>">Book now</a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <span class="tag">Popular genres</span>
            <h2 class="section-title mt-2">Find the perfect sound for your event</h2>
        </div>
        <div class="row g-3">
            <?php $genres = ['Jazz', 'Rock', 'Classical', 'Pop', 'Acoustic', 'DJ', 'Soul', 'Afrobeat']; ?>
            <?php foreach ($genres as $genre): ?>
                <div class="col-md-3 col-sm-6">
                    <div class="card-panel p-3 text-center">
                        <div class="mb-2"><span class="brand-mark"><?php echo strtoupper(substr($genre, 0, 1)); ?></span></div>
                        <h5><?php echo htmlspecialchars($genre, ENT_QUOTES, 'UTF-8'); ?></h5>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" id="how-it-works">
    <div class="container">
        <div class="text-center mb-4">
            <span class="tag">How it works</span>
            <h2 class="section-title mt-2">Booking your favorite musician is simple</h2>
        </div>
        <div class="row g-4">
            <?php $steps = [
                ['title' => '1. Search', 'text' => 'Browse artists by genre, city, and budget.' ],
                ['title' => '2. Compare', 'text' => 'Review profiles, availability, and pricing.' ],
                ['title' => '3. Book', 'text' => 'Submit a booking request and confirm your event details.' ],
                ['title' => '4. Enjoy', 'text' => 'Relax while musicians perform for your unforgettable event.' ],
            ]; ?>
            <?php foreach ($steps as $index => $step): ?>
                <div class="col-md-6 col-lg-3">
                    <div class="card-panel p-4 h-100">
                        <div class="brand-mark mb-3"><?php echo $index + 1; ?></div>
                        <h5><?php echo htmlspecialchars($step['title'], ENT_QUOTES, 'UTF-8'); ?></h5>
                        <p class="text-muted mb-0"><?php echo htmlspecialchars($step['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="py-5" id="about">
    <div class="container">
        <div class="row align-items-center g-4">
            <div class="col-lg-6">
                <img src="https://images.unsplash.com/photo-1524504388940-b1c1722653e1?auto=format&fit=crop&w=1200&q=80" alt="People enjoying live music" class="rounded-4" style="height: 420px; object-fit: cover; width: 100%;">
            </div>
            <div class="col-lg-6">
                <span class="tag">Why choose Musivanta</span>
                <h2 class="section-title mt-2">Trusted by clients, loved by musicians</h2>
                <p class="text-muted">Musivanta connects event organizers with vetted musicians for all kinds of gatherings. We reduce the stress of planning and ensure a reliable booking process from start to finish.</p>
                <ul class="list-unstyled text-muted">
                    <li class="mb-2">Verified musician profiles and ratings</li>
                    <li class="mb-2">Clear pricing and flexible booking options</li>
                    <li class="mb-2">Secure checkout and payment tracking</li>
                    <li class="mb-2">Responsive support and event coordination</li>
                </ul>
            </div>
        </div>
    </div>
</section>

<section class="py-5">
    <div class="container">
        <div class="text-center mb-4">
            <span class="tag">Testimonials</span>
            <h2 class="section-title mt-2">What our clients say</h2>
        </div>
        <div class="row g-4">
            <?php $testimonials = [
                ['name' => 'Emma R.', 'text' => 'Musivanta helped us book a jazz trio for our anniversary dinner. The process was smooth and the musicians were outstanding.'],
                ['name' => 'Jason T.', 'text' => 'A great platform for finding local talent. We booked a saxophonist within hours and the event felt perfect.'],
                ['name' => 'Priya N.', 'text' => 'The payment tracking and communication features made it easy to manage our event from planning to completion.'],
            ]; ?>
            <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-4">
                    <div class="card-panel p-4 h-100">
                        <div class="mb-3 text-warning fs-5">★★★★★</div>
                        <p class="text-muted"><?php echo htmlspecialchars($testimonial['text'], ENT_QUOTES, 'UTF-8'); ?></p>
                        <strong><?php echo htmlspecialchars($testimonial['name'], ENT_QUOTES, 'UTF-8'); ?></strong>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<?php include __DIR__ . '/includes/footer.php'; ?>
