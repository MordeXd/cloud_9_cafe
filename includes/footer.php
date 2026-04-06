</main>
<footer class="cafe-footer py-4 mt-5">
    <div class="container text-center">
        <p class="mb-1 fw-semibold">Cloud 9 Cafe</p>
        <p class="mb-0 small text-muted">Simple PHP cafe website project with Bootstrap and global CSS.</p>
    </div>
</footer>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<?php
// Cache-bust validate.js so the latest client-side rules load everywhere.
$validateVersion = @filemtime(__DIR__ . '/../assets/js/validate.js') ?: time();
?>
<script src="/cloud_9_cafe/assets/js/validate.js?v=<?= $validateVersion ?>"></script>
</body>
</html>
