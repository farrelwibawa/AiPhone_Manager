<?php
session_start();

// Koneksi ke database
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "aiphone_db";

$conn = mysqli_connect($host, $user, $pass, $dbname);
if (!$conn) {
    die("Koneksi gagal: " . mysqli_connect_error());
}

$error = "";
$success = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = mysqli_real_escape_string($conn, $_POST["username"]);
    $password = mysqli_real_escape_string($conn, $_POST["password"]);

    // Query adjusted to match the admin table structure
    $query = "SELECT * FROM admin WHERE name='$name' AND password='$password'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $_SESSION["admin"] = $name;
        $success = true; // Set success flag to trigger modal
    } else {
        $error = "Nama admin atau password salah.";
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login Admin - AiPhone Manager</title>
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f8f8f8;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 30px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }

        .login-container h2 {
            font-size: 28px;
            color: #222;
            margin-bottom: 25px;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 15px;
        }

        .login-container button {
            width: 100%;
            padding: 12px;
            background-color: #005eff;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        .login-container button:hover {
            background-color: #0044cc;
        }

        .login-container p {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }

        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            z-index: 1000;
        }

        .modal-content {
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 350px;
            animation: fadeIn 0.3s ease-in-out;
        }

        .modal-content h3 {
            color: #005eff;
            font-size: 24px;
            margin-bottom: 15px;
        }

        .modal-content p {
            color: #333;
            font-size: 16px;
            margin-bottom: 0;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
    </style>
</head>
<body>

<div class="login-container">
    <h2>Login Admin</h2>
    <?php if ($error): ?>
        <p><?php echo $error; ?></p>
    <?php endif; ?>
    <form method="POST">
        <input type="text" name="username" placeholder="Nama Admin" required>
        <input type="password" name="password" placeholder="Kata Sandi" required>
        <button type="submit">Masuk</button>
    </form>
</div>

<?php if ($success): ?>
    <div id="successModal" class="modal">
        <div class="modal-content">
            <h3>Login Berhasil!</h3>
            <p>Anda akan diarahkan ke halaman admin...</p>
        </div>
    </div>
    <script>
        // Show login success modal
        document.getElementById("successModal").style.display = "flex";
        // Redirect after 2 seconds
        setTimeout(function() {
            window.location.href = "list_article.php";
        }, 2000);
    </script>
<?php endif; ?>

</body>
</html>