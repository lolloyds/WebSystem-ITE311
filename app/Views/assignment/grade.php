<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-warning text-dark">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-pencil-square me-2"></i>Grade Assignment
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Assignment and Student Info -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <h5 class="text-muted">Assignment</h5>
                            <p class="mb-1"><strong>Title:</strong> <?= esc($submission['assignment_title']) ?></p>
                            <p class="mb-1"><strong>Course:</strong> <?= esc($submission['course_title']) ?></p>
                        </div>
                        <div class="col-md-6">
                            <h5 class="text-muted">Student</h5>
                            <p class="mb-1"><strong>Name:</strong> <?= esc($submission['student_name']) ?></p>
                            <p class="mb-1"><strong>Submitted:</strong> <?= date('M d, Y H:i', strtotime($submission['submitted_at'])) ?></p>
                            <?php if ($submission['status'] === 'late'): ?>
                                <span class="badge bg-danger">Late Submission</span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Submission Details -->
                    <div class="mb-4">
                        <h5 class="text-muted">Submission Details</h5>
                        <div class="border rounded p-3">
                            <?php if ($submission['file_path']): ?>
                                <p class="mb-2"><strong>File:</strong>
                                    <a href="<?= base_url($submission['file_path']) ?>" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                        <i class="bi bi-download me-1"></i>Download Submission
                                    </a>
                                </p>
                            <?php endif; ?>
                            <?php if ($submission['notes']): ?>
                                <p class="mb-0"><strong>Notes:</strong></p>
                                <div class="border-start border-info border-3 ps-3">
                                    <?= nl2br(esc($submission['notes'])) ?>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Grading Form -->
                    <form id="gradingForm">
                        <input type="hidden" name="submission_id" value="<?= $submission['id'] ?>">

                        <div class="mb-3">
                            <label for="grade" class="form-label">
                                <i class="bi bi-award me-1"></i>Grade <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="grade" name="grade"
                                   value="<?= esc($submission['grade'] ?? '') ?>" required
                                   placeholder="e.g., A, B+, 95, Pass">
                            <div class="form-text">
                                Enter grade as letter (A, B, C), percentage (90%), or points (85/100)
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="feedback" class="form-label">
                                <i class="bi bi-chat-quote me-1"></i>Feedback/Comments
                            </label>
                            <textarea class="form-control" id="feedback" name="feedback" rows="5"
                                      placeholder="Provide constructive feedback for the student..."><?= esc($submission['feedback'] ?? '') ?></textarea>
                            <div class="form-text">
                                Optional: Provide detailed feedback to help the student improve
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" id="saveBtn">
                                <i class="bi bi-check-circle me-2"></i>Save Grade
                            </button>
                            <a href="javascript:history.back()" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Submissions
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('gradingForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const saveBtn = document.getElementById('saveBtn');
    const originalText = saveBtn.innerHTML;

    // Disable button and show loading
    saveBtn.disabled = true;
    saveBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Saving...';

    // Validate form
    const grade = document.getElementById('grade').value.trim();

    if (!grade) {
        // Re-enable button
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;

        // Show validation error
        document.getElementById('grade').classList.add('is-invalid');
        return;
    }

    // Prepare form data
    const formData = new FormData(this);

    // Submit via AJAX
    fetch('<?= site_url('assignment/saveGrade') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-success alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-check-circle me-2"></i>${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            // Insert at top of card body
            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertDiv, cardBody.firstChild);

            // Scroll to top
            window.scrollTo(0, 0);

            // Redirect back after success
            setTimeout(() => {
                window.location.href = '<?= site_url('assignment/show/' . $submission['assignment_id']) ?>';
            }, 2000);
        } else {
            // Show error message
            const alertDiv = document.createElement('div');
            alertDiv.className = 'alert alert-danger alert-dismissible fade show';
            alertDiv.innerHTML = `
                <i class="bi bi-exclamation-triangle me-2"></i>${data.message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

            const cardBody = document.querySelector('.card-body');
            cardBody.insertBefore(alertDiv, cardBody.firstChild);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        const alertDiv = document.createElement('div');
        alertDiv.className = 'alert alert-danger alert-dismissible fade show';
        alertDiv.innerHTML = `
            <i class="bi bi-exclamation-triangle me-2"></i>An error occurred while saving the grade.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);
    })
    .finally(() => {
        // Re-enable button
        saveBtn.disabled = false;
        saveBtn.innerHTML = originalText;
    });
});

// Remove validation error styling on input
document.getElementById('grade').addEventListener('input', function() {
    this.classList.remove('is-invalid');
});
</script>
<?= $this->endSection() ?>
