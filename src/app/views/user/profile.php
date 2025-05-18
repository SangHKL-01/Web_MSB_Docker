<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Hồ Sơ - Shopee Style</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-[#f5f5f5] min-h-screen font-sans">

  <div class="max-w-7xl mx-auto flex pt-10">

    <!-- Sidebar -->
    <aside class="w-64 bg-white rounded-lg shadow p-6 h-fit mr-6">
      <div class="flex items-center gap-4 mb-6"> 
        <img src="<?= isset($user['avatar']) ? '/uploads/avatars/' . htmlspecialchars($user['avatar']) : '/public/assets/images/avatar.jpg' ?>"
             class="w-14 h-14 rounded-full border border-gray-300" alt="Avatar">
        <div>
          <p class="font-semibold"><?= $user['username'] ?></p>
        </div>
      </div>
      <nav class="text-gray-700 text-sm space-y-3">
        <a href="index.php" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-orange-100">
        <span>🏠</span> Home
        </a>
        <a href="" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-orange-100">
          <span>👤</span> Hồ sơ
        </a>
        <a href="index.php?controller=Product&action=history" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-orange-100">
          <span>📦</span> Lịch sử mua hàng
        </a>
        <a href="index.php?controller=user&action=change_password" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-orange-100">
          <span>🔒</span> Đổi mật khẩu
        </a>
        <a href="index.php?controller=user&action=logout" class="flex items-center gap-2 px-2 py-2 rounded hover:bg-orange-100 text-red-600">
         <span>🔓</span> Đăng xuất
        </a>
      </nav>
    </aside>

    <!-- Main Profile Content -->
<main class="flex-1 bg-white rounded-lg shadow p-8">
  <h2 class="text-xl font-semibold mb-6 border-b pb-2">Hồ Sơ Của Tôi</h2>

  <div class="flex flex-col lg:flex-row gap-10">
    <!-- Phần 2: Thông tin cá nhân -->
    <form method="POST" action="index.php?controller=user&action=change_profile" class="flex-1 space-y-5" id="profile-form">
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Họ tên</label>
    <input type="text" name="fullname" value="<?= isset($user['fullname']) ? $user['fullname'] : '' ?>" 
           class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" />
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
    <input type="email" name="email" value="<?= isset($user['email']) ? $user['email'] : '' ?>" 
    disabled
           class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" />
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Số điện thoại</label>
    <input type="text" name="phone" value="<?= isset($user['phone']) ? $user['phone'] : '' ?>" 
           class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" 
           pattern="[0-9]{10,11}" title="Số điện thoại phải chứa 10-11 chữ số" required />
    <div id="phoneError" style="color:red; display:none; font-size:0.95em;">Số điện thoại phải chỉ chứa các ký tự số và có 10-11 số.</div>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Giới tính</label>
    <select name="gioi_tinh" id="gioi_tinh"
      class="w-full border px-4 py-2 rounded-lg bg-white">
      <option value="Nam" <?= isset($user['gioi_tinh']) && $user['gioi_tinh'] === 'Nam' ? 'selected' : '' ?>>Nam</option>
      <option value="Nữ" <?= isset($user['gioi_tinh']) && $user['gioi_tinh'] === 'Nữ' ? 'selected' : '' ?>>Nữ</option>
      <option value="Khác" <?= isset($user['gioi_tinh']) && $user['gioi_tinh'] === 'Khác' ? 'selected' : '' ?>>Khác</option>
    </select>
  </div>
  <div>
    <label class="block text-sm font-medium text-gray-700 mb-1">Ngày sinh</label>
    <input type="date" name="ngay_sinh" value="<?= isset($user['ngay_sinh']) ? $user['ngay_sinh'] : '' ?>" 
           class="w-full border px-4 py-2 rounded-lg bg-white text-gray-800" />
  </div>
  <div>
    <button type="submit"
            class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition">
      Cập nhật
    </button>
  </div>
</form>



   <!-- Phần 3: Ảnh đại diện -->
<div class="w-64 flex flex-col items-center justify-start text-center">
  <img src="<?= isset($user['avatar']) ? '/uploads/avatars/' . htmlspecialchars($user['avatar']) : '/public/assets/images/avatar.jpg' ?>"
       class="w-50 h-50 rounded-full border border-gray-300 mb-4" alt="Avatar">
  
  <?php if (isset($_SESSION['success'])): ?>
    <div class="text-green-600 mb-2"><?= $_SESSION['success']; unset($_SESSION['success']); ?></div>
  <?php endif; ?>
  
  <?php if (isset($_SESSION['error'])): ?>
    <div class="text-red-600 mb-2"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
  <?php endif; ?>
  
  <form method="POST" action="index.php?controller=user&action=upload_avatar" enctype="multipart/form-data" class="w-full">
    <div class="mb-3">
      <input type="file" name="avatar" accept="image/*" required class="w-full text-sm">
    </div>
    <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition w-full">
      Tải lên
    </button>
  </form>
</div>

</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
  var form = document.getElementById('profile-form');
  if (form) {
    form.addEventListener('submit', function(e) {
      var phoneInput = form.querySelector('input[name="phone"]');
      var phoneError = document.getElementById('phoneError');
      var phoneValue = phoneInput.value.trim();
      var numberPattern = /^[0-9]{10,11}$/;
      if (!numberPattern.test(phoneValue)) {
        phoneError.style.display = 'block';
        phoneInput.classList.add('border-red-500');
        phoneInput.focus();
        e.preventDefault();
        return false;
      } else {
        phoneError.style.display = 'none';
        phoneInput.classList.remove('border-red-500');
      }
    });
  }
});
</script>
