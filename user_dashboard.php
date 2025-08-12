<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
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

// Handle search and filter
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$type_filter = isset($_GET['type']) ? $_GET['type'] : '';
$format_filter = isset($_GET['format']) ? $_GET['format'] : '';

// Build query based on filters
$sql = "SELECT r.*, u.fullname as uploader_name FROM resources r JOIN users u ON r.uploaded_by = u.user_id WHERE 1=1";
$params = [];

if (!empty($search)) {
     $sql .= " AND (r.title LIKE ? OR r.author LIKE ?)";
     $params[] = "%$search%";
     $params[] = "%$search%";
}

if (!empty($type_filter)) {
     $sql .= " AND r.type = ?";
     $params[] = $type_filter;
}

if (!empty($format_filter)) {
     $sql .= " AND r.format = ?";
     $params[] = $format_filter;
}

$sql .= " ORDER BY r.uploaded_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$resources = $stmt->fetchAll();

// Fetch user's borrowing history
$stmt = $pdo->prepare("SELECT br.*, r.title, r.author FROM borrow_records br JOIN resources r ON br.resource_id = r.resource_id WHERE br.user_id = ? ORDER BY br.borrowed_at DESC");
$stmt->execute([$_SESSION['user_id']]);
$borrow_history = $stmt->fetchAll();

// Get unique types and formats for filter dropdowns
$type_stmt = $pdo->query("SELECT DISTINCT type FROM resources ORDER BY type");
$types = $type_stmt->fetchAll(PDO::FETCH_COLUMN);

$format_stmt = $pdo->query("SELECT DISTINCT format FROM resources ORDER BY format");
$formats = $format_stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="en">

