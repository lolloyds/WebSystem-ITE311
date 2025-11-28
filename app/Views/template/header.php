<!doctype html>
<html>
<head>
  <title>ITE311-DORAIDO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
      <a class="navbar-brand" href="<?= base_url('/') ?>">ITE311-DORAIDO</a>
      <div class="collapse navbar-collapse">
        <ul class="navbar-nav ms-auto">
          <?php if (session()->get('isAuthenticated')): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="settingsDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="bi bi-gear-fill me-1"></i>Settings
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="settingsDropdown">
              <li><h6 class="dropdown-header">Account Settings</h6></li>
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#profileModal">
                <i class="bi bi-person-gear me-2"></i>Profile Settings
              </a></li>
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#passwordModal">
                <i class="bi bi-shield-lock me-2"></i>Change Password
              </a></li>
              <li><hr class="dropdown-divider"></li>
              <li><h6 class="dropdown-header">Preferences</h6></li>
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#notificationsModal">
                <i class="bi bi-bell me-2"></i>Notifications
              </a></li>
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#preferencesModal">
                <i class="bi bi-sliders me-2"></i>Preferences
              </a></li>
              <li class="nav-item">
            <a class="nav-link" href="<?= base_url('courses') ?>">Courses</a>
          </li>
              <?php $role = (string) session()->get('userRole'); ?>
              <?php if ($role === 'admin'): ?>
              <li><hr class="dropdown-divider"></li>
              <li><h6 class="dropdown-header">Admin Tools</h6></li>
              <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#adminModal">
                <i class="bi bi-tools me-2"></i>System Management
              </a></li>
              <?php endif; ?>
            </ul>
          </li>
          <?php endif; ?>
          <?php if (session()->get('isAuthenticated')): ?>
          <?php $role = (string) session()->get('userRole'); ?>
          <?php if ($role === 'admin'): ?>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Admin</a>
          </li>
          <?php endif; ?>
          <?php endif; ?>
          <li class="nav-item">
            <?php if (session()->get('isAuthenticated')): ?>
              <a class="btn btn-danger ms-2" href="<?= base_url('logout') ?>">Logout</a>
            <?php else: ?>
              <a class="btn btn-success ms-2" href="<?= base_url('login') ?>">Login</a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Settings Modals -->
  <?php if (session()->get('isAuthenticated')): ?>
  
  <!-- Profile Settings Modal -->
  <div class="modal fade" id="profileModal" tabindex="-1" aria-labelledby="profileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-primary text-white">
          <h5 class="modal-title" id="profileModalLabel">
            <i class="bi bi-person-gear me-2"></i>Profile Settings
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= base_url('settings/updateProfile') ?>" method="post">
          <div class="modal-body">
            <?= csrf_field() ?>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="name" class="form-label">Full Name</label>
                <input type="text" class="form-control" id="name" name="name" 
                       value="<?= esc(session()->get('userName') ?? '') ?>" required>
              </div>
              <div class="col-md-6">
                <label for="email" class="form-label">Email Address</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= esc(session()->get('userEmail') ?? '') ?>" required>
              </div>
              <div class="col-12">
                <label for="role" class="form-label">Role</label>
                <input type="text" class="form-control" value="<?= esc(ucfirst(session()->get('userRole') ?? '')) ?>" readonly>
                <div class="form-text">Your role cannot be changed</div>
              </div>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-primary">
              <i class="bi bi-save me-1"></i>Update Profile
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Change Password Modal -->
  <div class="modal fade" id="passwordModal" tabindex="-1" aria-labelledby="passwordModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="passwordModalLabel">
            <i class="bi bi-shield-lock me-2"></i>Change Password
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <form action="<?= base_url('settings/changePassword') ?>" method="post">
          <div class="modal-body">
            <?= csrf_field() ?>
            <div class="mb-3">
              <label for="current_password" class="form-label">Current Password</label>
              <input type="password" class="form-control" id="current_password" name="current_password" required>
            </div>
            <div class="mb-3">
              <label for="new_password" class="form-label">New Password</label>
              <input type="password" class="form-control" id="new_password" name="new_password" required>
            </div>
            <div class="mb-3">
              <label for="confirm_password" class="form-label">Confirm New Password</label>
              <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
          </div>
          <div class="modal-footer">
            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
            <button type="submit" class="btn btn-danger">
              <i class="bi bi-key me-1"></i>Change Password
            </button>
          </div>
        </form>
      </div>
    </div>
  </div>

  <!-- Notifications Modal -->
  <div class="modal fade" id="notificationsModal" tabindex="-1" aria-labelledby="notificationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-info text-white">
          <h5 class="modal-title" id="notificationsModalLabel">
            <i class="bi bi-bell me-2"></i>Notification Settings
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="email_notifications" checked>
                <label class="form-check-label" for="email_notifications">
                  Email Notifications
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="system_updates" checked>
                <label class="form-check-label" for="system_updates">
                  System Updates
                </label>
              </div>
            </div>
            <?php $role = (string) session()->get('userRole'); ?>
            <?php if ($role === 'teacher'): ?>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="student_enrollments" checked>
                <label class="form-check-label" for="student_enrollments">
                  Student Enrollment Alerts
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="assignment_submissions" checked>
                <label class="form-check-label" for="assignment_submissions">
                  Assignment Submission Alerts
                </label>
              </div>
            </div>
            <?php elseif ($role === 'student'): ?>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="course_updates" checked>
                <label class="form-check-label" for="course_updates">
                  Course Update Notifications
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="assignment_reminders" checked>
                <label class="form-check-label" for="assignment_reminders">
                  Assignment Reminders
                </label>
              </div>
            </div>
            <?php endif; ?>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-info">
            <i class="bi bi-save me-1"></i>Save Notifications
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Preferences Modal -->
  <div class="modal fade" id="preferencesModal" tabindex="-1" aria-labelledby="preferencesModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header bg-success text-white">
          <h5 class="modal-title" id="preferencesModalLabel">
            <i class="bi bi-sliders me-2"></i>User Preferences
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <?php $role = (string) session()->get('userRole'); ?>
            <?php if ($role === 'teacher'): ?>
            <div class="col-md-6">
              <label for="teaching_subjects" class="form-label">Teaching Subjects</label>
              <select class="form-select" id="teaching_subjects" multiple>
                <option value="mathematics">Mathematics</option>
                <option value="science">Science</option>
                <option value="english">English</option>
                <option value="history">History</option>
                <option value="computer_science">Computer Science</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="experience_level" class="form-label">Experience Level</label>
              <select class="form-select" id="experience_level">
                <option value="beginner">Beginner (0-2 years)</option>
                <option value="intermediate">Intermediate (3-5 years)</option>
                <option value="advanced">Advanced (6-10 years)</option>
                <option value="expert">Expert (10+ years)</option>
              </select>
            </div>
            <?php elseif ($role === 'student'): ?>
            <div class="col-md-6">
              <label for="learning_style" class="form-label">Learning Style</label>
              <select class="form-select" id="learning_style">
                <option value="visual">Visual Learner</option>
                <option value="auditory">Auditory Learner</option>
                <option value="kinesthetic">Kinesthetic Learner</option>
                <option value="reading">Reading/Writing Learner</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="difficulty_level" class="form-label">Preferred Difficulty Level</label>
              <select class="form-select" id="difficulty_level">
                <option value="beginner">Beginner</option>
                <option value="intermediate">Intermediate</option>
                <option value="advanced">Advanced</option>
              </select>
            </div>
            <?php endif; ?>
            <div class="col-md-6">
              <label for="timezone" class="form-label">Timezone</label>
              <select class="form-select" id="timezone">
                <option value="UTC">UTC</option>
                <option value="America/New_York">Eastern Time</option>
                <option value="America/Chicago">Central Time</option>
                <option value="America/Denver">Mountain Time</option>
                <option value="America/Los_Angeles">Pacific Time</option>
              </select>
            </div>
            <div class="col-md-6">
              <label for="language" class="form-label">Language</label>
              <select class="form-select" id="language">
                <option value="en">English</option>
                <option value="es">Spanish</option>
                <option value="fr">French</option>
                <option value="de">German</option>
              </select>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-success">
            <i class="bi bi-save me-1"></i>Save Preferences
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Admin Tools Modal (Admin only) -->
  <?php if ($role === 'admin'): ?>
  <div class="modal fade" id="adminModal" tabindex="-1" aria-labelledby="adminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl">
      <div class="modal-content">
        <div class="modal-header bg-danger text-white">
          <h5 class="modal-title" id="adminModalLabel">
            <i class="bi bi-tools me-2"></i>System Management
          </h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="row g-3">
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="maintenanceMode">
                <label class="form-check-label" for="maintenanceMode">
                  Maintenance Mode
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="userRegistration">
                <label class="form-check-label" for="userRegistration">
                  Allow User Registration
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="emailNotifications">
                <label class="form-check-label" for="emailNotifications">
                  Email Notifications
                </label>
              </div>
            </div>
            <div class="col-md-6">
              <div class="form-check form-switch">
                <input class="form-check-input" type="checkbox" id="debugMode">
                <label class="form-check-label" for="debugMode">
                  Debug Mode
                </label>
              </div>
            </div>
          </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
          <button type="button" class="btn btn-danger">
            <i class="bi bi-save me-1"></i>Save System Settings
          </button>
        </div>
      </div>
    </div>
  </div>
  <?php endif; ?>

  <?php endif; ?>

  <!-- Flash Messages -->
  <?php if (session()->getFlashdata('success')): ?>
    <div class="alert alert-success alert-dismissible fade show" role="alert">
      <i class="bi bi-check-circle me-2"></i>
      <?= esc(session()->getFlashdata('success')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <?php if (session()->getFlashdata('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
      <i class="bi bi-exclamation-triangle me-2"></i>
      <?= esc(session()->getFlashdata('error')) ?>
      <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
  <?php endif; ?>

  <!-- Main content -->
  <div class="container mt-4">
    <?= $this->renderSection('content') ?>
  </div>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>