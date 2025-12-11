<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<!-- Dashboard Header -->
<div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-3 shadow-sm"
     style="background: linear-gradient(90deg, #0d6efd, #0dcaf0); color: #fff;">
    <h1 class="h3 mb-0 d-flex align-items-center">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard ⚡
    </h1>
    <div class="d-flex align-items-center gap-2">
        <?php
            $userName = session()->get('userName');
            $userRole = ucfirst((string) session()->get('userRole'));
            $role     = $role ?? strtolower($userRole) ?? 'student';

            // Debug: Show role for troubleshooting (uncomment if needed)
            // echo "<!-- DEBUG: userRole='$userRole', role='$role' -->";
        ?>
        <?php if ($role === 'student'): ?>
            <button class="btn btn-info btn-sm" onclick="showStudentAssignments()">
                <i class="bi bi-file-earmark-text me-1"></i>Assignments
            </button>
            <button class="btn btn-success btn-sm" onclick="showStudentGrades()">
                <i class="bi bi-award me-1"></i>Grades
            </button>
        <?php endif; ?>
        <div class="text-end ms-3">
            <div class="fw-semibold">Welcome, <strong><?= esc($userName) ?></strong> 👋</div>
            <div class="small">
                <i class="bi bi-person-badge me-1"></i> Role: <?= esc($userRole) ?>
            </div>
        </div>
    </div>
</div>


<?php if ($role === 'admin'): ?>
    <!-- Admin Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h2 class="card-title text-dark">Welcome admin!</h2>
                    <a href="<?= base_url('manage-users') ?>" class="btn btn-primary mt-3">
                        <i class="bi bi-people-fill me-2"></i> Manage Users
                    </a>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Admin Dashboard -->
    <div class="row g-3">
        <!-- Total Users -->
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width:60px; height:60px;">
                        <i class="bi bi-people-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?= esc($totalUsers ?? 0) ?></div>
                        <small class="text-muted">Total Users 👥</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Users Table -->
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3 mt-3">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-clock-history me-2"></i> Recent Users 🕒
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>#</th>
                                    <th><i class="bi bi-person me-1"></i> Name</th>
                                    <th><i class="bi bi-envelope me-1"></i> Email</th>
                                    <th><i class="bi bi-shield-lock me-1"></i> Role</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!empty($recentUsers)): ?>
                                    <?php foreach ($recentUsers as $u): ?>
                                        <tr>
                                            <td><?= esc($u['id']) ?></td>
                                            <td><?= esc($u['name']) ?></td>
                                            <td><?= esc($u['email']) ?></td>
                                            <td><?= esc($u['role']) ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="4" class="text-center text-muted">No recent users 😶</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

<?php elseif ($role === 'teacher'): ?>
    <!-- Teacher Dashboard -->
    <div class="card shadow-sm border-0 rounded-3">
        <div class="card-header bg-primary text-white fw-semibold d-flex justify-content-between align-items-center">
            <span><i class="bi bi-journal-text me-2"></i> My Courses ✏️</span>
            <a href="#" class="btn btn-sm btn-light text-primary fw-semibold">
                <i class="bi bi-plus-circle me-1"></i> Create Course ➕
            </a>
        </div>
        <ul class="list-group list-group-flush">
            <?php if (!empty($myCourses)): ?>
                <?php foreach ($myCourses as $c): ?>
                    <li class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <span><i class="bi bi-book me-2 text-success"></i> <?= esc($c['title']) ?> 📖</span>
                            <span class="text-muted small">Created: <?= esc($c['created_at'] ?? '') ?></span>
                        </div>
                        <a href="<?= base_url('materials/upload/' . $c['id']) ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-upload me-1"></i> Upload Materials
                        </a>
                    </li>
                <?php endforeach; ?>
            <?php else: ?>
                <li class="list-group-item text-muted">No courses yet 😔</li>
            <?php endif; ?>
        </ul>
    </div>

