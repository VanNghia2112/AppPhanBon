<style>
    body {
        background: linear-gradient(135deg, #e8f5e9, #f1f8e9);
        font-family: "Segoe UI", sans-serif;
        margin: 0;
        padding: 40px 0;
    }

    .container-detect {
        width: 60%;
        margin: auto;
        background: #ffffff;
        padding: 35px;
        border-radius: 25px;
        box-shadow: 0px 12px 32px rgba(0, 0, 0, 0.12);
        animation: fadeIn 0.6s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    h2 {
        text-align: center;
        color: #2e7d32;
        font-size: 30px;
        font-weight: 700;
        margin-bottom: 25px;
    }

    .input-label {
        font-weight: 600;
        font-size: 16px;
        color: #2e7d32;
    }

    .preview-img {
        width: 100%;
        max-height: 420px;
        object-fit: cover;
        border-radius: 18px;
        margin-top: 18px;
        box-shadow: 0 6px 16px rgba(0,0,0,0.15);
        display: none;
    }

    .btn-detect {
        width: 100%;
        background: linear-gradient(90deg, #43a047, #2e7d32);
        color: white;
        font-size: 18px;
        padding: 12px;
        margin-top: 20px;
        border: none;
        border-radius: 12px;
        cursor: pointer;
        transition: 0.25s;
        font-weight: 600;
    }

    .btn-detect:hover {
        background: linear-gradient(90deg, #2e7d32, #1b5e20);
        transform: translateY(-2px);
        box-shadow: 0 6px 18px rgba(46,125,50,0.4);
    }

    .result-box {
        margin-top: 30px;
        padding: 25px;
        border-radius: 18px;
        background: #f5fbe8;
        border-left: 6px solid #8bc34a;
        animation: fadeIn 0.4s ease-in-out;
    }

    .disease-name {
        font-size: 26px;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 10px;
    }

    .badge {
        display: inline-block;
        padding: 6px 14px;
        border-radius: 20px;
        font-weight: 600;
        color: white;
        margin-bottom: 12px;
        font-size: 14px;
    }

    .badge-high { background: #d32f2f; }
    .badge-mid  { background: #f9a825; }
    .badge-low  { background: #388e3c; }

    .suggest-box {
        margin-top: 22px;
        background: #ffffff;
        padding: 20px;
        border-radius: 18px;
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .suggest-title {
        font-size: 20px;
        font-weight: 700;
        color: #2e7d32;
        margin-bottom: 10px;
    }

    .suggest-item {
        background: #e8f5e9;
        padding: 10px 14px;
        border-radius: 12px;
        margin-bottom: 8px;
        font-weight: 600;
        display: flex;
        justify-content: space-between;
    }

    /* Nút quay về */
    .btn-back {
        margin-left: 80px;
        padding: 8px 18px;
        background: #c8e6c9;
        border-radius: 10px;
        text-decoration: none;
        color: #2e7d32;
        font-weight: 600;
        transition: 0.2s;
    }

    .btn-back:hover {
        background: #a5d6a7;
    }
</style>

<a href="/Website-PhanBon/Product/" class="btn-back">← Quay về trang chủ</a>

<div class="container-detect">
    <h2>Nhận diện bệnh lá cà phê bằng AI</h2>

    <form action="" method="POST" enctype="multipart/form-data">
        <label class="input-label">Chọn ảnh lá cà phê:</label>
        <input id="imageInput" type="file" name="image" class="form-control" required>

        <img id="previewImg" class="preview-img"
             src="<?php echo $uploadedImage ?? ''; ?>"
             style="<?php echo isset($uploadedImage) ? 'display:block;' : 'display:none;'; ?>">

        <button class="btn-detect">Nhận diện bệnh</button>
    </form>

    <?php if (isset($result)): ?>

        <?php
            $confidence = round($result["confidence"] * 100, 2);
            if ($confidence >= 85) $badge = "badge-high";
            elseif ($confidence >= 60) $badge = "badge-mid";
            else $badge = "badge-low";

            $disease = $result["class"];

            // Gợi ý sản phẩm theo bệnh 
            $suggestions = [
                "Rust"      => ["Thuốc gốc đồng / Hexaconazole", "Tăng cường phân NPK 16-8-16"],
                "Miner"     => ["Phân NPK phục hồi", "Dung dịch kích rễ", "Amino acid"],
                "Phoma"     => ["Mancozeb", "Metalaxyl", "Phân hữu cơ vi sinh"],
                "BrownSpot" => ["Copper Hydroxide", "Phân Kali cao"],
                "Healthy"   => ["Không cần điều trị", "Bón phân hữu cơ định kỳ", "NPK 20-20-15"]
            ];
        ?>
        <?php
        $treatment = [
            "Rust"      => "Phun thuốc gốc đồng hoặc Hexaconazole. Tăng cường phân NPK 16-8-16.",
            "Miner"     => "Phun Abamectin, loại bỏ lá sâu, vệ sinh vườn thường xuyên.",
            "Phoma"     => "Dùng Mancozeb hoặc Metalaxyl. Cải thiện thông thoáng đất.",
            "BrownSpot" => "Phun Copper Hydroxide và bổ sung Kali để tăng sức đề kháng.",
            "Healthy"   => "Không cần điều trị. Chỉ cần chăm sóc và bón phân định kỳ."
        ];
        ?>

        <div class="result-box">
            <span class="badge <?php echo $badge; ?>">
                Độ tin cậy: <?php echo $confidence; ?>%
            </span>

            <div class="disease-name">
                <?php echo $disease === "Healthy" ? "Lá khỏe mạnh" : $disease; ?>
            </div>

            <p style="font-size:16px; margin-bottom:15px;">
                <?php
                    $desc = [
                        "Rust"      => "Bệnh gỉ sắt khiến lá vàng, rụng, giảm năng suất nghiêm trọng.",
                        "Miner"     => "Lá bị sâu đục lỗ, xuất hiện đường ngoằn ngoèo do sâu vẽ bùa.",
                        "Phoma"     => "Đốm bệnh lan nhanh trên lá, cháy mép lá và làm rụng cành.",
                        "BrownSpot" => "Xuất hiện đốm nâu lan rộng, gây khô cháy mô lá.",
                        "Healthy"   => "Lá khỏe mạnh, không phát hiện dấu hiệu bệnh."
                    ];
                    echo $desc[$disease];
                ?>
            </p>

            <div class="suggest-box">
                <div class="suggest-title">Gợi ý xử lý & phân bón phù hợp:</div>
                <p><strong>Hướng xử lý:</strong> <?= $treatment[$disease] ?></p>

                <?php foreach ($suggestions[$disease] as $item): ?>
                    <div class="suggest-item">
                        <span><?php echo $item; ?></span>
                        <span>→ ✔</span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    <?php endif; ?>
</div>

<script>
document.getElementById("imageInput").addEventListener("change", function(){
    const img = document.getElementById("previewImg");
    img.style.display = "block";
    img.src = URL.createObjectURL(this.files[0]);
});
</script>
