<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Course Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header">
                    <h3 class="card-title mb-0"><?= esc($course['title']) ?></h3>
                </div>
                <div class="card-body">
                    <p class="card-text"><?= esc($course['description']) ?></p>
                    <small class="text-muted">Created: <?= date('F j, Y', strtotime($course['created_at'])) ?></small>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Assignments Section -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-file-earmark-text-fill me-2"></i> Course Assignments
                    </h5>
                    <?php
                    $session = session();
                    $userRole = $session->get('userRole');
                    $userId = $session->get('userId');
                    $isTeacher = ($userRole === 'teacher' && $course['teacher_id'] == $userId) || $userRole === 'admin';
                    if ($isTeacher):
                    ?>
                        <a href="<?= site_url('assignment/create/' . $course['id']) ?>" class="btn btn-light btn-sm">
                            <i class="bi bi-plus-circle me-1"></i>Add Assignment
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <?php
                    $assignmentModel = new \App\Models\AssignmentModel();
                    $assignments = $assignmentModel->where('course_id', $course['id'])
                                                  ->orderBy('created_at', 'DESC')
                                                  ->findAll();
                    ?>

                    <?php if (empty($assignments)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No assignments available for this course yet.
                            <?php if ($isTeacher): ?>
                                <a href="<?= site_url('assignment/create/' . $course['id']) ?>" class="alert-link">Create the first assignment!</a>
                            <?php else: ?>
                                Check back later!
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <div class="row">
                            <?php foreach ($assignments as $assignment): ?>
                                <div class="col-md-6 mb-3">
                                    <div class="card h-100 border">
                                        <div class="card-body d-flex flex-column">
                                            <h6 class="card-title">
                                                <i class="bi bi-file-earmark-text text-success me-2"></i>
                                                <?= esc($assignment['title']) ?>
                                            </h6>
                                            <p class="card-text text-muted small flex-grow-1">
                                                <?= esc(substr($assignment['description'], 0, 150)) ?>
                                                <?= strlen($assignment['description']) > 150 ? '...' : '' ?>
                                            </p>
                                            <div class="mt-auto">
                                                <?php if (!empty($assignment['due_date'])): ?>
                                                    <small class="text-danger">
                                                        <i class="bi bi-calendar-x me-1"></i>
                                                        Due: <?= date('M d, Y H:i', strtotime($assignment['due_date'])) ?>
                                                    </small>
                                                    <br>
                                                <?php endif; ?>
                                                <small class="text-muted">
                                                    <i class="bi bi-clock me-1"></i>
                                                    Posted: <?= date('M d, Y H:i', strtotime($assignment['created_at'])) ?>
                                                </small>
                                            </div>
                                        </div>
                                        <div class="card-footer bg-light">
                                            <button class="btn btn-success btn-sm w-100" onclick="viewAssignment(<?= $assignment['id'] ?>)">
                                                <i class="bi bi-eye me-1"></i> View Assignment
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Course Materials Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="bi bi-folder-fill me-2"></i> Course Materials
                    </h5>
                </div>
                <div class="card-body">
                    <?php
                    $materialModel = new \App\Models\MaterialModel();
                    $materials = $materialModel->getMaterialsByCourse($course['id']);
                    ?>

                    <?php if (empty($materials)): ?>
                        <div class="alert alert-info">
                            <i class="bi bi-info-circle me-2"></i>
                            No materials available for this course yet. Check back later!
                        </div>
                    <?php else: ?>
                        <div class="list-group">
                            <?php foreach ($materials as $material): ?>
                                <div class="list-group-item d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <i class="bi bi-file-earmark me-2 text-primary"></i>
                                            <?= esc($material['file_name']) ?>
                                        </h6>
                                        <small class="text-muted">
                                            <i class="bi bi-clock me-1"></i>
                                            Uploaded: <?= date('M d, Y H:i', strtotime($material['created_at'])) ?>
                                        </small>
                                    </div>
                                    <div class="d-flex gap-2">
                                        <a href="<?= site_url('materials/view/' . $material['id']) ?>" class="btn btn-success btn-sm">
                                            <i class="bi bi-eye me-1"></i> View
                                        </a>
                                        <a href="<?= site_url('materials/download/' . $material['id']) ?>" class="btn btn-primary btn-sm">
                                            <i class="bi bi-download me-1"></i> Download
                                        </a>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Assignment Modal -->
<div class="modal fade" id="assignmentModal" tabindex="-1" aria-labelledby="assignmentModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignmentModalLabel">
                    <i class="bi bi-file-earmark-text me-2"></i>Assignment Details
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body" id="assignmentContent">
                <!-- Assignment content will be loaded here -->
            </div>
        </div>
    </div>
</div>

<script>
function viewAssignment(assignmentId) {
    // Load assignment details via AJAX
    fetch('<?= base_url('assignment/view/') ?>' + assignmentId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const assignment = data.assignment;
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
                `;

                document.getElementById('assignmentContent').innerHTML = content;
                new bootstrap.Modal(document.getElementById('assignmentModal')).show();
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
