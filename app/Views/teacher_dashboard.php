<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Welcome Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h2 class="card-title text-dark">Welcome Teacher!</h2>
                    <p class="text-muted mb-0">Manage your courses and upload materials</p>
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

    <div class="row g-4 mb-4">
        <!-- Row 1 -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100 border border-primary border-3 bg-primary bg-opacity-10">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-primary p-4 rounded-circle d-inline-block shadow">
                            <i class="bi bi-file-earmark-text text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h4 class="card-title fw-bold text-primary mb-3">📝 Create Assignment</h4>
                    <p class="card-text text-muted mb-4 fw-semibold">Create assignments for your courses.</p>
                    <button class="btn btn-primary btn-lg w-100 shadow-sm fw-bold" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                        <i class="bi bi-plus-circle-fill me-2"></i>CREATE NEW ASSIGNMENT
                    </button>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block">
                            <i class="bi bi-plus-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">Create Course</h5>
                    <p class="card-text text-muted mb-4">Create a new course for your students.</p>
                    <a href="<?= base_url('course/teacher') ?>" class="btn btn-success w-100">
                        <i class="bi bi-plus me-2"></i>Create Course
                    </a>
                </div>
            </div>
        </div>

        <!-- Row 2 -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-inline-block">
                            <i class="bi bi-megaphone text-warning" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">Announcements</h5>
                    <p class="card-text text-muted mb-4">Post and view important announcements for students.</p>
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

        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block">
                            <i class="bi bi-upload text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">Upload Materials</h5>
                    <p class="card-text text-muted mb-4">Upload course materials for your classes.</p>
                    <button class="btn btn-info w-100" disabled>
                        <i class="bi bi-upload me-2"></i>Upload Materials
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- My Courses Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="bi bi-book me-2"></i>My Courses</h5>
                </div>
                <div class="card-body">
                    <?php
                    $userId = session()->get('userId');
                    $db = \Config\Database::connect();
                    $courses = $db->table('courses')
                                 ->where('teacher_id', $userId)
                                 ->orderBy('created_at', 'DESC')
                                 ->get()
                                 ->getResultArray();
                    ?>

                    <?php if (!empty($courses)): ?>
                        <div class="row">
                            <?php foreach ($courses as $course): ?>
                                <div class="col-md-6 col-lg-4 mb-4">
                                    <div class="card h-100 border">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title">
                                                <i class="bi bi-book-fill text-primary me-2"></i>
                                                <?= esc($course['title']) ?>
                                            </h6>
                                            <p class="card-text text-muted small flex-grow-1">
                                                <?= esc($course['description']) ?>
                                            </p>
                                            <div class="mt-auto">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <div class="d-flex gap-2 flex-column">
                                                <button class="btn btn-success btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#createAssignmentModal" onclick="setCourseForAssignment(<?= $course['id'] ?>, '<?= esc($course['title'], 'js') ?>')">
                                                    <i class="bi bi-plus-circle me-1"></i> Create Assignment
                                                </button>
                                                <div class="d-flex gap-2">
                                                    <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-primary btn-sm flex-grow-1">
                                                        <i class="bi bi-upload me-1"></i> Upload Material
                                                    </a>
                                                    <a href="<?= base_url('course/view/' . $course['id']) ?>" class="btn btn-outline-secondary btn-sm">
                                                        <i class="bi bi-eye me-1"></i> View
                                                    </a>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">No Courses Assigned</h4>
                            <p class="text-muted">You don't have any courses assigned to you yet. Contact an administrator to assign courses.</p>
                        </div>
                    <?php endif; ?>
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
        // Reset modal title
        $('#createAssignmentModalLabel').html('<i class="bi bi-file-earmark-text me-2"></i>Create New Assignment');
    });

    // Function to load courses
    function loadCoursesForAssignment() {
        return $.get('<?= base_url('assignment/courses') ?>')
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

    // Function to pre-select course when creating assignment from course card
    window.setCourseForAssignment = function(courseId, courseTitle) {
        // Load courses first, then select the specific course
        loadCoursesForAssignment().then(function() {
            $('#assignmentCourse').val(courseId);
            // Optional: Update modal title to indicate which course
            $('#createAssignmentModalLabel').html('<i class="bi bi-file-earmark-text me-2"></i>Create Assignment for ' + courseTitle);
        });
    };
});
</script>
<?= $this->endSection() ?>
