<div class="main-content">
    <div class="breadcrumb">Trang chủ > Sản phẩm > Chỉnh sửa</div>
    <div class="content-padding" style="background: #fff; margin: 20px; padding: 20px; border: 1px solid #ddd;">
        <h3>Chỉnh sửa sản phẩm: <?= $product['name'] ?></h3>
        
        <form action="admin.php?url=update_product" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id" value="<?= $product['id'] ?>">
            <input type="hidden" name="current_image" value="<?= $product['image'] ?>">

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                <div>
                    <label>Tên sản phẩm:</label><br>
                    <input type="text" name="name" value="<?= $product['name'] ?>" style="width:100%; padding:8px; margin: 10px 0;">
                    
                    <label>Giá vốn (VNĐ):</label><br>
                    <input type="number" id="gia_von" name="gia_von" value="<?= $product['gia_von'] ?>" style="width:100%; padding:8px; margin: 10px 0;">
                    
                    <label>Lợi nhuận mong muốn (%):</label><br>
                    <input type="number" id="loi_nhuan" name="loi_nhuan" value="<?= $product['loi_nhuan'] ?>" style="width:100%; padding:8px; margin: 10px 0;">
                    
                    <p><strong>Giá bán dự kiến: <span id="gia_ban_preview" style="color: #e74c3c;">0</span>đ</strong></p>
                </div>

                <div>
                    <label>Hình ảnh hiện tại:</label><br>
                    <img src="public/images/<?= $product['image'] ?>" width="100" style="margin: 10px 0; border: 1px solid #ddd;"><br>
                    <label>Thay ảnh mới (để trống nếu giữ nguyên):</label>
                    <input type="file" name="image" style="margin: 10px 0;">
                    
                    <br><label>Hiện trạng:</label><br>
                    <select name="status" style="width:100%; padding:8px; margin: 10px 0;">
                        <option value="1" <?= $product['status']==1?'selected':'' ?>>Đang bán (Hiển thị)</option>
                        <option value="0" <?= $product['status']==0?'selected':'' ?>>Ngừng bán (Ẩn)</option>
                    </select>
                </div>
            </div>

            <button type="submit" style="background: #3498db; color:white; border:none; padding:10px 20px; border-radius:4px; cursor:pointer; margin-top:20px;">
                Lưu thay đổi
            </button>
        </form>
    </div>
</div>

<script>
// Script tự động tính giá bán khi Admin nhập giá vốn hoặc % lợi nhuận
const giaVon = document.getElementById('gia_von');
const loiNhuan = document.getElementById('loi_nhuan');
const giaBanPreview = document.getElementById('gia_ban_preview');

function tinhGia() {
    let gv = parseFloat(giaVon.value) || 0;
    let ln = parseFloat(loiNhuan.value) || 0;
    let gb = gv + (gv * ln / 100);
    giaBanPreview.innerText = new Intl.NumberFormat().format(gb);
}

giaVon.addEventListener('input', tinhGia);
loiNhuan.addEventListener('input', tinhGia);
window.onload = tinhGia;
</script>