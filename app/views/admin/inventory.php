<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Tồn kho - Administrator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <link rel="stylesheet" href="../public/css/css_admin/style.css">
    <style>
        body { background-color: #f5f7fa; }
        .main-content { padding: 30px; }
        .top-nav { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; padding-bottom: 15px; border-bottom: 1px solid #ddd; }
        .select2-container--default .select2-selection--single { height: 42px; border-radius: 8px; }
        .select2-container--default .select2-selection--single .select2-selection__rendered { line-height: 42px; color: #333; }
        .list-group-item { border: none; border-bottom: 1px solid #f0f0f0; padding: 12px 0; }
        .list-group-item:last-child { border-bottom: none; }
        .alert-info { background-color: #e8f4f8; border-color: #b3d9e8; color: #004d63; }
    </style>
</head>
<body>

  <div class="sidebar">
        <div class="sidebar-header">
            <i class="fa-solid fa-user-shield"></i>
            <div style="display:inline-block; vertical-align:middle; margin-left:10px; line-height:1.2;">
                <div style="font-weight:700;">Administrator</div>
                <div style="font-size:0.95rem; opacity:0.85;">
                    <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?><br>
                    <span style="font-size:0.85rem; opacity:0.7;"><?= htmlspecialchars($_SESSION['admin_username'] ?? '') ?></span>
                </div>
            </div>
        </div>
    <a href="admin.php?url=dashboard" class="menu-item"><i class="fa-solid fa-house"></i> Trang chủ Admin</a>

    <div class="menu-item" onclick="toggleProductMenu()" style="cursor: pointer;">
        <i class="fa-solid fa-cake-candles"></i> 
        Quản lý sản phẩm 
        <i class="fa-solid fa-chevron-down" id="arrow-icon" style="margin-left:auto; font-size: 10px; transition: 0.3s;"></i>
    </div>
    
    <div class="sub-menu" id="product-submenu">
        <a href="admin.php?url=categories" class="menu-item" style="padding-left: 40px; font-size: 13px;">
            <i class="fa-solid fa-list"></i> Danh mục
        </a>
        <a href="admin.php?url=products" class="menu-item" style="padding-left: 40px; font-size: 13px;">
            <i class="fa-solid fa-box"></i> Tất cả sản phẩm
        </a>
        <a href="admin.php?url=price_management" class="menu-item" style="padding-left: 40px; font-size: 13px;">
            <i class="fa-solid fa-tags"></i> Quản lý giá bán
        </a>
    </div>

    <a href="admin.php?url=orders" class="menu-item"><i class="fa-solid fa-cart-shopping"></i> Đơn hàng</a>
    <a href="admin.php?url=users" class="menu-item"><i class="fa-solid fa-users"></i> Quản lý người dùng</a>
    <a href="admin.php?url=import_product" class="menu-item">
    <i class="fa-solid fa-truck-ramp-box"></i> Quản lý nhập hàng
</a>
    <a href="admin.php?url=inventory" class="menu-item active"><i class="fa-solid fa-boxes-stacked"></i> Tồn kho / Báo cáo</a>
    <a href="admin_logout.php" class="menu-item" style="color: #e74c3c;"><i class="fa-solid fa-right-from-bracket"></i> Đăng xuất</a>
  </div>

  <div class="main-content">
    <div class="top-nav">
      <h2 style="margin: 0; font-weight: 600; color: #333;">Quản lý Tồn Kho</h2>
      <div><a href="index.php"><i class="fa-solid fa-globe"></i> Xem trang chủ</a></div>
    </div>

    <div class="row g-4">
      <!-- Left Panel: Tra cứu -->
      <div class="col-xl-5">
        <!-- Tra cứu tại thời điểm -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
          <h4 class="mb-3"><i class="fa-solid fa-clock"></i> Tra cứu tồn kho tại thời điểm</h4>
          <form method="get">
            <input type="hidden" name="url" value="inventory">
            
            <div class="mb-3">
              <label class="form-label">Sản phẩm</label>
              <select name="product_id" class="form-select select2-search">
                <option value="0">Chọn sản phẩm</option>
                <?php foreach ($allProducts ?? [] as $product): ?>
                  <option value="<?= (int) $product['id'] ?>" <?= ($productId ?? 0) === (int) $product['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($product['name'] ?? '') ?> (Tồn: <?= (int) ($product['stock'] ?? 0) ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="mb-3">
              <label class="form-label">Thời điểm cần tra cứu</label>
              <input type="datetime-local" name="at_datetime" class="form-control" value="<?= htmlspecialchars($atDatetime ?? '') ?>">
            </div>
            
            <button class="btn btn-primary w-100" type="submit">Tra cứu tồn kho</button>
          </form>
        </div>

        <!-- Báo cáo nhập-xuất -->
        <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
          <h4 class="mb-3"><i class="fa-solid fa-chart-bar"></i> Báo cáo nhập - xuất</h4>
          <form method="get">
            <input type="hidden" name="url" value="inventory">
            
            <div class="mb-3">
              <label class="form-label">Sản phẩm</label>
              <select name="product_id" class="form-select select2-search" required>
                <option value="0">Chọn sản phẩm</option>
                <?php foreach ($allProducts ?? [] as $product): ?>
                  <option value="<?= (int) $product['id'] ?>" <?= ($productId ?? 0) === (int) $product['id'] && ($fromDate ?? '') !== '' ? 'selected' : '' ?>>
                    <?= htmlspecialchars($product['name'] ?? '') ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            
            <div class="row g-2 mb-3">
              <div class="col-6">
                <label class="form-label">Từ ngày</label>
                <input type="date" name="from_date" class="form-control" value="<?= htmlspecialchars($fromDate ?? '') ?>" required>
              </div>
              <div class="col-6">
                <label class="form-label">Đến ngày</label>
                <input type="date" name="to_date" class="form-control" value="<?= htmlspecialchars($toDate ?? '') ?>" required>
              </div>
            </div>
            
            <button class="btn btn-success w-100" type="submit">Xem báo cáo</button>
          </form>
        </div>

        <!-- Cảnh báo sắp hết hàng -->
        <div class="bg-white rounded-4 shadow-sm p-4">
          <h5 class="mb-3"><i class="fa-solid fa-triangle-exclamation" style="color: #ff9800;"></i> Cảnh báo sắp hết hàng</h5>
          
          <form method="get" class="row g-3 mb-3">
            <input type="hidden" name="url" value="inventory">
            <!-- <div class="col-md-8">
              <label class="form-label">Ngưỡng cảnh báo</label>
              <input type="number" name="threshold" class="form-control" min="0" value="<?= (int) ($threshold ?? 5) ?>">
            </div> -->
            <!-- <div class="col-md-4 d-flex align-items-end">
              <button class="btn btn-outline-warning w-100" type="submit">Áp dụng</button>
            </div> -->
          </form>

          <ul class="list-group list-group-flush">
            <?php if (!empty($lowStockProducts)): ?>
              <?php foreach ($lowStockProducts as $product): ?>
                <li class="list-group-item px-0 d-flex justify-content-between align-items-center">
                  <span><?= htmlspecialchars($product['name'] ?? '') ?></span>
                  <strong style="color: <?= (int)($product['stock'] ?? 0) <= 0 ? '#d32f2f' : '#ff9800' ?>;">
                    <?= (int) ($product['stock'] ?? 0) ?>
                  </strong>
                </li>
              <?php endforeach; ?>
            <?php else: ?>
              <li class="list-group-item px-0 text-muted">Không có sản phẩm nào dưới ngưỡng cảnh báo.</li>
            <?php endif; ?>
          </ul>
        </div>
      </div>

      <!-- Right Panel: Bảng tồn kho / báo cáo -->
      <div class="col-xl-7">
        <!-- Báo cáo nhập-xuất -->
        <?php if (!empty($reportData)): ?>
          <div class="bg-white rounded-4 shadow-sm p-4 mb-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
              <h4 class="mb-0">
                <i class="fa-solid fa-file-chart-line"></i> 
                Báo cáo nhập - xuất
              </h4>
              <small class="text-muted">
                <?= date('d/m/Y', strtotime($reportData['from_date'])) ?> - <?= date('d/m/Y', strtotime($reportData['to_date'])) ?>
              </small>
            </div>

            <div class="alert alert-info mb-4">
              <strong><?= htmlspecialchars($reportData['product_name']) ?></strong>
            </div>

            <div class="table-responsive mb-4">
              <table class="table table-sm mb-0">
                <thead class="table-light">
                  <tr>
                    <th>Chỉ tiêu</th>
                    <th class="text-end">Số lượng</th>
                    <th class="text-end">Giá trị (đ)</th>
                  </tr>
                </thead>
                <tbody>
                  <tr style="background-color: #cee7ff;">
                    <td><strong>Nhập</strong></td>
                    <td class="text-end"><strong><?= number_format($reportData['import_qty'], 0, ',', '.') ?></strong></td>
                    <td class="text-end"><strong><?= number_format($reportData['import_value'], 0, ',', '.') ?></strong></td>
                  </tr>
                  <tr style="background-color: #fef5de;">
                    <td><strong>Xuất</strong></td>
                    <td class="text-end"><strong><?= number_format($reportData['export_qty'], 0, ',', '.') ?></strong></td>
                    <td class="text-end"><strong><?= number_format($reportData['export_value'], 0, ',', '.') ?></strong></td>
                  </tr>
                  <tr style="background-color: #dff0d8; border-top: 2px solid #5cb85c;">
                    <td><strong>Lối lọc ròng</strong></td>
                    <td class="text-end">
                      <strong style="color: <?= $reportData['net_change'] >= 0 ? '#5cb85c' : '#d9534f' ?>;">
                        <?= $reportData['net_change'] >= 0 ? '+' : '' ?><?= number_format($reportData['net_change'], 0, ',', '.') ?>
                      </strong>
                    </td>
                    <td class="text-end">
                      <strong style="color: <?= ($reportData['import_value'] - $reportData['export_value']) >= 0 ? '#5cb85c' : '#d9534f' ?>;">
                        <?= ($reportData['import_value'] - $reportData['export_value']) >= 0 ? '+' : '' ?><?= number_format($reportData['import_value'] - $reportData['export_value'], 0, ',', '.') ?>
                      </strong>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <div class="row g-2">
              <div class="col-6">
                <small class="text-muted d-block">Giá vốn</small>
                <strong><?= number_format($reportData['cost_price'], 0, ',', '.') ?> đ/cái</strong>
              </div>
              <div class="col-6">
                <a href="?url=inventory" class="btn btn-outline-secondary btn-sm w-100">Xóa lọc</a>
              </div>
            </div>
          </div>
        <?php endif; ?>

        <!-- Bảng tồn kho -->
        <?php if (empty($reportData)): ?>
          <div class="bg-white rounded-4 shadow-sm p-4">
            <h4 class="mb-3">
              <i class="fa-solid fa-cubes"></i> 
              <?= isset($searchedProduct) ? 'Kết quả tra cứu tồn kho' : 'Tồn kho hiện tại' ?>
            </h4>
            
          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th>STT</th>
                  <th>Sản phẩm</th>
                  <th class="text-end">Tồn kho</th>
                  <th class="text-end">Giá vốn</th>
                </tr>
              </thead>
              <tbody>
                <?php 
                $index = isset($searchedProduct) ? 1 : (($page ?? 1) > 1 ? (($page - 1) * 20) + 1 : 1);
                foreach ($products ?? [] as $product): 
                  // Nếu đang tra cứu, dùng stock tại thời điểm, ngược lại dùng stock hiện tại
                  $stock = isset($product['stock_at_time']) ? (int)$product['stock_at_time'] : (int)($product['stock'] ?? 0);
                  $cost = (float) ($product['gia_von'] ?? 0);
                  $isHistorical = isset($product['stock_at_time']);
                ?>
                  <tr>
                    <td><?= $index++ ?></td>
                    <td>
                      <?= htmlspecialchars($product['name'] ?? '') ?>
                      <?php if ($isHistorical): ?>
                        <br><small class="text-muted">Tại thời điểm: <?= htmlspecialchars($atDatetime ?? '') ?></small>
                      <?php endif; ?>
                    </td>
                    <td class="text-end">
                      <span class="badge" style="background-color: <?= $stock <= 5 ? '#d32f2f' : ($stock <= 20 ? '#ff9800' : '#4caf50') ?>;">
                        <?= $stock ?>
                      </span>
                    </td>
                    <td class="text-end"><?= number_format($cost, 0, ',', '.') ?> đ</td>
                  </tr>
                <?php endforeach; ?>
                <?php if (empty($products)): ?>
                  <tr>
                    <td colspan="4" class="text-center text-muted" style="padding: 30px;">Không có sản phẩm</td>
                  </tr>
                <?php endif; ?>
              </tbody>
            </table>
          </div>
          
          <!-- Phân trang -->
          <?php if (($totalPages ?? 0) > 1 && !isset($searchedProduct)): ?>
            <nav aria-label="Phân trang sản phẩm" class="mt-3">
              <ul class="pagination justify-content-center">
                <?php 
                $currentPage = $page ?? 1;
                $startPage = max(1, $currentPage - 2);
                $endPage = min($totalPages, $currentPage + 2);
                
                // Nút Previous
                if ($currentPage > 1): ?>
                  <li class="page-item">
                    <a class="page-link" href="?url=inventory&page=<?= $currentPage - 1 ?>&threshold=<?= $threshold ?? 5 ?>">Trước</a>
                  </li>
                <?php endif; ?>
                
                <?php for ($i = $startPage; $i <= $endPage; $i++): ?>
                  <li class="page-item <?= $i === $currentPage ? 'active' : '' ?>">
                    <a class="page-link" href="?url=inventory&page=<?= $i ?>&threshold=<?= $threshold ?? 5 ?>"><?= $i ?></a>
                  </li>
                <?php endfor; ?>
                
                // Nút Next
                <?php if ($currentPage < $totalPages): ?>
                  <li class="page-item">
                    <a class="page-link" href="?url=inventory&page=<?= $currentPage + 1 ?>&threshold=<?= $threshold ?? 5 ?>">Sau</a>
                  </li>
                <?php endif; ?>
              </ul>
            </nav>
          <?php endif; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
  <script>
    document.addEventListener('DOMContentLoaded', function() {
      $('.select2-search').select2({
        placeholder: '-- Chọn sản phẩm hoặc tìm kiếm --',
        allowClear: true,
        width: '100%'
      });
    });

    function toggleProductMenu() {
      const submenu = document.getElementById("product-submenu");
      const arrow = document.getElementById("arrow-icon");
      submenu.classList.toggle("show");
      arrow.classList.toggle("rotate");
    }

    // window.onload = function() {
    //   const urlParams = new URLSearchParams(window.location.search);
    //   const currentUrl = urlParams.get('url');

    //   if (currentUrl === 'inventory') {
    //     document.getElementById("product-submenu").classList.add("show");
    //     document.getElementById("arrow-icon").classList.add("rotate");
    //   }
    //}
  </script>
</body>
</html>