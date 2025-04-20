<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}
?>

<!doctype html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">

    <title>GPPS Agape Database</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #007bff, #0056b3);
            height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .custom-container {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            padding: 30px;
            border-radius: 15px;
            text-align: center;
            width: 90%;
            max-width: 600px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.3);
        }

        .custom-container h1 {
            font-size: 36px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .custom-container h3 {
            font-size: 18px;
            margin-bottom: 20px;
        }

        .custom-container .btn {
            margin: 10px;
            padding: 10px 20px;
            font-size: 16px;
            border-radius: 8px;
            transition: transform 0.3s;
        }

        .custom-container .btn:hover {
            transform: scale(1.1);
        }

        .custom-container .btn-success {
            border: none;
        }

        .custom-container .btn-warning {
            border: none;
        }
    </style>
  </head>
  <body>
    <div class="custom-container">
        <h3>Welcome to</h3>
        <h1>GPPS AGAPE DATABASE</h1>
        <div class="mt-4">
            <a href="jemaat_list.php" class="btn btn-outline-info ">Data Jemaat</a>
            <a href="absensi_list.php" class="btn btn-outline-info">Absensi Jemaat</a>
        </div>
    </div>
  </body>
</html>
