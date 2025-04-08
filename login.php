<?php
session_start();
include 'config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = md5($_POST['password']);

    $sql = "SELECT * FROM users WHERE username='$username' AND password='$password'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $_SESSION['loggedin'] = true;
        header('Location: dashboard.php');
    } else {
        echo "Username atau password salah";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        body {
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
            background: url('img/bglogin.png') no-repeat center center fixed;
            background-size: cover;
        }

        .login-container {
            display: flex;
            height: 100vh;
        }

        .image-section {
            flex: 1;
            background: url('img/bglogin1.jpg') no-repeat center center;
            background-size: cover;
        }

        .form-section {
            flex: 1;
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding: 50px;
        }

        .form-section h2 {
            font-size: 80px;
            font-weight: bold;
            font-style: italic;
            margin-bottom: 20px;
            text-align: center;
            color: #FFFFFF;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }
        .form-section h3 {
            font-size: 80px;
            font-weight: bold;
            font-style: italic;
            margin-bottom: 20px;
            text-align: center;
            color: #FFFFFF;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .form-section p {
            font-size: 21px;
            margin-bottom: 40px;
            font-style: italic;
            text-align: center;
            color: #FFFFFF;
            text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.5);
        }

        .form-group label {
            font-weight: bold;
        }

        .btn-primary {
            background-color: #4A148C;
            border: none;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
        }

        .btn-primary:hover {
            background-color: rgba(105, 27, 154, 0);
        }

        .form-control {
            background-color: #fff;
            border: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }

        .image-section img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="image-section">
            <img src="img/bglogin1.jpg">
        </div>
        <div class="form-section">
            <h2>Welcome</h2> 
               <h3> Mail Admin!</h3>
            <p>If you are an admin, please login first!</p>
            <form method="post">
                <div class="form-group">
                    <label for="username">Username</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-user"></i></span>
                        </div>
                        <input type="text" class="form-control" id="username" name="username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="password">Password</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        </div>
                        <input type="password" class="form-control" id="password" name="password" required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary btn-block">Login Sekarang!</button>
            </form>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>
