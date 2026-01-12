<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán MoMo</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f4f6f8; font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; }
        .momo-header { background-color: #a50064; color: white; padding: 15px; }
        .payment-box { background: white; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.1); overflow: hidden; }
        .qr-frame { border: 2px dashed #a50064; padding: 10px; border-radius: 8px; display: inline-block; }
        .loader { border: 4px solid #f3f3f3; border-top: 4px solid #a50064; border-radius: 50%; width: 30px; height: 30px; animation: spin 1s linear infinite; display: inline-block; vertical-align: middle; }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>
</head>
<body>
    <div class="momo-header d-flex justify-content-between align-items-center">
        <div class="container d-flex align-items-center">
            <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" height="35" class="bg-white rounded p-1 me-3">
            <h5 class="m-0 fw-normal">Cổng thanh toán an toàn</h5>
        </div>
    </div>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-md-5">
                <div class="payment-box text-center p-4">
                    <h5 class="mb-3">Quét mã để thanh toán</h5>
                    <div class="text-muted mb-4">Đơn hàng: <strong>#<?php echo time(); ?></strong></div>
                    
                    <div class="mb-4 position-relative">
                        <div class="qr-frame">
                            <img src="https://api.qrserver.com/v1/create-qr-code/?size=200x200&data=MoMo_Fake_Payment_Success" alt="QR Code" class="img-fluid">
                        </div>
                    </div>

                    <div class="alert alert-warning border-0 small">
                        <div id="status-text">
                            <div class="loader me-2"></div> Đang chờ quét mã...
                        </div>
                    </div>

                    <p class="text-muted small mt-3">Sử dụng App <strong>MoMo</strong> hoặc ứng dụng Camera hỗ trợ QR Code để quét mã.</p>
                    
                    <button id="btn-success" class="btn btn-outline-secondary btn-sm mt-3 w-100">
                        (Mô phỏng: Click để xác nhận đã quét)
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Tự động chuyển trang sau 5 giây hoặc khi bấm nút
        setTimeout(finishPayment, 5000); // 5 giây tự động xong

        document.getElementById('btn-success').addEventListener('click', finishPayment);

        function finishPayment() {
            document.getElementById('status-text').innerHTML = '<span class="text-success fw-bold">✔ Thanh toán thành công! Đang chuyển hướng...</span>';
            // Chuyển hướng về hàm xử lý thành công của bạn
            setTimeout(() => {
                window.location.href = "/Website-PhanBon/Product/momoReturn?resultCode=0&message=Success";
            }, 1000);
        }
    </script>
</body>
</html>