<head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>User Dashboard - SOT E-Library</title>
     <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
     <style>
          * {
               margin: 0;
               padding: 0;
               box-sizing: border-box;
               font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
          }

          body {
               background: #f5f7fa;
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
               text-decoration: none;
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

          /* Search and Filter Styles */
          .search-filter-container {
               background: #f8f9fa;
               padding: 20px;
               border-radius: 8px;
               margin-bottom: 25px;
          }

          .search-filter-form {
               display: flex;
               flex-wrap: wrap;
               gap: 15px;
               align-items: end;
          }

          .form-group {
               flex: 1;
               min-width: 200px;
          }

          label {
               display: block;
               margin-bottom: 5px;
               font-weight: 500;
               color: #555;
          }

          input[type="text"],
          select {
               width: 100%;
               padding: 10px;
               border: 1px solid #ddd;
               border-radius: 5px;
               font-size: 14px;
          }

          .btn {
               background: #667eea;
               color: white;
               border: none;
               padding: 10px 20px;
               border-radius: 5px;
               cursor: pointer;
               font-size: 14px;
               transition: background 0.3s;
               height: fit-content;
          }

          .btn:hover {
               background: #5a6fd8;
          }

          .btn-secondary {
               background: #6c757d;
          }

          .btn-secondary:hover {
               background: #5a6268;
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

          .borrow-btn {
               background: #667eea;
               color: white;
          }

          .action-btn:hover {
               opacity: 0.9;
          }

          .history-table {
               width: 100%;
               border-collapse: collapse;
          }

          .history-table th,
          .history-table td {
               padding: 12px;
               text-align: left;
               border-bottom: 1px solid #eee;
          }

          .history-table th {
               background: #f8f9fa;
               font-weight: 600;
          }

          .status-badge {
               padding: 3px 8px;
               border-radius: 3px;
               font-size: 0.8em;
               font-weight: 500;
          }

          .status-returned {
               background: #d4edda;
               color: #155724;
          }

          .status-borrowed {
               background: #cce5ff;
               color: #004085;
          }

          .results-info {
               margin-bottom: 20px;
               color: #666;
               font-style: italic;
          }
     </style>
</head>

<body>
     <div class="header">
          <div class="logo">
               <h1><i class="fas fa-book"></i> SOT E-Library</h1>
          </div>
          <div class="user-info">
               <span>Welcome, <?php echo htmlspecialchars($_SESSION['fullname']); ?></span>
               <a href="logout.php" class="logout-btn"><i class="fas fa-sign-out-alt"></i> Logout</a>
          </div>
     </div>

     <div class="container">
          <div class="sidebar">
               <ul class="sidebar-menu">
                    <li><a href="#" class="active"><i class="fas fa-home"></i> Dashboard</a></li>
                    <li><a href="#"><i class="fas fa-search"></i> Search Resources</a></li>
                    <li><a href="#"><i class="fas fa-history"></i> Borrowing History</a></li>
                    <li><a href="#"><i class="fas fa-user"></i> My Profile</a></li>
               </ul>
          </div>

          <div class="main-content">
               <!-- Search and Filter Section -->
               <div class="section">
                    <h2 class="section-title"><i class="fas fa-filter"></i> Search & Filter Resources</h2>

                    <div class="search-filter-container">
                         <form method="GET" class="search-filter-form">
                              <div class="form-group">
                                   <label for="search"><i class="fas fa-search"></i> Search (Title/Author):</label>
                                   <input type="text" id="search" name="search"
                                        value="<?php echo htmlspecialchars($search); ?>"
                                        placeholder="Enter title or author...">
                              </div>

                              <div class="form-group">
                                   <label for="type"><i class="fas fa-book"></i> Type:</label>
                                   <select id="type" name="type">
                                        <option value="">All Types</option>
                                        <?php foreach ($types as $type): ?>
                                             <option value="<?php echo htmlspecialchars($type); ?>" <?php echo ($type_filter == $type) ? 'selected' : ''; ?>>
                                                  <?php echo ucfirst(htmlspecialchars($type)); ?>
                                             </option>
                                        <?php endforeach; ?>
                                   </select>
                              </div>

                              <div class="form-group">
                                   <label for="format"><i class="fas fa-file"></i> Format:</label>
                                   <select id="format" name="format">
                                        <option value="">All Formats</option>
                                        <?php foreach ($formats as $format): ?>
                                             <option value="<?php echo htmlspecialchars($format); ?>" <?php echo ($format_filter == $format) ? 'selected' : ''; ?>>
                                                  <?php echo htmlspecialchars($format); ?>
                                             </option>
                                        <?php endforeach; ?>
                                   </select>
                              </div>

                              <button type="submit" class="btn">
                                   <i class="fas fa-search"></i> Search
                              </button>

                              <?php if (!empty($search) || !empty($type_filter) || !empty($format_filter)): ?>
                                   <a href="user_dashboard.php" class="btn btn-secondary">
                                        <i class="fas fa-times"></i> Clear Filters
                                   </a>
                              <?php endif; ?>
                         </form>
                    </div>

                    <div class="results-info">
                         <?php
                         $result_count = count($resources);
                         if (!empty($search) || !empty($type_filter) || !empty($format_filter)):
                              echo "Found <strong>$result_count</strong> resource(s) matching your criteria.";
                         else:
                              echo "Showing all <strong>$result_count</strong> available resources.";
                         endif;
                         ?>
                    </div>
               </div>

               <!-- Available Resources Section -->
               <div class="section">
                    <h2 class="section-title"><i class="fas fa-book-open"></i> Available Resources</h2>

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
                                        <a href="#" class="action-btn borrow-btn"
                                             onclick="alert('Borrow functionality will be implemented in next phase')">
                                             <i class="fas fa-download"></i> Borrow
                                        </a>
                                   </div>
                              </div>
                         <?php endforeach; ?>

                         <?php if (empty($resources)): ?>
                              <div style="grid-column: 1 / -1; text-align: center; padding: 40px;">
                                   <i class="fas fa-book-open" style="font-size: 3em; color: #ccc; margin-bottom: 20px;"></i>
                                   <h3>No resources found</h3>
                                   <p>
                                        <?php
                                        if (!empty($search) || !empty($type_filter) || !empty($format_filter)):
                                             echo "Try adjusting your search criteria or filters.";
                                        else:
                                             echo "No resources available yet. Check back later!";
                                        endif;
                                        ?>
                                   </p>
                              </div>
                         <?php endif; ?>
                    </div>
               </div>

               <!-- Borrowing History Section -->
               <div class="section">
                    <h2 class="section-title"><i class="fas fa-history"></i> My Borrowing History</h2>

                    <?php if (!empty($borrow_history)): ?>
                         <table class="history-table">
                              <thead>
                                   <tr>
                                        <th>Title</th>
                                        <th>Author</th>
                                        <th>Borrowed Date</th>
                                        <th>Due Date</th>
                                        <th>Status</th>
                                   </tr>
                              </thead>
                              <tbody>
                                   <?php foreach ($borrow_history as $record): ?>
                                        <tr>
                                             <td><?php echo htmlspecialchars($record['title']); ?></td>
                                             <td><?php echo htmlspecialchars($record['author']); ?></td>
                                             <td><?php echo date('M j, Y', strtotime($record['borrowed_at'])); ?></td>
                                             <td><?php echo date('M j, Y', strtotime($record['due_date'])); ?></td>
                                             <td>
                                                  <?php if ($record['returned_at']): ?>
                                                       <span class="status-badge status-returned">Returned</span>
                                                  <?php else: ?>
                                                       <span class="status-badge status-borrowed">Borrowed</span>
                                                  <?php endif; ?>
                                             </td>
                                        </tr>
                                   <?php endforeach; ?>
                              </tbody>
                         </table>
                    <?php else: ?>
                         <p>You haven't borrowed any resources yet.</p>
                    <?php endif; ?>
               </div>
          </div>
     </div>
</body>

</html>