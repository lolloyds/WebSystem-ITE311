<!-- Upload Material Modal -->
<div class="modal fade" id="uploadMaterialModal" tabindex="-1" aria-labelledby="uploadMaterialModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="uploadMaterialModalLabel">
                    <i class="bi bi-cloud-upload me-2"></i>Upload Learning Material
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="uploadMaterialForm" enctype="multipart/form-data">
                <div class="modal-body">
                    <input type="hidden" id="courseId" name="course_id">

                    <div class="mb-3">
                        <label for="materialFile" class="form-label">Select File</label>
                        <input type="file" class="form-control" id="materialFile" name="file"
                               accept=".pdf,.ppt,.pptx,.doc,.docx" required>
                        <div class="form-text">
                            Allowed formats: PDF, PPT, PPTX, DOC, DOCX. Maximum size: 10MB.
                        </div>
                    </div>

                    <div class="mb-3">
                        <label for="materialDescription" class="form-label">Description (Optional)</label>
                        <textarea class="form-control" id="materialDescription" name="description"
                                  rows="3" placeholder="Add a description for this material..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-cloud-upload me-2"></i>Upload
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
