<?php if (!isset($isDashboardLayout)) {
    $isDashboardLayout = false;
} ?>
<?php if ($isDashboardLayout): ?>
    </div>
<?php endif; ?>
</main>
<?php if (!$isDashboardLayout): ?>
    <footer class="cafe-footer py-4 mt-5">
        <div class="container text-center">
            <p class="mb-1 fw-semibold">Cloud 9 Cafe</p>
            <p class="mb-0 small text-muted">Simple PHP cafe website project with Bootstrap and global CSS.</p>
        </div>
    </footer>
<?php endif; ?>
<?php
if (!isset($isAdmin)) {
    $isAdmin = false;
}
?>
<?php if ($isAdmin): ?>
    <!-- Admin Restriction Modal -->
    <div class="modal fade" id="adminRestrictionModal" tabindex="-1" aria-labelledby="adminRestrictionLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="adminRestrictionLabel">Customer Access Required</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Please login as a customer to place orders.
                </div>
                <div class="modal-footer">
                    <a href="/cloud_9_cafe/auth/logout.php" class="btn btn-outline-secondary">Logout</a>
                    <a href="/cloud_9_cafe/auth/login.php" class="btn btn-primary">Login as Customer</a>
                </div>
            </div>
        </div>
    </div>
<?php endif; ?>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php
// Cache-bust validate.js so the latest client-side rules load everywhere.
$validateVersion = @filemtime(__DIR__ . '/../assets/js/validate.js') ?: time();
?>
<script src="/cloud_9_cafe/assets/js/validate.js?v=<?= $validateVersion ?>"></script>
</body>
</html>
