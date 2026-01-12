<?php include 'app/views/shares/header.php'; ?>

<style>
    /* Hiệu ứng nảy cho icon thành công */
    @keyframes bounceIn {
        0% { opacity: 0; transform: scale(0.3); }
        50% { opacity: 1; transform: scale(1.05); }
        70% { transform: scale(0.9); }
        100% { transform: scale(1); }
    }
    .success-icon {
        animation: bounceIn 0.8s cubic-bezier(0.215, 0.610, 0.355, 1.000) both;
    }
</style>

<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card border-0 shadow-lg rounded-4 overflow-hidden">
                <div class="card-header bg-success h-10"></div>
                
                <div class="card-body p-5 text-center bg-white">
                    
                    <div class="mb-4 success-icon">
                        <svg xmlns="http://www.w3.org/2000/svg" width="100" height="100" fill="currentColor" class="bi bi-check-circle-fill text-success" viewBox="0 0 16 16">
                            <path d="M16 8A8 8 0 1 1 0 8a8 8 0 0 1 16 0zm-3.97-3.03a.75.75 0 0 0-1.08.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-.01-1.05z"/>
                        </svg>
                    </div>

                    <h1 class="fw-bold text-success mb-3">Đặt hàng thành công!</h1>
                    <p class="text-muted fs-5 mb-4">
                        Cảm ơn bạn đã tin tưởng và