<?php elseif ($role === 'student'): ?>
    <!-- Student Dashboard -->
    <div class="row g-3">
        <!-- Enrolled Courses Section -->
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-success text-white fw-semibold">
                    <i class="bi bi-check-circle me-2"></i> My Enrolled Courses ✅
                </div>
                <div class="card-body p-0">
                    <div id="enrolled-courses-list">
                        <?php if (!empty($enrolledCourses)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($enrolledCourses as $course): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <i class="bi bi-book-fill text-success me-2"></i>
                                                <?= esc($course['title']) ?>
                                            </h6>
                                            <p class="mb-1 text-muted small"><?= esc($course['description']) ?></p>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-check me-1"></i>
                                                Enrolled: <?= date('M d, Y', strtotime($course['enrolled_at'])) ?>
                                            </small>
                                        </div>
                                        <div class="d-flex gap-2 align-items-center">
                                            <a href="<?= site_url('course/view/' . $course['id']) ?>" class="btn btn-primary btn-sm">
                                                <i class="bi bi-eye me-1"></i> View Materials
                                            </a>
                                            <span class="badge bg-success">Enrolled</span>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-bookmark-x fs-1 d-block mb-2"></i>
                                <p class="mb-0">You haven't enrolled in any courses yet.</p>
                                <small>Browse available courses below to get started!</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Available Courses Section -->
        <div class="col-12">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-header bg-primary text-white fw-semibold">
                    <i class="bi bi-collection-play me-2"></i> Available Courses 🎓
                </div>
                <div class="card-body p-0">
                    <div id="available-courses-list">
                        <?php if (!empty($availableCourses)): ?>
                            <div class="list-group list-group-flush">
                                <?php foreach ($availableCourses as $course): ?>
                                    <div class="list-group-item d-flex justify-content-between align-items-center" id="course-<?= $course['id'] ?>">
                                        <div class="flex-grow-1">
                                            <h6 class="mb-1">
                                                <i class="bi bi-bookmark-star text-warning me-2"></i>
                                                <?= esc($course['title']) ?>
                                            </h6>
                                            <p class="mb-1 text-muted small"><?= esc($course['description']) ?></p>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-plus me-1"></i>
                                                Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                            </small>
                                        </div>
                                        <div>
                                            <button class="btn btn-primary btn-sm enroll-btn" 
                                                    data-course-id="<?= $course['id'] ?>"
                                                    data-course-title="<?= esc($course['title']) ?>">
                                                <i class="bi bi-plus-circle me-1"></i> Enroll
                                            </button>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="p-4 text-center text-muted">
                                <i class="bi bi-bookmark-x fs-1 d-block mb-2"></i>
                                <p class="mb-0">No courses available at the moment.</p>
                                <small>Check back later for new courses!</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Container for Messages -->
    <div id="alert-container" class="position-fixed" style="top: 20px; right: 20px; z-index: 1050;"></div>

    <!-- jQuery and AJAX Enrollment Script -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
    $(document).ready(function() {
        // Handle enrollment button clicks
        $('.enroll-btn').on('click', function(e) {
            e.preventDefault();
            
            const button = $(this);
            const courseId = button.data('course-id');
            const courseTitle = button.data('course-title');
            
            // Disable button and show loading state
            button.prop('disabled', true);
            button.html('<i class="bi bi-hourglass-split me-1"></i> Enrolling...');
            
            // Send AJAX request
            $.post('<?= base_url('course/enroll') ?>', {
                course_id: courseId
            })
            .done(function(response) {
                if (response.success) {
                    // Show success message
                    showAlert('success', 'Successfully enrolled in "' + courseTitle + '"!');
                    
                    // Remove the course from available courses
                    $('#course-' + courseId).fadeOut(500, function() {
                        $(this).remove();
                        
                        // Check if no more courses available
                        if ($('#available-courses-list .list-group-item').length === 0) {
                            $('#available-courses-list').html(`
                                <div class="p-4 text-center text-muted">
                                    <i class="bi bi-check-circle fs-1 d-block mb-2 text-success"></i>
                                    <p class="mb-0">All available courses enrolled!</p>
                                    <small>Great job! You're enrolled in all available courses.</small>
                                </div>
                            `);
                        }
                    });
                    
                    // Add to enrolled courses list
                    addToEnrolledCourses(courseId, courseTitle, response.enrollment_id);
                    // Immediately refresh notifications (real-time UX)
                    if (typeof window.fetchNotifications === 'function') {
                        window.fetchNotifications();
                    }
                    
                } else {
                    // Show error message
                    showAlert('danger', response.message || 'Failed to enroll in the course.');
                    
                    // Re-enable button
                    button.prop('disabled', false);
                    button.html('<i class="bi bi-plus-circle me-1"></i> Enroll');
                }
            })
            .fail(function() {
                // Show error message
                showAlert('danger', 'An error occurred. Please try again.');
                
                // Re-enable button
                button.prop('disabled', false);
                button.html('<i class="bi bi-plus-circle me-1"></i> Enroll');
            });
        });
        
        // Function to show alert messages
        function showAlert(type, message) {
            const alertHtml = `
                <div class="alert alert-${type} alert-dismissible fade show" role="alert">
                    <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-triangle'} me-2"></i>
                    ${message}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            `;
            
            $('#alert-container').html(alertHtml);
            
            // Auto-dismiss after 5 seconds
            setTimeout(function() {
                $('.alert').alert('close');
            }, 5000);
        }
        
        // Function to add course to enrolled courses list
        function addToEnrolledCourses(courseId, courseTitle, enrollmentId) {
            const enrolledList = $('#enrolled-courses-list .list-group');
            const viewUrl = '<?= base_url('course/view') ?>/' + courseId;
            
            // If no enrolled courses yet, create the list
            if (enrolledList.length === 0) {
                $('#enrolled-courses-list').html(`
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div class="flex-grow-1">
                                <h6 class="mb-1">
                                    <i class="bi bi-book-fill text-success me-2"></i>
                                    ${courseTitle}
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Enrolled: ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                </small>
                            </div>
                            <div class="d-flex gap-2 align-items-center">
                                <a href="${viewUrl}" class="btn btn-primary btn-sm">
                                    <i class="bi bi-eye me-1"></i> View Materials
                                </a>
                                <span class="badge bg-success">Enrolled</span>
                            </div>
                        </div>
                    </div>
                `);
            } else {
                // Add to existing list
                const newCourseHtml = `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div class="flex-grow-1">
                            <h6 class="mb-1">
                                <i class="bi bi-book-fill text-success me-2"></i>
                                ${courseTitle}
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-calendar-check me-1"></i>
                                Enrolled: ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                            </small>
                        </div>
                        <div class="d-flex gap-2 align-items-center">
                            <a href="${viewUrl}" class="btn btn-primary btn-sm">
                                <i class="bi bi-eye me-1"></i> View Materials
                            </a>
                            <span class="badge bg-success">Enrolled</span>
                        </div>
                    </div>
                `;
                
                enrolledList.prepend(newCourseHtml);
            }
        }
    });

    // Assignment and Grades Functions
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

                // Show modal with content
                showModal('My Assignments', content);
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load assignments. Please try again.');
            });
    }

    function showStudentGrades() {
        // Load all grades for the student
        fetch('<?= base_url('assignment/getAllGrades') ?>')
            .then(response => response.json())
            .then(data => {
                let content = '<h5 class="mb-4">All My Grades</h5>';

                if (data.grades && data.grades.length > 0) {
                    content += '<div class="row">';

                    data.grades.forEach(grade => {
                        content += `
                            <div class="col-md-6 mb-3">
                                <div class="card h-100 border">
                                    <div class="card-body d-flex flex-column">
                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                            <h6 class="card-title mb-1">${grade.title}</h6>
                                            <span class="badge bg-success fs-6">${grade.grade}</span>
                                        </div>
                                        <p class="card-text text-muted small mb-1">
                                            <i class="bi bi-book me-1"></i>${grade.course_name}
                                        </p>
                                        ${grade.feedback ? `
                                        <p class="card-text small mb-1">
                                            <strong>Feedback:</strong> ${grade.feedback.substring(0, 100)}${grade.feedback.length > 100 ? '...' : ''}
                                        </p>
                                        ` : ''}
                                        <small class="text-muted mt-auto">
                                            <i class="bi bi-calendar-check me-1"></i>Graded: ${new Date(grade.graded_at).toLocaleString()}
                                        </small>
                                    </div>
                                    <div class="card-footer bg-light">
                                        <button class="btn btn-outline-success btn-sm w-100" onclick="viewGradeDetails(${grade.assignment_id})">
                                            <i class="bi bi-eye me-1"></i>View Details
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    });

                    content += '</div>';
                } else {
                    content += `
                        <div class="text-center py-5">
                            <i class="bi bi-award text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">No Grades Yet</h4>
                            <p class="text-muted">You don't have any graded assignments yet. Grades will appear here once your teachers have reviewed your submissions!</p>
                        </div>
                    `;
                }

                // Show modal with content
                showModal('My Grades', content);
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load grades. Please try again.');
            });
    }

    function viewAssignmentFromDashboard(assignmentId) {
        // Load assignment details
        fetch('<?= base_url('assignment/view/') ?>' + assignmentId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const assignment = data.assignment;
                    const content = `
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
                            <a href="<?= site_url('assignment/show/') ?>${assignmentId}" class="btn btn-primary">
                                <i class="bi bi-eye me-1"></i> Go to Assignment Page
                            </a>
                        </div>
                    `;

                    // Update modal content and show
                    const modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
                    document.querySelector('#assignmentModal .modal-title').textContent = assignment.title;
                    document.querySelector('#assignmentModal .modal-body').innerHTML = content;
                    modal.show();
                } else {
                    showAlert('danger', 'Failed to load assignment details.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'An error occurred while loading the assignment.');
            });
    }

    function viewGradeDetails(assignmentId) {
        // Load submission details with grade
        fetch('<?= base_url('assignment/getSubmission/') ?>' + assignmentId)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    const submission = data.submission;
                    const content = `
                        <div class="alert alert-success">
                            <h6><i class="bi bi-check-circle me-2"></i>Assignment Graded</h6>
                            <p class="mb-1">Grade: <strong>${submission.grade}</strong></p>
                            <p class="mb-0">Graded: ${new Date(submission.graded_at).toLocaleString()}</p>
                        </div>
                        ${submission.feedback ? `
                        <div class="mb-3">
                            <h6>Feedback:</h6>
                            <div class="border rounded p-3 bg-light">${submission.feedback.replace(/\n/g, '<br>')}</div>
                        </div>
                        ` : ''}
                        ${submission.file_path ? `
                        <div class="mb-3">
                            <h6>Your Submission:</h6>
                            <a href="${submission.file_path}" target="_blank" class="btn btn-outline-primary">
                                <i class="bi bi-download me-2"></i>Download Your Submission
                            </a>
                        </div>
                        ` : ''}
                    `;

                    // Update modal content and show
                    const modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
                    document.querySelector('#assignmentModal .modal-title').textContent = 'Grade Details';
                    document.querySelector('#assignmentModal .modal-body').innerHTML = content;
                    modal.show();
                } else {
                    showAlert('danger', 'Grade details not found.');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                showAlert('danger', 'Failed to load grade details.');
            });
    }

    function showModal(title, content) {
        // Create modal if it doesn't exist
        if (!document.getElementById('assignmentModal')) {
            const modalHtml = `
                <div class="modal fade" id="assignmentModal" tabindex="-1">
                    <div class="modal-dialog modal-lg">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h5 class="modal-title">${title}</h5>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                ${content}
                            </div>
                        </div>
                    </div>
                </div>
            `;
            document.body.insertAdjacentHTML('beforeend', modalHtml);
        } else {
            document.querySelector('#assignmentModal .modal-title').textContent = title;
            document.querySelector('#assignmentModal .modal-body').innerHTML = content;
        }

        const modal = new bootstrap.Modal(document.getElementById('assignmentModal'));
        modal.show();
    }
    </script>
<?php endif; ?>

<?= $this->endSection() ?>
