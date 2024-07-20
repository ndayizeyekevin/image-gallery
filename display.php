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
    // Fetch images for the current page
    $sql = "SELECT * FROM imagetable LIMIT :start, :limit";
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':start', $start, PDO::PARAM_INT);
    $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count total number of images
    $totalSql = "SELECT COUNT(*) AS total FROM imagetable";
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
    <title>Display Images</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="style.css"> <!-- Ensure you have your custom CSS file with styling -->
</head>
<body>
    <?php include 'nav.php'; ?>
    <div id="page-container">
        <div id="content-wrap">
            <div class="container mt-5">
                <h2 class="text-center">Uploaded Images</h2>
                <div class="row">
                    <?php foreach ($result as $row): ?>
                        <div class="col-md-3 mt-3">
                            <div class="card">
                                <img src="data:image/jpeg;base64,<?= base64_encode($row['Image']) ?>" class="card-img-top" alt="Image">
                                <div class="card-body">
                                    <p class="card-text"> <?= $row['Id'] ?></p>
                                    <form action="delete.php" method="post">
                                        <input type="hidden" name="image_id" value="<?= $row['Id'] ?>">
                                        <button type="submit" name="delete" onclick="return confirm('Are you sure you want to proceed?')" class="btn btn-danger">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <nav>
                    <ul class="pagination justify-content-center">
                        <?php if ($page > 1): ?>
                            <li class="page-item"><a class="page-link" href="display.php?page=<?= $page - 1 ?>">Previous</a></li>
                        <?php endif; ?>
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <li class="page-item <?= $i == $page ? 'active' : '' ?>"><a class="page-link" href="display.php?page=<?= $i ?>"><?= $i ?></a></li>
                        <?php endfor; ?>
                        <?php if ($page < $totalPages): ?>
                            <li class="page-item"><a class="page-link" href="display.php?page=<?= $page + 1 ?>">Next</a></li>
                        <?php endif; ?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>

    <?php include 'footer.php'; ?>
</body>
</html>
