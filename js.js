window.addEventListener("load", () => {
  const saved = JSON.parse(localStorage.getItem("projects")) || [];
  saved.forEach(addProjectToPage);
});

function addProject() {
  const name = document.getElementById("projectName").value.trim();
  const fileInput = document.getElementById("projectFile");
  const file = fileInput.files[0];

  if (!name || !file) {
    alert("⚠️ Please enter a project name and choose a file.");
    return;
  }

  const reader = new FileReader();
  reader.onload = function (e) {
    const dataUrl = e.target.result;
    const project = { name, dataUrl, fileType: file.type };
    const projects = JSON.parse(localStorage.getItem("projects")) || [];
    projects.push(project);
    localStorage.setItem("projects", JSON.stringify(projects));
    addProjectToPage(project);
    document.getElementById("projectName").value = "";
    fileInput.value = "";
  };
  reader.readAsDataURL(file);
}

function addProjectToPage(project) {
  const list = document.getElementById("projectList");
  const div = document.createElement("div");
  div.className = "project-item";

  let preview = "";
  if (project.fileType.startsWith("image/")) {
    preview = `<img src="${project.dataUrl}" alt="${project.name}">`;
  } else {
    preview = `<a href="${project.dataUrl}" download="${project.name}">📄 Download File</a>`;
  }

  div.innerHTML = `
    <h3>${project.name}</h3>
    ${preview}
    <button class="delete-btn" onclick="deleteProject('${project.name}')">🗑 Delete</button>
  `;
  list.appendChild(div);
}

function deleteProject(name) {
  let projects = JSON.parse(localStorage.getItem("projects")) || [];
  projects = projects.filter(p => p.name !== name);
  localStorage.setItem("projects", JSON.stringify(projects));
  document.getElementById("projectList").innerHTML = "";
  projects.forEach(addProjectToPage);
}
