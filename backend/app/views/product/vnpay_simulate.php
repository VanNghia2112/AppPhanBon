<?php
// Xử lý logic: Nếu chưa có biến $totalAmount từ Controller thì set mặc định để test giao diện
if (!isset($totalAmount)) {
    // Ưu tiên lấy từ URL (ví dụ: ?amount=500000), nếu không có thì mặc định 150.000
    $totalAmount = isset($_GET['amount']) ? $_GET['amount'] : 150000;
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng thanh toán VNPAY-QR</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f0f0f0; font-family: Arial, sans-serif; }
        .vnpay-header { background: white; border-bottom: 1px solid #ddd; padding: 15px 0; }
        .payment-container { max-width: 800px; margin: 30px auto; background: white; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); overflow: hidden; }
        .left-panel { background: #005baa; color: white; padding: 30px; }
        .right-panel { padding: 30px; }
        .bank-logo { 
            width: 80px; 
            height: 50px; 
            object-fit: contain; 
            border: 1px solid #eee; 
            border-radius: 4px; 
            padding: 5px; 
            cursor: pointer; 
            transition: all 0.2s; 
            background: white; /* Thêm nền trắng cho logo đẹp hơn */
        }
        .bank-logo:hover { border-color: #005baa; box-shadow: 0 0 5px rgba(0,91,170,0.3); transform: translateY(-2px); }
        .selected-bank { border-color: #005baa !important; border-width: 2px !important; background-color: #f0f8ff; }
    </style>
</head>
<body>
    <div class="vnpay-header">
        <div class="container">
            <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418196384.png" height="30" alt="VNPAY Logo">
        </div>
    </div>

    <div class="container">
        <div class="payment-container row g-0">
            <div class="col-md-5 left-panel">
                <h5 class="mb-4">Thông tin đơn hàng</h5>
                <p class="mb-1 opacity-75">Mã đơn hàng:</p>
                <h6 class="mb-3">#<?php echo time(); ?></h6>
                
                <p class="mb-1 opacity-75">Nội dung thanh toán:</p>
                <h6 class="mb-3">Thanh toan don hang Phan Bon</h6>
                
            </div>

            <div class="col-md-7 right-panel">
                <h5 class="mb-4 text-primary">Chọn phương thức thanh toán</h5>
                
                <div class="mb-3">
                    <label class="form-label fw-bold mb-3">Thẻ nội địa & Tài khoản ngân hàng</label>
                    <div class="d-flex flex-wrap gap-2">
                        <img src="https://upload.wikimedia.org/wikipedia/commons/2/25/Logo_MB_new.png" 
                             class="bank-logo" onclick="selectBank(this)" title="MB Bank">
                        
                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/02/Icon-Vietcombank.png" 
                             class="bank-logo" onclick="selectBank(this)" title="Vietcombank">
                        
                        
                        <img src="https://cdn.haitrieu.com/wp-content/uploads/2022/01/Logo-Agribank-V.png" 
                             class="bank-logo" onclick="selectBank(this)" title="Agribank">
                    </div>
                </div>

                

                <button id="btn-pay" class="btn btn-primary w-100 py-2 mt-2" disabled onclick="processPayment()">
                    Xác nhận thanh toán
                </button>
            </div>
        </div>
    </div>

    <script>
        function selectBank(img) {
            // Reset style tất cả logo
            document.querySelectorAll('.bank-logo').forEach(el => {
                el.classList.remove('selected-bank');
                el.style.borderColor = '#eee';
                el.style.borderWidth = '1px';
            });
            
            // Highlight logo được chọn
            img.classList.add('selected-bank');
            
            // Mở nút thanh toán
            document.getElementById('btn-pay').disabled = false;
        }

        function processPayment() {
            const btn = document.getElementById('btn-pay');
            const originalText = btn.innerHTML;
            
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang xử lý...';
            btn.disabled = true;

            // Giả lập độ trễ mạng 2 giây rồi chuyển hướng
            setTimeout(() => {
                // Chuyển hướng về controller xử lý kết quả
                // Bạn có thể thêm &amount=... vào URL nếu controller cần
                window.location.href = "/Website-PhanBon/Product/vnpayReturn?vnp_ResponseCode=00&vnp_TransactionStatus=00";
            }, 1500);
        }
    </script>
</body>
</html>