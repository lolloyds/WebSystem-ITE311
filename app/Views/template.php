<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Learning Management System</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

  <style>
    /* Custom navbar gradient */
    .navbar-custom {
      background: linear-gradient(90deg, #1e3a8a, #1e40af);
    }
    .navbar-brand {
      font-size: 1.5rem;
      font-weight: bold;
    }
    .notification-dropdown {
      min-width: 300px;
      max-height: 400px;
      overflow-y: auto;
    }
    .notification-item {
      padding: 10px;
      border-bottom: 1px solid #dee2e6;
      cursor: pointer;
    }
    .notification-item:hover {
      background-color: #f8f9fa;
    }
    .notification-item:last-child {
      border-bottom: none;
    }
    .badge {
      position: absolute;
      top: 5px;
      right: 5px;
    }
  </style>
</head>
<body>
  <!-- Navbar -->
  <nav class="navbar navbar-expand-lg navbar-dark navbar-custom shadow-sm">
    <div class="container-fluid">
      <!-- Brand -->
      <a class="navbar-brand fw-bold" href="<?= base_url('/') ?>">
        Learning Management System
      </a>

      <!-- Mobile Toggle -->
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#mainNavbar">
        <span class="navbar-toggler-icon"></span>
      </button>

      <!-- Navbar Links -->
      <div class="collapse navbar-collapse" id="mainNavbar">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('/') ?>">Home</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('about') ?>">About</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('contact') ?>">Contact</a>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="<?= base_url('courses') ?>">Courses</a>
          </li>

          <?php if (session()->get('isAuthenticated')): ?>
            <!-- Notifications Dropdown -->
            <li class="nav-item dropdown">
              <a class="nav-link position-relative" href="#" id="notificationDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="bi bi-bell"></i>
                <span id="notificationBadge" class="badge bg-danger badge-sm" style="display: none;">0</span>
              </a>
              <ul class="dropdown-menu dropdown-menu-end notification-dropdown" aria-labelledby="notificationDropdown">
                <li><h6 class="dropdown-header">Notifications</h6></li>
                <li><hr class="dropdown-divider"></li>
                <div id="notificationsList">
                  <li class="px-3 py-2 text-muted">No notifications</li>
                </div>
              </ul>
            </li>
            
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('announcements') ?>">Announcements</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="<?= base_url('dashboard') ?>">Dashboard</a>
            </li>
          <?php endif; ?>

          <?php if (session()->get('isAuthenticated')): ?>
            <?php $role = (string) session()->get('userRole'); ?>
            <?php if ($role === 'admin'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= base_url('admin/dashboard') ?>">Admin</a>
              </li>
            <?php elseif ($role === 'teacher'): ?>
              <li class="nav-item">
                <a class="nav-link" href="<?= base_url('teacher/dashboard') ?>">Teacher</a>
              </li>
            <?php endif; ?>
          <?php endif; ?>

          <li class="nav-item">
            <?php if (session()->get('isAuthenticated')): ?>
              <a class="nav-link" href="<?= base_url('logout') ?>">Logout</a>
            <?php else: ?>
              <a class="nav-link" href="<?= base_url('login') ?>">Login</a>
            <?php endif; ?>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Main content -->
  <?= $this->renderSection('content') ?>

  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

  <?php if (session()->get('isAuthenticated')): ?>
  <script>
    $(document).ready(function() {
      // Function to fetch notifications
      function fetchNotifications() {
        $.get('<?= base_url('notifications') ?>', function(response) {
          if (response.success) {
            updateNotificationBadge(response.count);
            updateNotificationsList(response.notifications);
          }
        }).fail(function() {
          console.error('Failed to fetch notifications');
        });
      }
      // Expose fetchNotifications globally so other scripts can trigger an immediate refresh
      window.fetchNotifications = fetchNotifications;

      // Update notification badge
      function updateNotificationBadge(count) {
        var badge = $('#notificationBadge');
        if (count > 0) {
          badge.text(count).show();
        } else {
          badge.hide();
        }
      }

      // Update notifications list
      function updateNotificationsList(notifications) {
        var listContainer = $('#notificationsList');
        listContainer.empty();

        if (notifications.length === 0) {
          listContainer.html('<li class="px-3 py-2 text-muted">No notifications</li>');
        } else {
          notifications.forEach(function(notification) {
            var isRead = notification.is_read == 1;
            var alertClass = isRead ? 'alert-secondary' : 'alert-info';
            var badgeStyle = isRead ? '' : 'border-left: 3px solid #0d6efd;';
            
            var item = $('<li class="px-2">' +
              '<div class="alert ' + alertClass + ' mb-1 notification-item" style="' + badgeStyle + '">' +
                '<div class="d-flex justify-content-between align-items-start">' +
                  '<div class="flex-grow-1">' +
                          '<small class="text-muted">' + formatDate(notification.created_at_ts || notification.created_at_iso || notification.created_at) + '</small><br>' +
                    '<p class="mb-0">' + notification.message + '</p>' +
                  '</div>' +
                  (!isRead ? 
                    '<button class="btn btn-sm btn-link p-0 mark-read-btn" data-id="' + notification.id + '">' +
                      '<i class="bi bi-check-circle text-success"></i>' +
                    '</button>' : '') +
                '</div>' +
              '</div>' +
            '</li>');

            listContainer.append(item);
          });
        }
      }

      // Format date for display
      function formatDate(dateOrTs) {
        var date;
        // If a number is provided, treat it as epoch ms
        if (typeof dateOrTs === 'number') {
          date = new Date(dateOrTs);
        } else if (typeof dateOrTs === 'string' && dateOrTs.match(/^\d+$/)) {
          // numeric string
          date = new Date(parseInt(dateOrTs, 10));
        } else {
          date = new Date(dateOrTs);
        }

        if (isNaN(date.getTime())) {
          return '';
        }

        var now = new Date();
        var diffMs = now.getTime() - date.getTime();
        var diffMins = Math.floor(diffMs / 60000);
        var diffHours = Math.floor(diffMs / 3600000);
        var diffDays = Math.floor(diffMs / 86400000);

        if (diffMins < 1) {
          return 'Just now';
        } else if (diffMins < 60) {
          return diffMins + ' minute' + (diffMins > 1 ? 's' : '') + ' ago';
        } else if (diffHours < 24) {
          return diffHours + ' hour' + (diffHours > 1 ? 's' : '') + ' ago';
        } else if (diffDays < 7) {
          return diffDays + ' day' + (diffDays > 1 ? 's' : '') + ' ago';
        } else {
          return date.toLocaleDateString();
        }
      }

      // Mark notification as read
      $(document).on('click', '.mark-read-btn', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var notificationId = $(this).data('id');
        var $item = $(this).closest('li');

        $.post('<?= base_url('notifications/mark_read/') ?>' + notificationId, function(response) {
          if (response.success) {
            // Remove the mark-as-read button and update styling
            $item.find('.alert').removeClass('alert-info').addClass('alert-secondary')
                 .css('border-left', '');
            $item.find('.mark-read-btn').remove();
            
            // Fetch updated count
            fetchNotifications();
          }
        }).fail(function() {
          console.error('Failed to mark notification as read');
        });
      });

      // Initial fetch on page load
      fetchNotifications();

      // Fetch notifications every 60 seconds (optional real-time updates)
      setInterval(fetchNotifications, 60000);
    });
  </script>
  <?php endif; ?>
</body>
</html>
