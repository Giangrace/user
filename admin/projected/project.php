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
    <title>Add Project - <?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></title>
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

        .main-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .form-container, .preview-container {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        .form-container h2, .preview-container h2 {
            color: #fff;
            font-size: 24px;
            margin-bottom: 20px;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            color: #fff;
            font-weight: 600;
            font-size: 14px;
            text-shadow: 0 2px 5px rgba(0, 0, 0, 0.3);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: 12px 15px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.3);
            border-radius: 8px;
            font-family: 'Poppins', sans-serif;
            font-size: 14px;
            color: #fff;
            transition: all 0.3s;
        }

        .form-group input::placeholder,
        .form-group textarea::placeholder {
            color: rgba(255, 255, 255, 0.6);
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            background: rgba(255, 255, 255, 0.2);
            border-color: rgba(255, 255, 255, 0.5);
        }

        .form-group select option {
            background: #2c3e50;
            color: #fff;
        }

        .form-group textarea {
            resize: vertical;
            min-height: 100px;
        }

        .file-upload {
            position: relative;
        }

        .file-upload input[type="file"] {
            display: none;
        }

        .file-upload-btn {
            display: inline-block;
            padding: 12px 30px;
            background: rgba(255, 255, 255, 0.15);
            border: 2px dashed rgba(255, 255, 255, 0.4);
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            color: #fff;
            font-weight: 500;
        }

        .file-upload-btn:hover {
            background: rgba(255, 255, 255, 0.25);
            border-color: rgba(255, 255, 255, 0.6);
        }

        .file-name {
            margin-top: 10px;
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
        }

        .submit-btn {
            width: 100%;
            padding: 15px;
            background: rgba(33, 150, 243, 0.3);
            border: 1px solid rgba(33, 150, 243, 0.5);
            color: white;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .submit-btn:hover:not(:disabled) {
            background: rgba(33, 150, 243, 0.5);
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(33, 150, 243, 0.3);
        }

        .submit-btn:disabled {
            opacity: 0.6;
            cursor: not-allowed;
        }

        .view-projects-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #fff;
            text-decoration: none;
            font-weight: 600;
            transition: all 0.3s;
        }

        .view-projects-link:hover {
            color: #a8d5ff;
        }

        .message {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }

        .success {
            background: rgba(76, 175, 80, 0.3);
            color: #fff;
            border: 1px solid rgba(76, 175, 80, 0.5);
            display: block;
        }

        .error {
            background: rgba(244, 67, 54, 0.3);
            color: #fff;
            border: 1px solid rgba(244, 67, 54, 0.5);
            display: block;
        }

        small {
            color: rgba(255, 255, 255, 0.7);
            font-size: 12px;
        }

        .preview-card {
            background: rgba(255, 255, 255, 0.05);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
        }

        .preview-card h3 {
            color: #fff;
            font-size: 20px;
            margin-bottom: 10px;
        }

        .preview-card p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            line-height: 1.6;
        }

        .preview-meta {
            display: flex;
            gap: 15px;
            margin-top: 15px;
            font-size: 13px;
            color: rgba(255, 255, 255, 0.7);
        }

        .preview-empty {
            text-align: center;
            padding: 40px 20px;
            color: rgba(255, 255, 255, 0.6);
        }

        .preview-empty i {
            font-size: 60px;
            margin-bottom: 15px;
            color: rgba(255, 255, 255, 0.3);
        }

        .category-web { 
            background: rgba(33, 150, 243, 0.3); 
            color: #a8d5ff; 
            border: 1px solid rgba(33, 150, 243, 0.5); 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
            display: inline-block; 
            margin-bottom: 15px; 
        }
        
        .category-mobile { 
            background: rgba(156, 39, 176, 0.3); 
            color: #e1bee7; 
            border: 1px solid rgba(156, 39, 176, 0.5); 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
            display: inline-block; 
            margin-bottom: 15px; 
        }
        
        .category-design { 
            background: rgba(255, 152, 0, 0.3); 
            color: #ffcc80; 
            border: 1px solid rgba(255, 152, 0, 0.5); 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
            display: inline-block; 
            margin-bottom: 15px; 
        }
        
        .category-other { 
            background: rgba(76, 175, 80, 0.3); 
            color: #c5e1a5; 
            border: 1px solid rgba(76, 175, 80, 0.5); 
            padding: 6px 15px; 
            border-radius: 20px; 
            font-size: 12px; 
            font-weight: 600; 
            display: inline-block; 
            margin-bottom: 15px; 
        }

        @media (max-width: 1024px) {
            .main-content {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            header {
                flex-direction: column;
                gap: 20px;
            }

            nav {
                flex-wrap: wrap;
                justify-content: center;
            }

            .form-container {
                padding: 20px;
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
                <a href="project.php" style="color: #a8d5ff;">Add Project</a>
                <a href="view_projects.php">View Projects</a>
                <a href="../login/logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </nav>
        </header>

        <div class="page-header">
            <h1><i class="fas fa-plus-circle"></i> Add New Project</h1>
            <p>Fill in the details to add a new project to your portfolio</p>
        </div>

        <div class="main-content">
            <div class="form-container">
                <h2><i class="fas fa-edit"></i> Project Details</h2>
                <div id="message" class="message"></div>

                <form id="projectForm" enctype="multipart/form-data">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    
                    <div class="form-group">
                        <label for="projectName">Project Name *</label>
                        <input type="text" id="projectName" name="project_name" required placeholder="Enter project name">
                    </div>

                    <div class="form-group">
                        <label for="projectDescription">Project Description *</label>
                        <textarea id="projectDescription" name="project_description" required placeholder="Describe your project..."></textarea>
                    </div>

                    <div class="form-group">
                        <label for="projectCategory">Category *</label>
                        <select id="projectCategory" name="project_category" required>
                            <option value="">Select a category</option>
                            <option value="Web Development">Web Development</option>
                            <option value="Mobile App">Mobile App</option>
                            <option value="UI/UX Design">UI/UX Design</option>
                            <option value="Other">Other</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>Upload File *</label>
                        <div class="file-upload">
                            <label for="projectFile" class="file-upload-btn">
                                <i class="fas fa-upload"></i> Choose File
                            </label>
                            <input type="file" id="projectFile" name="project_file" accept=".pdf,.doc,.docx,.zip,.rar,.jpg,.jpeg,.png" required>
                            <div class="file-name" id="fileName">No file chosen</div>
                        </div>
                        <small>Accepted: PDF, DOC, DOCX, ZIP, RAR, JPG, PNG (Max 10MB)</small>
                    </div>

                    <button type="submit" class="submit-btn">
                        <i class="fas fa-save"></i> Add Project
                    </button>
                </form>

                <a href="view_projects.php" class="view-projects-link">
                    <i class="fas fa-folder-open"></i> View All Projects
                </a>
            </div>

            <div class="preview-container">
                <h2><i class="fas fa-eye"></i> Preview</h2>
                <div id="previewContent" class="preview-empty">
                    <i class="fas fa-clipboard-list"></i>
                    <p>Your project preview will appear here as you fill in the form</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Show file name when selected
        document.getElementById('projectFile').addEventListener('change', function() {
            const fileName = this.files[0] ? this.files[0].name : 'No file chosen';
            document.getElementById('fileName').textContent = fileName;
            updatePreview();
        });

        // Update preview on input
        document.getElementById('projectName').addEventListener('input', updatePreview);
        document.getElementById('projectDescription').addEventListener('input', updatePreview);
        document.getElementById('projectCategory').addEventListener('change', updatePreview);

        function updatePreview() {
            const name = document.getElementById('projectName').value;
            const description = document.getElementById('projectDescription').value;
            const category = document.getElementById('projectCategory').value;
            const file = document.getElementById('projectFile').files[0];

            const previewContainer = document.getElementById('previewContent');

            if (!name && !description && !category) {
                previewContainer.innerHTML = '<div class="preview-empty"><i class="fas fa-clipboard-list"></i><p>Your project preview will appear here as you fill in the form</p></div>';
                return;
            }

            let categoryClass = 'category-other';
            if (category === 'Web Development') categoryClass = 'category-web';
            else if (category === 'Mobile App') categoryClass = 'category-mobile';
            else if (category === 'UI/UX Design') categoryClass = 'category-design';

            previewContainer.innerHTML = '<div class="preview-card">' +
                (category ? '<span class="' + categoryClass + '">' + category + '</span>' : '') +
                '<h3>' + (name || 'Project Name') + '</h3>' +
                '<p>' + (description || 'Project description will appear here...') + '</p>' +
                '<div class="preview-meta">' +
                '<span><i class="fas fa-calendar"></i> ' + new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' }) + '</span>' +
                (file ? '<span><i class="fas fa-paperclip"></i> ' + file.name + '</span>' : '') +
                '</div></div>';
        }

        // Handle form submission
        document.getElementById('projectForm').addEventListener('submit', function(e) {
            e.preventDefault();

            const formData = new FormData(this);
            const messageDiv = document.getElementById('message');
            const submitBtn = this.querySelector('.submit-btn');

            // Show loading state
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Adding Project...';
            messageDiv.style.display = 'none';

            console.log('Submitting form to add_project.php...');

            fetch('add_project.php', {
                method: 'POST',
                body: formData
            })
            .then(response => {
                console.log('Response status:', response.status);
                console.log('Response type:', response.headers.get('Content-Type'));
                return response.text();
            })
            .then(text => {
                console.log('Raw response:', text);
                
                let data;
                try {
                    data = JSON.parse(text);
                } catch (e) {
                    console.error('JSON parse error:', e);
                    throw new Error('Server returned invalid response: ' + text.substring(0, 100));
                }

                if (data.success) {
                    messageDiv.className = 'message success';
                    messageDiv.textContent = data.message;
                    messageDiv.style.display = 'block';

                    // Reset form
                    document.getElementById('projectForm').reset();
                    document.getElementById('fileName').textContent = 'No file chosen';
                    updatePreview();

                    // Redirect to view projects after 2 seconds
                    setTimeout(function() {
                        window.location.href = 'view_projects.php';
                    }, 2000);
                } else {
                    messageDiv.className = 'message error';
                    messageDiv.textContent = 'Error: ' + data.message;
                    messageDiv.style.display = 'block';
                    console.error('Server error:', data.message);
                }
            })
            .catch(error => {
                console.error('Fetch error:', error);
                messageDiv.className = 'message error';
                messageDiv.textContent = 'Error: ' + error.message;
                messageDiv.style.display = 'block';
            })
            .finally(function() {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-save"></i> Add Project';
            });
        });
    </script>
</body>
</html>