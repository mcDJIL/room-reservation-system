<?php

include '../../config/connection.php';

$history_rows = [];
$total_rows = 0;
$total_pages = 1;
$current_page = 1;
$per_page = 5;
$length = 0;

$search_term = isset($_GET['q']) ? trim($_GET['q']) : '';
$filter_date = isset($_GET['date']) ? trim($_GET['date']) : '';
$filter_status = isset($_GET['status']) ? trim($_GET['status']) : '';

$page_title = 'Riwayat Reservasiku - SatSet';
$is_logged_in = isset($_SESSION['is_login']) && $_SESSION['is_login'] === true;

$allowed_status = ['approved', 'waiting', 'rejected'];
if (!in_array($filter_status, $allowed_status, true)) {
        $filter_status = '';
}

if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $filter_date)) {
        $filter_date = '';
}

if (isset($_GET['page']) && is_numeric($_GET['page'])) {
        $current_page = max(1, (int) $_GET['page']);
}

if (!function_exists('bind_dynamic_params')) {
        function bind_dynamic_params($stmt, $types, $params)
        {
                $bind = [$types];
                foreach ($params as $key => $value) {
                        $bind[] = &$params[$key];
                }
                call_user_func_array('mysqli_stmt_bind_param', array_merge([$stmt], $bind));
        }
}

if (isset($_SESSION['user_id']) && is_numeric($_SESSION['user_id'])) {
        $user_id = (int) $_SESSION['user_id'];

        $where_sql = " WHERE re.user_id = ?";
        $types = 'i';
        $params = [$user_id];

        if ($search_term !== '') {
                $where_sql .= " AND (r.room_name LIKE ? OR b.name LIKE ? OR re.id = ?)";
                $search_like = '%' . $search_term . '%';
                $search_id = ctype_digit($search_term) ? (int) $search_term : -1;
                $types .= 'ssi';
                $params[] = $search_like;
                $params[] = $search_like;
                $params[] = $search_id;
        }

        if ($filter_date !== '') {
                $where_sql .= " AND re.reservation_date = ?";
                $types .= 's';
                $params[] = $filter_date;
        }

        if ($filter_status !== '') {
                $where_sql .= " AND re.status = ?";
                $types .= 's';
                $params[] = $filter_status;
        }

        $from_sql = "
                FROM reservations re
                LEFT JOIN users u ON re.user_id = u.id
                LEFT JOIN rooms r ON re.room_id = r.id
                LEFT JOIN buildings b ON r.building_id = b.id
                LEFT JOIN users au ON re.approved_by = au.id
        ";

        $count_sql = "SELECT COUNT(*) AS total " . $from_sql . $where_sql;
        $count_stmt = mysqli_prepare($conn, $count_sql);

        if ($count_stmt) {
                bind_dynamic_params($count_stmt, $types, $params);
                mysqli_stmt_execute($count_stmt);
                $count_result = mysqli_stmt_get_result($count_stmt);
                $count_row = $count_result ? mysqli_fetch_assoc($count_result) : null;
                $total_rows = $count_row ? (int) $count_row['total'] : 0;
                $length = $total_rows;
        }

        $total_pages = max(1, (int) ceil($total_rows / $per_page));
        $current_page = min($current_page, $total_pages);
        $start_item = $total_rows > 0 ? (($current_page - 1) * $per_page) + 1 : 0;
        $end_item = min($current_page * $per_page, $total_rows);
        $offset = ($current_page - 1) * $per_page;

        $data_sql = "
                SELECT
                        re.id AS reservation_id,
                        re.reservation_date,
                        re.start_hour,
                        re.end_hour,
                        re.reason,
                        re.status,
                        re.created_at,
                        r.room_name,
                        r.capacity,
                        b.name AS building_name,
                        u.name AS user_name,
                        au.name AS approved_by_name
                " . $from_sql . $where_sql . "
                ORDER BY re.created_at DESC
                LIMIT ? OFFSET ?
        ";

        $data_stmt = mysqli_prepare($conn, $data_sql);

        if ($data_stmt) {
                $data_types = $types . 'ii';
                $data_params = $params;
                $data_params[] = $per_page;
                $data_params[] = $offset;

                bind_dynamic_params($data_stmt, $data_types, $data_params);
                mysqli_stmt_execute($data_stmt);
                $data_result = mysqli_stmt_get_result($data_stmt);

                if ($data_result) {
                        $row_number = $start_item;
                        while ($row = mysqli_fetch_assoc($data_result)) {
                                $row['display_number'] = $row_number;
                                $history_rows[] = $row;
                                $row_number++;
                        }
                }
        }
}