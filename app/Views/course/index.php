<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Page Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">
                    <i class="bi bi-collection-play me-2"></i> Available Courses
                </h1>
                <small class="text-muted">Find and explore courses</small>
            </div>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <form class="d-flex gap-2" id="course-search-form">
                        <div class="input-group flex-grow-1">
                            <span class="input-group-text">
                                <i class="bi bi-search"></i>
                            </span>
                            <input
                                type="text"
                                class="form-control"
                                id="course-search"
                                placeholder="Search courses by title or description..."
                                value="<?= esc($search_term ?? '') ?>"
                            />
                        </div>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-search me-1"></i> Search
                        </button>
                        <button type="button" class="btn btn-outline-secondary" id="clear-search">
                            <i class="bi bi-x-circle me-1"></i> Clear
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Courses Listing -->
    <div class="row">
        <div class="col-12">
            <div id="courses-list">
                <?php if (!empty($courses)): ?>
                    <div class="row g-4">
                        <?php foreach ($courses as $course): ?>
                            <div class="col-md-6 col-lg-4">
                                <div class="card h-100 shadow-sm course-item" data-search-content="<?= strtolower(esc($course['title'] . ' ' . $course['description'])) ?>">
                                    <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    <i class="bi bi-book-fill text-primary me-2"></i>
                                    <?= esc($course['title']) ?>
                                </h5>
                            </div>
                                        <p class="card-text text-muted flex-grow-1 mb-3">
                                            <?= esc($course['description']) ?>
                                        </p>
                                        <div class="mt-auto">
                                            <small class="text-muted">
                                                <i class="bi bi-person me-1"></i>
                                                Teacher: <?= esc($course['teacher_name'] ?? 'Unknown') ?>
                                            </small>
                                            <br>
                                            <small class="text-muted">
                                                <i class="bi bi-calendar me-1"></i>
                                                Created: <?= date('M d, Y', strtotime($course['created_at'])) ?>
                                            </small>
                                        </div>
                                    </div>
<?php if (session()->get('isAuthenticated')): ?>
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex gap-2">
                                            <button class="btn btn-success btn-sm enroll-btn flex-grow-1"
                                                    data-course-id="<?= $course['id'] ?>"
                                                    data-course-title="<?= esc($course['title']) ?>">
                                                <i class="bi bi-plus-circle me-1"></i> Enroll
                                            </button>
                                        </div>
                                    </div>
<?php endif; ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mt-3">No courses found</h4>
                        <p class="text-muted">
                            <?= !empty($search_term) ? 'Try adjusting your search terms or clear the search to see all courses.' : 'No courses are available at the moment.' ?>
                        </p>
                        <?php if (!empty($search_term)): ?>
                            <button class="btn btn-primary" id="clear-search">
                                <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search
                            </button>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- jQuery Search and Filtering Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
