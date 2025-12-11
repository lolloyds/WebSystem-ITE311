<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <!-- Assignment Header -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="card-title mb-1">
                                <i class="bi bi-file-earmark-text me-2"></i><?= esc($assignment['title']) ?>
                            </h3>
                            <small class="text-light">
                                <i class="bi bi-book me-1"></i>Course: <?= esc($course['title']) ?> (<?= esc($course['course_code']) ?>)
                            </small>
                        </div>
                        <div class="text-end">
                            <?php if (!empty($assignment['due_date'])): ?>
                                <div class="badge bg-warning text-dark mb-2">
                                    <i class="bi bi-calendar-x me-1"></i>
                                    Due: <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?>
                                </div>
                            <?php endif; ?>
                            <br>
                            <small class="text-light">
                                <i class="bi bi-clock me-1"></i>
                                Posted: <?= date('M d, Y H:i', strtotime($assignment['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Assignment Description -->
                    <div class="mb-4">
                        <h5 class="text-muted mb-3">
                            <i class="bi bi-info-circle me-2"></i>Assignment Details
                        </h5>
                        <div class="border-start border-primary border-3 ps-3">
                            <div class="assignment-description">
                                <?= nl2br(esc($assignment['description'])) ?>
                            </div>
                        </div>
                    </div>

                    <!-- Assignment Attachment -->
                    <?php if (!empty($assignment['attachment'])): ?>
                        <div class="mb-4">
                            <h5 class="text-muted mb-3">
                                <i class="bi bi-paperclip me-2"></i>Attachment
                            </h5>
                            <div class="border-start border-info border-3 ps-3">
                                <a href="<?= base_url($assignment['attachment']) ?>" target="_blank" class="btn btn-outline-primary btn-sm">
                                    <i class="bi bi-download me-2"></i>Download Attachment
                                </a>
                                <small class="text-muted ms-2">
                                    <?= pathinfo($assignment['attachment'], PATHINFO_BASENAME) ?>
                                </small>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Assignment Actions -->
                    <div class="d-flex gap-2 justify-content-between align-items-center">
                        <div>
                            <?php
                            $session = session();
                            $userRole = $session->get('userRole');
                            $userId = $session->get('userId');
                            $isTeacher = ($userRole === 'teacher' && $course['teacher_id'] == $userId) || $userRole === 'admin';
                            ?>

                            <?php if ($isTeacher): ?>
                                <span class="badge bg-success">
                                    <i class="bi bi-person-check me-1"></i>You are the instructor for this course
                                </span>
                            <?php else: ?>
                                <span class="badge bg-info">
                                    <i class="bi bi-person me-1"></i>You are enrolled in this course
                                </span>
                            <?php endif; ?>
                        </div>

                        <div class="d-flex gap-2">
                            <?php if ($isTeacher): ?>
                                <!-- Teacher actions -->
                                <a href="<?= site_url('assignment/create/' . $course['id']) ?>" class="btn btn-success btn-sm">
                                    <i class="bi bi-plus-circle me-1"></i>Create Another Assignment
                                </a>
                            <?php else: ?>
                                <!-- Student actions -->
                                <button class="btn btn-primary btn-sm" onclick="markAsCompleted(<?= $assignment['id'] ?>)">
                                    <i class="bi bi-check-circle me-1"></i>Mark as Completed
                                </button>
                            <?php endif; ?>

                            <a href="<?= site_url('course/view/' . $course['id']) ?>" class="btn btn-secondary btn-sm">
                                <i class="bi bi-arrow-left me-1"></i>Back to Course
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Assignment Submission Section -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <?php if ($isTeacher): ?>
                            <i class="bi bi-list-check me-2"></i>Student Submissions
                        <?php else: ?>
                            <i class="bi bi-upload me-2"></i>Your Submission
                        <?php endif; ?>
                    </h5>
                </div>
                <div class="card-body">
                    <?php if ($isTeacher): ?>
                        <!-- Teacher View: List of submissions -->
                        <div id="submissionsList">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading submissions...</p>
                            </div>
                        </div>
                    <?php else: ?>
                        <!-- Student View: Submission form or status -->
                        <div id="submissionArea">
                            <div class="text-center">
                                <div class="spinner-border text-primary" role="status">
                                    <span class="visually-hidden">Loading...</span>
                                </div>
                                <p class="mt-2">Loading submission status...</p>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const assignmentId = <?= $assignment['id'] ?>;
    const isTeacher = <?= $isTeacher ? 'true' : 'false' ?>;

    if (isTeacher) {
        loadTeacherSubmissions(assignmentId);
    } else {
        loadStudentSubmission(assignmentId);
    }
});

// Load submissions for teacher view
function loadTeacherSubmissions(assignmentId) {
    fetch('<?= site_url('assignment/getSubmissions/') ?>' + assignmentId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayTeacherSubmissions(data.submissions);
        } else {
            document.getElementById('submissionsList').innerHTML =
                '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No submissions found yet.</div>';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('submissionsList').innerHTML =
            '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load submissions.</div>';
    });
}

