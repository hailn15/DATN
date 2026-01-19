<?php
// $title được truyền từ TienIchController (nếu có)
?>
<div class="mb-6">
    <h1 class="text-2xl font-semibold text-gray-700"><?php echo htmlspecialchars($title ?? 'Biểu mẫu Đăng ký Hồ sơ', ENT_QUOTES, 'UTF-8'); ?></h1>
</div>

<div class="bg-white p-6 rounded-lg shadow-md space-y-6">
    <p class="text-gray-700">
        Dưới đây là danh sách các biểu mẫu dùng đăng ký hồ sơ đối tượng bảo trợ xã hội và các hồ sơ liên quan.
        Cán bộ và người dân có thể tải về để sử dụng khi cần thiết.
    </p>

    <div class="border-t pt-4">
        <h2 class="text-lg font-semibold text-indigo-700 mb-3">CÁC MẪU TỜ KHAI DÀNH CHO ĐỐI TƯỢNG (MẪU 1)</h2>
        <ol class="list-decimal list-inside">
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_1a.docx'; ?>" 
                download="Mau_so_1a.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
                Tờ khai đề nghị trợ giúp xã hội (Áp dụng đối với đối tượng quy định tại khoản 1, khoản 2 và khoản 7 Điều 5 Nghị định số 20/2021/NĐ-CP ngày 15 tháng 3 năm 2021 của Chính phủ)
                </a>
                <span class="text-xs text-gray-500 ml-2">(Mẫu số 1a)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_1b.docx'; ?>" 
                download="Mau_so_1b.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
                Tờ khai đề nghị trợ giúp xã hội (Áp dụng đối với đối tượng quy định tại khoản 3 và khoản 8 Điều 5 Nghị định số20/2021/NĐ-CP ngày 15 tháng 3 năm 2021 của Chính phủ)                </a>
                <span class="text-xs text-gray-500 ml-2">(Mẫu số 1b)</span>
            </li>   
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_1c.docx'; ?>" 
                download="Mau_so_1c.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
                Tờ khai đề nghị trợ giúp xã hội (Áp dụng đối với đối tượng quy định tại khoản 4 Điều 5 Nghị định số20/2021/NĐ-CP ngày 15 tháng 3 năm 2021 của Chính phủ) </a>           
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 1c)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_1d.docx'; ?>" 
                download="Mau_so_1d.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
                Tờ khai đề nghị trợ giúp xã hội (Áp dụng đối với đối tượng quy định tại khoản 5 Điều 5 Nghị định số 20/2021/NĐ-CP ngày 15 tháng 3 năm 2021 của Chính phủ)</a>
                <span class="text-xs text-gray-500 ml-2">(Mẫu số 1d)</span>
            </li> 
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_1đ.docx'; ?>" 
                download="Mau_so_1đ.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
                Tờ khai đề nghị trợ giúp xã hội (Áp dụng đối với đối tượng quy định tại khoản 6 Điều 5 Nghị định số 20/2021/NĐ-CP ngày 15 tháng 3 năm 2021 của Chính phủ)</a>
                <span class="text-xs text-gray-500 ml-2">(Mẫu số 1đ)</span>
            </li>
            

                <!-- <p class="italic">Giấy tờ tùy thân của đối tượng (Bản sao CMND/CCCD/Giấy khai sinh).</p>            
                <p class="italic">Giấy tờ chứng minh thuộc diện đối tượng (ví dụ: Giấy xác nhận khuyết tật, Giấy chứng tử của cha/mẹ đối với trẻ mồ côi,...).</p>       -->
        </ol>
        <h2 class="text-lg font-semibold text-indigo-700 mb-3">CÁC MẪU TỜ KHAI HỘ - KHAI THIỆT HẠI</h2>
        <ol class="list-decimal list-inside">
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_2a.docx'; ?>" 
                download="Mau_so_2a.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
                Tờ khai hộ gia đình có người khuyết tật        </a>    
                <span class="text-xs text-gray-500 ml-2">(Mẫu số 2a)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_2b.docx'; ?>" 
                download="Mau_so_2b.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Tờ khai nhận chăm sóc, nuôi dưỡng </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 2b)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_03.docx'; ?>" 
                download="Mau_so_03.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Tờ khai đối tượng được nhận chăm sóc, nuôi dưỡng </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 03)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_04.docx'; ?>" 
                download="Mau_so_04.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Tờ khai đề nghị hỗ trợ chi phí mai táng (Áp dụng đối với đối tượng quy định tại Điều 5, khoản 1 Điều 14 Nghị định số 20/2021/NĐ-CP ngày 15 tháng 3 năm 2021 của Chính phủ) </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 04)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_06.docx'; ?>" 
                download="Mau_so_06.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Tờ khai đề nghị hỗ trợ về nhà ở (Áp dụng đối với đối tượng quy định tại Điều 15 Nghị định số20/2021/NĐ-CP ngày 15 tháng 3 năm 2021 của Chính phủ) </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 06)</span>
            </li>
            
        </ol>
        <h2 class="text-lg font-semibold text-indigo-700 mb-3">CÁC MẪU DANH SÁCH BÁO CÁO</h2>
        <ol class="list-decimal list-inside">
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_5a.docx'; ?>" 
                download="Mau_so_5a.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Danh sách hộ gia đình và số người trong hộ gia đình thiếu đói, nhu yếu phẩm </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 5a)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_5b.docx'; ?>" 
                download="Mau_so_5b.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Danh sách hộ gia đình và số người trong hộ gia đình thiếu đói, nhu yếu phẩm </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 5b)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_10a.docx'; ?>" 
                download="Mau_so_10a.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Số liệu kết quả thực hiện trợ giúp xã hội thường xuyên </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 10a)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_10b.docx'; ?>" 
                download="Mau_so_10b.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Số liệu thực hiện trợ giúp xã hội đột xuất </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 10b)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_10c.docx'; ?>" 
                download="Mau_so_10c.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Số liệu kết quả thực hiện chính sách đối với người cao tuổi </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 10c)</span>
            </li>
            <li>
                <a href="<?php echo BASE_URL . '/forms/Mau_so_10d.docx'; ?>" 
                download="Mau_so_10d.docx"
                class="text-blue-600 hover:text-blue-800 hover:underline">
Số liệu kết quả thực hiện chính sách đối với người khuyết tật </a>
            <span class="text-xs text-gray-500 ml-2">(Mẫu số 10d)</span>
            </li>
        </ol>
    </div>

    <div class="mt-6 text-sm text-gray-500">
        <p><strong>Lưu ý:</strong> Các biểu mẫu này chỉ mang tính chất tham khảo. Vui lòng liên hệ cán bộ phụ trách tại địa phương để được hướng dẫn chi tiết và nhận biểu mẫu chính thức (nếu có thay đổi).</p>
    </div>
</div>