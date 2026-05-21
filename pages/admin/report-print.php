<?php
$active = 'laporan';
$crumbs = 'Laporan | Cetak Laporan';

session_start();

require_once __DIR__ . '/../../actions/report/report_action.php';

$printQuery = $_SERVER['QUERY_STRING'] ?? '';
$printUrl = 'report-print.php' . ($printQuery !== '' ? '?' . $printQuery : '');
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <?php include __DIR__ . '/../../includes/header.php'; ?>
    <style>
        body.print-only {
            background: #fff;
        }

        .print-shell {
            max-width: 1200px;
            margin: 0 auto;
            padding: 28px;
        }

        .print-topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 16px;
            margin-bottom: 20px;
        }

        .print-brand {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .print-brand small {
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: .08em;
            font-weight: 700;
        }

        .print-title {
            margin: 0;
            font-size: 28px;
            color: #0f172a;
        }

        .print-meta-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 12px;
            margin: 18px 0 22px;
        }

        .print-card {
            border: 1px solid #e2e8f0;
            border-radius: 14px;
            padding: 14px 16px;
            background: #fff;
        }

        .print-card span {
            display: block;
            color: #64748b;
            font-size: 12px;
            margin-bottom: 8px;
        }

        .print-card strong {
            font-size: 26px;
            color: #0f172a;
        }

        .print-section-title {
            font-size: 16px;
            font-weight: 700;
            margin: 22px 0 10px;
            color: #0f172a;
        }

        .print-table {
            width: 100%;
            border-collapse: collapse;
        }

        .print-table th,
        .print-table td {
            border: 1px solid #cbd5e1;
            padding: 10px 8px;
            vertical-align: top;
            font-size: 12px;
        }

        .print-table th {
            background: #eff6ff;
            text-transform: uppercase;
            letter-spacing: .04em;
        }

        .print-note {
            color: #64748b;
            font-size: 12px;
        }

        @media print {
            .no-print {
                display: none !important;
            }

            body.print-only {
                background: #fff !important;
            }

            .print-shell {
                padding: 0;
            }

            @page {
                size: A4 landscape;
                margin: 12mm;
            }
        }
    </style>
</head>

<body class="print-only" onload="window.print();">
    <div class="print-shell">
        <div class="print-topbar no-print">
            <div class="print-brand">
                <small>RentRoom Admin Report</small>
                <h1 class="print-title">Laporan Reservasi Ruangan</h1>
                <div class="print-note">Filter aktif: <strong><?php echo htmlspecialchars($filterLabel); ?></strong></div>
            </div>
            <div>
                <a class="btn btn-outline-secondary" href="report.php<?php echo $printQuery !== '' ? '?' . htmlspecialchars($printQuery) : ''; ?>">Kembali</a>
                <button type="button" class="btn btn-primary no-print" onclick="window.print()">Cetak</button>
            </div>
        </div>

        <div class="print-meta-grid">
            <div class="print-card">
                <span>Total Reservasi</span>
                <strong><?php echo $summaryTotal; ?></strong>
            </div>
            <div class="print-card">
                <span>Approved</span>
                <strong><?php echo $summaryApproved; ?></strong>
            </div>
            <div class="print-card">
                <span>Waiting</span>
                <strong><?php echo $summaryWaiting; ?></strong>
            </div>
            <div class="print-card">
                <span>Rejected</span>
                <strong><?php echo $summaryRejected; ?></strong>
            </div>
        </div>

        <div class="print-note">Dibuat: <strong><?php echo date('d M Y H:i'); ?></strong></div>

        <div class="print-section-title">Detail Data Reservasi</div>
        <table class="print-table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Peminjam</th>
                    <th>Email</th>
                    <th>Gedung</th>
                    <th>Ruangan</th>
                    <th>Tanggal</th>
                    <th>Jam</th>
                    <th>Alasan</th>
                    <th>Status</th>
                    <th>Disetujui Oleh</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($filteredReservations)): ?>
                    <tr>
                        <td colspan="10" style="text-align:center; color:#64748b;">Tidak ada data reservasi yang cocok dengan filter.</td>
                    </tr>
                <?php else: ?>
                    <?php foreach ($filteredReservations as $index => $row): ?>
                        <tr>
                            <td><?php echo $index + 1; ?></td>
                            <td><?php echo htmlspecialchars($row['user_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['user_email'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['building_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars($row['room_name'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars(date('d M Y', strtotime($row['reservation_date']))); ?></td>
                            <td><?php echo htmlspecialchars(report_time_range($row)); ?></td>
                            <td><?php echo htmlspecialchars($row['reason'] ?? '-'); ?></td>
                            <td><?php echo htmlspecialchars(report_status_label($row['status'] ?? 'waiting')); ?></td>
                            <td><?php echo htmlspecialchars($row['approved_by_name'] ?? '-'); ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</body>

</html>
