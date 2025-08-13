<?php
session_start();

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
     header("Location: index.php");
     exit();
}

// Database connection
$host = 'localhost';
$dbname = 'sot_e_library';
$username = 'root'; // Change if needed
$password = '';     // Change if needed

try {
     $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
     die("Database connection failed: " . $e->getMessage());
}

// Handle resource upload
$upload_message = "";
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['upload_resource'])) {
     $title = trim($_POST['title']);
     $author = trim($_POST['author']);
     $type = $_POST['type'];
     $format = $_POST['format'];

     // File upload handling
     if (isset($_FILES['resource_file']) && $_FILES['resource_file']['error'] == 0) {
          $upload_dir = 'uploads/';
          if (!is_dir($upload_dir)) {
               mkdir($upload_dir, 0777, true);
          }

          $file_name = uniqid() . '_' . basename($_FILES['resource_file']['name']);
          $target_file = $upload_dir . $file_name;

          // Check file type
          $allowed_types = ['pdf', 'epub', 'docx'];
          $file_extension = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

          if (in_array($file_extension, $allowed_types)) {
               if (move_uploaded_file($_FILES['resource_file']['tmp_name'], $target_file)) {
                    // Insert into database
                    $stmt = $pdo->prepare("INSERT INTO resources (title, author, type, format, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?, ?)");
                    if ($stmt->execute([$title, $author, $type, $format, $target_file, $_SESSION['user_id']])) {
                         $upload_message = "Resource uploaded successfully!";
                    } else {
                         $upload_message = "Database error. Please try again.";
                    }
               } else {
                    $upload_message = "Error uploading file.";
               }
          } else {
               $upload_message = "Only PDF, DOCX and EPUB files are allowed.";
          }
     } else {
          $upload_message = "Please select a file to upload.";
     }
}

// Fetch all resources for display
$stmt = $pdo->query("SELECT r.*, u.fullname as uploader_name FROM resources r JOIN users u ON r.uploaded_by = u.user_id ORDER BY r.uploaded_at DESC");
$resources = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Admin Dashboard - SOT E-Library</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <style>
          * {
               margin: 0;
               padding: 0;
               box-sizing: border-box;
               font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          }

          body {
               background: #5f6b7cff;
               color: #333;
          }

          .header {
               background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
               color: white;
               padding: 20px 40px;
               display: flex;
               justify-content: space-between;
               align-items: center;
               box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
          }

          .logo h1 {
               font-size: 1.8em;
          }

          .user-info {
               display: flex;
               align-items: center;
               gap: 15px;
          }

          .logout-btn {
               background: rgba(255, 255, 255, 0.2);
               color: white;
               border: 1px solid rgba(255, 255, 255, 0.3);
               padding: 8px 15px;
               border-radius: 5px;
               cursor: pointer;
               transition: background 0.3s;
          }

          .logout-btn:hover {
               background: rgba(255, 255, 255, 0.3);
          }

          .container {
               display: flex;
               min-height: calc(100vh - 70px);
          }

          .sidebar {
               width: 250px;
               background: white;
               padding: 20px 0;
               box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
          }

          .sidebar-menu {
               list-style: none;
          }

          .sidebar-menu li {
               margin-bottom: 5px;
          }

          .sidebar-menu a {
               display: flex;
               align-items: center;
               padding: 12px 20px;
               color: #555;
               text-decoration: none;
               transition: all 0.3s;
               gap: 10px;
          }

          .sidebar-menu a:hover,
          .sidebar-menu a.active {
               background: #667eea;
               color: white;
          }

          .main-content {
               flex: 1;
               padding: 30px;
          }

          .section {
               background: white;
               border-radius: 10px;
               padding: 25px;
               margin-bottom: 30px;
               box-shadow: 0 2px 10px rgba(0, 0, 0, 0.05);
          }

          .section-title {
               font-size: 1.5em;
               margin-bottom: 20px;
               color: #333;
               display: flex;
               align-items: center;
               gap: 10px;
          }

          .form-group {
               margin-bottom: 20px;
          }

          label {
               display: block;
               margin-bottom: 5px;
               font-weight: 500;
               color: #555;
          }

          input[type="text"],
          input[type="file"],
          select {
               width: 100%;
               padding: 12px;
               border: 1px solid #ddd;
               border-radius: 5px;
               font-size: 16px;
          }

          .btn {
               background: #667eea;
               color: white;
               border: none;
               padding: 12px 25px;
               border-radius: 5px;
               cursor: pointer;
               font-size: 16px;
               transition: background 0.3s;
          }

          .btn:hover {
               background: #5a6fd8;
          }

          .message {
               padding: 10px;
               border-radius: 5px;
               margin-bottom: 20px;
          }

          .message.success {
               background: #d4edda;
               color: #155724;
               border: 1px solid #c3e6cb;
          }

          .message.error {
               background: #f8d7da;
               color: #721c24;
               border: 1px solid #f5c6cb;
          }

          .resources-grid {
               display: grid;
               grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
               gap: 20px;
          }

          .resource-card {
               border: 1px solid #eee;
               border-radius: 8px;
               padding: 20px;
               background: white;
               box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
               transition: transform 0.3s, box-shadow 0.3s;
          }

          .resource-card:hover {
               transform: translateY(-5px);
               box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
          }

          .resource-title {
               font-size: 1.2em;
               font-weight: 600;
               margin-bottom: 10px;
               color: #333;
          }

          .resource-meta {
               color: #666;
               font-size: 0.9em;
               margin-bottom: 15px;
          }

          .resource-type {
               display: inline-block;
               padding: 3px 8px;
               border-radius: 3px;
               font-size: 0.8em;
               font-weight: 500;
               background: #667eea;
               color: white;
          }

          .resource-actions {
               display: flex;
               gap: 10px;
               margin-top: 15px;
          }

          .action-btn {
               padding: 6px 12px;
               border-radius: 3px;
               text-decoration: none;
               font-size: 0.9em;
               transition: background 0.3s;
          }

          .view-btn {
               background: #28a745;
               color: white;
          }

          .delete-btn {
               background: #dc3545;
               color: white;
          }

          .action-btn:hover {
               opacity: 0.9;
          }
     </style>
