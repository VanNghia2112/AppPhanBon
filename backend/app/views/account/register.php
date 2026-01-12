<?php include 'app/views/shares/header.php'; ?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6 col-md-8">
            <div class="card shadow-lg border-0 rounded-4">
                <div class="card-header bg-primary text-white text-center py-3 rounded-top-4">
                    <h3 class="mb-0 fw-bold">Tạo tài khoản mới</h3>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($errors) && !empty($errors)): ?>
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                <?php foreach ($errors as $err): ?>
                                    <li><?= htmlspecialchars($err) ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php endif; ?>

                    <form action="/Website-PhanBon/account/save" method="post" class="needs-validation" novalidate>
                        
                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="username" class="form-label fw-semibold">Tên đăng nhập</label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="username" name="username" placeholder="Nhập username" required>
                                <div class="invalid-feedback">Vui lòng nhập tên đăng nhập.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="fullname" class="form-label fw-semibold">Họ và tên</label>
                                <input type="text" class="form-control form-control-lg rounded-3" id="fullname" name="fullname" placeholder="Nhập họ tên đầy đủ" required>
                                <div class="invalid-feedback">Vui lòng nhập họ và tên.</div>
                            </div>
                        </div>

                        <div class="row g-3 mb-4">
                            <div class="col-md-6">
                                <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                                <input type="password" class="form-control form-control-lg rounded-3" id="password" name="password" placeholder="Nhập mật khẩu" required>
                                <div class="invalid-feedback">Vui lòng nhập mật khẩu.</div>
                            </div>
                            <div class="col-md-6">
                                <label for="confirmpassword" class="form-label fw-semibold">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control form-control-lg rounded-3" id="confirmpassword" name="confirmpassword" placeholder="Nhập lại mật khẩu" required>
                                <div class="invalid-feedback">Vui lòng xác nhận mật khẩu.</div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg rounded-3 fw-bold shadow-sm">
                                <i class="bi bi-person-plus me-2"></i> Đăng ký
                            </button>
                        </div>
                    </form>
                </div>
                <div class="card-footer text-center text-muted py-3">
                    <small>Đã có tài khoản? <a href="/Website-PhanBon/account/login" class="text-primary fw-semibold">Đăng nhập</a></small>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
(() => {
  'use strict';
  const forms = document.querySelectorAll('.needs-validation');
  Array.from(forms).forEach(form => {
    form.addEventListener('submit', event => {
      if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
      }
      form.classList.add('was-validated');
    }, false);
  });
})();
</script>

<?php include 'app/views/shares/footer.php'; ?>
