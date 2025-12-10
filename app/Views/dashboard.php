<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="mb-0 text-light">Dashboard</h1>
    <div>
        <?php if ($userRole === 'student'): ?>
            <button class="btn btn-info me-2" onclick="showStudentAssignments()">
                <i class="bi bi-file-earmark-text me-1"></i>My Assignments
            </button>
        <?php elseif ($userRole === 'teacher'): ?>
            <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#createAssignmentModal">
                <i class="bi bi-plus-circle me-1"></i>Create Assignment
            </button>
        <?php endif; ?>
        <a href="<?= base_url('logout') ?>" class="btn btn-outline-primary">Logout</a>
    </div>
</div>

<div class="alert alert-success" role="alert">
    Welcome, <?= esc(session('userName')) ?>!
</div>

<?php
// Load recent announcements for students
$announcements = $db->table('announcements')
                   ->orderBy('created_at', 'DESC')
                   ->limit(3) // Show only 3 recent announcements
                   ->get()
                   ->getResultArray();
?>

<?php if (!empty($announcements)): ?>
<!-- Recent Announcements Section -->
<div class="card shadow-sm border-0 bg-dark text-light mb-4">
    <div class="card-header bg-warning text-dark">
        <h5 class="mb-0"><i class="bi bi-megaphone me-2"></i>Recent Announcements</h5>
    </div>
    <div class="card-body">
        <?php foreach ($announcements as $announcement): ?>
            <div class="mb-3 pb-3 border-bottom border-secondary">
                <h6 class="text-warning mb-2"><?= esc($announcement['title']) ?></h6>
                <p class="text-light small mb-1"><?= nl2br(esc($announcement['content'])) ?></p>
                <small class="text-muted">
                    <i class="bi bi-calendar me-1"></i>
                    <?= date('M d, Y \a\t g:i A', strtotime($announcement['created_at'])) ?>
                </small>
            </div>
        <?php endforeach; ?>
        <div class="text-center">
            <a href="<?= base_url('announcements') ?>" class="btn btn-outline-warning btn-sm">View All Announcements</a>
        </div>
    </div>
</div>
<?php endif; ?>

<?php
$userRole = session()->get('userRole');
$userId = session()->get('userId');

if ($userRole === 'student'):
    // Load enrolled courses for students
    $db = \Config\Database::connect();
    $enrollments = $db->table('enrollments')
                     ->select('courses.title, courses.description, enrollments.enrolled_at, courses.id as course_id')
                     ->join('courses', 'courses.id = enrollments.course_id')
                     ->where('enrollments.user_id', $userId)
                     ->orderBy('enrollments.enrolled_at', 'DESC')
                     ->get()
                     ->getResultArray();

    // Load available courses for students
    $availableCourses = $db->table('courses')
                          ->select('courses.*, users.name as teacher_name')
                          ->join('users', 'users.id = courses.teacher_id', 'left')
                          ->whereNotIn('courses.id', array_column($enrollments, 'course_id') ?: [0])
                          ->orderBy('courses.created_at', 'DESC')
                          ->limit(6) // Limit to 6 for dashboard
                          ->get()
                          ->getResultArray();
