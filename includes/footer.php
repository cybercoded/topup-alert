<?php if (isset($isAdminPage) && $isAdminPage): ?>
    </div><!-- .content-area -->
</div><!-- .main-content -->
<?php endif; ?>
<script src="<?= strpos($_SERVER['PHP_SELF'] ?? '', '/admin/') !== false ? '../' : '' ?>assets/js/main.js"></script>
</body>
</html>
