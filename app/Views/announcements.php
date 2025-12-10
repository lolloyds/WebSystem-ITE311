<?= $this->extend('template') ?>

<?= $this->section('content') ?>
<div class="container-fluid">
    <!-- Announcements Banner -->
    <div class="row">
        <div class="col-12">
            <div class="bg-primary text-white p-4 mb-4">
                <div class="d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-bullhorn me-3" style="font-size: 2rem;"></i>
                        <div>
                            <h2 class="mb-1">Announcements</h2>
                            <p class="mb-0">Stay updated with the latest news and announcements</p>
                        </div>
                    </div>
                    <?php
                    $userRole = session()->get('userRole');
                    if ($userRole === 'admin' || $userRole === 'teacher'):
                    ?>
                    <div>
                        <button class="btn btn-warning" data-bs-toggle="modal" data-bs-target="#createAnnouncementModal">
                            <i class="fas fa-plus me-2"></i>Create New Announcement
                        </button>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Announcements Content -->
    <div class="row">
        <div class="col-12">
            <?php if (empty($announcements)): ?>
                <div class="alert alert-info">
                    <i class="fas fa-info-circle me-2"></i>
                    No announcements available at the moment.
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="card mb-3 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title text-primary fw-bold">
                                <?= esc($announcement['title']) ?>
                            </h5>
                            <p class="card-text">
                                <?= nl2br(esc($announcement['content'])) ?>
                            </p>
                            <small class="text-muted">
                                <i class="fas fa-clock me-1"></i>
                                Posted on: <?= date('F j, Y \a\t g:i A', strtotime($announcement['created_at'])) ?>
                            </small>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Create Announcement Modal -->
<div class="modal fade" id="createAnnouncementModal" tabindex="-1" aria-labelledby="createAnnouncementModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="createAnnouncementModalLabel">
                    <i class="fas fa-bullhorn me-2"></i>Create New Announcement
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
                        <i class="fas fa-paper-plane me-1"></i>Post Announcement
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- jQuery for Announcement Creation -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
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
        submitBtn.html('<i class="fas fa-spinner fa-spin me-1"></i>Posting...');

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

                    // Reload page to show new announcement
                    location.reload();
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
});
</script>
<?= $this->endSection() ?>
