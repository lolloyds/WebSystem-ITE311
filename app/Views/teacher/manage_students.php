<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Header Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                        <div>
                            <h2 class="card-title mb-3">Manage Students</h2>
                            <div class="mb-3">
                                <label for="courseSelect" class="form-label fw-bold">Select Course:</label>
                                <select class="form-select" id="courseSelect" name="course_id">
                                    <option value="">-- Choose a course --</option>
                                    <?php if (!empty($courses)): ?>
                                        <?php foreach ($courses as $course): ?>
                                            <option value="<?= esc($course['id']) ?>" <?= (isset($_GET['course_id']) && $_GET['course_id'] == $course['id']) ? 'selected' : '' ?>>
                                                <?= esc($course['title'] . ' (' . $course['course_code'] . ')') ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            <p class="text-muted mb-0">
                                <?php if (!empty($courses)): ?>
                                    Course: <?= esc($courses[0]['title'] . ' (' . $courses[0]['course_code'] . ')') ?>
                                <?php else: ?>
                                    No courses assigned
                                <?php endif; ?>
                            </p>
                        </div>
                </div>
            </div>
        </div>
    </div>



    <!-- Student List Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people me-2"></i>Enrolled Students</h5>
                    <span id="studentCount" class="badge bg-light text-dark"><?= $studentCount ?> student<?= $studentCount !== 1 ? 's' : '' ?></span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="studentsTable">
                            <thead class="table-light">
                                <tr>
                                    <th>Student ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Program</th>
                                    <th>Year Level</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="studentsTableBody">
                                <?php if (!empty($enrolledStudents)): ?>
                                    <?php foreach ($enrolledStudents as $student): ?>
                                        <tr>
                                            <td><?= esc($student['student_id'] ?? 'N/A') ?></td>
                                            <td><?= esc($student['name']) ?></td>
                                            <td><?= esc($student['email']) ?></td>
                                            <td><?= esc($student['program'] ?? 'N/A') ?></td>
                                            <td><?= esc($student['year_level'] ?? 'N/A') ?></td>
                                            <td><?= esc($student['course_name']) ?></td>
                                            <td>
                                                <?php
                                                $status = $student['status'] ?? 'pending';
                                                if ($status === 'approved') {
                                                    echo '<span class="badge bg-success">Enrolled</span>';
                                                } elseif ($status === 'pending') {
                                                    echo '<span class="badge bg-warning">Pending</span>';
                                                } elseif ($status === 'rejected') {
                                                    echo '<span class="badge bg-danger">Rejected</span>';
                                                } else {
                                                    echo '<span class="badge bg-secondary">' . ucfirst($status) . '</span>';
                                                }
                                                ?>
                                            </td>
                                            <td>
                                                <div class="d-flex gap-2">
                                                    <button type="button" class="btn btn-sm btn-outline-success approve-btn" data-student-id="<?= $student['id'] ?>" data-course-id="<?= $student['course_id'] ?>" data-student-name="<?= addslashes($student['name']) ?>" data-course-name="<?= addslashes($student['course_name']) ?>" onclick="approveStudent(this.dataset.studentId, this.dataset.courseId, this.dataset.studentName, this.dataset.courseName)" style="cursor: pointer;">
                                                        <i class="bi bi-check-circle"></i> Approve
                                                    </button>
                                                    <button type="button" class="btn btn-sm btn-outline-danger reject-btn" data-student-id="<?= $student['id'] ?>" data-course-id="<?= $student['course_id'] ?>" data-student-name="<?= addslashes($student['name']) ?>" data-course-name="<?= addslashes($student['course_name']) ?>" onclick="rejectStudent(this.dataset.studentId, this.dataset.courseId, this.dataset.studentName, this.dataset.courseName)" style="cursor: pointer;">
                                                        <i class="bi bi-x-circle"></i> Reject
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Loading indicator -->
                    <div id="loadingIndicator" class="text-center py-4 d-none">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                        <p class="mt-2 text-muted">Loading students...</p>
                    </div>

                    <!-- Empty state -->
                    <div id="emptyState" class="text-center py-5 d-none">
                        <i class="bi bi-people text-muted" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mt-3">No Students Found</h4>
                        <p class="text-muted">Try adjusting your search criteria or filters.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Student Details Modal -->
<div class="modal fade" id="studentDetailsModal" tabindex="-1" aria-labelledby="studentDetailsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="studentDetailsModalLabel">Student Details</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="studentDetailsContent">
                <!-- Student details will be loaded here -->
            </div>
        </div>
    </div>
