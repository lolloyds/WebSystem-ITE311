/**
 * Course Materials AJAX functionality
 */

class CourseMaterialsManager {
    constructor() {
        this.currentCourseId = null;
        this.uploadModal = null;
        this.materialsList = null;
        this.init();
    }

    init() {
        this.setupEventListeners();
        this.uploadModal = new bootstrap.Modal(document.getElementById('uploadMaterialModal'));
    }

    setupEventListeners() {
        // Upload button click
        document.addEventListener('click', (e) => {
            if (e.target.matches('.upload-material-btn') || e.target.closest('.upload-material-btn')) {
                e.preventDefault();
                const button = e.target.closest('.upload-material-btn');
                this.currentCourseId = button.dataset.courseId;
                this.showUploadModal(this.currentCourseId);
            }
        });

        // Upload form submission
        document.getElementById('uploadMaterialForm').addEventListener('submit', (e) => {
            e.preventDefault();
            this.uploadMaterial();
        });

        // Delete material
        document.addEventListener('click', (e) => {
            if (e.target.matches('.delete-material-btn') || e.target.closest('.delete-material-btn')) {
                e.preventDefault();
                const button = e.target.closest('.delete-material-btn');
                const materialId = button.dataset.materialId;
                this.deleteMaterial(materialId);
            }
        });

        // Preview button
        document.addEventListener('click', (e) => {
            if (e.target.matches('.preview-btn') || e.target.closest('.preview-btn')) {
                e.preventDefault();
                const button = e.target.closest('.preview-btn');
                const materialId = button.dataset.materialId;
                const fileName = button.dataset.fileName;
                this.previewMaterial(materialId, fileName);
            }
        });
    }

    showUploadModal(courseId) {
        document.getElementById('courseId').value = courseId;
        document.getElementById('uploadMaterialForm').reset();
        this.uploadModal.show();
    }

    async uploadMaterial() {
        const form = document.getElementById('uploadMaterialForm');
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        // Disable submit button
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="bi bi-hourglass me-2"></i>Uploading...';

        try {
            const response = await fetch(`/courses/${this.currentCourseId}/materials/upload`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                this.showToast('success', result.message);
                this.uploadModal.hide();
                this.refreshMaterialsList();
            } else {
                this.showToast('error', result.message);
            }
        } catch (error) {
            console.error('Upload error:', error);
            this.showToast('error', 'An error occurred during upload');
        } finally {
            // Re-enable submit button
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="bi bi-cloud-upload me-2"></i>Upload';
        }
    }

    async deleteMaterial(materialId) {
        if (!confirm('Are you sure you want to delete this material? This action cannot be undone.')) {
            return;
        }

        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
        const csrfHash = document.querySelector('meta[name="csrf-hash"]').getAttribute('content');

        try {
            const response = await fetch(`/materials/${materialId}`, {
                method: 'DELETE',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken,
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    [csrfToken]: csrfHash
                })
            });

            const result = await response.json();

