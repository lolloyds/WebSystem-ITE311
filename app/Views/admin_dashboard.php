<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 15px; border: none;">
                <div class="card-body text-center p-5">
                    <h2 class="card-title text-dark mb-3">Welcome to Admin Dashboard</h2>
                    <p class="text-muted mb-4">Manage your Learning Management System efficiently</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3 fw-bold text-dark">Quick Actions</h4>
        </div>
    </div>

    <div class="row g-4">
        <!-- Course Management -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius: 15px; border: none; transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block">
                            <i class="bi bi-collection text-primary" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">Course Management</h5>
                    <p class="card-text text-muted mb-4">Manage courses, view statistics, and update course details.</p>
                    <a href="<?= base_url('course/admin') ?>" class="btn btn-primary w-100">
                        <i class="bi bi-gear me-2"></i>Manage Courses
                    </a>
                </div>
            </div>
        </div>

        <!-- User Management -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius: 15px; border: none; transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block">
                            <i class="bi bi-people text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">User Management</h5>
                    <p class="card-text text-muted mb-4">Add, edit, and manage user accounts and roles.</p>
                    <a href="<?= base_url('manage-users') ?>" class="btn btn-success w-100">
                        <i class="bi bi-person-plus me-2"></i>Manage Users
                    </a>
                </div>
            </div>
        </div>

        <!-- Announcement Management -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius: 15px; border: none; transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-inline-block">
                            <i class="bi bi-megaphone text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">Announcements</h5>
                    <p class="card-text text-muted mb-4">Create and manage system announcements.</p>
                    <div class="d-grid gap-2">
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                            <i class="bi bi-plus-circle me-2"></i>Create Announcement
                        </button>
                        <a href="<?= base_url('announcements') ?>" class="btn btn-outline-warning">
                            <i class="bi bi-eye me-2"></i>View All Announcements
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Assignment Management -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100 border border-primary border-3 bg-primary bg-opacity-10" style="border-radius: 15px; border: none; transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-primary p-4 rounded-circle d-inline-block shadow">
                            <i class="bi bi-file-earmark-text text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="card-title fw-bold text-primary mb-3">📝 Assignments</h4>
                    <p class="card-text text-muted mb-4 fw-semibold">Create and manage course assignments.</p>
                    <button class="btn btn-primary btn-lg w-100 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                        <i class="bi bi-plus-circle-fill me-2"></i>CREATE ASSIGNMENT
                    </button>
                </div>
            </div>
        </div>

        <!-- System Overview -->
        <div class="col-md-6 col-lg-4">
            <div class="card shadow-sm h-100" style="border-radius: 15px; border: none; transition: transform 0.2s;">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block">
                            <i class="bi bi-bar-chart text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">System Overview</h5>
                    <p class="card-text text-muted mb-4">View system statistics and analytics.</p>
                    <button class="btn btn-info w-100" disabled>
                        <i class="bi bi-graph-up me-2"></i>Coming Soon
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Announcement Modal -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAnnouncementModalLabel">
                    <i class="bi bi-megaphone me-2"></i>Create New Announcement
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createAnnouncementForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="announcementTitle" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="announcementTitle" name="title" required>
                        <div class="invalid-feedback">
                            Please provide a title for the announcement.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="announcementContent" class="form-label">Content <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="announcementContent" name="content" rows="5" required
                                  placeholder="Enter the announcement content here..."></textarea>
                        <div class="invalid-feedback">
                            Please provide content for the announcement.
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-warning">
                        <i class="bi bi-send me-1"></i>Post Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Create Assignment Modal -->
