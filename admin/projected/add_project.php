<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();
include 'db_conn.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

$user_id = $_SESSION['user_id'];
$error_message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Get form data
        $project_name = trim($_POST['project_name']);
        $project_description = trim($_POST['project_description']);
        $project_category = trim($_POST['project_category']);
        
        // Validate inputs
        if (empty($project_name) || empty($project_description) || empty($project_category)) {
            throw new Exception("All fields are required");
        }
        
        // Handle file upload
        $upload_dir = 'uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($upload_dir)) {
            if (!mkdir($upload_dir, 0777, true)) {
                throw new Exception("Failed to create uploads directory");
            }
        }
        
        // Check if file was uploaded
        if (!isset($_FILES['project_file']) || $_FILES['project_file']['error'] !== UPLOAD_ERR_OK) {
            $upload_errors = array(
                UPLOAD_ERR_INI_SIZE => 'File too large (server limit)',
                UPLOAD_ERR_FORM_SIZE => 'File too large (form limit)',
                UPLOAD_ERR_PARTIAL => 'File only partially uploaded',
                UPLOAD_ERR_NO_FILE => 'No file was uploaded',
                UPLOAD_ERR_NO_TMP_DIR => 'No temporary directory',
                UPLOAD_ERR_CANT_WRITE => 'Failed to write file to disk',
                UPLOAD_ERR_EXTENSION => 'File upload stopped by extension'
            );
            $error_code = $_FILES['project_file']['error'];
            throw new Exception($upload_errors[$error_code] ?? 'Unknown upload error');
        }
        
        // Get file info
        $file_name = basename($_FILES['project_file']['name']);
        $file_tmp = $_FILES['project_file']['tmp_name'];
        
        // Create unique filename to prevent overwrites
        $file_extension = pathinfo($file_name, PATHINFO_EXTENSION);
        $unique_filename = time() . '_' . uniqid() . '.' . $file_extension;
        $file_path = $upload_dir . $unique_filename;
        
        // Move uploaded file
        if (!move_uploaded_file($file_tmp, $file_path)) {
            throw new Exception("Failed to move uploaded file. Check folder permissions.");
        }
        
        // Insert into database
        $stmt = $conn->prepare("INSERT INTO projects (user_id, project_name, project_description, project_category, file_name, file_path, created_at) VALUES (?, ?, ?, ?, ?, ?, NOW())");
        
        if (!$stmt) {
            throw new Exception("Database prepare failed: " . $conn->error);
        }
        
        $stmt->bind_param("isssss", $user_id, $project_name, $project_description, $project_category, $file_name, $file_path);
        
        if (!$stmt->execute()) {
            throw new Exception("Database execution failed: " . $stmt->error);
        }
        
        $stmt->close();
        $conn->close();
        
        // Success - redirect to view projects
        header("Location: view_projects.php?success=1");
        exit();
        
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

