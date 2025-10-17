<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<!-- Dashboard Header -->
<div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-3 shadow-sm" 
     style="background: linear-gradient(90deg, #0d6efd, #0dcaf0); color: #fff;">
    <h1 class="h3 mb-0 d-flex align-items-center">
        <i class="bi bi-speedometer2 me-2"></i> Dashboard ⚡
    </h1>
    <div class="text-end">
        <?php 
            $userName = session()->get('userName'); 
            $userRole = ucfirst((string) session()->get('userRole')); 
            $role     = $role ?? strtolower($userRole) ?? 'student';
        ?>
        <div class="fw-semibold">Welcome, <strong><?= esc($userName) ?></strong> 👋</div>
        <div class="small">
            <i class="bi bi-person-badge me-1"></i> Role: <?= esc($userRole) ?>
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

        <!-- Total Courses -->
        <div class="col-md-6 col-xl-4">
            <div class="card shadow-sm border-0 rounded-3">
                <div class="card-body d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-3" 
                         style="width:60px; height:60px;">
                        <i class="bi bi-book-fill fs-3"></i>
                    </div>
                    <div>
                        <div class="h3 mb-0"><?= esc($totalCourses ?? 0) ?></div>
                        <small class="text-muted">Total Courses 📚</small>
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
    <!-- Teacher Welcome Message -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body text-center p-5">
                    <h2 class="card-title text-dark">Welcome Teacher!</h2>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
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
                                        <div>
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
                                        <span class="badge bg-success">Enrolled</span>
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
            
            // If no enrolled courses yet, create the list
            if (enrolledList.length === 0) {
                $('#enrolled-courses-list').html(`
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center">
                            <div>
                                <h6 class="mb-1">
                                    <i class="bi bi-book-fill text-success me-2"></i>
                                    ${courseTitle}
                                </h6>
                                <small class="text-muted">
                                    <i class="bi bi-calendar-check me-1"></i>
                                    Enrolled: ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                                </small>
                            </div>
                            <span class="badge bg-success">Enrolled</span>
                        </div>
                    </div>
                `);
            } else {
                // Add to existing list
                const newCourseHtml = `
                    <div class="list-group-item d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="mb-1">
                                <i class="bi bi-book-fill text-success me-2"></i>
                                ${courseTitle}
                            </h6>
                            <small class="text-muted">
                                <i class="bi bi-calendar-check me-1"></i>
                                Enrolled: ${new Date().toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}
                            </small>
                        </div>
                        <span class="badge bg-success">Enrolled</span>
                    </div>
                `;
                
                enrolledList.prepend(newCourseHtml);
            }
        }
    });
    </script>
<?php endif; ?>

<?= $this->endSection() ?>