</div>

<!-- Status Update Modal -->
<div class="modal fade" id="statusUpdateModal" tabindex="-1" aria-labelledby="statusUpdateModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="statusUpdateModalLabel">Approve/Reject Student</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="statusUpdateForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Current Status</label>
                        <input type="text" class="form-control" id="currentStatus" readonly>
                    </div>
                    <div class="mb-3">
                        <label for="newStatus" class="form-label">Action <span class="text-danger">*</span></label>
                        <select class="form-select" id="newStatus" name="new_status" required>
                            <option value="approved">Approve Student</option>
                            <option value="rejected">Reject Student</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="statusRemarks" class="form-label">Remarks</label>
                        <textarea class="form-control" id="statusRemarks" name="remarks" rows="3" placeholder="Optional remarks for approval/rejection..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="statusStudentId" name="student_id">
                    <input type="hidden" id="statusCourseId" name="course_id">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle me-1"></i>Approve/Reject
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Helper function to get cookie value
function getCookie(name) {
    const value = `; ${document.cookie}`;
    const parts = value.split(`; ${name}=`);
    if (parts.length === 2) return parts.pop().split(';').shift();
    return null;
}

// Global functions for action buttons - defined outside document ready for immediate availability
window.displayStudentDetails = function(student) {
    const enrollmentDate = student.enrollments && student.enrollments.length > 0
        ? new Date(student.enrollments[0].enrolled_at).toLocaleDateString()
        : 'N/A';

    const content = `
        <div class="row">
            <div class="col-md-6">
                <h6 class="text-primary">Basic Information</h6>
                <table class="table table-sm">
                    <tr>
                        <td><strong>Student ID:</strong></td>
                        <td>${student.student_id || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Full Name:</strong></td>
                        <td>${student.name}</td>
                    </tr>
                    <tr>
                        <td><strong>Email:</strong></td>
                        <td>${student.email}</td>
                    </tr>
                    <tr>
                        <td><strong>Program:</strong></td>
                        <td>${student.program || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Year Level:</strong></td>
                        <td>${student.year_level || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Section:</strong></td>
                        <td>${student.section || 'N/A'}</td>
                    </tr>
                    <tr>
                        <td><strong>Status:</strong></td>
                        <td>${getStatusBadgeGlobal(student.status)}</td>
                    </tr>
                </table>
            </div>
            <div class="col-md-6">
                <h6 class="text-primary">Enrollment Information</h6>
                <p><strong>Enrollment Date:</strong> ${enrollmentDate}</p>
                ${student.enrollments && student.enrollments.length > 0 ? `
                    <p><strong>Enrolled Courses:</strong></p>
                    <ul class="list-unstyled">
                        ${student.enrollments.map(e => `<li>• ${e.course_name} (${e.course_code})</li>`).join('')}
                    </ul>
                ` : '<p>No enrollment information available.</p>'}
            </div>
        </div>
    `;

    $('#studentDetailsContent').html(content);
};

window.getStatusBadgeGlobal = function(status) {
    if (status === 'approved') {
        return '<span class="badge bg-success">Enrolled</span>';
    } else if (status === 'pending') {
        return '<span class="badge bg-warning">Pending</span>';
    } else if (status === 'rejected') {
        return '<span class="badge bg-danger">Rejected</span>';
    } else {
        return '<span class="badge bg-secondary">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
    }
};

window.viewStudentDetails = function(studentId) {
    $.ajax({
        url: '<?= base_url('teacher/getStudentDetails/') ?>' + studentId,
        type: 'GET',
        success: function(response) {
            if (response.error) {
                alert(response.error);
                return;
            }

            displayStudentDetails(response);
            $('#studentDetailsModal').modal('show');
        },
        error: function() {
            alert('Failed to load student details.');
        }
    });
};

window.updateStudentStatus = function(studentId, courseId, currentStatus) {
    $('#statusStudentId').val(studentId);
    $('#statusCourseId').val(courseId);
    $('#currentStatus').val(currentStatus.charAt(0).toUpperCase() + currentStatus.slice(1));
    $('#newStatus').val(currentStatus);
    $('#statusRemarks').val('');
    $('#statusUpdateModal').modal('show');
};

