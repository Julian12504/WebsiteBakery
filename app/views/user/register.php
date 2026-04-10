<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký - Sweet Home</title>
 <link rel="stylesheet" href="css/login.css"> <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
.address-fields {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 10px;
}
.address-fields input {
    grid-column: 1 / -1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}
.address-fields select {
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 8px;
    font-size: 14px;
}
</style>
</head>
<body class="login-body">

    <div class="login-container">
        <div class="login-box">
            <h2>GIA NHẬP NHÀ NGỌT</h2>
            <p>Tạo tài khoản để nhận nhiều ưu đãi hấp dẫn.</p>
            
            <?php if(isset($error)): ?>
                <div style="background: #fff5f5; color: #e53e3e; padding: 10px; border-radius: 8px; margin-bottom: 15px; font-size: 14px; border: 1px solid #feb2b2;">
                    <i class="fa-solid fa-circle-exclamation"></i> <?php echo $error; ?>
                </div>
            <?php endif; ?>
            
            <form action="index.php?url=register" method="POST" onsubmit="return validateForm()">
                <div class="input-group">
                    <label>Họ và tên:</label>
                    <input type="text" name="fullname" placeholder="Nguyễn Văn A" 
                           value="<?php echo isset($_POST['fullname']) ? htmlspecialchars($_POST['fullname']) : ''; ?>" required>
                </div>

                <div class="input-group">
                    <label>Email:</label>
                    <input type="email" name="email" placeholder="example@email.com" 
                           pattern="^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$"
                           value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>" 
                           required title="Email không hợp lệ (ví dụ: user@example.com)">
                </div>

                <div class="input-group">
                    <label>Số điện thoại:</label>
                    <input type="text" name="phone" placeholder="090xxxxxxx" 
                           pattern="\d{10}"
                           maxlength="10"
                           value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>" 
                           required title="Số điện thoại phải đủ 10 chữ số">
                </div>
                <div class="input-group">
    <label>Tên đăng nhập:</label>
    <input type="text" name="username" placeholder="Ví dụ: khachhang123" required>
</div>
                <div class="input-group">
                    <label>Mật khẩu:</label>
                    <input type="password" name="password" placeholder="********" required>
                </div>

                <div class="input-group">
                    <label>Nhập lại mật khẩu:</label>
                    <input type="password" name="confirm_password" placeholder="********" required>
                </div>

                <div class="input-group">
                    <label>Địa chỉ giao hàng mặc định:</label>
                    <div class="address-fields">
                        <select name="province" id="province" required>
                            <option value="">Chọn Tỉnh/Thành phố</option>
                        </select>
                        <select name="ward" id="ward" required disabled>
                            <option value="">Chọn Phường/Xã</option>
                        </select>
                        <input type="text" name="address_detail" id="address_detail" placeholder="Số nhà, đường, thôn..." required>
                        <input type="hidden" name="address_default" id="address_default_hidden">
                    </div>
                </div>
                
                <button type="submit" class="btn-login">Đăng Ký</button>
            </form>
            
            <div class="login-footer">
                <p>Đã có tài khoản? <a href="index.php?url=login">Đăng Nhập Ngay</a></p>
                <a href="index.php?url=home" class="back-home">← Quay về Trang Chủ</a>
            </div>
        </div>
    </div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const provinceSelect = document.getElementById('province');
    const wardSelect = document.getElementById('ward');

    let addressData = {};

    // Fetch address data
    fetch('http://tinhthanhpho.com/api/v1/new-provinces')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                addressData = data.data;
                populateProvinces();
            } else {
                console.error('API error:', data);
                alert('Không thể tải dữ liệu địa chỉ.');
            }
        })
        .catch(error => {
            console.error('Error fetching provinces:', error);
            alert('Không thể tải dữ liệu địa chỉ. Vui lòng thử lại.');
        });

    function populateProvinces() {
        provinceSelect.innerHTML = '<option value="">Chọn Tỉnh/Thành phố</option>';
        addressData.forEach(province => {
            const option = document.createElement('option');
            option.value = province.code;
            option.textContent = province.name;
            provinceSelect.appendChild(option);
        });
    }

    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';

        if (provinceId) {
            fetch(`http://tinhthanhpho.com/api/v1/new-provinces/${provinceId}/wards`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        wardSelect.disabled = false;
                        data.data.forEach(ward => {
                            const option = document.createElement('option');
                            option.value = ward.code;
                            option.textContent = ward.name;
                            wardSelect.appendChild(option);
                        });
                    } else {
                        console.error('API error:', data);
                        wardSelect.disabled = true;
                    }
                })
                .catch(error => {
                    console.error('Error fetching wards:', error);
                    wardSelect.disabled = true;
                });
        } else {
            wardSelect.disabled = true;
        }
        updateAddress();
    });

    function updateAddress() {
        const provinceText = provinceSelect.options[provinceSelect.selectedIndex]?.text || '';
        const wardText = wardSelect.options[wardSelect.selectedIndex]?.text || '';
        const detail = document.getElementById('address_detail').value;
        const fullAddress = `${detail}, ${wardText}, ${provinceText}`.replace(/^, |, $/, '');
        document.getElementById('address_default_hidden').value = fullAddress;
    }

    provinceSelect.addEventListener('change', function() {
        const provinceId = this.value;
        districtSelect.innerHTML = '<option value="">Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option value="">Chọn Phường/Xã</option>';
        wardSelect.disabled = true;

        if (provinceId) {
            const province = addressData.find(p => p.code == provinceId);
            if (province && province.districts) {
                districtSelect.disabled = false;
                province.districts.forEach(district => {
                    const option = document.createElement('option');
                    option.value = district.code;
                    option.textContent = district.name;
                    districtSelect.appendChild(option);
                });
            } else {
                districtSelect.disabled = true;
            }
        } else {
            districtSelect.disabled = true;
        }
        updateAddress();
    });

    wardSelect.addEventListener('change', updateAddress);
    document.getElementById('address_detail').addEventListener('input', updateAddress);
});

// Validation function
function validateForm() {
    const email = document.querySelector('input[name="email"]').value.trim();
    const phone = document.querySelector('input[name="phone"]').value.trim();
    
    // Regex pattern cho email
    const emailRegex = /^[a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;
    
    // Regex pattern cho số điện thoại (10 số)
    const phoneRegex = /^\d{10}$/;
    
    // Kiểm tra email
    if (!emailRegex.test(email)) {
        alert('❌ Email không hợp lệ!\nVí dụ: user@example.com');
        document.querySelector('input[name="email"]').focus();
        return false;
    }
    
    // Kiểm tra số điện thoại
    if (!phoneRegex.test(phone)) {
        alert('❌ Số điện thoại phải đúng 10 chữ số!\nVí dụ: 0901234567');
        document.querySelector('input[name="phone"]').focus();
        return false;
    }
    
    return true;
}

</script>

</body>
</html>