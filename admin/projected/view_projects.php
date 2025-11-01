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
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
        }

        /* Header */
        header {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px 40px;
            border-radius: 15px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .logo {
            font-size: 24px;
            font-weight: 700;
            color: #667eea;
            text-decoration: none;
        }

        nav {
            display: flex;
            gap: 30px;
            align-items: center;
        }

        nav a {
            text-decoration: none;
            color: #333;
            font-weight: 500;
            transition: color 0.3s;
        }

        nav a:hover {
            color: #667eea;
        }

        .logout-btn {
            background: #667eea;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .logout-btn:hover {
            background: #5568d3;
            color: white;
        }

        /* Page Title */
        .page-header {
            background: rgba(255, 255, 255, 0.95);
            padding: 30px;
            border-radius: 15px;
            margin-bottom: 30px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .page-header h1 {
            color: #333;
            font-size: 36px;
            margin-bottom: 10px;
        }

        .page-header p {
            color: #666;
            font-size: 16px;
        }

        /* Filter Section */
        .filter-section {
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 15px;
            margin-bottom: 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .filter-section select {
            padding: 10px 20px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            cursor: pointer;
            background: white;
        }

        .add-project-btn {
            background: #667eea;
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
            background: #5568d3;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        /* Projects Grid */
        .projects-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 30px;
            margin-bottom: 30px;
        }

        .project-card {
            background: white;
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            position: relative;
        }

        .project-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .project-category {
            display: inline-block;
            padding: 6px 15px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 15px;
        }

        .category-web { background: #e3f2fd; color: #1976d2; }
        .category-mobile { background: #f3e5f5; color: #7b1fa2; }
        .category-design { background: #fff3e0; color: #f57c00; }
        .category-other { background: #e8f5e9; color: #388e3c; }

        .project-card h3 {
            color: #333;
            font-size: 22px;
            margin-bottom: 12px;
            line-height: 1.3;
        }

        .project-card p {
            color: #666;
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
            color: #999;
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
            background: #667eea;
            color: white;
        }

        .btn-download:hover {
            background: #5568d3;
        }

        .btn-delete {
            background: #ff5252;
            color: white;
        }

        .btn-delete:hover {
            background: #ff1744;
        }

        /* Empty State */
        .empty-state {
            background: white;
            border-radius: 15px;
            padding: 60px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .empty-state i {
            font-size: 80px;
            color: #e0e0e0;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #666;
            font-size: 24px;
            margin-bottom: 10px;
        }

        .empty-state p {
            color: #999;
            margin-bottom: 30px;
        }

        /* Loading State */
        .loading {
            text-align: center;
            padding: 60px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }

        .loading i {
            font-size: 50px;
            color: #667eea;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        /* Error Message */
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }

        /* Responsive */
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
    <div class="container">
        <!-- Header -->
        <header>
            <a href="../login/profile.php" class="logo"><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></a>
            <nav>
                <a href="../login/profile.php">Home</a>
                <a href="../html/About.html">About</a>
                <a href="../html/portfolio.html">Portfolio</a>
                <a href="../html/service.html">Service</a>
                <a href="project.php">Add Project</a>
                <a href="view_projects.php" style="color: #667eea;">View Projects</a>
                <a href="../login/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </header>

        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="fas fa-folder-open"></i> My Projects</h1>
            <p>View, download, and manage all your saved projects</p>
        </div>

        <!-- Error Message -->
        <div class="error-message" id="errorMessage"></div>

        <!-- Filter Section -->
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

        <!-- Projects Grid -->
        <div id="projectsContainer">
            <div class="loading">
                <i class="fas fa-spinner"></i>
                <p>Loading projects...</p>
            </div>
        </div>
    </div>

    <script>
        let allProjects = [];

        // Load projects when page loads
        document.addEventListener('DOMContentLoaded', function() {
            loadProjects();
        });

        // Category filter
        document.getElementById('categoryFilter').addEventListener('change', function() {
            filterProjects(this.value);
        });

        // Load projects from server
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

        // Display projects
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
                
                html += `
                    <div class="project-card" data-category="${project.category}">
                        <span class="project-category ${categoryClass}">${project.category}</span>
                        <h3>${escapeHtml(project.project_name)}</h3>
                        <p>${escapeHtml(project.description)}</p>
                        <div class="project-meta">
                            <span><i class="fas fa-calendar"></i> ${date}</span>
                            ${project.file_path ? '<span><i class="fas fa-paperclip"></i> Has Attachment</span>' : ''}
                        </div>
                        <div class="project-actions">
                            ${project.file_path ? 
                                `<button class="btn-download" onclick="downloadFile('${project.file_path}', '${project.project_name}')">
                                    <i class="fas fa-download"></i> Download
                                </button>` : 
                                '<button class="btn-download" disabled style="opacity: 0.5; cursor: not-allowed;">
                                    <i class="fas fa-download"></i> No File
                                </button>'
                            }
                            <button class="btn-delete" onclick="deleteProject(${project.id}, '${escapeHtml(project.project_name)}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    </div>
                `;
            });
            
            html += '</div>';
            container.innerHTML = html;
        }

        // Display empty state
        function displayEmptyState() {
            const container = document.getElementById('projectsContainer');
            container.innerHTML = `
                <div class="empty-state">
                    <i class="fas fa-folder-open"></i>
                    <h3>No Projects Yet</h3>
                    <p>Start by adding your first project!</p>
                    <a href="project.php" class="add-project-btn">
                        <i class="fas fa-plus"></i> Add Your First Project
                    </a>
                </div>
            `;
        }

        // Filter projects by category
        function filterProjects(category) {
            if (category === 'all') {
                displayProjects(allProjects);
            } else {
                const filtered = allProjects.filter(p => p.category === category);
                displayProjects(filtered);
            }
        }

        // Get category class for styling
        function getCategoryClass(category) {
            const classes = {
                'Web Development': 'category-web',
                'Mobile App': 'category-mobile',
                'UI/UX Design': 'category-design',
                'Other': 'category-other'
            };
            return classes[category] || 'category-other';
        }

        // Download file
        function downloadFile(filePath, projectName) {
            window.open(filePath, '_blank');
        }

        // Delete project
        function deleteProject(id, name) {
            if (!confirm(`Are you sure you want to delete "${name}"? This action cannot be undone.`)) {
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
                    // Remove from allProjects array
                    allProjects = allProjects.filter(p => p.id != id);
                    
                    // Re-display projects
                    const currentFilter = document.getElementById('categoryFilter').value;
                    filterProjects(currentFilter);
                    
                    // Show success message
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

        // Show error message
        function showError(message) {
            const errorDiv = document.getElementById('errorMessage');
            errorDiv.textContent = message;
            errorDiv.style.display = 'block';
            
            setTimeout(() => {
                errorDiv.style.display = 'none';
            }, 5000);
        }

        // Escape HTML to prevent XSS
        function escapeHtml(text) {
            const div = document.createElement('div');
            div.textContent = text;
            return div.innerHTML;
        }
    </script>
</body>
</html>