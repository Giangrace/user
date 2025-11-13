<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

$firstName = $_SESSION['first_name'];
$lastName = $_SESSION['last_name'];
$userId = $_SESSION['user_id'];

// Database configuration
$host = 'localhost';
$dbname = 'user';  // Your database name
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Fetch user's projects
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE user_id = :user_id ORDER BY created_at DESC");
    $stmt->execute([':user_id' => $userId]);
    $projects = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
    $projects = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Projects - <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        #bg-video {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            z-index: -1;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            z-index: -1;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            position: relative;
            z-index: 1;
        }

        header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px 40px;
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #fff;
            text-decoration: none;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.3);
        }

        nav {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #fff;
            font-weight: 500;
            transition: all 0.3s;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        nav a:hover {
            color: #a8d5ff;
            transform: translateY(-2px);
        }

        .logout-btn {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .page-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            text-align: center;
        }

        .page-header h1 {
            color: #fff;
            font-size: 36px;
            margin-bottom: 10px;
            text-shadow: 0 2px 10px rgba(0, 0, 0, 0.5);
        }

        .page-header p {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
        }

        .add-project-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 30px;
            background: rgba(33, 150, 243, 0.3);
            border: 1px solid rgba(33, 150, 243, 0.5);
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: all 0.3s;
        }

        .add-project-btn:hover {
            background: rgba(33, 150, 243, 0.5);
            transform: translateY(-2px);
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-bottom: 40px;
        }

        .project-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
            transition: all 0.3s;
            position: relative;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.4);
        }

        .project-category {
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 15px;
        }

        .category-web {
            background: rgba(33, 150, 243, 0.3);
            color: #a8d5ff;
            border: 1px solid rgba(33, 150, 243, 0.5);
        }

        .category-mobile {
            background: rgba(156, 39, 176, 0.3);
            color: #e1bee7;
            border: 1px solid rgba(156, 39, 176, 0.5);
        }

        .category-design {
            background: rgba(255, 152, 0, 0.3);
            color: #ffcc80;
            border: 1px solid rgba(255, 152, 0, 0.5);
        }

        .category-other {
            background: rgba(76, 175, 80, 0.3);
            color: #c5e1a5;
            border: 1px solid rgba(76, 175, 80, 0.5);
        }

        .project-card h3 {
            color: #fff;
            font-size: 22px;
            margin-bottom: 12px;
        }

        .project-card p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 15px;
        }

        .project-meta {
            display: flex;
            align-items: center;
            gap: 15px;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .download-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: rgba(76, 175, 80, 0.3);
            border: 1px solid rgba(76, 175, 80, 0.5);
            color: #c5e1a5;
            text-decoration: none;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.3s;
            margin-top: 10px;
        }

        .download-btn:hover {
            background: rgba(76, 175, 80, 0.5);
            transform: translateY(-2px);
        }

        .action-buttons {
            position: absolute;
            top: 15px;
            right: 15px;
            display: flex;
            gap: 8px;
        }

        .edit-btn, .delete-btn {
            background: rgba(244, 67, 54, 0.3);
            border: 1px solid rgba(244, 67, 54, 0.5);
            color: #fff;
            padding: 8px 12px;
            border-radius: 6px;
            cursor: pointer;
            transition: all 0.3s;
            border: none;
        }

        .edit-btn {
            background: rgba(33, 150, 243, 0.3);
            border: 1px solid rgba(33, 150, 243, 0.5);
        }

        .edit-btn:hover {
            background: rgba(33, 150, 243, 0.5);
        }

        .delete-btn:hover {
            background: rgba(244, 67, 54, 0.5);
        }

        .empty-state {
            text-align: center;
            padding: 80px 20px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
        }

        .empty-state i {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.3);
            margin-bottom: 20px;
        }

        .empty-state h2 {
            color: #fff;
            font-size: 28px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.7);
            font-size: 16px;
            margin-bottom: 25px;
        }

        .error-message {
            background: rgba(244, 67, 54, 0.3);
            color: #fff;
            border: 1px solid rgba(244, 67, 54, 0.5);
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            text-align: center;
        }

        /* Modal Styles */
        .modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal.active {
            display: flex;
        }

        .modal-content {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            padding: 30px;
            border-radius: 15px;
            width: 90%;
            max-width: 500px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.5);
        }

        .modal-header {
            font-size: 24px;
            margin-bottom: 20px;
            color: #333;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 12px;
            border: 2px solid #ddd;
            border-radius: 8px;
            font-size: 15px;
            font-family: 'Poppins', sans-serif;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #2196f3;
        }

        .modal-actions {
            display: flex;
            gap: 10px;
            margin-top: 20px;
        }

        .btn {
            flex: 1;
            padding: 12px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 15px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-cancel {
            background: #6b7280;
            color: white;
        }

        .btn-cancel:hover {
            background: #4b5563;
        }

        .btn-save {
            background: #2196f3;
            color: white;
        }

        .btn-save:hover {
            background: #1976d2;
        }

        @media (max-width: 768px) {
            .projects-grid {
                grid-template-columns: 1fr;
            }
            
            header {
                flex-direction: column;
                gap: 20px;
            }
            
            nav {
                flex-wrap: wrap;
                justify-content: center;
            }
        }
    </style>
</head>
<body>
    <video autoplay muted loop id="bg-video">
        <source src="../login/Live Wallpaper 4K Computer CPU.mp4" type="video/mp4">
    </video>

    <div class="container">
        <header>
            <a href="../login/profile.php" class="logo"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></a>
            <nav>
                <a href="../login/profile.php">Home</a>
                <a href="../html/About.html">About</a>
                <a href="../html/portfolio.html">Portfolio</a>
                <a href="../html/service.html">Service</a>
                <a href="project.php">Add Project</a>
                <a href="view_projects.php" style="color: #a8d5ff;">View Projects</a>
                <a href="../login/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </header>

        <div class="page-header">
            <h1><i class="fas fa-folder-open"></i> My Projects</h1>
            <p>View and manage all your projects</p>
            <a href="project.php" class="add-project-btn">
                <i class="fas fa-plus"></i> Add New Project
            </a>
        </div>

        <?php if (isset($error)): ?>
            <div class="error-message">
                <i class="fas fa-exclamation-circle"></i> <?php echo htmlspecialchars($error); ?>
            </div>
        <?php endif; ?>

        <?php if (empty($projects)): ?>
            <div class="empty-state">
                <i class="fas fa-folder-open"></i>
                <h2>No Projects Yet</h2>
                <p>You haven't added any projects. Start by creating your first project!</p>
                <a href="project.php" class="add-project-btn">
                    <i class="fas fa-plus"></i> Create Your First Project
                </a>
            </div>
        <?php else: ?>
            <div class="projects-grid">
                <?php foreach ($projects as $project): 
                    $categoryClass = 'category-other';
                    if ($project['project_category'] === 'Web Development') $categoryClass = 'category-web';
                    elseif ($project['project_category'] === 'Mobile App') $categoryClass = 'category-mobile';
                    elseif ($project['project_category'] === 'UI/UX Design') $categoryClass = 'category-design';
                ?>
                    <div class="project-card" data-project-id="<?php echo $project['id']; ?>">
                        <div class="action-buttons">
                            <button class="edit-btn" onclick="openEditModal(<?php echo $project['id']; ?>, '<?php echo htmlspecialchars(addslashes($project['project_name'])); ?>', '<?php echo htmlspecialchars(addslashes($project['project_description'])); ?>')">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="delete-btn" onclick="deleteProject(<?php echo $project['id']; ?>)">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                        
                        <span class="project-category <?php echo $categoryClass; ?>">
                            <?php echo htmlspecialchars($project['project_category']); ?>
                        </span>
                        
                        <h3><?php echo htmlspecialchars($project['project_name']); ?></h3>
                        <p><?php echo htmlspecialchars($project['project_description']); ?></p>
                        
                        <div class="project-meta">
                            <span>
                                <i class="fas fa-calendar"></i>
                                <?php echo date('M d, Y', strtotime($project['created_at'])); ?>
                            </span>
                            <?php if ($project['file_name']): ?>
                                <span>
                                    <i class="fas fa-paperclip"></i>
                                    <?php echo htmlspecialchars($project['file_name']); ?>
                                </span>
                            <?php endif; ?>
                        </div>
                        
                        <?php if ($project['file_path']): ?>
                            <a href="download.php?id=<?php echo $project['id']; ?>" class="download-btn">
                                <i class="fas fa-download"></i>
                                Download File
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>

    <!-- Edit Modal -->
    <div class="modal" id="editModal">
        <div class="modal-content">
            <h2 class="modal-header"><i class="fas fa-edit"></i> Edit Project</h2>
            <form id="editForm">
                <input type="hidden" id="editProjectId">
                <div class="form-group">
                    <label for="editProjectName">Project Name</label>
                    <input type="text" id="editProjectName" placeholder="Enter project name" required>
                </div>
                <div class="form-group">
                    <label for="editProjectDescription">Project Description</label>
                    <textarea id="editProjectDescription" rows="4" placeholder="Enter project description" required></textarea>
                </div>
                <div class="modal-actions">
                    <button type="button" class="btn btn-cancel" onclick="closeEditModal()">Cancel</button>
                    <button type="submit" class="btn btn-save">Save Changes</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function openEditModal(id, name, description) {
            document.getElementById('editProjectId').value = id;
            document.getElementById('editProjectName').value = name;
            document.getElementById('editProjectDescription').value = description;
            document.getElementById('editModal').classList.add('active');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.remove('active');
            document.getElementById('editForm').reset();
        }

        document.getElementById('editForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const id = document.getElementById('editProjectId').value;
            const name = document.getElementById('editProjectName').value;
            const description = document.getElementById('editProjectDescription').value;
            
            fetch('edit_project.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    project_id: id,
                    project_name: name,
                    project_description: description
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Project updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Failed to update project. Please try again.');
            });
        });

        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeEditModal();
            }
        });

        function deleteProject(projectId) {
            if (!confirm('Are you sure you want to delete this project?')) {
                return;
            }

            fetch('delete_project.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ project_id: projectId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const card = document.querySelector(`[data-project-id="${projectId}"]`);
                    card.style.transition = 'all 0.3s';
                    card.style.opacity = '0';
                    card.style.transform = 'scale(0.8)';
                    setTimeout(() => {
                        card.remove();
                        const grid = document.querySelector('.projects-grid');
                        if (grid && grid.children.length === 0) {
                            location.reload();
                        }
                    }, 300);
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while deleting the project.');
            });
        }
    </script>
</body>
</html>