<div class="modal fade" id="createAssignmentModal" tabindex="-1" aria-labelledby="createAssignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAssignmentModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Create New Assignment
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="createAssignmentForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="assignmentCourse" class="form-label">Course <span class="text-danger">*</span></label>
                        <select class="form-control" id="assignmentCourse" name="course_id" required>
                            <option value="">Select a course...</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a course for the assignment.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="assignmentTitle" class="form-label">Title <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="assignmentTitle" name="title" required>
                        <div class="invalid-feedback">
                            Please provide a title for the assignment.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="assignmentDescription" class="form-label">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="assignmentDescription" name="description" rows="5" required
                                  placeholder="Enter the assignment description and instructions..."></textarea>
                        <div class="invalid-feedback">
                            Please provide a description for the assignment.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="assignmentDueDate" class="form-label">Due Date (Optional)</label>
                        <input type="datetime-local" class="form-control" id="assignmentDueDate" name="due_date">
                        <div class="form-text">Leave empty if no due date is required.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>Create Assignment
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1) !important;
}

body {
    background-color: #f8f9fa;
}
</style>

<script>
$(document).ready(function() {
    // Handle announcement creation
    $('#createAnnouncementForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();

        // Basic validation
        const title = $('#announcementTitle').val().trim();
        const content = $('#announcementContent').val().trim();

        if (!title || !content) {
            if (!title) $('#announcementTitle').addClass('is-invalid');
            if (!content) $('#announcementContent').addClass('is-invalid');
            return;
        }

        // Clear validation errors
        form.find('.is-invalid').removeClass('is-invalid');

        // Disable button and show loading
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Posting...');

        // Submit announcement
        $.ajax({
            url: '<?= base_url('announcement/create') ?>',
            type: 'POST',
            data: {
                title: title,
                content: content
            },
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#createAnnouncementModal').modal('hide');

                    // Reset form
                    form[0].reset();

                    // Show success message
                    alert('Announcement posted successfully!');

                    // Optionally refresh page or update UI
                    // location.reload();
                } else {
                    alert(response.message || 'Failed to post announcement.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                // Re-enable button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });

    // Clear validation on input
    $('#announcementTitle, #announcementContent').on('input', function() {
        $(this).removeClass('is-invalid');
    });

    // Load courses for assignment creation when modal is shown
    $('#createAssignmentModal').on('show.bs.modal', function() {
        loadCoursesForAssignment();
    });

    // Function to load courses
    function loadCoursesForAssignment() {
        $.get('<?= base_url('assignment/courses') ?>')
            .done(function(response) {
                if (response.courses) {
                    let options = '<option value="">Select a course...</option>';
                    response.courses.forEach(function(course) {
                        options += `<option value="${course.id}">${course.title}</option>`;
                    });
                    $('#assignmentCourse').html(options);
                }
            })
            .fail(function() {
                alert('Failed to load courses. Please try again.');
            });
    }

    // Handle assignment creation
    $('#createAssignmentForm').on('submit', function(e) {
        e.preventDefault();

        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        const originalText = submitBtn.html();

        // Basic validation
        const courseId = $('#assignmentCourse').val();
        const title = $('#assignmentTitle').val().trim();
        const description = $('#assignmentDescription').val().trim();

        if (!courseId || !title || !description) {
            if (!courseId) $('#assignmentCourse').addClass('is-invalid');
            if (!title) $('#assignmentTitle').addClass('is-invalid');
            if (!description) $('#assignmentDescription').addClass('is-invalid');
            return;
        }

        // Clear validation errors
        form.find('.is-invalid').removeClass('is-invalid');

        // Disable button and show loading
        submitBtn.prop('disabled', true);
        submitBtn.html('<i class="bi bi-hourglass-split me-1"></i>Creating...');

        // Prepare form data
        const formData = new FormData(this);

        // Submit assignment
        $.ajax({
            url: '<?= base_url('assignment/create') ?>',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Close modal
                    $('#createAssignmentModal').modal('hide');

                    // Reset form
                    form[0].reset();

                    // Show success message
                    alert('Assignment created successfully!');

                    // Optionally refresh page or update UI
                    // location.reload();
                } else {
                    alert(response.message || 'Failed to create assignment.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            },
            complete: function() {
                // Re-enable button
                submitBtn.prop('disabled', false);
                submitBtn.html(originalText);
            }
        });
    });

    // Clear validation on input
    $('#assignmentCourse, #assignmentTitle, #assignmentDescription').on('input change', function() {
        $(this).removeClass('is-invalid');
    });
});
</script>
<?= $this->endSection() ?>