if (isset($conn)) {
    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Project - junard Taoy</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 50%, #0f172a 100%);
            color: white;
            min-height: 100vh;
            position: relative;
            overflow-x: hidden;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                linear-gradient(90deg, rgba(59, 130, 246, 0.1) 1px, transparent 1px),
                linear-gradient(rgba(59, 130, 246, 0.1) 1px, transparent 1px);
            background-size: 50px 50px;
            opacity: 0.2;
            z-index: 0;
        }

        .container {
            position: relative;
            z-index: 1;
            max-width: 1400px;
            margin: 0 auto;
            padding: 0 24px;
        }

        header {
            background: rgba(15, 23, 42, 0.5);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid rgba(59, 130, 246, 0.2);
            padding: 16px 0;
        }

        nav {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
        }

        .logo {
            font-size: 24px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            gap: 24px;
            align-items: center;
            flex-wrap: wrap;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            transition: color 0.3s;
        }

        .nav-links a:hover {
            color: #60a5fa;
        }

        .logout-btn {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 6px;
            background: transparent;
            color: white;
            cursor: pointer;
            transition: background 0.3s;
            text-decoration: none;
        }

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .hero {
            text-align: center;
            padding: 48px 0;
        }

        .hero-box {
            background: rgba(30, 41, 59, 0.3);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 48px;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .hero-title {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 16px;
        }

        .plus-icon {
            background: white;
            color: #2563eb;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            font-weight: bold;
        }

        .hero h1 {
            font-size: 36px;
            margin: 0;
        }

        .hero p {
            color: #d1d5db;
            font-size: 18px;
        }

        .content {
            padding-bottom: 48px;
        }

        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .card {
            background: rgba(30, 41, 59, 0.4);
            backdrop-filter: blur(15px);
            border-radius: 16px;
            padding: 32px;
            border: 1px solid rgba(59, 130, 246, 0.2);
        }

        .card-header {
            display: flex;
            align-items: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .card-header h2 {
            font-size: 24px;
            font-weight: bold;
        }

        .error-alert {
            background: rgba(220, 38, 38, 0.2);
            border: 1px solid rgba(220, 38, 38, 0.5);
            border-radius: 8px;
            padding: 16px;
            margin-bottom: 24px;
            color: #fca5a5;
        }

        .form-group {
            margin-bottom: 24px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .required {
            color: #f87171;
        }

        input[type="text"],
        textarea,
        select,
        input[type="file"] {
            width: 100%;
            background: rgba(51, 65, 85, 0.5);
            border: 1px solid #475569;
            border-radius: 8px;
            padding: 12px 16px;
            color: white;
            font-size: 14px;
            transition: all 0.3s;
        }

        input[type="text"]:focus,
        textarea:focus,
        select:focus {
            outline: none;
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.3);
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        select option {
            background: #1e293b;
            color: white;
        }

        .submit-btn {
            width: 100%;
            background: #2563eb;
            color: white;
            font-weight: 600;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.3s;
            box-shadow: 0 4px 20px rgba(37, 99, 235, 0.3);
            font-size: 16px;
        }

        .submit-btn:hover {
            background: #1d4ed8;
        }

        .preview-box {
            background: rgba(15, 23, 42, 0.5);
            border-radius: 12px;
            padding: 32px;
            border: 1px solid #334155;
            text-align: center;
        }

        .category-badge {
            display: inline-block;
            background: #2563eb;
            color: white;
            font-size: 14px;
            padding: 4px 16px;
            border-radius: 20px;
            margin-bottom: 24px;
        }

        .preview-title {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 16px;
        }

        .preview-desc {
            color: #9ca3af;
            margin-bottom: 32px;
            min-height: 60px;
        }

        .preview-icons {
            display: flex;
            justify-content: center;
            gap: 16px;
        }

        .icon-box {
            background: rgba(51, 65, 85, 0.5);
            padding: 16px;
            border-radius: 8px;
        }

        .icon-square {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #3b82f6, #8b5cf6);
            border-radius: 4px;
        }

        .icon-circle {
            width: 48px;
            height: 48px;
            background: linear-gradient(135deg, #8b5cf6, #ec4899);
            border-radius: 50%;
        }

        small {
            display: block;
            margin-top: 8px;
            color: #9ca3af;
            font-size: 13px;
        }

        @media (max-width: 1024px) {
            .grid {
                grid-template-columns: 1fr;
            }
            
            .nav-links {
                gap: 12px;
            }
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <nav>
                <div class="logo">junard Taoy</div>
                <div class="nav-links">
                    <a href="dashboard.php">Home</a>
                    <a href="#" style="font-weight: 600;">Add Project</a>
                    <a href="view_projects.php">View Projects</a>
                    <a href="../login/logout.php" class="logout-btn">
                        <span>↪</span> Logout
                    </a>
                </div>
            </nav>
        </div>
    </header>

    <section class="hero">
        <div class="container">
            <div class="hero-box">
                <div class="hero-title">
                    <div class="plus-icon">+</div>
                    <h1>Add New Project</h1>
                </div>
                <p>Fill in the details to add a new project to your portfolio</p>
            </div>
        </div>
    </section>

    <section class="content">
        <div class="container">
            <div class="grid">
                <div class="card">
                    <div class="card-header">
                        <span>✏️</span>
                        <h2>Project Details</h2>
                    </div>

                    <?php if (!empty($error_message)): ?>
                        <div class="error-alert">
                            <strong>Error:</strong> <?php echo htmlspecialchars($error_message); ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST" enctype="multipart/form-data" id="projectForm">
                        <div class="form-group">
                            <label>Project Name <span class="required">*</span></label>
                            <input type="text" name="project_name" id="projectName" required placeholder="Enter project name" value="<?php echo isset($_POST['project_name']) ? htmlspecialchars($_POST['project_name']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label>Project Description <span class="required">*</span></label>
                            <textarea name="project_description" id="projectDescription" required placeholder="Enter project description"><?php echo isset($_POST['project_description']) ? htmlspecialchars($_POST['project_description']) : ''; ?></textarea>
                        </div>

                        <div class="form-group">
                            <label>Category <span class="required">*</span></label>
                            <select name="project_category" id="projectCategory" required>
                                <option value="">-- Select a category --</option>
                                <option value="Web Development" <?php echo (isset($_POST['project_category']) && $_POST['project_category'] == 'Web Development') ? 'selected' : ''; ?>>Web Development</option>
                                <option value="Mobile App" <?php echo (isset($_POST['project_category']) && $_POST['project_category'] == 'Mobile App') ? 'selected' : ''; ?>>Mobile App</option>
                                <option value="UI/UX Design" <?php echo (isset($_POST['project_category']) && $_POST['project_category'] == 'UI/UX Design') ? 'selected' : ''; ?>>UI/UX Design</option>
                                <option value="Data Science" <?php echo (isset($_POST['project_category']) && $_POST['project_category'] == 'Data Science') ? 'selected' : ''; ?>>Data Science</option>
                                <option value="Other" <?php echo (isset($_POST['project_category']) && $_POST['project_category'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label>Project File <span class="required">*</span></label>
                            <input type="file" name="project_file" id="projectFile" required>
                            <small>Supported formats: Images, PDFs, Documents (Max 20MB)</small>
                        </div>

                        <button type="submit" class="submit-btn">📤 Add Project</button>
                    </form>
                </div>

                <div class="card">
                    <div class="card-header">
                        <span>👁️</span>
                        <h2>Preview</h2>
                    </div>

                    <div class="preview-box">
                        <div class="category-badge" id="previewCategory">Select Category</div>
                        <h3 class="preview-title" id="previewTitle">Project Name</h3>
                        <p class="preview-desc" id="previewDesc">Project description will appear here...</p>
                        
                        <div class="preview-icons">
                            <div class="icon-box">
                                <div class="icon-square"></div>
                            </div>
                            <div class="icon-box">
                                <div class="icon-circle"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <script>
        // Live preview updates
        const projectName = document.getElementById('projectName');
        const projectDescription = document.getElementById('projectDescription');
        const projectCategory = document.getElementById('projectCategory');
        const previewTitle = document.getElementById('previewTitle');
        const previewDesc = document.getElementById('previewDesc');
        const previewCategory = document.getElementById('previewCategory');

        projectName.addEventListener('input', (e) => {
            previewTitle.textContent = e.target.value || 'Project Name';
        });

        projectDescription.addEventListener('input', (e) => {
            previewDesc.textContent = e.target.value || 'Project description will appear here...';
        });

        projectCategory.addEventListener('change', (e) => {
            previewCategory.textContent = e.target.value || 'Select Category';
        });
    </script>
</body>
</html>