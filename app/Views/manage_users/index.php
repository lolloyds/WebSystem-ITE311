<?= $this->extend('template') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="d-flex justify-content-between align-items-center mb-4 p-3 rounded-3 shadow-sm" 
     style="background: linear-gradient(90deg, #0d6efd, #0dcaf0); color: #fff;">
    <h1 class="h3 mb-0 d-flex align-items-center">
        <i class="bi bi-people-fill me-2"></i> Manage Users 👥
    </h1>
    <button type="button" class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addUserModal">
        <i class="bi bi-person-plus me-1"></i> Add User
    </button>
</div>

<!-- Alert Container -->
<div id="alert-container" class="position-fixed" style="top: 80px; right: 20px; z-index: 1050; max-width: 400px;"></div>

<style>
    .table tbody td {
        vertical-align: middle !important;
    }
    .table thead th {
        vertical-align: middle !important;
    }
    .table tbody td[class*="status"],
    .table tbody td:has(.badge) {
        vertical-align: middle !important;
    }
    .table tbody td .badge {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        vertical-align: middle !important;
        margin: 0 auto !important;
    }
</style>

<!-- Users Table -->
<div class="card shadow-sm border-0 rounded-3">
    <div class="card-header bg-primary text-white fw-semibold">
        <i class="bi bi-table me-2"></i> All Users
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th style="width: 5%;" class="align-middle">#</th>
                        <th style="width: 18%;" class="align-middle"><i class="bi bi-person me-1"></i> Full Name</th>
                        <th style="width: 22%;" class="align-middle"><i class="bi bi-envelope me-1"></i> Email/Username</th>
                        <th style="width: 12%;" class="align-middle"><i class="bi bi-shield-lock me-1"></i> Role</th>
                        <th style="width: 10%;" class="align-middle text-center"><i class="bi bi-toggle-on me-1"></i> Status</th>
                        <th style="width: 18%;" class="align-middle"><i class="bi bi-calendar me-1"></i> Created</th>
                        <th style="width: 15%;" class="text-center align-middle"><i class="bi bi-gear me-1"></i> Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($users)): ?>
                        <?php foreach ($users as $user): ?>
                            <?php 
                            $isProtected = ($user['id'] == $protectedAdminId);
                            $roleBadgeClass = 'bg-secondary';
                            if ($user['role'] === 'admin') {
                                $roleBadgeClass = 'bg-danger';
                            } elseif ($user['role'] === 'teacher') {
                                $roleBadgeClass = 'bg-warning text-dark';
                            } elseif ($user['role'] === 'student') {
                                $roleBadgeClass = 'bg-info';
                            }
                            ?>
                            <?php 
                            $userStatus = $user['status'] ?? 'active';
                            $rowClass = $isProtected ? 'table-warning' : '';
                            if ($userStatus === 'inactive' && !$isProtected) {
                                $rowClass .= ' table-secondary';
                            }
                            ?>
                            <tr id="user-row-<?= $user['id'] ?>" class="<?= trim($rowClass) ?>">
                                <td class="align-middle"><?= esc($user['id']) ?></td>
                                <td class="align-middle">
                                    <?= esc($user['name']) ?>
                                    <?php if ($isProtected): ?>
                                        <span class="badge bg-danger ms-1" title="Protected Admin Account">
                                            <i class="bi bi-shield-check"></i> Protected
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle"><?= esc($user['email']) ?></td>
                                <td class="align-middle">
                                    <?php if ($isProtected): ?>
                                        <!-- Protected admin - show role but disable dropdown -->
                                        <span class="badge <?= $roleBadgeClass ?>">
                                            <?= ucfirst(esc($user['role'])) ?>
                                        </span>
                                    <?php else: ?>
                                        <!-- Regular user - editable role dropdown -->
                                        <select class="form-select form-select-sm role-select" 
                                                data-user-id="<?= $user['id'] ?>"
                                                style="width: auto; display: inline-block;">
                                            <option value="student" <?= $user['role'] === 'student' ? 'selected' : '' ?>>Student</option>
                                            <option value="teacher" <?= $user['role'] === 'teacher' ? 'selected' : '' ?>>Teacher</option>
                                            <option value="admin" <?= $user['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                                        </select>
                                    <?php endif; ?>
                                </td>
                                <td class="align-middle text-center" style="vertical-align: middle !important; height: 100%;">
                                    <div style="display: flex; align-items: center; justify-content: center; height: 100%;">
                                        <?php 
                                        $userStatus = $user['status'] ?? 'active';
                                        $statusBadgeClass = ($userStatus === 'active') ? 'bg-success' : 'bg-secondary';
                                        $statusText = ucfirst($userStatus);
                                        ?>
                                        <span class="badge <?= $statusBadgeClass ?>" id="status-badge-<?= $user['id'] ?>" style="display: inline-flex; align-items: center; justify-content: center;">
                                            <i class="bi bi-<?= $userStatus === 'active' ? 'check-circle' : 'x-circle' ?> me-1"></i>
                                            <?= esc($statusText) ?>
                                        </span>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <small class="text-muted">
                                        <?= $user['created_at'] ? date('M d, Y', strtotime($user['created_at'])) : 'N/A' ?>
                                    </small>
                                </td>
                                <td class="text-center align-middle">
                                    <div class="btn-group btn-group-sm" role="group">
                                        <!-- Change Password Button -->
                                        <button type="button" 
                                                class="btn btn-outline-primary change-password-btn" 
                                                data-user-id="<?= $user['id'] ?>"
                                                data-user-name="<?= esc($user['name']) ?>"
                                                title="Change Password">
                                            <i class="bi bi-key"></i>
                                        </button>
                                        
                                        <?php if (!$isProtected): ?>
                                            <!-- Deactivate/Activate Button (only for non-protected users) -->
                                            <button type="button" 
                                                    class="btn btn-outline-<?= $userStatus === 'active' ? 'warning' : 'success' ?> toggle-status-btn" 
                                                    data-user-id="<?= $user['id'] ?>"
                                                    data-user-name="<?= esc($user['name']) ?>"
                                                    data-current-status="<?= esc($userStatus) ?>"
                                                    title="<?= $userStatus === 'active' ? 'Deactivate Account' : 'Activate Account' ?>">
                                                <i class="bi bi-<?= $userStatus === 'active' ? 'pause-circle' : 'play-circle' ?>"></i>
                                            </button>
                                        <?php else: ?>
                                            <!-- Disabled button for protected admin -->
                                            <button type="button" 
                                                    class="btn btn-outline-secondary" 
                                                    disabled
                                                    title="Cannot deactivate protected admin account">
                                                <i class="bi bi-shield-check"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                No users found.
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal fade" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="addUserModalLabel">
                    <i class="bi bi-person-plus me-2"></i> Add New User
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="addUserForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="userName" class="form-label">Full Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="userName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="userEmail" class="form-label">Email/Username <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" id="userEmail" name="email" required>
                        <div class="form-text">Must be a valid email address and unique.</div>
                    </div>
                    <div class="mb-3">
                        <label for="userPassword" class="form-label">Password <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="userPassword" name="password" value="password123" readonly>
                        <input type="hidden" name="password" value="password123">
                        <div class="alert alert-info mt-2 mb-0" role="alert">
                            <i class="bi bi-info-circle me-2"></i>
                            <strong>Default Password:</strong> All new users will be created with the default password <code>password123</code>. Users should change their password after first login.
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="userRole" class="form-label">Role <span class="text-danger">*</span></label>
                        <select class="form-select" id="userRole" name="role" required>
                            <option value="">Select Role</option>
                            <option value="student">Student</option>
                            <option value="teacher">Teacher</option>
                            <option value="admin">Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Create User
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Change Password Modal -->
<div class="modal fade" id="changePasswordModal" tabindex="-1" aria-labelledby="changePasswordModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="changePasswordModalLabel">
                    <i class="bi bi-key me-2"></i> Change Password
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form id="changePasswordForm">
                <input type="hidden" id="changePasswordUserId" name="user_id">
                <div class="modal-body">
                    <p class="mb-3">
                        <strong>User:</strong> <span id="changePasswordUserName"></span>
                    </p>
                    <div class="mb-3">
                        <label for="newPassword" class="form-label">New Password <span class="text-danger">*</span></label>
                        <input type="password" class="form-control" id="newPassword" name="password" required>
                        <div class="form-text">Minimum 8 characters, must contain at least one letter and one number.</div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Update Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
    // Function to show alert messages
    function showAlert(type, message) {
        const alertClass = type === 'success' ? 'alert-success' : 'alert-danger';
        const iconClass = type === 'success' ? 'check-circle' : 'exclamation-triangle';
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                <i class="bi bi-${iconClass} me-2"></i>
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

    // Handle Add User Form Submission
    $('#addUserForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            name: $('#userName').val().trim(),
            email: $('#userEmail').val().trim(),
            password: 'password123', // Always use default password
            role: $('#userRole').val()
        };

        // Basic client-side validation
        if (!formData.name || !formData.email || !formData.role) {
            showAlert('danger', 'Please fill in all required fields.');
            return;
        }

        // Disable submit button
        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Creating...');

        $.ajax({
            url: '<?= base_url('manage-users/add') ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#addUserModal').modal('hide');
                    // Reset form and restore default password
                    $('#addUserForm')[0].reset();
                    $('#userPassword').val('password123');
                    // Reload page after 1 second to show new user
                    setTimeout(function() {
                        location.reload();
                    }, 1000);
                } else {
                    showAlert('danger', response.message);
                    submitBtn.prop('disabled', false).html(originalText);
                }
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Handle Role Change
    $('.role-select').on('change', function() {
        const userId = $(this).data('user-id');
        const newRole = $(this).val();
        const $select = $(this);

        // Disable select during update
        $select.prop('disabled', true);

        $.ajax({
            url: '<?= base_url('manage-users/update-role') ?>',
            method: 'POST',
            data: {
                user_id: userId,
                role: newRole
            },
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    // Update badge color if needed
                    const roleBadgeClass = {
                        'admin': 'bg-danger',
                        'teacher': 'bg-warning text-dark',
                        'student': 'bg-info'
                    };
                } else {
                    showAlert('danger', response.message);
                    // Revert to original value
                    location.reload();
                }
                $select.prop('disabled', false);
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
                location.reload();
            }
        });
    });

    // Handle Change Password Button Click
    $('.change-password-btn').on('click', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        
        $('#changePasswordUserId').val(userId);
        $('#changePasswordUserName').text(userName);
        $('#changePasswordModal').modal('show');
    });

    // Handle Change Password Form Submission
    $('#changePasswordForm').on('submit', function(e) {
        e.preventDefault();
        
        const formData = {
            user_id: $('#changePasswordUserId').val(),
            password: $('#newPassword').val()
        };

        if (!formData.password) {
            showAlert('danger', 'Please enter a new password.');
            return;
        }

        const submitBtn = $(this).find('button[type="submit"]');
        const originalText = submitBtn.html();
        submitBtn.prop('disabled', true).html('<i class="bi bi-hourglass-split me-1"></i> Updating...');

        $.ajax({
            url: '<?= base_url('manage-users/change-password') ?>',
            method: 'POST',
            data: formData,
            dataType: 'json',
            success: function(response) {
                if (response.success) {
                    showAlert('success', response.message);
                    $('#changePasswordModal').modal('hide');
                    $('#changePasswordForm')[0].reset();
                } else {
                    showAlert('danger', response.message);
                }
                submitBtn.prop('disabled', false).html(originalText);
            },
            error: function() {
                showAlert('danger', 'An error occurred. Please try again.');
                submitBtn.prop('disabled', false).html(originalText);
            }
        });
    });

    // Handle Toggle Status Button Click (Deactivate/Activate)
    $(document).on('click', '.toggle-status-btn', function() {
        const userId = $(this).data('user-id');
        const userName = $(this).data('user-name');
        const currentStatus = $(this).data('current-status');
        const action = currentStatus === 'active' ? 'deactivate' : 'activate';
        
        if (confirm('Are you sure you want to ' + action + ' the account for user "' + userName + '"?')) {
            const $btn = $(this);
            const $row = $('#user-row-' + userId);
            $btn.prop('disabled', true).html('<i class="bi bi-hourglass-split"></i>');

            $.ajax({
                url: '<?= base_url('manage-users/toggle-status') ?>',
                method: 'POST',
                data: {
                    user_id: userId
                },
                dataType: 'json',
                success: function(response) {
                    if (response.success) {
                        showAlert('success', response.message);
                        
                        // Update status badge
                        const newStatus = response.new_status;
                        const $badge = $('#status-badge-' + userId);
                        const newBadgeClass = newStatus === 'active' ? 'bg-success' : 'bg-secondary';
                        const newStatusText = newStatus.charAt(0).toUpperCase() + newStatus.slice(1);
                        const newIcon = newStatus === 'active' ? 'check-circle' : 'x-circle';
                        
                        $badge.removeClass('bg-success bg-secondary')
                               .addClass(newBadgeClass)
                               .html('<i class="bi bi-' + newIcon + ' me-1"></i>' + newStatusText)
                               .css('display', 'inline-flex')
                               .css('align-items', 'center')
                               .css('justify-content', 'center');
                        
                        // Update button
                        const newBtnClass = newStatus === 'active' ? 'btn-outline-warning' : 'btn-outline-success';
                        const newBtnIcon = newStatus === 'active' ? 'pause-circle' : 'play-circle';
                        const newBtnTitle = newStatus === 'active' ? 'Deactivate Account' : 'Activate Account';
                        
                        $btn.removeClass('btn-outline-warning btn-outline-success')
                            .addClass(newBtnClass)
                            .attr('data-current-status', newStatus)
                            .attr('title', newBtnTitle)
                            .html('<i class="bi bi-' + newBtnIcon + '"></i>');
                        
                        // Update row styling if needed
                        if (newStatus === 'inactive') {
                            $row.addClass('table-secondary');
                        } else {
                            $row.removeClass('table-secondary');
                        }
                    } else {
                        showAlert('danger', response.message);
                    }
                    $btn.prop('disabled', false);
                },
                error: function() {
                    showAlert('danger', 'An error occurred. Please try again.');
                    $btn.prop('disabled', false);
                }
            });
        }
    });

    // Reset modals when closed
    $('#addUserModal, #changePasswordModal').on('hidden.bs.modal', function() {
        $(this).find('form')[0].reset();
        // Restore default password for Add User modal
        if ($(this).attr('id') === 'addUserModal') {
            $('#userPassword').val('password123');
        }
    });
});
</script>

<?= $this->endSection() ?>

