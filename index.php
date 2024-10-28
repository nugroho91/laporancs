<?php
include 'db.php';

// Menangani pengiriman form untuk menambah laporan
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $nama = htmlspecialchars(trim($_POST['nama']));
    $kontak = htmlspecialchars(trim($_POST['kontak']));
    $tanggal_masuk = htmlspecialchars(trim($_POST['tanggal_masuk']));
    $jenis_permasalahan = htmlspecialchars(trim($_POST['jenis_permasalahan']));
    $provinsi = htmlspecialchars(trim($_POST['provinsi']));
    $kota_kabupaten = htmlspecialchars(trim($_POST['kota_kabupaten']));
    $deskripsi = htmlspecialchars(trim($_POST['deskripsi']));
    $status = 'Belum Ditangani';

    $sql = "INSERT INTO laporan (nama, kontak, tanggal_masuk, jenis_permasalahan, provinsi, kota_kabupaten, deskripsi, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nama, $kontak, $tanggal_masuk, $jenis_permasalahan, $provinsi, $kota_kabupaten, $deskripsi, $status]);
}

// Menangani pengiriman form untuk mengubah status
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['update'])) {
    $id = htmlspecialchars(trim($_POST['id']));
    $status = htmlspecialchars(trim($_POST['status']));

    $sql = "UPDATE laporan SET status = ? WHERE id = ?";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$status, $id]);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Permasalahan Customer Service</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1 class="text-center">Laporan Permasalahan Customer Service</h1>

        <form method="POST" class="mt-4">
            <input type="hidden" name="add" value="1">
            <div class="form-group">
                <input type="text" name="nama" class="form-control" placeholder="Nama Pelapor" required>
            </div>
            <div class="form-group">
                <input type="text" name="kontak" class="form-control" placeholder="Kontak Pelapor" required>
            </div>
            <div class="form-group">
                <input type="date" name="tanggal_masuk" class="form-control" required>
            </div>
            <div class="form-group">
                <select name="jenis_permasalahan" class="form-control" required>
                    <option value="">Pilih Jenis Permasalahan</option>
                    <option value="Aplikasi">Aplikasi</option>
                    <option value="Dashboard">Dashboard</option>
                    <option value="Pertanyaan Umum">Pertanyaan Umum/lainnya</option>
                </select>
            </div>
            <div class="form-group">
                <input type="text" name="provinsi" class="form-control" placeholder="Provinsi" required>
            </div>
            <div class="form-group">
                <input type="text" name="kota_kabupaten" class="form-control" placeholder="Kota/Kabupaten" required>
            </div>
            <div class="form-group">
                <textarea name="deskripsi" class="form-control" placeholder="Deskripsi Permasalahan" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Tambah Laporan</button>
        </form>

        <h2 class="mt-5">Daftar Laporan</h2>
        <table class="table table-bordered mt-3">
            <thead class="thead-light">
                <tr>
                    <th>ID</th>
                    <th>Nama</th>
                    <th>Kontak</th>
                    <th>Tanggal Masuk</th>
                    <th>Jenis Permasalahan</th>
                    <th>Provinsi</th>
                    <th>Kota/Kabupaten</th>
                    <th>Deskripsi</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $sql = "SELECT * FROM laporan";
                $stmt = $pdo->query($sql);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                    echo "<tr>
                        <td>" . htmlspecialchars($row['id']) . "</td>
                        <td>" . htmlspecialchars($row['nama']) . "</td>
                        <td>" . htmlspecialchars($row['kontak']) . "</td>
                        <td>" . htmlspecialchars($row['tanggal_masuk']) . "</td>
                        <td>" . htmlspecialchars($row['jenis_permasalahan']) . "</td>
                        <td>" . htmlspecialchars($row['provinsi']) . "</td>
                        <td>" . htmlspecialchars($row['kota_kabupaten']) . "</td>
                        <td>" . htmlspecialchars($row['deskripsi']) . "</td>
                        <td>" . htmlspecialchars($row['status']) . "</td>
                        <td>";
                    
                    // Jika status adalah "Selesai", tampilkan "Sukses"
                    if ($row['status'] == 'Selesai') {
                        echo "<span class='badge badge-success'>Sukses</span>";
                    } else {
                        echo "<button class='btn btn-warning' data-toggle='modal' data-target='#updateModal' data-id='" . htmlspecialchars($row['id']) . "' data-status='" . htmlspecialchars($row['status']) . "'>Ubah Status</button>";
                    }
                    
                    echo "</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>

    <!-- Modal untuk mengubah status -->
    <div class="modal fade" id="updateModal" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="updateModalLabel">Ubah Status</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <form method="POST">
                    <input type="hidden" name="update" value="1">
                    <input type="hidden" name="id" id="status-id">
                    <div class="modal-body">
                        <div class="form-group">
                            <select name="status" id="status-select" class="form-control" required>
                                <option value="">Pilih Status</option>
                                <option value="Belum Ditangani">Belum Ditangani</option>
                                <option value="Sedang Diproses">Sedang Diproses</option>
                                <option value="Selesai">Selesai</option>
                            </select>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Tutup</button>
                        <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS and dependencies -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Script untuk mengisi data modal dengan informasi status yang dipilih
        $('#updateModal').on('show.bs.modal', function (event) {
            var button = $(event.relatedTarget); // Tombol yang memicu modal
            var id = button.data('id'); // Mendapatkan ID dari tombol
            var status = button.data('status'); // Mendapatkan status dari tombol

            var modal = $(this);
            modal.find('#status-id').val(id); // Mengisi input ID
            modal.find('#status-select').val(status); // Mengisi input status
        });
    </script>
</body>
</html>