</head>

<body>
     <div class="header">
          <div class="logo">
               <h1><i class="fas fa-book"></i> SOT E-Library Admin</h1>
          </div>
          <div class="user-info">
               <span>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?> (Admin)</span>
               <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
     </div>

     <div class="container">
          <div class="sidebar">
               <ul class="sidebar-menu">
                    <li><a href="#" class="active"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                    <li><a href="#"><i class="fas fa-book"></i> Manage Resources</a></li>
                    <li><a href="#"><i class="fas fa-users"></i> User Management</a></li>
                    <li><a href="#"><i class="fas fa-chart-bar"></i> Reports</a></li>
               </ul>
          </div>

          <div class="main-content">
               <!-- Upload Section -->
               <div class="section">
                    <h2 class="section-title"><i class="fas fa-cloud-upload-alt"></i> Upload New Resource</h2>

                    <?php if (!empty($upload_message)): ?>
                         <div
                              class="message <?php echo strpos($upload_message, 'successfully') !== false ? 'success' : 'error'; ?>">
                              <?php echo htmlspecialchars($upload_message); ?>
                         </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data">
                         <div class="form-group">
                              <label for="title">Title:</label>
                              <input type="text" id="title" name="title" required>
                         </div>

                         <div class="form-group">
                              <label for="author">Author:</label>
                              <input type="text" id="author" name="author" required>
                         </div>

                         <div class="form-group">
                              <label for="type">Type:</label>
                              <select id="type" name="type" required>
                                   <option value="e-book">E-Book</option>
                                   <option value="journal">Journal</option>
                                   <option value="article">Article</option>
                              </select>
                         </div>

                         <div class="form-group">
                              <label for="format">Format:</label>
                              <select id="format" name="format" required>
                                   <option value="PDF">PDF</option>
                                   <option value="EPUB">EPUB</option>
                                   <option value="DOCX">DOCX</option>
                              </select>
                         </div>

                         <div class="form-group">
                              <label for="resource_file">File (PDF/EPUB/DOCX):</label>
                              <input type="file" id="resource_file" name="resource_file" accept=".pdf,.epub,.docx"
                                   required>
                         </div>

                         <button type="submit" name="upload_resource" class="btn">
                              <i class="fas fa-upload"></i> Upload Resource
                         </button>
                    </form>
               </div>

               <!-- Resources Section -->
               <div class="section">
                    <h2 class="section-title"><i class="fas fa-list"></i> All Resources</h2>

                    <div class="resources-grid">
                         <?php foreach ($resources as $resource): ?>
                              <div class="resource-card">
                                   <div class="resource-title"><?php echo htmlspecialchars($resource['title']); ?></div>
                                   <div class="resource-meta">
                                        <strong>Author:</strong> <?php echo htmlspecialchars($resource['author']); ?><br>
                                        <strong>Type:</strong> <span
                                             class="resource-type"><?php echo ucfirst($resource['type']); ?></span><br>
                                        <strong>Format:</strong> <?php echo $resource['format']; ?><br>
                                        <strong>Uploaded by:</strong>
                                        <?php echo htmlspecialchars($resource['uploader_name']); ?><br>
                                        <strong>Date:</strong>
                                        <?php echo date('M j, Y', strtotime($resource['uploaded_at'])); ?>
                                   </div>
                                   <div class="resource-actions">
                                        <a href="<?php echo htmlspecialchars($resource['file_path']); ?>"
                                             class="action-btn view-btn" target="_blank">
                                             <i class="fas fa-eye"></i> View
                                        </a>
                                        <a href="#" class="action-btn delete-btn"
                                             onclick="return confirm('Are you sure you want to delete this resource?')">
                                             <i class="fas fa-trash"></i> Delete
                                        </a>
                                   </div>
                              </div>
                         <?php endforeach; ?>

                         <?php if (empty($resources)): ?>
                              <p>No resources available yet. Upload your first resource above!</p>
                         <?php endif; ?>
                    </div>
               </div>
          </div>
     </div>
</body>

</html>