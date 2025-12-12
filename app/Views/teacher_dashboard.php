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

    <!-- Enrollment Overview -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3 fw-bold text-dark">Enrollment Overview</h4>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Total Enrolled Students -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100 border border-success border-3 bg-success bg-opacity-10">
                <div class="card-body text-center p-4">
                    <div class="mb-3">
                        <div class="bg-success p-4 rounded-circle d-inline-block shadow">
                            <i class="bi bi-people-fill text-white" style="font-size: 3rem;"></i>
                        </div>
                    </div>
                    <h2 class="card-title fw-bold text-success mb-2"><?= $totalEnrollments ?? 0 ?></h2>
                    <p class="card-text text-muted mb-0 fw-semibold">Total Enrolled Students</p>
                    <small class="text-muted">Across all your courses</small>
                </div>
            </div>
        </div>

        <!-- Enrolled Students -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-body">
                    <h5 class="card-title fw-bold text-dark mb-3">
                        <i class="bi bi-people me-2"></i>Enrolled Students
                    </h5>
                    <div id="enrolledStudentsList">
                        <?php if (!empty($recentEnrollments)): ?>
                            <div class="table-responsive" style="max-height: 350px; overflow-y: auto;">
                                <table class="table table-sm table-hover mb-0">
                                    <thead class="table-light sticky-top">
                                        <tr>
                                            <th class="border-0 py-2">Student</th>
                                            <th class="border-0 py-2">Details</th>
                                            <th class="border-0 py-2">Courses</th>
                                            <th class="border-0 py-2">Enrolled</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php
                                        // Get unique students with full details (avoid duplicates if student enrolled in multiple courses)
                                        $uniqueStudents = [];
                                        foreach ($recentEnrollments as $enrollment) {
                                            $studentKey = $enrollment['user_id'];
                                            if (!isset($uniqueStudents[$studentKey])) {
                                                // Get full student details
                                                $studentDetails = $db->table('users')->where('id', $enrollment['user_id'])->get()->getRowArray();
                                                if ($studentDetails) {
                                                    $uniqueStudents[$studentKey] = array_merge($enrollment, $studentDetails);
                                                }
                                            }
                                        }
                                        $displayStudents = array_slice(array_values($uniqueStudents), 0, 8);
                                        ?>

                                        <?php foreach ($displayStudents as $student): ?>
                                            <tr>
                                                <td class="py-2">
                                                    <div class="d-flex align-items-center">
                                                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-2 flex-shrink-0">
                                                            <i class="bi bi-person-check text-success" style="font-size: 0.8rem;"></i>
                                                        </div>
                                                        <div>
                                                            <div class="fw-semibold text-truncate" style="max-width: 120px;">
                                                                <?= esc($student['name']) ?>
                                                            </div>
                                                            <small class="text-muted">
                                                                ID: <?= esc($student['student_id'] ?? 'N/A') ?>
                                                            </small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-2">
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-envelope me-1"></i><?= esc($student['email']) ?>
                                                    </small>
                                                    <small class="text-muted d-block">
                                                        <i class="bi bi-mortarboard me-1"></i>
                                                        <?= esc($student['program'] ?? 'N/A') ?> -
                                                        Yr <?= esc($student['year_level'] ?? 'N/A') ?>
                                                    </small>
                                                </td>
                                                <td class="py-2">
                                                    <span class="badge bg-primary">
                                                        <i class="bi bi-book me-1"></i>
                                                        <?php
                                                        // Count how many courses this student is enrolled in
                                                        $studentEnrollments = array_filter($recentEnrollments, function($e) use ($student) {
                                                            return $e['user_id'] == $student['user_id'];
                                                        });
                                                        $courseCount = count($studentEnrollments);
                                                        echo $courseCount . ' course' . ($courseCount > 1 ? 's' : '');
                                                        ?>
                                                    </span>
                                                </td>
                                                <td class="py-2">
                                                    <small class="text-muted">
                                                        <?= date('M d, Y', strtotime($student['enrolled_at'])) ?>
                                                    </small>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                            <div class="text-center mt-3">
                                <a href="<?= base_url('teacher/manage-students') ?>" class="btn btn-outline-success btn-sm">
                                    <i class="bi bi-eye me-1"></i>View All Students
                                </a>
                            </div>
                        <?php else: ?>
                            <div class="text-center py-4">
                                <i class="bi bi-people text-muted" style="font-size: 2rem;"></i>
                                <p class="text-muted mt-2 mb-0">No enrolled students yet</p>
                                <small class="text-muted">Students will appear here when they enroll in your courses</small>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Assignment Statistics -->
    <div class="row mb-4">
        <div class="col-12">
            <h4 class="mb-3 fw-bold text-dark">Assignment Overview</h4>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <!-- Total Assignments -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm h-100 border border-info border-3 bg-info bg-opacity-10">
                <div class="card-body text-center p-3">
                    <div class="mb-2">
                        <div class="bg-info p-3 rounded-circle d-inline-block shadow">
                            <i class="bi bi-file-earmark-text text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h3 class="card-title fw-bold text-info mb-1"><?= $assignmentStats['totalAssignments'] ?? 0 ?></h3>
                    <p class="card-text text-muted mb-0 fw-semibold small">Total Assignments</p>
                </div>
            </div>
        </div>

        <!-- Pending Grading -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm h-100 border border-warning border-3 bg-warning bg-opacity-10">
                <div class="card-body text-center p-3">
                    <div class="mb-2">
                        <div class="bg-warning p-3 rounded-circle d-inline-block shadow">
                            <i class="bi bi-pencil-square text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h3 class="card-title fw-bold text-warning mb-1"><?= $assignmentStats['pendingGrading'] ?? 0 ?></h3>
                    <p class="card-text text-muted mb-0 fw-semibold small">Pending Grading</p>
                </div>
            </div>
        </div>

        <!-- Overdue Assignments -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm h-100 border border-danger border-3 bg-danger bg-opacity-10">
                <div class="card-body text-center p-3">
                    <div class="mb-2">
                        <div class="bg-danger p-3 rounded-circle d-inline-block shadow">
                            <i class="bi bi-exclamation-triangle text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h3 class="card-title fw-bold text-danger mb-1"><?= $assignmentStats['overdueAssignments'] ?? 0 ?></h3>
                    <p class="card-text text-muted mb-0 fw-semibold small">Overdue</p>
                </div>
            </div>
        </div>

        <!-- Due Soon -->
        <div class="col-lg-3 mb-4">
            <div class="card shadow-sm h-100 border border-primary border-3 bg-primary bg-opacity-10">
                <div class="card-body text-center p-3">
                    <div class="mb-2">
                        <div class="bg-primary p-3 rounded-circle d-inline-block shadow">
                            <i class="bi bi-clock text-white" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h3 class="card-title fw-bold text-primary mb-1"><?= $assignmentStats['upcomingDue'] ?? 0 ?></h3>
                    <p class="card-text text-muted mb-0 fw-semibold small">Due Soon</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Activity and Upcoming Due Dates -->
    <div class="row mb-4">
        <div class="col-lg-8 mb-4">
            <!-- Recent Activity -->
            <div class="card shadow-sm h-100">
                <div class="card-header bg-dark text-white">
                    <h6 class="mb-0"><i class="bi bi-activity me-2"></i>Recent Activity</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($recentActivity)): ?>
                        <div class="activity-feed" style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($recentActivity as $activity): ?>
                                <div class="d-flex mb-3 pb-3 border-bottom">
                                    <div class="flex-shrink-0 me-3">
                                        <?php if ($activity['type'] === 'submission'): ?>
                                            <div class="bg-success bg-opacity-10 p-2 rounded-circle">
                                                <i class="bi bi-file-earmark-arrow-up text-success"></i>
                                            </div>
                                        <?php elseif ($activity['type'] === 'grade'): ?>
                                            <div class="bg-primary bg-opacity-10 p-2 rounded-circle">
                                                <i class="bi bi-check-circle text-primary"></i>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                    <div class="flex-grow-1">
                                        <p class="mb-1 text-dark small"><?= esc($activity['message']) ?></p>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            <?= date('M d, Y H:i', strtotime($activity['timestamp'])) ?>
                                        </small>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-activity text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No recent activity</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <!-- Upcoming Assignments -->
            <div class="card shadow-sm h-100">
                <div class="card-header bg-warning text-white">
                    <h6 class="mb-0"><i class="bi bi-calendar-event me-2"></i>Due Soon</h6>
                </div>
                <div class="card-body">
                    <?php if (!empty($upcomingAssignments)): ?>
                        <div class="upcoming-list" style="max-height: 400px; overflow-y: auto;">
                            <?php foreach ($upcomingAssignments as $assignment): ?>
                                <div class="mb-3 pb-3 border-bottom">
                                    <h6 class="mb-2">
                                        <i class="bi bi-file-earmark-text me-1 text-warning"></i>
                                        <?= esc($assignment['title']) ?>
                                    </h6>
                                    <p class="text-muted small mb-2">
                                        <i class="bi bi-book me-1"></i><?= esc($assignment['course_name']) ?>
                                    </p>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <small class="text-danger fw-semibold">
                                            <i class="bi bi-calendar-x me-1"></i>
                                            Due: <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?>
                                        </small>
                                    </div>
                                    <div class="d-flex justify-content-between">
                                        <small class="text-muted">
                                            <i class="bi bi-people me-1"></i>
                                            <?= $assignment['total_submissions'] ?> submitted
                                        </small>
                                        <?php if ($assignment['pending_grading'] > 0): ?>
                                            <span class="badge bg-warning">
                                                <i class="bi bi-pencil me-1"></i>
                                                <?= $assignment['pending_grading'] ?> to grade
                                            </span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-check text-muted" style="font-size: 2rem;"></i>
                            <p class="text-muted mt-2 mb-0">No assignments due soon</p>
                        </div>
                    <?php endif; ?>
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
                            <i class="bi bi-people text-info" style="font-size: 2rem;"></i>
                        </div>
                    </div>
                    <h5 class="card-title fw-bold text-dark mb-3">Manage Students</h5>
                    <p class="card-text text-muted mb-4">View and manage enrolled students in your courses.</p>
                    <a href="<?= base_url('teacher/manage-students') ?>" class="btn btn-info w-100">
                        <i class="bi bi-people me-2"></i>Manage Students
                    </a>
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
                                            <div class="d-flex justify-content-between align-items-center mt-auto">
                                                <small class="text-muted">
                                                    <i class="bi bi-calendar me-1"></i>
                                                    Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                                </small>
                                                <?php if (($enrollmentStats[$course['id']] ?? 0) > 0): ?>
                                                    <span class="badge bg-success">
                                                        <i class="bi bi-people me-1"></i>
                                                        <?= $enrollmentStats[$course['id']] ?> enrolled
                                                    </span>
                                                <?php endif; ?>
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
    // Auto-refresh enrollment statistics every 30 seconds
    setInterval(function() {
        refreshEnrollmentStats();
    }, 30000);

    // Function to refresh enrollment statistics
    function refreshEnrollmentStats() {
        $.ajax({
            url: '<?= base_url('teacher/getEnrollmentStats') ?>',
            type: 'GET',
            success: function(response) {
                if (response.totalEnrollments !== undefined) {
                    // Update total enrollments count
                    $('.card-title.fw-bold.text-success').text(response.totalEnrollments);
                }

                if (response.recentEnrollments && response.recentEnrollments.length > 0) {
                    // Update enrolled students table (avoid duplicates and fetch full details)
                    let enrolledStudentsHtml = '';
                    let uniqueStudents = {};

                    // First, collect unique students and get their full details
                    response.recentEnrollments.forEach(function(enrollment) {
                        if (!uniqueStudents[enrollment.user_id]) {
                            // In real-time updates, we may not have full user details
                            // So we'll work with what we have and update basic info
                            uniqueStudents[enrollment.user_id] = enrollment;
                        }
                    });

                    let displayStudents = Object.values(uniqueStudents).slice(0, 8);
                    displayStudents.forEach(function(student) {
                        const enrolledDate = new Date(student.enrolled_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });

                        // Count courses for this student
                        let courseCount = response.recentEnrollments.filter(e => e.user_id === student.user_id).length;

                        // Use available data, fallback to basic info
                        const studentName = student.student_name || student.name || 'Unknown';
                        const studentId = student.student_id || 'N/A';
                        const email = student.email || '';
                        const program = student.program || 'N/A';
                        const yearLevel = student.year_level || 'N/A';

                        enrolledStudentsHtml += `
                            <tr>
                                <td class="py-2">
                                    <div class="d-flex align-items-center">
                                        <div class="bg-success bg-opacity-10 p-2 rounded-circle me-2 flex-shrink-0">
                                            <i class="bi bi-person-check text-success" style="font-size: 0.8rem;"></i>
                                        </div>
                                        <div>
                                            <div class="fw-semibold text-truncate" style="max-width: 120px;">
                                                ${studentName}
                                            </div>
                                            <small class="text-muted">
                                                ID: ${studentId}
                                            </small>
                                        </div>
                                    </div>
                                </td>
                                <td class="py-2">
                                    <small class="text-muted d-block">
                                        <i class="bi bi-envelope me-1"></i>${email}
                                    </small>
                                    <small class="text-muted d-block">
                                        <i class="bi bi-mortarboard me-1"></i>
                                        ${program} - Yr ${yearLevel}
                                    </small>
                                </td>
                                <td class="py-2">
                                    <span class="badge bg-primary">
                                        <i class="bi bi-book me-1"></i>
                                        ${courseCount} course${courseCount > 1 ? 's' : ''}
                                    </span>
                                </td>
                                <td class="py-2">
                                    <small class="text-muted">
                                        ${enrolledDate}
                                    </small>
                                </td>
                            </tr>
                        `;
                    });

                    // Update the enrolled students table body
                    $('#enrolledStudentsList table tbody').html(enrolledStudentsHtml);
                }

                if (response.enrollmentStats && response.courses) {
                    // Update enrollment counts on course cards
                    response.courses.forEach(function(course) {
                        const enrollmentCount = response.enrollmentStats[course.id] || 0;
                        // Find the specific course card by looking for the course title and update its badge
                        $('.card').each(function() {
                            const card = $(this);
                            if (card.text().includes(course.title)) {
                                const badgeContainer = card.find('.d-flex.justify-content-between.align-items-center');
                                const existingBadge = badgeContainer.find('.badge.bg-success');

                                if (enrollmentCount > 0) {
                                    if (existingBadge.length > 0) {
                                        // Update existing badge
                                        existingBadge.html(`<i class="bi bi-people me-1"></i>${enrollmentCount} enrolled`);
                                    } else {
                                        // Create new badge
                                        badgeContainer.append(`<span class="badge bg-success"><i class="bi bi-people me-1"></i>${enrollmentCount} enrolled</span>`);
                                    }
                                } else {
                                    // Remove badge if count is 0
                                    existingBadge.remove();
                                }
                            }
                        });
                    });
                }
            },
            error: function() {
                console.log('Failed to refresh enrollment stats');
            }
        });
    }
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
