<?php
// app/views/home/index.php
// Các biến được truyền từ HomeController:
// $title, $currentUser, $statsDoiTuong, $statsHoSoTroCap, $statsHoTroKhanCap, $statsHoSoChamSoc
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($title); ?></title>
    
    <!-- Bạn có thể di chuyển CSS này vào file riêng nếu muốn -->
    <style>
        :root {
            --primary-blue: #3498db;
            --positive-green: #2ecc71;
            --warning-orange: #f39c12;
            --care-purple: #9b59b6;
            --danger-red: #e74c3c;
            --dark-text: #2c3e50;
            --light-text: #7f8c8d;
            --border-color: #ecf0f1;
            --card-bg: #ffffff;
            --body-bg: #f4f7f6;
        }

        /* Thiết lập cơ bản cho toàn trang */
        body {
            background-color: var(--body-bg);
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
        }

        /* Container chính cho dashboard */
        .dashboard-container { 
            display: grid;
            grid-template-columns: 1fr; 
            gap: 1.75rem; /* 28px */
            margin-top: 1.25rem; /* 20px */
        }
        .dashboard-row { 
            display: grid;
            gap: 1.75rem;
        }
        .dashboard-row-full-width {
            grid-template-columns: 1fr;
        }
        .dashboard-row-three-cols {
            grid-template-columns: repeat(3, 1fr);
        }

        /* Kiểu dáng cho từng thẻ thống kê */
        .stat-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            background-color: var(--card-bg);
            box-shadow: 0 4px 20px rgba(0,0,0,0.05);
            padding: 1.5rem; /* 24px */
            display: flex;
            flex-direction: column;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 25px rgba(0,0,0,0.08);
        }
        
        /* Layout thẻ ngang cho thống kê Đối tượng */
        .stat-card.horizontal-layout {
            flex-direction: row; 
            align-items: flex-start; 
        }
        .stat-card.horizontal-layout .stat-card-header {
            flex-direction: column; 
            align-items: flex-start; 
            margin-right: 2rem;
            margin-bottom: 0; 
            padding-bottom: 0;
            border-bottom: none; 
            min-width: 220px; 
        }
        .stat-card.horizontal-layout .stat-card-header .icon {
            margin-bottom: 0.75rem; 
        }
        .stat-card.horizontal-layout .stat-card-body {
            display: flex; 
            flex-wrap: wrap; 
            gap: 1.5rem 2rem; 
            flex-grow: 1;
        }
        .stat-card.horizontal-layout .stat-card-body > div { 
            flex-basis: calc(50% - 1rem); 
        }

        /* Phần đầu của thẻ */
        .stat-card-header {
            display: flex;
            align-items: center;
            margin-bottom: 1.25rem;
            padding-bottom: 1rem;
            border-bottom: 1px solid var(--border-color);
        }
        .stat-card-header .icon {
            font-size: 1.5rem; 
            margin-right: 1rem;
            width: 48px;
            height: 48px;
            display: inline-flex;
            justify-content: center;
            align-items: center;
            background-color: #f0f5fa;
            border-radius: 50%;
        }
        .stat-card-header h3 {
            margin: 0;
            font-size: 1.25rem; 
            color: var(--dark-text);
            font-weight: 600;
        }

        .stat-card-body {
            flex-grow: 1;
        }

        /* Số liệu chính, to, nổi bật */
        .main-stat {
            margin-bottom: 1rem; 
        }
        .main-stat .label {
            font-size: 0.9rem; 
            color: var(--light-text);
            display: block;
            margin-bottom: 4px; 
        }
        .main-stat .value {
            font-size: 2.25rem; 
            font-weight: 700;
            line-height: 1.2;
        }

        /* Màu sắc cho icon và số liệu chính */
        .icon-blue { color: var(--primary-blue); }
        .value-blue { color: var(--primary-blue); }
        .icon-green { color: var(--positive-green); }
        .value-green { color: var(--positive-green); }
        .icon-orange { color: var(--warning-orange); }
        .value-orange { color: var(--warning-orange); }
        .icon-purple { color: var(--care-purple); }
        .value-purple { color: var(--care-purple); }

        /* Thông tin bổ sung (số tiền, số đối tượng) */
        .additional-info {
            margin-top: 1.25rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border-color);
            font-size: 0.95em; 
            color: var(--light-text);
        }
        .additional-info p {
            margin: 0.5rem 0; 
            display: flex;
            justify-content: space-between;
        }
        .additional-info .label {
            font-weight: 500;
            color: var(--dark-text);
        }
        .additional-info .info-value {
            font-weight: 600;
            color: var(--dark-text);
        }
        .money-value {
            font-weight: bold;
            color: var(--danger-red);
        }

        /* Danh sách chi tiết theo trạng thái */
        .status-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }
        .status-list-header {
            margin-top: 1.25rem;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: var(--dark-text);
        }
        .status-list li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.6rem 0.25rem; 
            border-bottom: 1px solid #f9fafb;
            font-size: 0.9rem; 
        }
        .status-list li:last-child {
            border-bottom: none;
        }
        .status-list .status-label {
            color: var(--light-text);
        }
        .status-list .status-count {
            font-weight: 600;
            color: var(--primary-blue);
            background-color: #eaf5ff;
            padding: 2px 10px; 
            border-radius: 12px; 
            min-width: 28px; 
            text-align: center;
            font-size: 0.85em; 
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .dashboard-row-three-cols {
                grid-template-columns: 1fr 1fr;
            }
        }
        @media (max-width: 768px) {
            .dashboard-row-three-cols {
                grid-template-columns: 1fr; 
            }
            .stat-card.horizontal-layout {
                flex-direction: column; 
                align-items: stretch;
            }
            .stat-card.horizontal-layout .stat-card-header {
                flex-direction: row; 
                align-items: center;
                margin-right: 0;
                margin-bottom: 1.25rem; 
                padding-bottom: 1rem;
                border-bottom: 1px solid var(--border-color);
            }
            .stat-card.horizontal-layout .stat-card-header .icon {
                margin-bottom: 0;
            }
            .stat-card.horizontal-layout .stat-card-body > div {
                flex-basis: 100%; 
            }
        }
    </style>
