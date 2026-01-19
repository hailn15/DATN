<?php
// $title được truyền từ TienIchController (nếu có)
?>
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title ?? 'Hướng dẫn Sử dụng Phần mềm', ENT_QUOTES, 'UTF-8'); ?></h1>
</div>

<div class="bg-white p-6 rounded-lg shadow-md space-y-6">
    <p class="text-gray-700">
        Chào mừng bạn đến với phần hướng dẫn sử dụng HỆ THỐNG QUẢN LÝ ĐỐI TƯỢNG CHÍNH SÁCH XÃ HỘI XÃ KIM ĐƯỜNG. Dưới đây là các nội dung chính giúp bạn làm quen và thao tác hiệu quả trên phần mềm.
    </p>

    <section>
        <h2 class="text-xl font-semibold text-indigo-700 mb-3 border-b pb-2">I. Giới thiệu chung</h2>
        <p class="text-gray-600 leading-relaxed">
            Hệ thống được xây dựng nhằm mục đích tin học hóa công tác quản lý đối tượng bảo trợ xã hội, quản lý hồ sơ trợ cấp, hỗ trợ khẩn cấp và chăm sóc cộng đồng.
            Hệ thống giúp cán bộ dễ dàng theo dõi, cập nhật thông tin, tra cứu và lập báo cáo thống kê.
        </p>
    </section>

    <section>
        <h2 class="text-xl font-semibold text-indigo-700 mb-3 border-b pb-2">II. Hướng dẫn các chức năng chính</h2>
        <div class="space-y-4">
            <div>
                <h3 class="text-lg font-medium text-slate-800">1. Quản lý hồ sơ đối tượng</h3>
                <ul class="list-disc list-inside pl-5 text-gray-600 space-y-1 mt-1">
                    <li><strong>Xem danh sách:</strong> Truy cập mục "QUẢN LÝ HỒ SƠ ĐỐI TƯỢNG" -> "Danh sách Đối tượng". Tại đây bạn có thể tìm kiếm, lọc và xem thông tin cơ bản của các đối tượng.</li>
                    <li><strong>Thêm mới đối tượng:</strong> Chọn "Thêm mới Đối tượng" từ menu hoặc nút "Thêm mới" trên trang danh sách. Điền đầy đủ thông tin vào biểu mẫu. Các trường có dấu <span class="text-red-500">*</span> là bắt buộc.</li>
                    <li><strong>Chỉnh sửa thông tin:</strong> Từ danh sách đối tượng, nhấn vào tên đối tượng hoặc nút "Sửa" để cập nhật thông tin.</li>
                    <li><strong>Xóa đối tượng:</strong> Sử dụng nút "Xóa" trên danh sách. Lưu ý: chỉ xóa được đối tượng nếu không có dữ liệu liên quan (hồ sơ, chi trả,...).</li>
                    <li><strong>Xem hỗ trợ đã có:</strong> Click vào trợ cấp hoặc biểu tượng "thêm trợ cấp" để xem các trợ cấp đang hưởng</li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-medium text-slate-800">2. Quản lý trợ cấp thường xuyên</h3>
                 <ul class="list-disc list-inside pl-5 text-gray-600 space-y-1 mt-1">
                    <li><strong>Xem danh sách hồ sơ:</strong> Truy cập "HỖ TRỢ THƯỜNG XUYÊN" -> "Danh sách Hồ sơ ".</li>
                    <li><strong>Tạo hồ sơ trợ cấp mới:</strong> Từ trang danh sách đối tượng, nhấn nút "Hồ sơ" của đối tượng tương ứng, sau đó chọn "Thêm mới Trợ cấp".</li>
                    <li><strong>Cập nhật trạng thái, chi trả:</strong> Các chức năng này sẽ được hướng dẫn chi tiết bởi quản trị viên hoặc tài liệu chuyên sâu.</li>
                </ul>
            </div>
             <div>
                <h3 class="text-lg font-medium text-slate-800">3. Quản lý Hỗ trợ Khẩn cấp & Chăm sóc Cộng đồng</h3>
                <p class="text-gray-600 mt-1">Tương tự như quản lý Hồ sơ Trợ cấp, bạn có thể truy cập các mục tương ứng trên sidebar để xem danh sách và tạo mới hồ sơ.</p>
            </div>
            
            <div>
                <h3 class="text-lg font-medium text-slate-800">4. Quản lý chính sách </h3>
                <ul class="list-disc list-inside pl-5 text-gray-600 space-y-1 mt-1">
                    <li>Quản lý các danh mục như: Mức trợ cấp, Loại hình hỗ trợ, loại hình/đối tượng chăm sóc tại cộng đồng</li>
                    <li>Thêm, cập nhật, xóa các chính sách mới quy định theo loại đối tượng, loại trợ cấp và mức hỗ trợ tương ứng.</li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-medium text-slate-800">5. Quản lý địa phương </h3>
                <ul class="list-disc list-inside pl-5 text-gray-600 space-y-1 mt-1">
                    <li>Quản lý các thôn trên địa phương </li>
                    <li>Thêm, cập nhật, xóa các thôn và mô tả để thuận tiện theo dõi</li>
                </ul>
            </div>
            <div>
                <h3 class="text-lg font-medium text-slate-800">6. Quản lý người dùng (Dành cho Admin)</h3>
                <ul class="list-disc list-inside pl-5 text-gray-600 space-y-1 mt-1">
                    <li>Admin có quyền quản lý các tài khoản truy cập vào hệ thống</li>
                    
                </ul>
            </div>
            
        </div>
    </section>

    <section>
        <h2 class="text-xl font-semibold text-indigo-700 mb-3 border-b pb-2">III. Một số lưu ý</h2>
        <ul class="list-disc list-inside pl-5 text-gray-600 space-y-1">
            <li>Luôn đảm bảo thông tin nhập liệu chính xác và đầy đủ.</li>
            <li>Thường xuyên kiểm tra và cập nhật trạng thái hồ sơ.</li>
            <li>Bảo mật tài khoản đăng nhập, không chia sẻ cho người khác.</li>
            <li>Nếu gặp sự cố hoặc có thắc mắc, vui lòng liên hệ bộ phận kỹ thuật hoặc quản trị viên hệ thống.</li>
        </ul>
    </section>

     <div class="mt-6 text-sm text-gray-500">
        <p>Tài liệu này sẽ được cập nhật thường xuyên. Chúc bạn làm việc hiệu quả!</p>
    </div>
</div>