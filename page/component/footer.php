<footer class="mt-auto py-5" style="
    background: var(--background);
    border-top: 1px solid #e2e8f0;
">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-center text-md-start">
                <span class="fw-bold" style="background: linear-gradient(135deg, var(--primary), #a855f7); -webkit-background-clip: text; -webkit-text-fill-color: transparent;">ElevateSafe</span>
                <span class="text-muted ms-2">&copy; <span id="current-year"></span> All Rights Reserved.</span>
            </div>
            <div class="col-md-6 text-center text-md-end mt-3 mt-md-0">
                <div class="d-flex justify-content-center justify-content-md-end gap-4 text-muted small">
                    <span class="d-flex align-items-center gap-1"><i class="fas fa-shield-alt text-success opacity-50"></i> Secure Connection</span>
                    <span class="d-flex align-items-center gap-1"><i class="fas fa-bolt text-warning opacity-50"></i> High Performance</span>
                </div>
            </div>
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('current-year').textContent = new Date().getFullYear();

    // Bootstrap form validation
    (function() {
        'use strict'
        const forms = document.querySelectorAll('.needs-validation')
        Array.from(forms).forEach(form => {
            form.addEventListener('submit', event => {
                if (!form.checkValidity()) {
                    event.preventDefault()
                    event.stopPropagation()
                }
                form.classList.add('was-validated')
            }, false)
        })
    })()
</script>
</body>
</html>