window.rejectStudent = function(studentId, courseId, studentName, courseName) {
    if (confirm(`Are you sure you want to reject ${studentName} from "${courseName}"? The student will be marked as rejected.`)) {
        updateEnrollmentStatus(studentId, courseId, 'rejected');
    }
};

window.approveStudent = function(studentId, courseId, studentName, courseName) {
    if (confirm(`Are you sure you want to approve ${studentName} for "${courseName}"?`)) {
        updateEnrollmentStatus(studentId, courseId, 'approved');
    }
};

window.updateEnrollmentStatus = function(studentId, courseId, newStatus) {
    // Get CSRF token
    const csrfToken = $('meta[name="X-CSRF-TOKEN"]').attr('content') || getCookie('csrf_cookie_name');

    const data = {
        student_id: studentId,
        course_id: courseId,
        new_status: newStatus
    };

    const ajaxOptions = {
        url: '<?= base_url('teacher/updateStudentStatus') ?>',
        type: 'POST',
        data: data,
        success: function(response) {
            if (response.success) {
                alert(response.message);
                const selectedCourseId = $('#courseSelect').val();
                loadStudents(selectedCourseId); // Reload the table with current course filter
            } else {
                alert('Error: ' + (response.message || 'Failed to update enrollment status.'));
            }
        },
        error: function(xhr, status, error) {
            alert('AJAX Error: ' + xhr.status + ' - ' + error);
        }
    };

    if (csrfToken) {
        ajaxOptions.headers = {
            'X-CSRF-TOKEN': csrfToken
        };
    }

    $.ajax(ajaxOptions);
};

window.removeFromCourse = function(studentId, studentName, courseId, courseName) {
    if (confirm(`Are you sure you want to remove ${studentName} from "${courseName}"?`)) {
        // Get CSRF token
        const csrfToken = $('meta[name="X-CSRF-TOKEN"]').attr('content') || getCookie('csrf_cookie_name');

        const data = {
            student_id: studentId,
            course_id: courseId
        };

        const ajaxOptions = {
            url: '<?= base_url('teacher/removeStudentFromCourse') ?>',
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    alert(response.message);
                    const selectedCourseId = $('#courseSelect').val();
                    loadStudents(selectedCourseId); // Reload the table with current course filter
                } else {
                    alert(response.message || 'Failed to remove student from course.');
                }
            },
            error: function(xhr, status, error) {
                alert('An error occurred. Please try again.');
            }
        };

        if (csrfToken) {
            ajaxOptions.headers = {
                'X-CSRF-TOKEN': csrfToken
            };
        }

        $.ajax(ajaxOptions);
    }
};



