<?php

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../config/connection.php';

$search = trim($_GET['q'] ?? '');
$filterType = $_GET['filter_type'] ?? 'all';
$filterDate = $_GET['filter_date'] ?? '';
$filterMonth = $_GET['filter_month'] ?? '';
$filterYear = $_GET['filter_year'] ?? date('Y');

function report_status_label(string $status): string
{
    $value = strtolower($status);

    if ($value === 'approved') {
        return 'Approved';
    }

    if ($value === 'rejected') {
        return 'Rejected';
    }

    return 'Waiting';
}

function report_status_class(string $status): string
{
    $value = strtolower($status);

    if ($value === 'approved') {
        return 'success';
    }

    if ($value === 'rejected') {
        return 'danger';
    }

    return 'warning';
}

function report_time_range(array $row): string
{
    $start = isset($row['start_hour']) ? date('H:i', strtotime($row['start_hour'])) : '-';
    $end = isset($row['end_hour']) ? date('H:i', strtotime($row['end_hour'])) : '-';

    return $start . ' - ' . $end;
}

$reservations = [];
$sql = "SELECT r.id, r.user_id, r.room_id, r.reservation_date, r.start_hour, r.end_hour, r.reason, r.status, r.approved_by, r.created_at,
               u.name AS user_name, u.email AS user_email,
               rm.room_name, rm.capacity,
               b.name AS building_name,
               au.name AS approved_by_name
        FROM reservations r
        LEFT JOIN users u ON u.id = r.user_id
        LEFT JOIN rooms rm ON rm.id = r.room_id
        LEFT JOIN buildings b ON b.id = rm.building_id
        LEFT JOIN users au ON au.id = r.approved_by
        ORDER BY r.reservation_date DESC, r.start_hour DESC, r.id DESC";

if ($result = $conn->query($sql)) {
    while ($row = $result->fetch_assoc()) {
        $reservations[] = $row;
    }
    $result->free();
}

$filteredReservations = array_values(array_filter($reservations, function ($row) use ($search, $filterType, $filterDate, $filterMonth, $filterYear) {
    $dateValue = $row['reservation_date'] ?? '';
    $matchSearch = true;

    if ($search !== '') {
        $haystack = implode(' ', [
            $row['id'] ?? '',
            $row['user_name'] ?? '',
            $row['user_email'] ?? '',
            $row['building_name'] ?? '',
            $row['room_name'] ?? '',
            $row['reason'] ?? '',
            $row['status'] ?? '',
        ]);
        $matchSearch = stripos($haystack, $search) !== false;
    }

    $matchFilter = true;
    if ($filterType === 'date' && $filterDate !== '') {
        $matchFilter = $dateValue === $filterDate;
    } elseif ($filterType === 'month' && $filterMonth !== '') {
        $matchFilter = substr($dateValue, 0, 7) === $filterMonth;
    } elseif ($filterType === 'year' && $filterYear !== '') {
        $matchFilter = substr($dateValue, 0, 4) === $filterYear;
    }

    return $matchSearch && $matchFilter;
}));

$summaryTotal = count($filteredReservations);
$summaryWaiting = count(array_filter($filteredReservations, fn($row) => ($row['status'] ?? '') === 'waiting'));
$summaryApproved = count(array_filter($filteredReservations, fn($row) => ($row['status'] ?? '') === 'approved'));
$summaryRejected = count(array_filter($filteredReservations, fn($row) => ($row['status'] ?? '') === 'rejected'));

$yearOptions = [];
$currentYear = (int)date('Y');
for ($year = $currentYear - 5; $year <= $currentYear + 1; $year++) {
    $yearOptions[] = (string)$year;
}

$filterLabel = 'Semua data';
if ($filterType === 'date' && $filterDate !== '') {
    $filterLabel = 'Tanggal ' . $filterDate;
} elseif ($filterType === 'month' && $filterMonth !== '') {
    $filterLabel = 'Bulan ' . date('F Y', strtotime($filterMonth . '-01'));
} elseif ($filterType === 'year' && $filterYear !== '') {
    $filterLabel = 'Tahun ' . $filterYear;
}
