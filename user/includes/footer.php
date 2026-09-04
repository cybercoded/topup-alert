    </div><!-- .content-area -->
</div><!-- .main-content -->
<script src="../assets/js/main.js"></script>
<script>
// Amount chip selector
document.querySelectorAll('.amount-chip').forEach(chip => {
    chip.addEventListener('click', function() {
        const target = this.dataset.target;
        const value = this.dataset.value;
        if (target) {
            const input = document.querySelector(target);
            if (input) input.value = value;
        }
        this.parentElement.querySelectorAll('.amount-chip').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
    });
});

// Network button selector
document.querySelectorAll('.network-btn').forEach(btn => {
    btn.addEventListener('click', function() {
        const input = this.querySelector('input');
        if (input) input.checked = true;
        this.parentElement.querySelectorAll('.network-btn').forEach(b => b.classList.remove('active'));
        this.classList.add('active');
    });
});
</script>
</body>
</html>
