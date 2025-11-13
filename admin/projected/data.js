console.log('Testing project cards...');
document.querySelectorAll('.project-card').forEach(card => {
    console.log('Project ID:', card.dataset.projectId);
});