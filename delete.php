<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'db_config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete'])) {
    $imageId = $_POST['image_id'];

    try {
        // Start transaction
        $conn->beginTransaction();

        // Move image to backup table
        $moveSql = "INSERT INTO imagebackuptable (Image, UserId, ImageId) SELECT Image, UserId, Id FROM imagetable WHERE Id = :imageId";
        $moveStmt = $conn->prepare($moveSql);
        $moveStmt->bindParam(':imageId', $imageId, PDO::PARAM_INT);

        if ($moveStmt->execute()) {
            // Delete image from main table
            $deleteSql = "DELETE FROM imagetable WHERE Id = :imageId";
            $deleteStmt = $conn->prepare($deleteSql);
            $deleteStmt->bindParam(':imageId', $imageId, PDO::PARAM_INT);

            if ($deleteStmt->execute()) {
                $successMsg = "Image deleted successfully.";
                $conn->commit();
            } else {
                $conn->rollBack();
                $errorMsg = "Error deleting image.";
            }
        } else {
            $conn->rollBack();
            $errorMsg = "Error moving image to backup.";
        }
    } catch (PDOException $e) {
        $conn->rollBack();
        $errorMsg = "Error: " . $e->getMessage();
    }

    // Close statements
    $moveStmt = null;
    $deleteStmt = null;
}

header('Location: display.php');
exit();
?>
