<?php
// Vars: $title, $quyenOptions, $oldData, $errors
?>
<h1 class="text-2xl font-semibold text-gray-700 mb-6"><?php echo htmlspecialchars($title); ?></h1>

<form action="<?php echo url('user/store'); ?>" method="POST" class="bg-white p-6 rounded-lg shadow-md max-w-2xl mx-auto">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="mb-4">
            <label for="ten_dang_nhap" class="form-label">Tên đăng nhập <span class="text-red-500">*</span></label>
            <input type="text" id="ten_dang_nhap" name="ten_dang_nhap" required class="form-input <?php echo isset($errors['ten_dang_nhap']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($oldData['ten_dang_nhap'] ?? ''); ?>">
            <?php if (isset($errors['ten_dang_nhap'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ten_dang_nhap']; ?></p><?php endif; ?>
        </div>
        <div class="mb-4">
            <label for="mat_khau" class="form-label">Mật khẩu <span class="text-red-500">*</span></label>
            <input type="password" id="mat_khau" name="mat_khau" required class="form-input <?php echo isset($errors['mat_khau']) ? 'border-red-500' : ''; ?>">
            <?php if (isset($errors['mat_khau'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['mat_khau']; ?></p><?php endif; ?>
        </div>
        <div class="mb-4 md:col-span-2">
            <label for="ho_ten" class="form-label">Họ và Tên <span class="text-red-500">*</span></label>
            <input type="text" id="ho_ten" name="ho_ten" required class="form-input <?php echo isset($errors['ho_ten']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($oldData['ho_ten'] ?? ''); ?>">
            <?php if (isset($errors['ho_ten'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['ho_ten']; ?></p><?php endif; ?>
        </div>
        <div class="mb-4">
            <label for="email" class="form-label">Email</label>
            <input type="email" id="email" name="email" class="form-input <?php echo isset($errors['email']) ? 'border-red-500' : ''; ?>" value="<?php echo htmlspecialchars($oldData['email'] ?? ''); ?>">
            <?php if (isset($errors['email'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['email']; ?></p><?php endif; ?>
        </div>
        <div class="mb-4">
            <label for="so_dien_thoai" class="form-label">Số điện thoại</label>
            <input type="text" id="so_dien_thoai" name="so_dien_thoai" class="form-input" value="<?php echo htmlspecialchars($oldData['so_dien_thoai'] ?? ''); ?>">
        </div>
        <div class="mb-4 md:col-span-2">
            <label for="quyen" class="form-label">Quyền <span class="text-red-500">*</span></label>
            <select id="quyen" name="quyen" class="form-input <?php echo isset($errors['quyen']) ? 'border-red-500' : ''; ?>">
                <?php foreach ($quyenOptions as $value => $label): ?>
                    <option value="<?php echo $value; ?>" <?php echo (isset($oldData['quyen']) && $oldData['quyen'] == $value) ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($label); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php if (isset($errors['quyen'])): ?><p class="text-red-500 text-xs mt-1"><?php echo $errors['quyen']; ?></p><?php endif; ?>
        </div>
    </div>
    <div class="mt-6 pt-4 border-t border-gray-200 flex justify-end space-x-3">
        <a href="<?php echo url('user/index'); ?>" class="btn bg-gray-200 text-gray-700 hover:bg-gray-300">Hủy</a>
        <button type="submit" class="btn btn-primary">Lưu Người dùng</button>
    </div>
</form>