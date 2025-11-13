// Add Project Form Submission
$(document).ready(function() {
    
    // Handle Add Project Form Submit
    $('#addProjectForm').submit(function(e) {
        e.preventDefault(); // Prevent default form submission
        
        // Get form data
        var formData = new FormData(this);
        
        // Show loading state
        var submitBtn = $(this).find('button[type="submit"]');
        var originalBtnText = submitBtn.text();
        submitBtn.prop('disabled', true).text('Uploading...');
        
        // Remove previous error messages
        $('.error-message').remove();
        
        $.ajax({
            url: 'add_project.php',
            type: 'POST',
            data: formData,
            contentType: false,
            processData: false,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.message);
                    
                    // Reset form
                    $('#addProjectForm')[0].reset();
                    
                    // Redirect to projects page or reload
                    window.location.href = 'view_projects.php';
                } else {
                    // Show error message
                    showError(response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('AJAX Error:', error);
                console.log('Response:', xhr.responseText);
                
                // Try to parse error response
                try {
                    var response = JSON.parse(xhr.responseText);
                    showError(response.message || 'An error occurred. Please try again.');
                } catch(e) {
                    showError('Server error: ' + error + '. Check console for details.');
                }
            },
            complete: function() {
                // Restore button
                submitBtn.prop('disabled', false).text(originalBtnText);
            }
        });
    });
    
    // Handle Delete Project
    $('.delete-btn').click(function(e) {
        e.preventDefault();
        
        var projectId = $(this).data('id');
        var projectName = $(this).data('name') || 'this project';
        
        // Confirm deletion
        if (!confirm('Are you sure you want to delete "' + projectName + '"?')) {
            return;
        }
        
        // Show loading on button
        var deleteBtn = $(this);
        var originalHtml = deleteBtn.html();
        deleteBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        $.ajax({
            url: 'delete_project.php',
            type: 'POST',
            data: { id: projectId },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    // Show success message
                    alert(response.message);
                    
                    // Remove the project card from DOM
                    deleteBtn.closest('.project-card').fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Or reload the page
                    // location.reload();
                } else {
                    alert('Error: ' + response.message);
                    deleteBtn.prop('disabled', false).html(originalHtml);
                }
            },
            error: function(xhr, status, error) {
                console.error('Delete Error:', error);
                console.log('Response:', xhr.responseText);
                
                alert('Failed to delete project. Check console for details.');
                deleteBtn.prop('disabled', false).html(originalHtml);
            }
        });
    });
    
    // File input preview
    $('#project_file').change(function() {
        var fileName = $(this).val().split('\\').pop();
        var fileSize = this.files[0] ? (this.files[0].size / 1024 / 1024).toFixed(2) : 0;
        
        if (fileName) {
            var fileInfo = fileName + ' (' + fileSize + ' MB)';
            $('.file-name-display').text(fileInfo);
            
            // Validate file size (10MB max)
            if (fileSize > 10) {
                showError('File size must be less than 10MB');
                $(this).val('');
                $('.file-name-display').text('');
            }
        } else {
            $('.file-name-display').text('');
        }
    });
    
    // Helper function to show error messages
    function showError(message) {
        // Remove existing error
        $('.error-message').remove();
        
        // Create error element
        var errorHtml = '<div class="error-message" style="background-color: #f8d7da; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 15px; border: 1px solid #f5c6cb;">' +
                       '<strong>Error:</strong> ' + message +
                       '</div>';
        
        // Insert at top of form
        $('#addProjectForm').prepend(errorHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.error-message').fadeOut(300, function() {
                $(this).remove();
            });
        }, 5000);
    }
    
    // Edit button handler (if you have edit functionality)
    $('.edit-btn').click(function(e) {
        e.preventDefault();
        var projectId = $(this).data('id');
        window.location.href = 'edit.php?id=' + projectId;
    });
    
    // Download button handler
    $('.download-btn').click(function(e) {
        e.preventDefault();
        var filePath = $(this).data('file');
        
        if (filePath) {
            window.location.href = 'download.php?file=' + encodeURIComponent(filePath);
        } else {
            alert('No file available for download');
        }
    });
    
});

// Form validation (optional but recommended)
function validateForm() {
    var isValid = true;
    var errorMessages = [];
    
    // Check project name
    var projectName = $('#project_name').val().trim();
    if (projectName === '') {
        errorMessages.push('Project name is required');
        isValid = false;
    }
    
    // Check description
    var description = $('#project_description').val().trim();
    if (description === '') {
        errorMessages.push('Project description is required');
        isValid = false;
    }
    
    // Check category
    var category = $('#project_category').val();
    if (category === '' || category === null) {
        errorMessages.push('Please select a category');
        isValid = false;
    }
    
    // Check file
    var file = $('#project_file')[0].files[0];
    if (!file) {
        errorMessages.push('Please select a file to upload');
        isValid = false;
    }
    
    if (!isValid) {
        alert('Please fix the following errors:\n\n' + errorMessages.join('\n'));
    }
    
    return isValid;
}