?>

    <!-- Available Courses Section -->
    <?php if (!empty($availableCourses)): ?>
    <div class="card shadow-sm border-0 bg-dark text-light mb-4">
        <div class="card-header bg-info text-white">
            <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Available Courses</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <?php foreach ($availableCourses as $course): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100 bg-secondary text-light">
                            <div class="card-body">
                                <h6 class="card-title"><?= esc($course['title']) ?></h6>
                                <p class="card-text small"><?= esc($course['description']) ?></p>
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i>Teacher: <?= esc($course['teacher_name'] ?? 'Unknown') ?>
                                </small>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                </small>
                            </div>
                            <div class="card-footer bg-transparent">
                                <button class="btn btn-success btn-sm enroll-btn w-100"
                                        data-course-id="<?= $course['id'] ?>"
                                        data-course-title="<?= esc($course['title']) ?>">
                                    <i class="bi bi-plus-circle me-1"></i> Enroll
                                </button>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-3">
                <a href="<?= base_url('courses') ?>" class="btn btn-outline-primary">Browse All Courses</a>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Enrolled Courses Section -->
    <div class="card shadow-sm border-0 bg-dark text-light mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0"><i class="bi bi-check-circle me-2"></i>My Enrolled Courses</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($enrollments)): ?>
                <div class="row">
                    <?php foreach ($enrollments as $enrollment): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 bg-secondary text-light">
                                <div class="card-body">
                                    <h6 class="card-title"><?= esc($enrollment['title']) ?></h6>
                                    <p class="card-text small"><?= esc($enrollment['description']) ?></p>
                                    <small class="text-muted">Enrolled: <?= date('M d, Y', strtotime($enrollment['enrolled_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">You haven't enrolled in any courses yet.</p>
            <?php endif; ?>
        </div>
    </div>

<?php elseif ($userRole === 'teacher'):
    // Load courses created by teacher
    $db = \Config\Database::connect();
    $courses = $db->table('courses')
                 ->where('teacher_id', $userId)
                 ->orderBy('created_at', 'DESC')
                 ->get()
                 ->getResultArray();
?>
    <div class="card shadow-sm border-0 bg-dark text-light mb-4">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="bi bi-book me-2"></i>My Courses</h5>
        </div>
        <div class="card-body">
            <?php if (!empty($courses)): ?>
                <div class="row">
                    <?php foreach ($courses as $course): ?>
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 bg-secondary text-light">
                                <div class="card-body">
                                    <h6 class="card-title"><?= esc($course['title']) ?></h6>
                                    <p class="card-text small"><?= esc($course['description']) ?></p>
                                    <small class="text-muted">Created: <?= date('M d, Y', strtotime($course['created_at'])) ?></small>
                                </div>
                                <div class="card-footer">
                                    <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-primary btn-sm">Upload Material</a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <p class="text-muted mb-0">You haven't created any courses yet.</p>
            <?php endif; ?>
        </div>
    </div>

<?php else: ?>
    <div class="card shadow-sm border-0 bg-dark text-light">
        <div class="card-body">
            <p class="mb-0">This is a protected page only visible after login.</p>
        </div>
    </div>
<?php endif; ?>

<!-- Create Assignment Modal (for teachers) -->
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

<!-- Student Assignments Modal -->
<div class="modal fade" id="studentAssignmentsModal" tabindex="-1" aria-labelledby="studentAssignmentsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentAssignmentsModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>My Assignments
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="studentAssignmentsContent">
                <!-- Assignments content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- jQuery for Enrollment and Assignments -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Handle enrollment (from dashboard)
    $(document).on('click', '.enroll-btn', function(e) {
        e.preventDefault();

        const button = $(this);
        const courseId = button.data('course-id');
        const courseTitle = button.data('course-title');

        button.prop('disabled', true);
        button.html('<i class="bi bi-hourglass-split me-1"></i> Enrolling...');

        $.post('<?= base_url('course/enroll') ?>', {
            course_id: courseId
        })
        .done(function(response) {
            if (response.success) {
                button.removeClass('btn-success').addClass('btn-secondary');
                button.html('<i class="bi bi-check-circle me-1"></i> Enrolled');
                button.prop('disabled', true);

                // Show success message
                alert('Successfully enrolled in "' + courseTitle + '"!');

                // Refresh the page to update enrolled courses and available courses
                location.reload();
            } else {
                alert(response.message || 'Failed to enroll in the course.');
                button.prop('disabled', false);
                button.html('<i class="bi bi-plus-circle me-1"></i> Enroll');
            }
        })
        .fail(function() {
            alert('An error occurred. Please try again.');
            button.prop('disabled', false);
            button.html('<i class="bi bi-plus-circle me-1"></i> Enroll');
        });
    });

    // Load courses for teacher assignment creation when modal is shown
    $('#createAssignmentModal').on('show.bs.modal', function() {
        loadCoursesForTeacherAssignment();
    });

    // Function to load courses for teacher assignment creation
    function loadCoursesForTeacherAssignment() {
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

    // Handle assignment creation for teachers
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

// Function to show student assignments
function showStudentAssignments() {
    // Load assignments content
    fetch('<?= base_url('assignment/student/list') ?>')
        .then(response => response.json())
        .then(data => {
            let content = '';

            if (data.assignments && data.assignments.length > 0) {
                content = '<div class="row">';

                data.assignments.forEach(assignment => {
                    const dueDate = assignment.due_date ?
                        new Date(assignment.due_date).toLocaleString() : 'No due date';
                    const courseName = assignment.course_name || 'Unknown Course';

                    content += `
                        <div class="col-md-6 mb-3">
                            <div class="card h-100 border">
                                <div class="card-body d-flex flex-column">
                                    <h6 class="card-title">
                                        <i class="bi bi-file-earmark-text text-success me-2"></i>
                                        ${assignment.title}
                                    </h6>
                                    <p class="card-text text-muted small mb-1">
                                        <strong>Course:</strong> ${courseName}
                                    </p>
                                    <p class="card-text small text-truncate">
                                        ${assignment.description.substring(0, 100)}${assignment.description.length > 100 ? '...' : ''}
                                    </p>
                                    <div class="mt-auto">
                                        ${assignment.due_date ? `<small class="text-danger">
                                            <i class="bi bi-calendar-x me-1"></i>Due: ${dueDate}
                                        </small><br>` : ''}
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>Posted: ${new Date(assignment.created_at).toLocaleString()}
                                        </small>
                                    </div>
                                </div>
                                <div class="card-footer bg-light">
                                    <button class="btn btn-success btn-sm w-100" onclick="viewAssignmentFromDashboard(${assignment.id})">
                                        <i class="bi bi-eye me-1"></i> View Assignment
                                    </button>
                                </div>
                            </div>
                        </div>
                    `;
                });

                content += '</div>';
            } else {
                content = `
                    <div class="text-center py-5">
                        <i class="bi bi-file-earmark-text text-muted" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mt-3">No Assignments Yet</h4>
                        <p class="text-muted">You don't have any assignments at the moment. Check back later!</p>
                    </div>
                `;
            }

            document.getElementById('studentAssignmentsContent').innerHTML = content;
            new bootstrap.Modal(document.getElementById('studentAssignmentsModal')).show();
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Failed to load assignments. Please try again.');
        });
}

// Function to view assignment details from dashboard
function viewAssignmentFromDashboard(assignmentId) {
    // Load assignment details
    fetch('<?= base_url('assignment/view/') ?>' + assignmentId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignment = data.assignment;
                const modal = document.getElementById('studentAssignmentsModal');

                let content = `
                    <div class="mb-3">
                        <h4>${assignment.title}</h4>
                        <hr>
                    </div>
                    <div class="mb-3">
                        <h6>Description:</h6>
                        <p class="text-muted">${assignment.description.replace(/\n/g, '<br>')}</p>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>
                                Posted: ${new Date(assignment.created_at).toLocaleString()}
                            </small>
                        </div>
                        ${assignment.due_date ? `
                        <div class="col-md-6 text-end">
                            <small class="text-danger">
                                <i class="bi bi-calendar-x me-1"></i>
                                Due: ${new Date(assignment.due_date).toLocaleString()}
                            </small>
                        </div>
                        ` : ''}
                    </div>
                    <div class="mt-3 text-center">
                        <button type="button" class="btn btn-secondary" onclick="new bootstrap.Modal(document.getElementById('studentAssignmentsModal')).show()">
                            <i class="bi bi-arrow-left me-1"></i>Back to Assignments
                        </button>
                    </div>
                `;

                document.getElementById('studentAssignmentsContent').innerHTML = content;
            } else {
                alert('Failed to load assignment details.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while loading the assignment.');
        });
}
</script>
<?= $this->endSection() ?>
