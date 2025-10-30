// Load projects on page load
document.addEventListener('DOMContentLoaded', loadProjects);

// Handle form submission
document.getElementById('projectForm').addEventListener('submit', function(e) {
  e.preventDefault();
  addProject();
});

function addProject() {
  const formData = new FormData();
  const projectName = document.getElementById('projectName').value;
  const projectDescription = document.getElementById('projectDescription').value;
  const projectCategory = document.getElementById('projectCategory').value;
  const projectFile = document.getElementById('projectFile').files[0];

  // Validation
  if (!projectName || !projectDescription || !projectCategory) {
    showMessage('Please fill in all required fields!', 'error');
    return;
  }

  formData.append('projectName', projectName);
  formData.append('projectDescription', projectDescription);
  formData.append('projectCategory', projectCategory);
  if (projectFile) {
    formData.append('projectFile', projectFile);
  }

  // Send to server - FIXED PATH
  fetch('../admin/add_project.php', {
    method: 'POST',
    body: formData
  })
  .then(response => {
    // Check if response is ok before parsing
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      showMessage('Project added successfully!', 'success');
      document.getElementById('projectForm').reset();
      loadProjects();
    } else {
      showMessage(data.message || 'Error adding project!', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showMessage('Error: ' + error.message, 'error');
  });
}

function loadProjects() {
  // FIXED PATH - Points to admin folder
  fetch('../admin/get_projects.php')
    .then(response => {
      // Check if response is ok before parsing
      if (!response.ok) {
        throw new Error('Network response was not ok');
      }
      return response.json();
    })
    .then(data => {
      const projectList = document.getElementById('projectList');
      projectList.innerHTML = '';

      if (data.success && data.projects && data.projects.length > 0) {
        data.projects.forEach(project => {
          const card = document.createElement('div');
          card.className = 'project-card';
          card.innerHTML = `
            <button class="delete-btn" onclick="deleteProject(${project.id})">
              <i class="fas fa-trash"></i> Delete
            </button>
            <h3><i class="fas fa-project-diagram"></i> ${project.project_name}</h3>
            <p><strong>Category:</strong> ${project.category}</p>
            <p><strong>Description:</strong> ${project.description}</p>
            <p><small>Added: ${new Date(project.created_at).toLocaleDateString()}</small></p>
            ${project.file_path ? `<a href="../admin/${project.file_path}" class="file-link" target="_blank">
              <i class="fas fa-download"></i> Download File
            </a>` : ''}
          `;
          projectList.appendChild(card);
        });
      } else {
        projectList.innerHTML = '<p style="color: rgba(255,255,255,0.6); text-align: center;">No projects yet. Add your first project!</p>';
      }
    })
    .catch(error => {
      console.error('Error loading projects:', error);
      showMessage('Error loading projects: ' + error.message, 'error');
    });
}

function deleteProject(id) {
  if (!confirm('Are you sure you want to delete this project?')) {
    return;
  }

  // FIXED PATH
  fetch('../admin/delete_project.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: 'id=' + id
  })
  .then(response => {
    // Check if response is ok before parsing
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    return response.json();
  })
  .then(data => {
    if (data.success) {
      showMessage('Project deleted successfully!', 'success');
      loadProjects();
    } else {
      showMessage(data.message || 'Error deleting project!', 'error');
    }
  })
  .catch(error => {
    console.error('Error:', error);
    showMessage('Error: ' + error.message, 'error');
  });
}

function showMessage(message, type) {
  const messageDiv = document.getElementById('message');
  messageDiv.textContent = message;
  messageDiv.className = 'message ' + type;
  messageDiv.style.display = 'block';

  setTimeout(() => {
    messageDiv.style.display = 'none';
  }, 5000);
}