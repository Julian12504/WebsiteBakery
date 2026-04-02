// ============================================================================
// ĐỀ 4 - HỆ THỐNG VẬN CHUYỂN HÀNG HÓA
// ============================================================================
const $ = (id) => document.getElementById(id);

const STUDENT_INFO = "3122411059 - Lê Thanh Hùng";

const requiredFieldIds = [
  "tenGui",
  "sdtGui",
  "cityGui",
  "districtGui",
  "addressGui",
  "tenNhan",
  "sdtNhan",
  "cityNhan",
  "districtNhan",
  "addressNhan",
];

function setError(fieldId, message) {
  const input = $(fieldId);
  const err = $(`err-${fieldId}`);

  if (!input || !err) return;

  if (message) {
    err.textContent = `* ${message}`;
    err.style.display = "block";
    input.classList.add("input-error");
  } else {
    err.textContent = "";
    err.style.display = "none";
    input.classList.remove("input-error");
  }
}

function normalizeText(value) {
  return value.replace(/\s+/g, " ").trim();
}

function validateRequired(fieldId) {
  const input = $(fieldId);
  if (!input) return false;

  const value = normalizeText(String(input.value || ""));
  if (!value) {
    setError(fieldId, "Không được để trống.");
    return false;
  }

  return true;
}

function validateFullName(fieldId) {
  const input = $(fieldId);
  if (!input) return false;

  const value = normalizeText(String(input.value || ""));
  if (!value) {
    setError(fieldId, "Không được để trống.");
    return false;
  }

  if (!value.includes(" ")) {
    setError(fieldId, "Phải có ít nhất 1 khoảng trắng.");
    return false;
  }

  const namePattern = /^[A-Za-zÀ-ỹ\s]+$/;
  if (!namePattern.test(value)) {
    setError(fieldId, "Không chứa số/ký tự lạ.");
    return false;
  }

  setError(fieldId, "");
  return true;
}

function validatePhone(fieldId) {
  const input = $(fieldId);
  if (!input) return false;

  const value = normalizeText(String(input.value || ""));
  if (!value) {
    setError(fieldId, "Không được để trống.");
    return false;
  }

  if (!/^0\d{9}$/.test(value)) {
    setError(fieldId, "SDT phải là 10 số (Bắt đầu bằng 0).");
    return false;
  }

  setError(fieldId, "");
  return true;
}

function validateAddressText(fieldId) {
  const input = $(fieldId);
  if (!input) return false;

  const value = normalizeText(String(input.value || ""));
  if (!value) {
    setError(fieldId, "Không được để trống.");
    return false;
  }

  if (!value.includes(" ")) {
    setError(fieldId, "Phải có ít nhất 1 khoảng trắng.");
    return false;
  }

  const textPattern = /^[A-Za-zÀ-ỹ0-9\s/]+$/;
  if (!textPattern.test(value)) {
    setError(fieldId, "Chỉ dùng chữ, số, khoảng trắng và ký tự /");
    return false;
  }

  setError(fieldId, "");
  return true;
}

function validateSelect(fieldId) {
  const input = $(fieldId);
  if (!input) return false;

  if (!input.value) {
    setError(fieldId, "Vui lòng chọn dữ liệu.");
    return false;
  }

  setError(fieldId, "");
  return true;
}

