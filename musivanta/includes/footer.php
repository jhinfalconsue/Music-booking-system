    </main>

    <footer class="site-footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-lg-4">
                    <div class="brand-box d-flex align-items-center gap-2 mb-3">
                        <span class="brand-mark">M</span>
                        <h5 class="mb-0">Musivanta</h5>
                    </div>
                    <p class="text-muted">Professional musician booking, management, and secure payments for memorable events.</p>
                </div>
                <div class="col-md-4 col-lg-2">
                    <h6>Company</h6>
                    <ul>
                        <li><a href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/index.php#about">About</a></li>
                        <li><a href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/index.php#how-it-works">How it works</a></li>
                        <li><a href="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/customer/browse-musicians.php">Browse</a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-lg-3">
                    <h6>Support</h6>
                    <ul>
                        <li><a href="/login.php">Login</a></li>
                        <li><a href="/register.php">Register</a></li>
                        <li><a href="/forgot-password.php">Reset Password</a></li>
                    </ul>
                </div>
                <div class="col-md-4 col-lg-3" id="contact">
                    <h6>Contact</h6>
                    <ul>
                        <li>hello@musivanta.com</li>
                        <li>+1 (555) 234-7889</li>
                        <li>88 Sound Avenue, New York</li>
                    </ul>
                </div>
            </div>
            <div class="footer-bottom">
                <p>© <?php echo date('Y'); ?> Musivanta. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="<?php echo htmlspecialchars($appBasePath, ENT_QUOTES, 'UTF-8'); ?>/assets/script.js"></script>
</body>
</html>
