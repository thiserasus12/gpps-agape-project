<?php
include '../includes/db.php';

$id = $_GET['id'] ?? null;

if (!$id) {
    echo "ID tidak ditemukan.";
    exit;
}

try {
    // Ambil data jemaat berdasarkan ID
    $stmt = $conn->prepare("SELECT * FROM jemaat WHERE id = ?");
    $stmt->execute([$id]);
    $jemaat = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$jemaat) {
        echo "Data jemaat tidak ditemukan.";
        exit;
    }
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
    exit;
}
?>

<html lang="id">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Kartu Keanggotaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Pacifico&amp;display=swap" rel="stylesheet"/>
    <style>
        .card-container {
            width: 8.56cm;
            height: 10cm;
            background: white;
            padding: 10px;
            border-radius: 8px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }

        @media print {
            
            
            #card {
                position: absolute;
                left: 0;
                top: 0;
                width: 8.56cm;
                height: 10cm;
            }
        }
    </style>
</head>
<body class="bg-gray-200 flex flex-col items-center justify-center min-h-screen">
    <div id="card" class="card-container">
        <div class="relative">
            <div class="bg-gradient-to-r from-teal-400 to-blue-500 rounded-t-lg p-2 text-white flex justify-between items-center">
                <h1 class="font-pacifico text-sm">Kartu Keanggotaan</h1>
                <img src="logo.jpg" alt="GPPS AGAPE logo" class="w-12 h-7"/>
            </div>
            <div class="p-2 flex items-center">
                <div class="flex-1">
                    <h2 class="text-sm font-bold"><?php echo htmlspecialchars($jemaat['nama_anggota']); ?></h2>
                    <p class="text-gray-600 text-xs"><?php echo htmlspecialchars($jemaat['nomor_anggota']); ?></p>
                    <img src="generate_qr.php?id=<?php echo $jemaat['id']; ?>" alt="QR Code" class="w-20 h-20 mt-2"/>
                </div>
                <img src="uploads/<?php echo htmlspecialchars($jemaat['image']); ?>" alt="Profile picture" class="w-16 h-16 rounded-full border-2 border-gray-300"/>
            </div>
        </div>
        <div class="bg-gradient-to-r from-teal-400 to-blue-500 rounded-b-lg p-2 mt-2">
            <div class="bg-black h-4 mb-2"></div>
            <ol class="text-white text-xs list-decimal list-inside">
                <li>Pemegang kartu ini adalah jemaat Gereja Pantekosta Pusat Surabaya AGAPE.</li>
                <br>
                <li>Bila menemukan kartu ini, mohon kembalikan ke alamat:
                    <strong>Jl. Pagarsih 136, Bandung.</strong>
                </li>
                <br>
                <li>Pemegang kartu ini berhak menerima beragam benefit di GPPS Agape.</li>
            </ol>
        </div>
    </div>
    
    <div class="mt-4 flex gap-2">
        <button id="downloadBtn" class="px-4 py-2 bg-blue-500 text-white rounded-lg shadow hover:bg-blue-600">
            <i class="fas fa-download"></i> Download Card
        </button>

        <button id="printBtn" class="px-4 py-2 bg-green-500 text-white rounded-lg shadow hover:bg-green-600">
            <i class="fas fa-print"></i> Print Card
        </button>
    </div>

    <script>
        document.getElementById("downloadBtn").addEventListener("click", function () {
            html2canvas(document.getElementById("card"), {
                scale: 8,
                useCORS: true,
                backgroundColor: "#ffffff"
            }).then(canvas => {
                let link = document.createElement("a");
                link.href = canvas.toDataURL("image/png");
                link.download = "kartu_keanggotaan.png";
                link.click();
            });
        });

        document.getElementById("printBtn").addEventListener("click", function () {
        let printContent = document.getElementById("card").outerHTML;
        let originalContent = document.body.innerHTML;

        document.body.innerHTML = printContent;
        window.print();
        document.body.innerHTML = originalContent;
        location.reload(); // Reload agar halaman kembali normal
    });
    </script>
</body>
</html>