function validateField(fieldId) {
  switch (fieldId) {
    case "tenGui":
    case "tenNhan":
      return validateFullName(fieldId);
    case "sdtGui":
    case "sdtNhan":
      return validatePhone(fieldId);
    case "addressGui":
    case "addressNhan":
      return validateAddressText(fieldId);
    case "cityGui":
    case "districtGui":
    case "cityNhan":
    case "districtNhan":
      return validateSelect(fieldId);
    default:
      return validateRequired(fieldId);
  }
}
// --- DỮ LIỆU ĐỊA CHÍNH (Cung cấp sẵn cho sinh viên) ---
const dataAddress = {
  SGN: {
    name: "TP. Hồ Chí Minh",
    districts: [
      "Quận 1",
      "Quận 3",
      "Quận 5",
      "Quận 7",
      "Quận 10",
      "Quận Tân Bình",
      "Thủ Đức",
    ],
  },
  HAN: {
    name: "Hà Nội",
    districts: ["Ba Đình", "Đống Đa", "Cầu Giấy", "Hà Đông"],
  },
  DAD: { name: "Đà Nẵng", districts: ["Hải Châu", "Sơn Trà", "Ngũ Hành Sơn"] },
  CTO: { name: "Cần Thơ", districts: ["Ninh Kiều", "Cái Răng", "Bình Thủy"] },
};

// --- PHẦN 1: ĐỊA CHỈ & VÙNG MIỀN (CASCADING DROPDOWN) ---
function initCities() {
  const cityGui = $("cityGui");
  const cityNhan = $("cityNhan");
  if (!cityGui || !cityNhan) return;

  cityGui.innerHTML = '<option value="">-- Tỉnh/Thành phố --</option>';
  cityNhan.innerHTML = '<option value="">-- Tỉnh/Thành phố --</option>';

  Object.entries(dataAddress).forEach(([code, cityInfo]) => {
    const optionGui = document.createElement("option");
    optionGui.value = code;
    optionGui.textContent = cityInfo.name;

    const optionNhan = document.createElement("option");
    optionNhan.value = code;
    optionNhan.textContent = cityInfo.name;

    cityGui.appendChild(optionGui);
    cityNhan.appendChild(optionNhan);
  });

  loadDistrict("Gui");
  loadDistrict("Nhan");
}

function loadDistrict(type) {
  const cityEl = $(`city${type}`);
  const districtEl = $(`district${type}`);
  if (!cityEl || !districtEl) return;

  districtEl.innerHTML = '<option value="">-- Quận/Huyện --</option>';
  districtEl.disabled = !cityEl.value;

  if (!cityEl.value || !dataAddress[cityEl.value]) {
    checkKhuVuc();
    return;
  }

  dataAddress[cityEl.value].districts.forEach((district) => {
    const option = document.createElement("option");
    option.value = district;
    option.textContent = district;
    districtEl.appendChild(option);
  });

  checkKhuVuc();
}

function checkKhuVuc() {
  const cityGui = $("cityGui")?.value || "";
  const cityNhan = $("cityNhan")?.value || "";
  const txt = $("txtTuyenDuong");
  if (!txt) return;

  if (!cityGui || !cityNhan) {
    txt.textContent = "Vui lòng chọn địa chỉ Gửi và Nhận";
    txt.dataset.heso = "1";
    txt.style.color = "#c0392b";
    return;
  }

  const sameCity = cityGui === cityNhan;
  txt.dataset.heso = sameCity ? "1" : "1.5";
  txt.textContent = sameCity
    ? "CÙNG TỈNH (Hệ số x1.0)"
    : "LIÊN TỈNH (Hệ số x1.5)";
  txt.style.color = sameCity ? "#27ae60" : "#e74c3c";
}

// --- PHẦN 2: KIỆN HÀNG ĐỘNG ---
function renderDanhSachKien() {}

// --- PHẦN 3: VALIDATE VÀ THU HỘ COD ---
function kiemTraInline(fieldId) {}

function onCODChange() {}

function kiemTraCODInline() {}

// --- PHẦN 4: TÍNH TIỀN ---
function updateState() {}

function kiemTraToanBoLoi() {}

function tinhTien() {}

// --- PHẦN 5: SUBMIT & MÃ VẬN ĐƠN ---
function onAgreeChange() {}

function onSubmit(e) {}

// ============================================================================
// LẮNG NGHE SỰ KIỆN TỪ DOM (ADD EVENT LISTENER)
// ============================================================================
// Sinh viên tự gọi DOM, xử lý sự kiện và addEventListener phù hợp với từng chức năng.
