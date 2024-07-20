<?php
session_start();
if (!isset($_SESSION['username'])) {
    header('Location: login.php');
    exit();
}

require_once 'db_config.php';

$userId = $_SESSION['user_id'];

// Pagination
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$limit = 8; // Number of images per page
$start = ($page - 1) * $limit;

try {
    // Fetch images from backup table for the current page
    $sql = "SELECT * FROM imagebackuptable ORDER BY ImageId LIMIT :start, :limit";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total number of backup images
    $totalSql = "SELECT COUNT(*) AS total FROM imagebackuptable";
    $totalStmt = $conn->query($totalSql);
    $totalImages = $totalStmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Calculate total pages
    $totalPages = ceil($totalImages / $limit);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Display Backup Images</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <?php include 'nav.php'; ?>
    <div id="page-container">
        <div id="content-wrap">
            <div class="container mt-5">
                <h2 class="text-center">Backup Images</h2>
                <div class="row">
                    <?php foreach ($result as $row): ?>
                        <div class="col-md-3 mt-3">
                            <div class="card">
                                <img src="data:image/jpeg;base64,<?= base64_encode($row['Image']) ?>" class="card-img-top" alt="Image">
                                <div class="card-body">
                                    <p class="card-text"> <?= $row['ImageId'] ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <nav>
                <ul class="pagination justify-content-center">
                    <?php if ($page > 1): ?>
                        <li class="page-item"><a class="page-link" href="display_backup.php?page=<?= $page - 1 ?>">Previous</a></li>
                    <?php endif; ?>
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="display_backup.php?page=<?= $i ?>"><?= $i ?></a></li>
                    <?php endfor; ?>
                    <?php if ($page < $totalPages): ?>
                        <li class="page-item"><a class="page-link" href="display_backup.php?page=<?= $page + 1 ?>">Next</a></li>
                    <?php endif; ?>
                </ul>
            </nav>
            </div>
        </div>
    </div>
    <?php include 'footer.php'; ?>
</body>
</html>