// Display submissions in teacher view
function displayTeacherSubmissions(submissions) {
    if (submissions.length === 0) {
        document.getElementById('submissionsList').innerHTML =
            '<div class="alert alert-info"><i class="bi bi-info-circle me-2"></i>No submissions found yet.</div>';
        return;
    }

    let html = '<div class="table-responsive"><table class="table table-hover">';
    html += '<thead><tr><th>Student</th><th>Submitted</th><th>Status</th><th>Grade</th><th>Actions</th></tr></thead><tbody>';

    submissions.forEach(submission => {
        const submittedDate = submission.submitted_at ? new Date(submission.submitted_at).toLocaleDateString() : 'Not submitted';
        const statusBadge = getStatusBadge(submission.status, submission.grade);
        const gradeDisplay = submission.grade || (submission.status === 'graded' ? 'Graded' : 'Not graded');

        html += `<tr>
            <td>${submission.student_name}</td>
            <td>${submittedDate}</td>
            <td>${statusBadge}</td>
            <td>${gradeDisplay}</td>
            <td>
                ${submission.file_path ? `<a href="${submission.file_path}" target="_blank" class="btn btn-sm btn-outline-primary me-1"><i class="bi bi-download"></i></a>` : ''}
                ${submission.status === 'submitted' || submission.status === 'late' ?
                    `<button class="btn btn-sm btn-success" onclick="gradeSubmission(${submission.id})"><i class="bi bi-check-circle"></i> Grade</button>` :
                    `<button class="btn btn-sm btn-warning" onclick="gradeSubmission(${submission.id})"><i class="bi bi-pencil"></i> Edit Grade</button>`}
            </td>
        </tr>`;
    });

    html += '</tbody></table></div>';
    document.getElementById('submissionsList').innerHTML = html;
}

// Load student submission status
function loadStudentSubmission(assignmentId) {
    fetch('<?= site_url('assignment/getSubmission/') ?>' + assignmentId, {
        method: 'GET',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            displayStudentSubmission(data.submission);
        } else {
            displaySubmissionForm(assignmentId);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        document.getElementById('submissionArea').innerHTML =
            '<div class="alert alert-danger"><i class="bi bi-exclamation-triangle me-2"></i>Failed to load submission status.</div>';
    });
}