var isAuthenticated = <?= session()->get('isAuthenticated') ? 'true' : 'false' ?>;
</script>
<script>
$(document).ready(function() {
    let searchTimeout;

    // Client-side filtering
    function filterCourses(searchTerm) {
        const $courses = $('.course-item');
        let visibleCount = 0;

        if (!searchTerm.trim()) {
            $courses.show();
            visibleCount = $courses.length;
        } else {
            $courses.each(function() {
                const content = $(this).data('search-content') || '';
                if (content.includes(searchTerm.toLowerCase())) {
                    $(this).show();
                    visibleCount++;
                } else {
                    $(this).hide();
                }
            });
        }

        // Show/hide no results message
        const $noResults = $('#courses-list .text-center');
        if (visibleCount === 0 && $courses.length > 0) {
            if ($noResults.length === 0) {
                $('#courses-list').html(`
                    <div class="text-center py-5">
                        <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                        <h4 class="text-muted mt-3">No courses match your search</h4>
                        <p class="text-muted">Try different keywords or clear the search.</p>
                        <button class="btn btn-primary" id="clear-search">
                            <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search
                        </button>
                    </div>
                `);
            }
        } else if (visibleCount === 0 && $courses.length === 0) {
            // Already showing no courses message
        } else {
            $noResults.remove();
        }
    }

    // Handle search input (client-side filtering with debounce)
    $('#course-search').on('input', function() {
        const searchTerm = $(this).val().trim();
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(function() {
            filterCourses(searchTerm);
        }, 300); // 300ms debounce
    });

    // Handle form submit (server-side search)
    $('#course-search-form').on('submit', function(e) {
        e.preventDefault();
        const searchTerm = $('#course-search').val().trim();

        if (searchTerm) {
            // Perform AJAX search
            $('#courses-list').html(`
                <div class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">Searching...</span>
                    </div>
                    <p class="mt-2 text-muted">Searching courses...</p>
                </div>
            `);

            $.get('<?= base_url('courses/search') ?>', { q: searchTerm })
                .done(function(data) {
                    // Update URL without reload
                    const newUrl = new URL(window.location);
                    newUrl.searchParams.set('q', searchTerm);
                    window.history.replaceState({}, '', newUrl);

                    renderCourses(data);
                })
                .fail(function() {
                    $('#courses-list').html(`
                        <div class="text-center py-5">
                            <i class="bi bi-exclamation-triangle text-danger" style="font-size: 3rem;"></i>
                            <h4 class="text-danger mt-3">Search Failed</h4>
                            <p class="text-muted">Please try again later.</p>
                            <button class="btn btn-primary" onclick="location.reload()">
                                <i class="bi bi-arrow-clockwise me-1"></i> Reload Page
                            </button>
                        </div>
                    `);
                });
        }
    });

    // Handle clear search
    $(document).on('click', '#clear-search', function() {
        $('#course-search').val('');
        $('#courses-list').html(`
            <div class="text-center py-5">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">Loading...</span>
                </div>
                <p class="mt-2 text-muted">Loading courses...</p>
            </div>
        `);

        // Clear URL parameter
        const newUrl = new URL(window.location);
        newUrl.searchParams.delete('q');
        window.history.replaceState({}, '', newUrl);

        // Reload page to show all courses
        location.reload();
    });

    // Function to render courses from AJAX response
    function renderCourses(courses) {
        if (!courses || courses.length === 0) {
            $('#courses-list').html(`
                <div class="text-center py-5">
                    <i class="bi bi-search text-muted" style="font-size: 3rem;"></i>
                    <h4 class="text-muted mt-3">No courses found</h4>
                    <p class="text-muted">Try adjusting your search terms.</p>
                    <button class="btn btn-primary" id="clear-search">
                        <i class="bi bi-arrow-counterclockwise me-1"></i> Clear Search
                    </button>
                </div>
            `);
            return;
        }

        let html = '<div class="row g-4">';
        courses.forEach(function(course) {
            const teacherName = course.teacher_name || 'Unknown';
            const createdDate = new Date(course.created_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
            const searchContent = (course.title + ' ' + course.description).toLowerCase();

            html += `
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm course-item" data-search-content="${searchContent}">
                        <div class="card-body d-flex flex-column">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h5 class="card-title mb-0 flex-grow-1">
                                    <i class="bi bi-book-fill text-primary me-2"></i>
                                    ${course.title}
                                </h5>
                            </div>
                            <p class="card-text text-muted flex-grow-1 mb-3">
                                ${course.description}
                            </p>
                            <div class="mt-auto">
                                <small class="text-muted">
                                    <i class="bi bi-person me-1"></i>
                                    Teacher: ${teacherName}
                                </small>
                                <br>
                                <small class="text-muted">
                                    <i class="bi bi-calendar me-1"></i>
                                    Created: ${createdDate}
                                </small>
                            </div>
                        </div>
                        ${isAuthenticated ? `
                        <div class="card-footer bg-transparent">
                            <div class="d-flex gap-2">
                                <button class="btn btn-success btn-sm enroll-btn flex-grow-1"
                                        data-course-id="${course.id}"
                                        data-course-title="${course.title}">
                                    <i class="bi bi-plus-circle me-1"></i> Enroll
                                </button>
                            </div>
                        </div>
                        ` : ''}
                    </div>
                </div>
            `;
        });
        html += '</div>';

        $('#courses-list').html(html);
    }

    // Handle enrollment (from dashboard logic)
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

                // Show success message (simple implementation)
                alert('Successfully enrolled in "' + courseTitle + '"!');
                // Immediately refresh notifications (real-time UX)
                if (typeof window.fetchNotifications === 'function') {
                    window.fetchNotifications();
                }
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
});
</script>

<?= $this->endSection() ?>
