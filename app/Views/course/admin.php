<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm mb-4" style="border-radius: 10px;">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold text-primary" href="#">
                <i class="bi bi-gear-fill me-2"></i>Course Management
            </a>
            <div class="d-flex">
                <a href="<?= base_url('admin') ?>" class="btn btn-outline-secondary me-2">
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
                            <h2 class="mb-0 fw-bold text-primary"><?= $totalCourses ?? 0 ?></h2>
                            <p class="text-muted mb-0">Total Courses</p>
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
                            <i class="bi bi-check-circle text-success" style="font-size: 2rem;"></i>
                        </div>
                        <div>
                            <h2 class="mb-0 fw-bold text-success"><?= $activeCourses ?? 0 ?></h2>
                            <p class="text-muted mb-0">Active Courses</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm" style="border-radius: 10px; border: none;">
                <div class="card-body">
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius: 10px 0 0 10px;">
                            <i class="bi bi-search"></i>
                        </span>
                        <input type="text" class="form-control" id="course-search" placeholder="Search by course title, code, or teacher..." style="border-radius: 0;">
                        <button class="btn btn-primary" type="button" id="search-btn" style="border-radius: 0 10px 10px 0;">
                            <i class="bi bi-search me-1"></i>Search
                        </button>
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
                        <i class="bi bi-table me-2"></i>All Courses
                    </h5>
                    <button class="btn btn-success" id="create-course-btn" data-bs-toggle="modal" data-bs-target="#createCourseModal">
                        <i class="bi bi-plus-circle me-2"></i>Create Course
                    </button>
                </div>
                <div class="card-body">
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
                                    <th>Teacher</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="courses-tbody">
                                <?php if (!empty($courses)): ?>
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
                                            <td><?= esc($course['teacher_name'] ?? 'N/A') ?></td>
                                            <td>
                                                <span class="badge bg-<?= ($course['status'] ?? 'active') === 'active' ? 'success' : 'secondary' ?>">
                                                    <?= ucfirst($course['status'] ?? 'active') ?>
                                                </span>
                                            </td>
                                            <td>
                                                <button class="btn btn-sm btn-outline-primary edit-btn"
                                                        data-course-id="<?= $course['id'] ?>"
                                                        title="Edit Details">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center py-4">
                                            <i class="bi bi-info-circle text-muted" style="font-size: 2rem;"></i>
                                            <p class="text-muted mt-2">No courses found.</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
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
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="create_teacher_id" class="form-label fw-bold">Teacher <span class="text-danger">*</span></label>
                            <select class="form-select" id="create_teacher_id" name="teacher_id" required>
                                <option value="">Select Teacher</option>
                                <!-- Teachers will be loaded via AJAX -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="create_schedule" class="form-label fw-bold">Schedule</label>
                            <input type="text" class="form-control" id="create_schedule" name="schedule" placeholder="e.g., MWF 9:00-10:00 AM">
                        </div>
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

