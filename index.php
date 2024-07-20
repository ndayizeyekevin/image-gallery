<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit'])) {
    $userId = $_SESSION['user_id'];

    $file = $_FILES['file'];
    $fileName = $file['name'];
    $fileTmpName = $file['tmp_name'];

    $imageData = file_get_contents($fileTmpName);

    try {
        $sql = "INSERT INTO imagetable (Image, UserId) VALUES (:imageData, :userId)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':imageData', $imageData, PDO::PARAM_LOB);
        $stmt->bindParam(':userId', $userId, PDO::PARAM_INT);

        if ($stmt->execute()) {
            $successMsg = "Image uploaded successfully.";
        } else {
            $errorMsg = "Error uploading image.";
        }
    } catch (PDOException $e) {
        $errorMsg = "Error uploading image: " . $e->getMessage();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Image Upload</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div class="container">
        <div class="row justify-content-center mt-5">
            <div class="col-md-6">
                <?php if (isset($successMsg)): ?>
                    <div class="alert alert-success" role="alert">
                        <?= $successMsg ?>
                    </div>
                <?php endif; ?>
                <?php if (isset($errorMsg)): ?>
                    <div class="alert alert-danger" role="alert">
                        <?= $errorMsg ?>
                    </div>
                <?php endif; ?>
                <div class="card">
                    <div class="card-header">
                        Upload Image
                    </div>
                    <div class="card-body">
                        <form action="index.php" method="post" enctype="multipart/form-data">
                            <div class="form-group">
                                <label for="file">Choose image to upload:</label>
                                <input type="file" name="file" id="file" class="form-control-file">
                            </div>
                            <button type="submit" name="submit" class="btn btn-primary">Upload Image</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
<footer id="footer" class="footer py-5 bg-dark text-white">
    <div class="container text-center">
        <span class="text-muted text-light">© 2024 Group8. All rights reserved.</span>
        <p>Designed and developed by Kevin</p>
    </div>
</footer>
</body>
</html>
