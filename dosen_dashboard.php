<?php
session_start();
include 'db.php'; // Memasukkan koneksi database

// Cek apakah pengguna adalah dosen
if ($_SESSION['role'] !== 'dosen') {
    header("Location: login.php");
    exit();
}

// Ambil data nilai mahasiswa
$grades = $conn->query("SELECT grades.id, grades.course_name, grades.grade, users.username AS mahasiswa_name
                        FROM grades
                        JOIN users ON grades.mahasiswa_id = users.id");

// Proses input nilai
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $mahasiswa_id = $_POST['mahasiswa_id'];
    $course_name = $_POST['course_name'];
    $grade = $_POST['grade'];

    $query = $conn->prepare("REPLACE INTO grades (mahasiswa_id, dosen_id, course_name, grade) VALUES (?, ?, ?, ?)");
    $query->bind_param("iisd", $mahasiswa_id, $_SESSION['user_id'], $course_name, $grade);
    $query->execute();
    header("Location: dosen_dashboard.php");
}

// Ambil data mahasiswa
$mahasiswa = $conn->query("SELECT * FROM users WHERE role = 'mahasiswa'");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dosen Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
    <div class="container mx-auto">
        <h2 class="text-4xl font-bold mb-6 text-center text-purple-600">Dosen Dashboard</h2>
        
        <!-- Tautan Logout -->
        <div class="text-right mb-4">
            <a href="logout.php" class="text-red-500 hover:text-red-700">Logout</a>
        </div>

        <form method="POST" class="mb-6 bg-white p-6 rounded-lg shadow-md">
            <h3 class="text-2xl font-semibold mb-4 text-purple-600">Input/Update Nilai Mahasiswa</h3>
            <select name="mahasiswa_id" class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-400" required>
                <option value="">Pilih Mahasiswa</option>
                <?php while ($mhs = $mahasiswa->fetch_assoc()): ?>
                    <option value="<?php echo $mhs['id']; ?>"><?php echo $mhs['username']; ?></option>
                <?php endwhile; ?>
            </select>
            <input type="text" name="course_name" placeholder="Mata Kuliah" class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-400" required>
            <input type="number" step="0.01" name="grade" placeholder="Nilai" class="w-full p-3 border border-gray-300 rounded mb-4 focus:outline-none focus:ring-2 focus:ring-purple-400" required>
            <button type="submit" class="bg-green-500 text-white w-full p-3 rounded hover:bg-green-600 transition duration-200">Simpan Nilai</button>
        </form>

        <h3 class="text-2xl font-semibold mb-4 text-purple-600">Daftar Nilai Mahasiswa</h3>
        <table class="min-w-full bg-white rounded-lg shadow-md">
            <thead class="bg-gray-200">
                <tr>
                    <th class="py-3 px-4 border">ID</th>
                    <th class="py-3 px-4 border">Mahasiswa</th>
                    <th class="py-3 px-4 border">Mata Kuliah</th>
                    <th class="py-3 px-4 border">Nilai</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $grades->fetch_assoc()): ?>
                    <tr class="hover:bg-gray-100 transition duration-200">
                        <td class="py-2 px-4 border"><?php echo $row['id']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $row['mahasiswa_name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $row['course_name']; ?></td>
                        <td class="py-2 px-4 border"><?php echo $row['grade']; ?></td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    <script src="assets/main.js"></script>
</body>
</html>
