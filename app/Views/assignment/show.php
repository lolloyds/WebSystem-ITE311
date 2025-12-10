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

            <!-- Assignment Comments/Submission Section (for future implementation) -->
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-chat-dots me-2"></i>Assignment Discussion
                    </h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        Assignment discussion and submission features will be available soon.
                        <?php if (!$isTeacher): ?>
                            Contact your instructor if you have questions about this assignment.
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Function to mark assignment as completed (placeholder for future implementation)
function markAsCompleted(assignmentId) {
    alert('Assignment completion tracking will be implemented soon!');
    // TODO: Implement assignment completion tracking
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