            if (result.status === 'success') {
                this.showToast('success', result.message);
                this.refreshMaterialsList();
            } else {
                this.showToast('error', result.message || 'Failed to delete material');
            }
        } catch (error) {
            console.error('Delete error:', error);
            this.showToast('error', 'An error occurred during deletion');
        }
    }

    async refreshMaterialsList() {
        if (!this.currentCourseId) return;

        try {
            const response = await fetch(`/courses/${this.currentCourseId}/materials`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });

            const result = await response.json();

            if (result.status === 'success') {
                this.updateMaterialsList(result.data);
                // Also refresh teacher dashboard materials if we're on that page
                this.refreshTeacherDashboardMaterials(this.currentCourseId, result.data);
            }
        } catch (error) {
            console.error('Refresh error:', error);
        }
    }

    updateMaterialsList(materials) {
        const container = document.getElementById('materialsList');
        if (!container) return;

        if (materials.length === 0) {
            container.innerHTML = `
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    No materials available yet. Upload your first file!
                </div>
            `;
            return;
        }

        const materialsHtml = materials.map(material => `
            <div class="list-group-item d-flex justify-content-between align-items-center material-item" data-material-id="${material.id}">
                <div class="d-flex align-items-center">
                    <i class="bi bi-file-earmark me-3 text-primary fs-4"></i>
                    <div>
                        <h6 class="mb-1">
                            <a href="/materials/download/${material.id}" class="text-decoration-none" target="_blank">
                                ${this.escapeHtml(material.file_name_original)}
                            </a>
                        </h6>
                        ${material.description ? `<p class="mb-1 text-muted small">${this.escapeHtml(material.description)}</p>` : ''}
                        <small class="text-muted">
                            <i class="bi bi-person me-1"></i>Uploaded by ${this.escapeHtml(material.uploader_name)}
                            <i class="bi bi-clock ms-2 me-1"></i>${this.formatDate(material.uploaded_at)}
                        </small>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <a href="/materials/download/${material.id}" class="btn btn-primary btn-sm" title="Download">
                        <i class="bi bi-download"></i>
                    </a>
                    ${this.canDeleteMaterial(material) ? `
                        <button class="btn btn-danger btn-sm delete-material-btn" data-material-id="${material.id}" title="Delete">
                            <i class="bi bi-trash"></i>
                        </button>
                    ` : ''}
                </div>
            </div>
        `).join('');

        container.innerHTML = `<div class="list-group">${materialsHtml}</div>`;
    }

    canDeleteMaterial(material) {
        // Check if user is teacher/admin and owns the material
        const userRole = document.body.dataset.userRole;
        const userId = document.body.dataset.userId;

        return userRole === 'admin' || (userRole === 'teacher' && material.uploaded_by == userId);
    }

    formatDate(dateString) {
        const date = new Date(dateString);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString([], {hour: '2-digit', minute:'2-digit'});
    }

    escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    refreshTeacherDashboardMaterials(courseId, materials) {
        const container = document.getElementById(`materialsList-${courseId}`);
        if (!container) return; // Not on teacher dashboard

        if (materials.length === 0) {
            container.innerHTML = '<small class="text-muted">No materials uploaded yet</small>';
            return;
        }

        // Show up to 3 materials with view/download buttons
        const displayMaterials = materials.slice(0, 3);
        let materialsHtml = `<small class="text-muted fw-bold">Materials (${materials.length}):</small>
                            <div class="mt-1">`;

        displayMaterials.forEach(material => {
            const fileExtension = this.getFileExtension(material.file_name_original).toLowerCase();
            const canPreview = ['pdf', 'txt', 'jpg', 'jpeg', 'png', 'gif'].includes(fileExtension);

            materialsHtml += `
                <div class="d-flex align-items-center justify-content-between mb-2 p-2 border rounded">
                    <div class="d-flex align-items-center flex-grow-1 me-2">
                        <i class="bi bi-file-earmark me-2 text-primary" style="font-size: 0.9rem;"></i>
                        <div class="flex-grow-1">
                            <div class="small fw-medium text-truncate" style="max-width: 150px;" title="${this.escapeHtml(material.file_name_original)}">
                                ${this.escapeHtml(material.file_name_original)}
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-person me-1"></i>${this.escapeHtml(material.uploader_name)}
                                <i class="bi bi-clock ms-2 me-1"></i>${this.formatDate(material.uploaded_at)}
                            </small>
                        </div>
                    </div>
                    <div class="d-flex gap-1">
                        ${canPreview ? `
                            <button class="btn btn-outline-info btn-sm preview-btn"
                                    data-material-id="${material.id}"
                                    data-file-name="${this.escapeHtml(material.file_name_original)}"
                                    title="Preview">
                                <i class="bi bi-eye"></i>
                            </button>
                        ` : ''}
                        <a href="/materials/download/${material.id}"
                           class="btn btn-outline-primary btn-sm"
                           title="Download"
                           target="_blank">
                            <i class="bi bi-download"></i>
                        </a>
                        ${this.canDeleteMaterial(material) ? `
                            <button class="btn btn-outline-danger btn-sm delete-material-btn"
                                    data-material-id="${material.id}"
                                    title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        ` : ''}
                    </div>
                </div>
            `;
        });

        if (materials.length > 3) {
            materialsHtml += `
                <div class="text-center mt-2">
                    <small class="text-muted">
                        +${materials.length - 3} more materials...
                        <a href="/course/view/${courseId}" class="text-decoration-none ms-1">view all in course</a>
                    </small>
                </div>
            `;
        }

        materialsHtml += '</div>';
        container.innerHTML = materialsHtml;

        // Add event listeners for preview buttons
        this.setupPreviewButtons();
    }

    getFileExtension(filename) {
        return filename.split('.').pop();
    }

    setupPreviewButtons() {
        // Preview buttons are already handled in setupEventListeners
        // This method can be used for additional setup if needed
    }

    async previewMaterial(materialId, fileName) {
        try {
            // Open file in new tab for preview (browsers handle preview automatically)
            window.open(`/materials/${materialId}/view`, '_blank');
        } catch (error) {
            console.error('Preview error:', error);
            this.showToast('error', 'Unable to preview file');
        }
    }

    showToast(type, message) {
        // Create toast element if it doesn't exist
        let toastContainer = document.querySelector('.toast-container');
        if (!toastContainer) {
            toastContainer = document.createElement('div');
            toastContainer.className = 'toast-container position-fixed top-0 end-0 p-3';
            toastContainer.style.zIndex = '9999';
            document.body.appendChild(toastContainer);
        }

        const toastId = 'toast-' + Date.now();
        const toastHtml = `
            <div id="${toastId}" class="toast align-items-center text-white bg-${type === 'success' ? 'success' : 'danger'} border-0" role="alert">
                <div class="d-flex">
                    <div class="toast-body">
                        <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                        ${message}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        `;

        toastContainer.insertAdjacentHTML('beforeend', toastHtml);

        const toastElement = document.getElementById(toastId);
        const toast = new bootstrap.Toast(toastElement, { delay: 4000 });
        toast.show();

        // Remove toast element after it's hidden
        toastElement.addEventListener('hidden.bs.toast', () => {
            toastElement.remove();
        });
    }
}

// Initialize when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new CourseMaterialsManager();
});