// Display existing submission for student
function displayStudentSubmission(submission) {
    let html = '';

    if (submission.grade) {
        // Graded submission
        html += `<div class="alert alert-success">
            <h6><i class="bi bi-check-circle me-2"></i>Assignment Graded</h6>
            <p class="mb-1"><strong>Grade:</strong> ${submission.grade}</p>`;
        if (submission.feedback) {
            html += `<p class="mb-1"><strong>Feedback:</strong> ${submission.feedback}</p>`;
        }
        html += `<small class="text-muted">Graded on: ${new Date(submission.graded_at).toLocaleDateString()}</small></div>`;
    } else {
        // Submitted but not graded
        html += `<div class="alert alert-info">
            <i class="bi bi-info-circle me-2"></i>Your assignment has been submitted and is pending grading.
        </div>`;
    }

    // Show submission details
    html += `<div class="border rounded p-3 mb-3">
        <h6>Submission Details</h6>
        <p><strong>Submitted:</strong> ${new Date(submission.submitted_at).toLocaleDateString()}</p>`;
    if (submission.file_path) {
        html += `<p><strong>File:</strong> <a href="${submission.file_path}" target="_blank">${submission.file_path.split('/').pop()}</a></p>`;
    }
    if (submission.notes) {
        html += `<p><strong>Notes:</strong> ${submission.notes}</p>`;
    }
    html += '</div>';

    document.getElementById('submissionArea').innerHTML = html;
}

// Display submission form for student
function displaySubmissionForm(assignmentId) {
    const html = `
        <form id="submissionForm">
            <input type="hidden" name="assignment_id" value="${assignmentId}">

            <div class="mb-3">
                <label for="submissionFile" class="form-label">
                    <i class="bi bi-file-earmark me-1"></i>Upload File <span class="text-danger">*</span>
                </label>
                <input type="file" class="form-control" id="submissionFile" name="file" required
                       accept=".pdf,.doc,.docx,.txt,.jpg,.jpeg,.png,.zip,.rar">
                <div class="form-text">
                    Allowed file types: PDF, DOC, DOCX, TXT, JPG, JPEG, PNG, ZIP, RAR. Max size: 20MB.
                </div>
            </div>

            <div class="mb-3">
                <label for="submissionNotes" class="form-label">
                    <i class="bi bi-chat-dots me-1"></i>Notes (Optional)
                </label>
                <textarea class="form-control" id="submissionNotes" name="notes" rows="3"
                          placeholder="Add any additional notes or comments..."></textarea>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">
                <i class="bi bi-upload me-2"></i>Submit Assignment
            </button>
        </form>
    `;

    document.getElementById('submissionArea').innerHTML = html;

    // Add form submission handler
    document.getElementById('submissionForm').addEventListener('submit', function(e) {
        e.preventDefault();
        submitAssignment(assignmentId);
    });
}

// Submit assignment
function submitAssignment(assignmentId) {
    const submitBtn = document.getElementById('submitBtn');
    const originalText = submitBtn.innerHTML;

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-2"></i>Submitting...';

    const formData = new FormData(document.getElementById('submissionForm'));

    fetch('<?= site_url('assignment/submit') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            document.getElementById('submissionArea').innerHTML =
                '<div class="alert alert-success"><i class="bi bi-check-circle me-2"></i>Assignment submitted successfully!</div>';
        } else {
            alert('Error: ' + data.message);
            submitBtn.disabled = false;
            submitBtn.innerHTML = originalText;
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while submitting the assignment.');
        submitBtn.disabled = false;
        submitBtn.innerHTML = originalText;
    });
}

// Grade submission (opens modal or redirects)
function gradeSubmission(submissionId) {
    window.location.href = '<?= site_url('assignment/grade/') ?>' + submissionId;
}

// Helper function to get status badge
function getStatusBadge(status, grade) {
    if (grade) {
        return '<span class="badge bg-success">Graded</span>';
    }

    switch(status) {
        case 'submitted':
            return '<span class="badge bg-warning">Submitted</span>';
        case 'late':
            return '<span class="badge bg-danger">Late</span>';
        default:
            return '<span class="badge bg-secondary">Not Submitted</span>';
    }
}
</script>

<style>
.assignment-description {
    line-height: 1.6;
    font-size: 1.1rem;
}

.assignment-description p {
    margin-bottom: 1rem;
}

.assignment-description p:last-child {
    margin-bottom: 0;
}
</style>
<?= $this->endSection() ?>
