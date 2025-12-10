<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4" style="border-radius: 10px;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="bi bi-book me-2"></i>My Courses
            </a>
            <div class="d-flex">
                <a href="<?= base_url('teacher/dashboard') ?>" class="btn btn-outline-secondary me-2">
                    <i class="bi bi-arrow-left me-1"></i>Back to Dashboard
                </a>
            </div>
        </div>
    </nav>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="bi bi-collection text-primary" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold text-primary"><?= count($courses ?? []) ?></h2>
                            <p class="text-muted mb-0">My Courses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                <div class="card-body text-center">
                    <div class="d-flex align-items-center justify-content-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="bi bi-plus-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold text-success">Create</h2>
                            <p class="text-muted mb-0">New Course</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Table -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                <div class="card-header bg-white d-flex justify-content-between align-items-center" style="border-radius: 10px 10px 0 0;">
                    <h5 class="mb-0 fw-bold text-dark">
                        <i class="bi bi-table me-2"></i>My Courses
                    </h5>
                    <button class="btn btn-success" id="create-course-btn" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                        <i class="bi bi-plus-circle me-2"></i>Create Course
                    </button>
                </div>
                <div class="card-body">
                    <?php if (!empty($courses)): ?>
                        <div class="table-responsive">
                            <table class="table table-hover" id="courses-table">
                                <thead class="table-light">
                                    <tr>
                                        <th>Course Code</th>
                                        <th>Course Title</th>
                                        <th>Description</th>
                                        <th>School Year</th>
                                        <th>Semester</th>
                                        <th>Schedule</th>
                                        <th>Status</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($courses as $course): ?>
                                        <tr>
                                            <td><?= esc($course['course_code'] ?? 'N/A') ?></td>
                                            <td><?= esc($course['title']) ?></td>
                                            <td>
                                                <span title="<?= esc($course['description']) ?>">
                                                    <?= strlen($course['description']) > 50 ? esc(substr($course['description'], 0, 50)) . '...' : esc($course['description']) ?>
                                                </span>
                                            </td>
                                            <td><?= esc($course['school_year'] ?? 'N/A') ?></td>
                                            <td><?= esc($course['semester'] ?? 'N/A') ?></td>
                                            <td><?= esc($course['schedule'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-<?= ($course['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($course['status'] ?? 'active') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <div class="btn-group" role="group">
                                                    <a href="<?= base_url('course/view/' . $course['id']) ?>" class="btn btn-sm btn-outline-primary" title="View Course">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="<?= base_url('materials/upload/' . $course['id']) ?>" class="btn btn-sm btn-outline-secondary" title="Upload Materials">
                                                        <i class="bi bi-upload"></i>
                                                    </a>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="bi bi-book text-muted" style="font-size: 3rem;"></i>
                            <h4 class="text-muted mt-3">No Courses Yet</h4>
                            <p class="text-muted">You haven't created any courses yet. Click the "Create Course" button to get started.</p>
                            <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                                <i class="bi bi-plus-circle me-2"></i>Create Your First Course
                            </button>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Create Course Modal -->
<div class="modal fade" id="createCourseModal" tabindex="-1" aria-labelledby="createCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-success text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="createCourseModalLabel">
                    <i class="bi bi-plus-circle me-2"></i>Create New Course
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="create-course-form">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_course_code" class="form-label fw-bold">Course Code <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_course_code" name="course_code" placeholder="e.g., CS101" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_title" class="form-label fw-bold">Course Title <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="create_title" name="title" placeholder="Enter course title" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_school_year" class="form-label fw-bold">School Year <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_school_year" name="school_year" required>
                                <option value="">Select School Year</option>
                                <option value="2023-2024">2023-2024</option>
                                <option value="2024-2025">2024-2025</option>
                                <option value="2025-2026">2025-2026</option>
                                <option value="2026-2027">2026-2027</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_semester" class="form-label fw-bold">Semester <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_start_date" class="form-label fw-bold">Start Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_end_date" class="form-label fw-bold">End Date <span class="text-danger">*</span></label>
                            <input type="date" class="form-control" id="create_end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="create_description" class="form-label fw-bold">Description <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="create_description" name="description" rows="3" placeholder="Enter course description" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="create_schedule" class="form-label fw-bold">Schedule</label>
                        <input type="text" class="form-control" id="create_schedule" name="schedule" placeholder="e.g., MWF 9:00-10:00 AM">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="create_status_active" value="active" checked>
                            <label class="form-check-label" for="create_status_active">
                                Active
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="create_status_inactive" value="inactive">
                            <label class="form-check-label" for="create_status_inactive">
                                Inactive
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-plus-circle me-1"></i>Create Course
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Create course form submission
    $('#create-course-form').submit(function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const startDate = new Date($('#create_start_date').val());
        const endDate = new Date($('#create_end_date').val());

        if (startDate > endDate) {
            alert('Start date cannot be after end date.');
            return;
        }

        const $submitBtn = $(this).find('button[type="submit"]');
        const originalText = $submitBtn.html();
        $submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i>Creating...');

        $.post('<?= base_url('course/teacher/create') ?>', formData)
            .done(function(response) {
                if (response.success) {
                    $('#createCourseModal').modal('hide');
                    alert('Course created successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            })
            .fail(function() {
                alert('Failed to create course. Please try again.');
            })
            .always(function() {
                $submitBtn.prop('disabled', false).html(originalText);
            });
    });
});
</script>

<style>
body {
    background-color: #f8f9fa;
}

.card {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1) !important;
}

.table th {
    background-color: #f8f9fa;
    border-top: none;
    font-weight: 600;
    color: #495057;
}

.table td {
    vertical-align: middle;
}

.btn {
    border-radius: 8px;
}

.form-control, .form-select {
    border-radius: 8px;
    border: 1px solid #dee2e6;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

.modal-content {
    border: none;
    box-shadow: 0 10px 30px rgba(0,0,0,0.2);
}
</style>

<?= $this->endSection() ?>
