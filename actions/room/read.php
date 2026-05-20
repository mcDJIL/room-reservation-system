<?php

include 'connection.php';

$sql = "SELECT id, room_name, capacity, facility, short_description, detail_description, is_active FROM rooms";
$result = mysqli_query($conn, $sql);

if ($result) {
    if (mysqli_num_rows($result) > 0) {
        echo "<table border='1' cellpadding='10' cellspacing='0'>";
        echo "<tr>
                <th>ID</th>
                <th>Nama Ruangan</th>
                <th>Kapasitas</th>
                <th>Fasilitas</th>
                <th>Deskripsi Singkat</th>
                <th>Deskripsi Detail</th>
                <th>Status</th>
              </tr>";

        while ($row = mysqli_fetch_assoc($result)) {
            $status = $row['is_active'] == 1 ? 'Aktif' : 'Tidak Aktif';
            
            echo "<tr>";
            echo "<td>" . htmlspecialchars($row['id']) . "</td>";
            echo "<td>" . htmlspecialchars($row['room_name']) . "</td>";
            echo "<td>" . htmlspecialchars($row['capacity']) . "</td>";
            echo "<td>" . htmlspecialchars($row['facility']) . "</td>";
            echo "<td>" . htmlspecialchars($row['short_description']) . "</td>";
            echo "<td>" . htmlspecialchars($row['detail_description']) . "</td>";
            echo "<td>" . $status . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "Tidak ada data ruangan.";
    }
} else {
    echo "Gagal mengambil data: " . mysqli_error($conn);
}

mysqli_close($conn);
?>