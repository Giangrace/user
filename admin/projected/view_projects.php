<?php
session_start();

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login/index.php");
    exit();
}

$firstName = $_SESSION['first_name'];
$lastName = $_SESSION['last_name'];
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
            -webkit-backdrop-filter: blur(10px);
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

        .logout-btn:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        .page-header {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
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
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .filter-section {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .filter-section select {
            padding: 10px 20px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            cursor: pointer;
            color: #fff;
        }

        .filter-section select option {
            background: #2c3e50;
            color: #fff;
        }

        .add-project-btn {
            background: rgba(255, 255, 255, 0.2);
            border: 1px solid rgba(255, 255, 255, 0.3);
            color: white;
            padding: 12px 30px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            transition: all 0.3s;
        }

        .add-project-btn:hover {
            background: rgba(255, 255, 255, 0.3);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(255, 255, 255, 0.2);
        }

        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .project-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
            transition: all 0.3s;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 12px 40px rgba(0, 0, 0, 0.4);
            border-color: rgba(255, 255, 255, 0.4);
        }

        .project-category {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
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
            line-height: 1.3;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .project-card p {
            color: rgba(255, 255, 255, 0.85);
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .project-meta {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .project-meta i {
            margin-right: 5px;
        }

        .project-actions {
            display: flex;
            gap: 10px;
        }

        .btn-download, .btn-delete {
            flex: 1;
            padding: 10px;
            border: none;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-download {
            background: rgba(33, 150, 243, 0.3);
            border: 1px solid rgba(33, 150, 243, 0.5);
            color: #fff;
        }

        .btn-download:hover {
            background: rgba(33, 150, 243, 0.5);
            box-shadow: 0 5px 15px rgba(33, 150, 243, 0.3);
        }

        .btn-delete {
            background: rgba(244, 67, 54, 0.3);
            border: 1px solid rgba(244, 67, 54, 0.5);
            color: #fff;
        }

        .btn-delete:hover {
            background: rgba(244, 67, 54, 0.5);
            box-shadow: 0 5px 15px rgba(244, 67, 54, 0.3);
        }

        .empty-state {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            padding: 60px;
            text-align: center;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .empty-state i {
            font-size: 80px;
            color: rgba(255, 255, 255, 0.4);
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 10px;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .empty-state p {
            color: rgba(255, 255, 255, 0.8);
            margin-bottom: 30px;
        }

        .loading {
            text-align: center;
            padding: 60px;
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .loading i {
            font-size: 50px;
            color: #fff;
            animation: spin 1s linear infinite;
        }

        .loading p {
            color: #fff;
            margin-top: 20px;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .error-message {
            background: rgba(244, 67, 54, 0.3);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(244, 67, 54, 0.5);
            color: #fff;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
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

            .filter-section {
                flex-direction: column;
                gap: 15px;
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
            <p>View, download, and manage all your saved projects</p>
        </div>

        <div class="error-message" id="errorMessage"></div>

        <div class="filter-section">
            <select id="categoryFilter">
                <option value="all">All Categories</option>
                <option value="Web Development">Web Development</option>
                <option value="Mobile App">Mobile App</option>
                <option value="UI/UX Design">UI/UX Design</option>
                <option value="Other">Other</option>
            </select>
            <a href="project.php" class="add-project-btn">
                <i class="fas fa-plus"></i> Add New Project
            </a>
        </div>

        <div id="projectsContainer">
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading projects...</p>
            </div>
        </div>
    </div>

    <script>
        let allProjects = [];

        document.addEventListener('DOMContentLoaded', function() {
            loadProjects();
        });

        document.getElementById('categoryFilter').addEventListener('change', function() {
            filterProjects(this.value);
        });

        function loadProjects() {
            fetch('get_project.php')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        allProjects = data.projects;
                        displayProjects(allProjects);
                    } else {
                        showError('Failed to load projects: ' + data.message);
                        displayEmptyState();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showError('Error loading projects. Please refresh the page.');
                    displayEmptyState();
                });
        }

        function displayProjects(projects) {
            const container = document.getElementById('projectsContainer');
            
            if (projects.length === 0) {
                displayEmptyState();
                return;
            }

            let html = '<div class="projects-grid">';
            
            projects.forEach(project => {
                const categoryClass = getCategoryClass(project.category);
                const date = new Date(project.created_at).toLocaleDateString('en-US', {
                    year: 'numeric',
                    month: 'short',
                    day: 'numeric'
                });
                
                html += '<div class="project-card" data-category="' + project.category + '">';
                html += '<span class="project-category ' + categoryClass + '">' + project.category + '</span>';
                html += '<h3>' + escapeHtml(project.project_name) + '</h3>';
                html += '<p>' + escapeHtml(project.description) + '</p>';
                html += '<div class="project-meta">';
                html += '<span><i class="fas fa-calendar"></i> ' + date + '</span>';
                if (project.file_path) {
                    html += '<span><i class="fas fa-paperclip"></i> Has Attachment</span>';
                }
                html += '</div>';
                html += '<div class="project-actions">';
                
                if (project.file_path) {
                    html += '<button class="btn-download" onclick="downloadFile(\'' + project.file_path + '\', \'' + escapeHtml(project.project_name) + '\')">';
                    html += '<i class="fas fa-download"></i> Download</button>';
                } else {
                    html += '<button class="btn-download" disabled style="opacity: 0.5; cursor: not-allowed;">';
                    html += '<i class="fas fa-download"></i> No File</button>';
                }
                
                html += '<button class="btn-delete" onclick="deleteProject(' + project.id + ', \'' + escapeHtml(project.project_name) + '\')">';
                html += '<i class="fas fa-trash"></i> Delete</button>';
                html += '</div></div>';
            });
            
            html += '</div>';
            container.innerHTML = html;
        }

        function displayEmptyState() {
            const container = document.getElementById('projectsContainer');
            container.innerHTML = '<div class="empty-state">' +
                '<i class="fas fa-folder-open"></i>' +
                '<h3>No Projects Yet</h3>' +
                '<p>Start by adding your first project!</p>' +
                '<a href="project.php" class="add-project-btn">' +
                '<i class="fas fa-plus"></i> Add Your First Project</a></div>';
        }

        function filterProjects(category) {
            if (category === 'all') {
                displayProjects(allProjects);
            } else {
                const filtered = allProjects.filter(p => p.category === category);
                displayProjects(filtered);
            }
        }

        function getCategoryClass(category) {
            const classes = {
                'Web Development': 'category-web',
                'Mobile App': 'category-mobile',
                'UI/UX Design': 'category-design',
                'Other': 'category-other'
            };
            return classes[category] || 'category-other';
        }

        function downloadFile(filePath, projectName) {
            window.open(filePath, '_blank');
        }

        function deleteProject(id, name) {
            if (!confirm('Are you sure you want to delete "' + name + '"? This action cannot be undone.')) {
                return;
            }

            const formData = new FormData();
            formData.append('id', id);

            fetch('delete.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    allProjects = allProjects.filter(p => p.id != id);
                    const currentFilter = document.getElementById('categoryFilter').value;
                    filterProjects(currentFilter);
                    alert('Project deleted successfully!');
                } else {
                    showError('Failed to delete project: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showError('Error deleting project. Please try again.');
            });
        }

        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            
            setTimeout(function() {
                errorDiv.style.display = 'none';
            }, 5000);
        }

        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>