$(document).ready(function() {
    // Initialize student count on page load
    const initialStudentCount = $('#studentsTableBody tr').length;
    $('#studentCount').text(initialStudentCount + ' student' + (initialStudentCount !== 1 ? 's' : ''));

    // Handle course selection change
    $('#courseSelect').on('change', function() {
        const selectedCourseId = $(this).val();
        loadStudents(selectedCourseId);
    });

    // Handle approve button clicks using event delegation
    $(document).on('click', '.approve-btn', function(e) {
        e.preventDefault();
        console.log('Approve button clicked!');
        const studentId = $(this).data('student-id');
        const courseId = $(this).data('course-id');
        const studentName = $(this).data('student-name');
        const courseName = $(this).data('course-name');
        console.log('Button data:', {studentId, courseId, studentName, courseName});
        approveStudent(studentId, courseId, studentName, courseName);
    });

    // Handle reject button clicks using event delegation
    $(document).on('click', '.reject-btn', function(e) {
        e.preventDefault();
        console.log('Reject button clicked!');
        const studentId = $(this).data('student-id');
        const courseId = $(this).data('course-id');
        const studentName = $(this).data('student-name');
        const courseName = $(this).data('course-name');
        console.log('Button data:', {studentId, courseId, studentName, courseName});
        rejectStudent(studentId, courseId, studentName, courseName);
    });

    // Debug: Check if event handlers are attached
    console.log('Event handlers attached. Approve buttons:', $('.approve-btn').length, 'Reject buttons:', $('.reject-btn').length);

    // Auto-refresh students list every 30 seconds to show newly enrolled students
    setInterval(function() {
        const selectedCourseId = $('#courseSelect').val();
        loadStudents(selectedCourseId, true); // true = silent refresh
    }, 30000); // 30 seconds

    function loadStudents(courseId = null, silent = false) {
        if (!silent) {
            $('#loadingIndicator').removeClass('d-none');
            $('#emptyState').addClass('d-none');
            $('#studentsTableBody').empty();
        }

        let url = '<?= base_url('teacher/getStudents') ?>';
        if (courseId) {
            url += '?course_id=' + courseId;
        }

        $.ajax({
            url: url,
            type: 'GET',
            success: function(response) {
                if (!silent) {
                    $('#loadingIndicator').addClass('d-none');
                }

                if (response.students && response.students.length > 0) {
                    displayStudents(response.students, silent);
                } else {
                    if (!silent) {
                        $('#emptyState').removeClass('d-none');
                        $('#studentCount').text('0 students');
                    }
                }
            },
            error: function() {
                if (!silent) {
                    $('#loadingIndicator').addClass('d-none');
                    alert('Failed to load students. Please try again.');
                }
            }
        });
    }

    function displayStudents(students, silent = false) {
        const tbody = $('#studentsTableBody');
        tbody.empty();

        students.forEach(function(student) {
            const statusBadge = getStatusBadge(student.status);

            const row = `
                <tr>
                    <td>${student.student_id || 'N/A'}</td>
                    <td>${student.name}</td>
                    <td>${student.email}</td>
                    <td>${student.program || 'N/A'}</td>
                    <td>${student.year_level || 'N/A'}</td>
                    <td>${student.course_name}</td>
                    <td>${statusBadge}</td>
                    <td>
                        <div class="d-flex gap-2">
                            <button type="button" class="btn btn-sm btn-outline-success approve-btn" data-student-id="${student.id}" data-course-id="${student.course_id}" data-student-name="${student.name.replace(/"/g, '"')}" data-course-name="${student.course_name.replace(/"/g, '"')}" onclick="approveStudent(this.dataset.studentId, this.dataset.courseId, this.dataset.studentName, this.dataset.courseName)" style="cursor: pointer;">
                                <i class="bi bi-check-circle"></i> Approve
                            </button>
                            <button type="button" class="btn btn-sm btn-outline-danger reject-btn" data-student-id="${student.id}" data-course-id="${student.course_id}" data-student-name="${student.name.replace(/"/g, '"')}" data-course-name="${student.course_name.replace(/"/g, '"')}" onclick="rejectStudent(this.dataset.studentId, this.dataset.courseId, this.dataset.studentName, this.dataset.courseName)" style="cursor: pointer;">
                                <i class="bi bi-x-circle"></i> Reject
                            </button>
                        </div>
                    </td>
                </tr>
            `;
            tbody.append(row);
        });

        if (!silent) {
            $('#studentCount').text(students.length + ' student' + (students.length !== 1 ? 's' : ''));
        }
    }

    function getStatusBadge(status) {
        status = status || 'pending'; // Default to pending if status is null/undefined
        if (status === 'approved') {
            return '<span class="badge bg-success">Enrolled</span>';
        } else if (status === 'pending') {
            return '<span class="badge bg-warning">Pending</span>';
        } else if (status === 'rejected') {
            return '<span class="badge bg-danger">Rejected</span>';
        } else {
            return '<span class="badge bg-secondary">' + status.charAt(0).toUpperCase() + status.slice(1) + '</span>';
        }
    }

    // Handle status update form submission
    $('#statusUpdateForm').on('submit', function(e) {
        e.preventDefault();

        // Get CSRF token
        const csrfToken = $('meta[name="X-CSRF-TOKEN"]').attr('content') || getCookie('csrf_cookie_name');

        const data = {
            student_id: $('#statusStudentId').val(),
            course_id: $('#statusCourseId').val(),
            new_status: $('#newStatus').val(),
            remarks: $('#statusRemarks').val()
        };

        const ajaxOptions = {
            url: '<?= base_url('teacher/updateStudentStatus') ?>',
            type: 'POST',
            data: data,
            success: function(response) {
                if (response.success) {
                    $('#statusUpdateModal').modal('hide');
                    alert(response.message);
                    const selectedCourseId = $('#courseSelect').val();
                    loadStudents(selectedCourseId); // Reload the table with current course filter
                } else {
                    alert(response.message || 'Failed to update student status.');
                }
            },
            error: function() {
                alert('An error occurred. Please try again.');
            }
        };

        if (csrfToken) {
            ajaxOptions.headers = {
                'X-CSRF-TOKEN': csrfToken
            };
        }

        $.ajax(ajaxOptions);
    });
});
</script>
<?= $this->endSection() ?>
