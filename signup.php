<?php
session_start();
if (isset($_SESSION['username'])) {
    header('Location: index.php');
    exit();
}

require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // Check if username already exists
        $checkSql = "SELECT COUNT(*) FROM users WHERE Username = :username";
        $checkStmt = $conn->prepare($checkSql);
        $checkStmt->bindParam(':username', $username, PDO::PARAM_STR);
        $checkStmt->execute();
        $userExists = $checkStmt->fetchColumn();

        if ($userExists) {
            $error = "Username already exists";
        } else {
            // Insert new user
            $sql = "INSERT INTO users (Username, Password) VALUES (:username, :password)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':username', $username, PDO::PARAM_STR);
            $stmt->bindParam(':password', $password, PDO::PARAM_STR);
            $stmt->execute();

            header('Location: login.php');
            exit();
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Signup</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header">
                        Signup
                    </div>
                    <div class="card-body">
                        <form action="signup.php" method="post">
                            <div class="form-group">
                                <label for="username">Username</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Password</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <?php if (isset($error)): ?>
                                <div class="alert alert-danger" role="alert">
                                    <?= $error ?>
                                </div>
                            <?php endif; ?>
                            <button type="submit" class="btn btn-primary">Signup</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