</head>
<body>

<div class="mb-4">
    <h1 class="text-2xl font-semibold text-gray-800"><?php echo htmlspecialchars($title); ?></h1>
    <p class="text-gray-600 mt-1">Chào mừng <strong><?php echo htmlspecialchars($currentUser['ho_ten'] ?? $currentUser['ten_dang_nhap'] ?? 'bạn'); ?></strong> đến với HỆ THỐNG QUẢN LÝ ĐỐI TƯỢNG CHÍNH SÁCH XÃ HỘI XÃ KIM ĐƯỜNG!</p>
</div>

<div class="dashboard-container">
    <!-- Dòng 1: Thống kê Đối tượng -->
    <div class="dashboard-row dashboard-row-full-width">
        <div class="stat-card horizontal-layout"> 
            <div class="stat-card-header">
                <h3>Đối tượng Bảo trợ</h3>
            </div>
            <div class="stat-card-body">
                <div> 
                    <div class="main-stat">
                        <span class="label">Tổng số đối tượng</span>
                        <span class="value value-blue"><?php echo htmlspecialchars($statsDoiTuong['total']); ?></span>
                    </div>
                    <p class="status-list-header">Trạng thái hồ sơ:</p>
                    <ul class="status-list">
                        <?php 
                        if (isset($statsDoiTuong['trang_thai_ho_so_dt_counts']) && is_array($statsDoiTuong['trang_thai_ho_so_dt_counts'])):
                            foreach($statsDoiTuong['trang_thai_ho_so_dt_counts'] as $statusKey => $count): ?>
                                <li>
                                    <span class="status-label"><?php echo htmlspecialchars($statsDoiTuong['trang_thai_ho_so_dt_labels'][$statusKey] ?? ucfirst(str_replace('_', ' ', $statusKey))); ?></span>
                                    <span class="status-count"><?php echo htmlspecialchars($count); ?></span>
                                </li>
                            <?php endforeach; 
                        endif;
                        ?>
                    </ul>
                </div>
                <?php if (!empty($statsDoiTuong['count_by_loai'])): ?>
                    <div> 
                        <p class="status-list-header">Phân loại đối tượng:</p>
                        <ul class="status-list">
                            <?php foreach ($statsDoiTuong['count_by_loai'] as $loai): ?>
                                <li>
                                    <span class="status-label"><?php echo htmlspecialchars($loai['ten_loai']); ?></span>
                                    <span class="status-count"><?php echo htmlspecialchars($loai['count']); ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Dòng 2: Ba loại Hồ sơ -->
    <div class="dashboard-row dashboard-row-three-cols">
        <!-- Thống kê Hồ sơ Trợ cấp Hàng tháng -->
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="icon icon-green"><i class="fas fa-file-invoice-dollar"></i></span>
                <h3>Trợ cấp Hàng tháng</h3>
            </div>
            <div class="stat-card-body">
                <div class="main-stat">
                    <span class="label">Tổng số hồ sơ</span>
                    <span class="value value-green"><?php echo htmlspecialchars($statsHoSoTroCap['total'] ?? 0); ?></span>
                </div>
                <ul class="status-list">
                    <?php 
                    if (isset($statsHoSoTroCap['counts_by_status']) && is_array($statsHoSoTroCap['counts_by_status'])):
                        foreach ($statsHoSoTroCap['counts_by_status'] as $statusKey => $count): ?>
                            <li>
                                <span class="status-label"><?php echo htmlspecialchars($statsHoSoTroCap['trang_thai_options'][$statusKey] ?? ucfirst(str_replace('_',' ',$statusKey))); ?></span>
                                <span class="status-count"><?php echo htmlspecialchars($count); ?></span>
                            </li>
                        <?php endforeach; 
                    endif;
                    ?>
                </ul>
                 <div class="additional-info">
                     <p>
                        <span class="label">Số ĐT đang hưởng:</span> 
                        <span class="info-value"><?php echo htmlspecialchars($statsHoSoTroCap['doi_tuong_dang_huong_count'] ?? 0); ?></span>
                    </p>
                     <p>
                        <span class="label">Tổng chi trả/tháng:</span> 
                        <span class="money-value"><?php echo number_format($statsHoSoTroCap['total_muc_tro_cap_dang_huong'] ?? 0, 0, ',', '.'); ?> VNĐ</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Thống kê Hỗ trợ Khẩn cấp -->
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="icon icon-orange"><i class="fas fa-bolt"></i></span>
                <h3>Hỗ trợ Khẩn cấp</h3>
            </div>
            <div class="stat-card-body">
                <div class="main-stat">
                    <span class="label">Tổng số hồ sơ</span>
                    <span class="value value-orange"><?php echo htmlspecialchars($statsHoTroKhanCap['total'] ?? 0); ?></span>
                </div>
                <ul class="status-list">
                     <?php 
                     if (isset($statsHoTroKhanCap['counts_by_status']) && is_array($statsHoTroKhanCap['counts_by_status'])):
                        foreach ($statsHoTroKhanCap['counts_by_status'] as $statusKey => $count): ?>
                        <li>
                            <span class="status-label"><?php echo htmlspecialchars($statsHoTroKhanCap['trang_thai_options'][$statusKey] ?? ucfirst(str_replace('_',' ',$statusKey))); ?></span>
                            <span class="status-count"><?php echo htmlspecialchars($count); ?></span>
                        </li>
                        <?php endforeach; 
                     endif;
                     ?>
                </ul>
                <div class="additional-info">
                    <p>
                        <span class="label">Tổng tiền đã hỗ trợ:</span> 
                        <span class="money-value"><?php echo number_format($statsHoTroKhanCap['total_gia_tri_da_ho_tro_tien_mat'] ?? 0, 0, ',', '.'); ?> VNĐ</span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Thống kê Hồ sơ Chăm sóc Cộng đồng -->
        <div class="stat-card">
            <div class="stat-card-header">
                <span class="icon icon-purple"><i class="fas fa-hands-helping"></i></span>
                <h3>Chăm sóc Cộng đồng</h3>
            </div>
            <div class="stat-card-body">
                <div class="main-stat">
                    <span class="label">Tổng số hồ sơ</span>
                    <span class="value value-purple"><?php echo htmlspecialchars($statsHoSoChamSoc['total'] ?? 0); ?></span>
                </div>
                <ul class="status-list">
                     <?php 
                     if (isset($statsHoSoChamSoc['counts_by_status']) && is_array($statsHoSoChamSoc['counts_by_status'])):
                        foreach ($statsHoSoChamSoc['counts_by_status'] as $statusKey => $count): ?>
                        <li>
                            <span class="status-label"><?php echo htmlspecialchars($statsHoSoChamSoc['trang_thai_options'][$statusKey] ?? ucfirst(str_replace('_',' ',$statusKey))); ?></span>
                            <span class="status-count"><?php echo htmlspecialchars($count); ?></span>
                        </li>
                        <?php endforeach; 
                     endif;
                     ?>
                </ul>
                <div class="additional-info">
                    <p>
                        <span class="label">Số ĐT đang chăm sóc:</span> 
                        <span class="info-value"><?php echo htmlspecialchars($statsHoSoChamSoc['doi_tuong_dang_cham_soc_count'] ?? 0); ?></span>
                    </p>
                    <p>
                        <span class="label">Kinh phí dự kiến (đã duyệt):</span> 
                        <span class="money-value"><?php echo number_format($statsHoSoChamSoc['total_kinh_phi_du_kien_da_phe_duyet'] ?? 0, 0, ',', '.'); ?> VNĐ</span>
                    </p>
                </div>
            </div>
        </div>
    </div> 
</div> 

<p style="margin-top: 2rem; font-style: italic; color: #888; text-align: center;">
    Lưu ý: Các số liệu thống kê được tính dựa trên dữ liệu hiện tại của các hồ sơ trong hệ thống.
</p>
</body>
</html>