<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal" tabindex="-1" aria-labelledby="editCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <div class="modal-header bg-primary text-white" style="border-radius: 15px 15px 0 0;">
                <h5 class="modal-title" id="editCourseModalLabel">
                    <i class="bi bi-pencil me-2"></i>Edit Course Details
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="edit-course-form">
                <div class="modal-body">
                    <input type="hidden" id="course_id" name="course_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="course_code" class="form-label fw-bold">Course Code</label>
                            <input type="text" class="form-control" id="course_code" name="course_code" readonly style="background-color: #f8f9fa;">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="title" class="form-label fw-bold">Course Title</label>
                            <input type="text" class="form-control" id="title" name="title" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="school_year" class="form-label fw-bold">School Year</label>
                            <select class="form-select" id="school_year" name="school_year" required>
                                <option value="">Select School Year</option>
                                <option value="2023-2024">2023-2024</option>
                                <option value="2024-2025">2024-2025</option>
                                <option value="2025-2026">2025-2026</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label fw-bold">Semester</label>
                            <select class="form-select" id="semester" name="semester" required>
                                <option value="">Select Semester</option>
                                <option value="1st Semester">1st Semester</option>
                                <option value="2nd Semester">2nd Semester</option>
                                <option value="Summer">Summer</option>
                            </select>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="start_date" class="form-label fw-bold">Start Date</label>
                            <input type="date" class="form-control" id="start_date" name="start_date" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="end_date" class="form-label fw-bold">End Date</label>
                            <input type="date" class="form-control" id="end_date" name="end_date" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="description" class="form-label fw-bold">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="teacher_id" class="form-label fw-bold">Teacher</label>
                            <select class="form-select" id="teacher_id" name="teacher_id" required>
                                <option value="">Select Teacher</option>
                                <!-- Teachers will be loaded via AJAX -->
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="schedule" class="form-label fw-bold">Schedule</label>
                            <input type="text" class="form-control" id="schedule" name="schedule" placeholder="e.g., MWF 9:00-10:00 AM">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-bold">Status</label>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status_active" value="active" checked>
                            <label class="form-check-label" for="status_active">
                                Active
                            </label>
                        </div>
                        <div class="form-check">
                            <input class="form-check-input" type="radio" name="status" id="status_inactive" value="inactive">
                            <label class="form-check-label" for="status_inactive">
                                Inactive
                            </label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="bi bi-x-circle me-1"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i>Update
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    // Load teachers for dropdown
    function loadTeachers() {
        $.get('<?= base_url('manage-users/get-teachers') ?>')
            .done(function(data) {
                let options = '<option value="">Select Teacher</option>';
                data.forEach(function(teacher) {
                    options += `<option value="${teacher.id}">${teacher.name}</option>`;
                });
                $('#teacher_id').html(options);
            })
            .fail(function() {
                console.error('Failed to load teachers');
            });
    }

    // Search functionality
    function searchCourses() {
        const searchTerm = $('#course-search').val().trim();
        const $searchBtn = $('#search-btn');
        const originalText = $searchBtn.html();

        $searchBtn.prop('disabled', true).html('<i class="bi bi-hourglass me-1"></i>Searching...');
        $('#courses-tbody').html('<tr><td colspan="9" class="text-center"><div class="spinner-border text-primary" role="status"><span class="visually-hidden">Searching...</span></div><p class="mt-2 text-muted">Searching courses...</p></td></tr>');

        $.get('<?= base_url('course/adminCourses') ?>', { search: searchTerm })
            .done(function(data) {
                renderCoursesTable(data, searchTerm);
            })
            .fail(function() {
                $('#courses-tbody').html('<tr><td colspan="9" class="text-center text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Failed to search courses. Please try again.</td></tr>');
            })
            .always(function() {
                $searchBtn.prop('disabled', false).html(originalText);
            });
    }

    $('#search-btn').click(searchCourses);
    $('#course-search').keypress(function(e) {
        if (e.which === 13) {
            searchCourses();
        }
    });

    // Render courses table
    function renderCoursesTable(courses) {
        if (!courses || courses.length === 0) {
            $('#courses-tbody').html('<tr><td colspan="9" class="text-center py-4"><i class="bi bi-info-circle text-muted" style="font-size: 2rem;"></i><p class="text-muted mt-2">No courses found.</p></td></tr>');
            return;
        }

        let html = '';
        courses.forEach(function(course) {
            const statusBadge = course.status === 'active' ? 'bg-success' : 'bg-secondary';
            const description = course.description.length > 50 ? course.description.substring(0, 50) + '...' : course.description;

            html += `
                <tr>
                    <td>${course.course_code || 'N/A'}</td>
                    <td>${course.title}</td>
                    <td><span title="${course.description}">${description}</span></td>
                    <td>${course.school_year || 'N/A'}</td>
                    <td>${course.semester || 'N/A'}</td>
                    <td>${course.schedule || 'N/A'}</td>
                    <td>${course.teacher_name || 'N/A'}</td>
                    <td><span class="badge ${statusBadge}">${course.status ? course.status.charAt(0).toUpperCase() + course.status.slice(1) : 'Active'}</span></td>
                    <td>
                        <button class="btn btn-sm btn-outline-primary edit-btn" data-course-id="${course.id}" title="Edit Details">
                            <i class="bi bi-pencil"></i>
                        </button>
                    </td>
                </tr>
            `;
        });
        $('#courses-tbody').html(html);
    }

    // Edit course modal
    $(document).on('click', '.edit-btn', function() {
        const courseId = $(this).data('course-id');

        // Load course details
        $.get('<?= base_url('course/getCourse') ?>/' + courseId)
            .done(function(data) {
                if (data.error) {
                    alert('Error: ' + data.error);
                    return;
                }

                $('#course_id').val(data.id);
                $('#course_code').val(data.course_code);
                $('#title').val(data.title);
                $('#school_year').val(data.school_year);
                $('#semester').val(data.semester);
                $('#start_date').val(data.start_date);
                $('#end_date').val(data.end_date);
                $('#description').val(data.description);
                $('#teacher_id').val(data.teacher_id);
                $('#schedule').val(data.schedule);
                $(`input[name="status"][value="${data.status}"]`).prop('checked', true);

                loadTeachers();
                $('#editCourseModal').modal('show');
            })
            .fail(function() {
                alert('Failed to load course details.');
            });
    });

    // Update course form submission
    $('#edit-course-form').submit(function(e) {
        e.preventDefault();

        const formData = $(this).serialize();
        const startDate = new Date($('#start_date').val());
        const endDate = new Date($('#end_date').val());

        if (startDate > endDate) {
            alert('Start date cannot be after end date.');
            return;
        }

        $.post('<?= base_url('course/update') ?>', formData)
            .done(function(response) {
                if (response.success) {
                    $('#editCourseModal').modal('hide');
                    alert('Course updated successfully!');
                    location.reload();
                } else {
                    alert('Error: ' + response.message);
                }
            })
            .fail(function() {
                alert('Failed to update course. Please try again.');
            });
    });

    // Load teachers on modal show
    $('#editCourseModal').on('shown.bs.modal', function() {
        loadTeachers();
    });

    // Load teachers for create modal
    function loadTeachersForCreate() {
        // Show loading state
        $('#create_teacher_id').html('<option value="">Loading teachers...</option>');

        $.get('<?= base_url('manage-users/get-teachers') ?>')
            .done(function(data) {
                console.log('Teachers loaded for create modal:', data);
                let options = '<option value="">Select Teacher</option>';

                // Check if response is an error
                if (data && typeof data === 'object' && data.error) {
                    console.error('Server returned error:', data.error);
                    options += '<option value="" disabled>Error: ' + data.error + '</option>';
                } else if (data && Array.isArray(data) && data.length > 0) {
                    data.forEach(function(teacher) {
                        if (teacher.id && teacher.name) {
                            options += `<option value="${teacher.id}">${teacher.name}</option>`;
                        }
                    });
                    if (options === '<option value="">Select Teacher</option>') {
                        options += '<option value="" disabled>No valid teachers found</option>';
                    }
                } else {
                    options += '<option value="" disabled>No teachers available</option>';
                }
                $('#create_teacher_id').html(options);
            })
            .fail(function(xhr, status, error) {
                console.error('Failed to load teachers for create modal:', status, error, xhr.responseText);
                let options = '<option value="">Select Teacher</option>';
                options += '<option value="" disabled>Failed to load teachers</option>';
                $('#create_teacher_id').html(options);
            });
    }

    // Load teachers when create course modal is shown
    $('#createCourseModal').on('shown.bs.modal', function() {
        // Reset form
        $('#create-course-form')[0].reset();
        // Load teachers
        loadTeachersForCreate();
    });

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

        $.post('<?= base_url('course/create') ?>', formData)
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
