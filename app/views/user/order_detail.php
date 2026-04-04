<div class="container order-detail-container" style="padding: 20px; max-width: 800px; margin: auto;">
    <h2 class="profile-title">Chi Tiết Đơn Hàng #DH<?= $order['id'] ?></h2>
    
    <div class="order-status-banner" style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 5px solid #d81b60;">
        <p>Trạng thái: <strong>
            <?php 
                if($order['status'] == 0) echo "Chờ xác nhận";
                elseif($order['status'] == 4) echo "Đã hủy";
                else echo "Đang xử lý";
            ?>
        </strong></p>
        <p>Ngày đặt: <?= date('d/m/Y H:i', strtotime($order['order_date'])) ?></p>
    </div>

    <?php if($order['status'] == 0): ?>
        <button onclick="openCancelModal()" style="background: #e74c3c; color: white; border: none; padding: 10px 20px; border-radius: 5px; cursor: pointer; margin-bottom: 20px;">
            Hủy đơn hàng này
        </button>
    <?php endif; ?>
<?php if($order['status'] == 4): ?>
    <div style="background: #fff5f5; padding: 15px; border-radius: 8px; margin-bottom: 20px; border: 1px solid #ffcccc;">
        <p style="color: #e74c3c; font-weight: bold;">Đơn hàng đã bị hủy</p>
        <p style="font-size: 14px;">Lý do: <?= $order['cancel_reason'] ?></p>
        <a href="index.php?url=reorder&id=<?= $order['id'] ?>" 
           style="display: inline-block; margin-top: 10px; background: #d81b60; color: white; padding: 10px 20px; border-radius: 5px; text-decoration: none;">
           MUA LẠI ĐƠN HÀNG NÀY
        </a>
    </div>
<?php endif; ?>
    <table style="width: 100%; border-collapse: collapse; background: #fff;">
        <tr style="border-bottom: 2px solid #eee;">
            <th style="text-align: left; padding: 10px;">Sản phẩm</th>
            <th>Số lượng</th>
            <th>Thành tiền</th>
        </tr>
        <?php foreach($order_items as $item): ?>
        <tr style="border-bottom: 1px solid #eee;">
            <td style="padding: 10px;"><?= $item['product_name'] ?></td>
            <td style="text-align: center;"><?= $item['quantity'] ?></td>
            <td style="text-align: right;"><?= number_format($item['price'] * $item['quantity'], 0, ',', '.') ?>đ</td>
        </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="2" style="text-align: right; padding: 15px; font-weight: bold;">Tổng cộng:</td>
            <td style="text-align: right; color: #d81b60; font-weight: bold; font-size: 1.2em;"><?= number_format($order['total_amount'], 0, ',', '.') ?>đ</td>
        </tr>
    </table>
</div>

<div id="cancelModal" class="modal" style="display:none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5);">
    <div style="background: white; width: 400px; margin: 100px auto; padding: 20px; border-radius: 8px;">
        <h3>Lý do hủy đơn hàng</h3>
        <form action="index.php?url=cancel_order" method="POST">
            <input type="hidden" name="order_id" value="<?= $order['id'] ?>">
            
            <div style="margin: 15px 0;">
                <input type="radio" name="reason" value="Thay đổi ý định" checked> Thay đổi ý định<br>
                <input type="radio" name="reason" value="Tìm thấy giá rẻ hơn"> Tìm thấy giá rẻ hơn<br>
                <input type="radio" name="reason" value="Thời gian giao hàng quá lâu"> Giao hàng lâu quá<br>
                <input type="radio" name="reason" value="other" id="reasonOther"> Khác (tự viết lý do)
            </div>

            <textarea id="otherText" name="other_reason" placeholder="Nhập lý do của bạn..." style="display:none; width: 100%; margin-top: 10px;" rows="3"></textarea>

            <div style="margin-top: 20px; text-align: right;">
                <button type="button" onclick="closeCancelModal()" style="padding: 8px 15px;">Đóng</button>
                <button type="submit" style="background: #e74c3c; color: white; border: none; padding: 8px 15px; border-radius: 5px;">Xác nhận hủy</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCancelModal() { document.getElementById('cancelModal').style.display = 'block'; }
function closeCancelModal() { document.getElementById('cancelModal').style.display = 'none'; }

// Hiện/ẩn textarea khi chọn "Khác"
document.getElementsByName('reason').forEach(radio => {
    radio.addEventListener('change', function() {
        document.getElementById('otherText').style.display = (this.value === 'other') ? 'block' : 'none';
    });
});
</script>