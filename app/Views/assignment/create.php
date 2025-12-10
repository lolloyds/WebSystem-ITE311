<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h4 class="card-title mb-0">
                        <i class="bi bi-plus-circle me-2"></i>Create New Assignment
                    </h4>
                </div>
                <div class="card-body">
                    <!-- Success/Error Messages -->
                    <?php if (session()->has('success')): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle me-2"></i><?= session('success') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (session()->has('error')): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle me-2"></i><?= session('error') ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php endif; ?>

                    <!-- Course Information -->
                    <div class="mb-4">
                        <h5 class="text-muted">
                            <i class="bi bi-book me-2"></i>Course: <?= esc($course['title']) ?>
                        </h5>
                        <p class="text-muted small mb-0">Course Code: <?= esc($course['course_code']) ?></p>
                    </div>

                    <!-- Assignment Form -->
                    <form id="assignmentForm">
                        <input type="hidden" name="course_id" value="<?= $course['id'] ?>">

                        <div class="mb-3">
                            <label for="title" class="form-label">
                                <i class="bi bi-tag me-1"></i>Assignment Title <span class="text-danger">*</span>
                            </label>
                            <input type="text" class="form-control" id="title" name="title" required
                                   placeholder="Enter assignment title">
                            <div class="invalid-feedback">
                                Please provide a title for the assignment.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">
                                <i class="bi bi-textarea me-1"></i>Description <span class="text-danger">*</span>
                            </label>
                            <textarea class="form-control" id="description" name="description" rows="5" required
                                      placeholder="Enter assignment description and instructions"></textarea>
                            <div class="invalid-feedback">
                                Please provide a description for the assignment.
                            </div>
                        </div>

                        <div class="mb-4">
                            <label for="due_date" class="form-label">
                                <i class="bi bi-calendar-x me-1"></i>Due Date (Optional)
                            </label>
                            <input type="datetime-local" class="form-control" id="due_date" name="due_date">
                            <div class="form-text">
                                Leave empty if there's no specific due date.
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-success" id="submitBtn">
                                <i class="bi bi-check-circle me-2"></i>Create Assignment
                            </button>
                            <a href="<?= site_url('course/view/' . $course['id']) ?>" class="btn btn-secondary">
                                <i class="bi bi-arrow-left me-2"></i>Back to Course
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('assignmentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;

    // Disable button and show loading
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Creating...';

    // Validate form
    const title = document.getElementById('title').value.trim();
    const description = document.getElementById('description').value.trim();

    if (!title || !description) {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;

        // Show validation errors
        if (!title) {
            document.getElementById('title').classList.add('is-invalid');
        }
        if (!description) {
            document.getElementById('description').classList.add('is-invalid');
        }
        return;
    }

    // Prepare form data
    const formData = new FormData(this);

    // Submit via AJAX
    fetch('<?= site_url('assignment/create') ?>', {
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

            // Reset form
            document.getElementById('assignmentForm').reset();

            // Scroll to top
            window.scrollTo(0, 0);

            // Redirect to assignment show page so students can see it immediately
            setTimeout(() => {
                window.location.href = '<?= site_url('assignment/show/') ?>' + data.assignment_id;
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
            <i class="bi bi-exclamation-triangle me-2"></i>An error occurred while creating the assignment.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        const cardBody = document.querySelector('.card-body');
        cardBody.insertBefore(alertDiv, cardBody.firstChild);
    })
    .finally(() => {
        // Re-enable button
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
});

// Remove validation error styling on input
document.getElementById('title').addEventListener('input', function() {
    this.classList.remove('is-invalid');
});

document.getElementById('description').addEventListener('input', function() {
    this.classList.remove('is-invalid');
});
</script>
<?= $this->endSection() ?>
