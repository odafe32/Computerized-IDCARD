@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Notifications</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Notifications</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Notifications</p>
                            <h4 class="mb-2">{{ $stats['total'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="mdi mdi-bell font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Unread</p>
                            <h4 class="mb-2 text-warning">{{ $stats['unread'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="mdi mdi-bell-alert font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Today</p>
                            <h4 class="mb-2 text-info">{{ $stats['today'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-info rounded-3">
                                <i class="mdi mdi-calendar-today font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">This Week</p>
                            <h4 class="mb-2 text-success">{{ $stats['this_week'] }}</h4>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="mdi mdi-calendar-week font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters and Actions -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <form method="GET" action="{{ route('admin.notifications.index') }}" class="d-flex gap-2">
                                <!-- Search -->
                                <div class="position-relative">
                                    <input type="text"
                                           class="form-control"
                                           name="search"
                                           value="{{ $filters['search'] ?? '' }}"
                                           placeholder="Search notifications..."
                                           style="padding-left: 35px;">
                                    <i class="mdi mdi-magnify position-absolute" style="left: 10px; top: 50%; transform: translateY(-50%); color: #adb5bd;"></i>
                                </div>

                                <!-- Type Filter -->
                                <select name="type" class="form-select" style="width: auto;">
                                    <option value="">All Types</option>
                                    <option value="info" {{ ($filters['type'] ?? '') === 'info' ? 'selected' : '' }}>Info</option>
                                    <option value="success" {{ ($filters['type'] ?? '') === 'success' ? 'selected' : '' }}>Success</option>
                                    <option value="warning" {{ ($filters['type'] ?? '') === 'warning' ? 'selected' : '' }}>Warning</option>
                                    <option value="danger" {{ ($filters['type'] ?? '') === 'danger' ? 'selected' : '' }}>Danger</option>
                                </select>

                                <!-- Status Filter -->
                                <select name="status" class="form-select" style="width: auto;">
                                    <option value="">All Status</option>
                                    <option value="unread" {{ ($filters['status'] ?? '') === 'unread' ? 'selected' : '' }}>Unread</option>
                                    <option value="read" {{ ($filters['status'] ?? '') === 'read' ? 'selected' : '' }}>Read</option>
                                </select>

                                <button type="submit" class="btn btn-primary">
                                    <i class="mdi mdi-filter-variant"></i> Filter
                                </button>

                                @if(array_filter($filters))
                              <a href="{{ route('admin.notifications.index') }}" class="btn btn-outline-secondary">
                                    <i class="mdi mdi-refresh"></i> Clear
                                </a>
                                @endif
                            </form>
                        </div>

                        <div class="col-md-6 text-end">
                            <div class="btn-group" role="group">
                                <button type="button" class="btn btn-success" id="markAllReadBtn">
                                    <i class="mdi mdi-check-all"></i> Mark All Read
                                </button>
                                <button type="button" class="btn btn-warning" id="clearReadBtn">
                                    <i class="mdi mdi-delete-sweep"></i> Clear Read
                                </button>
                                <button type="button" class="btn btn-danger" id="bulkDeleteBtn" disabled>
                                    <i class="mdi mdi-delete"></i> Delete Selected
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="mdi mdi-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="mdi mdi-alert-circle me-2"></i>{{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Notifications List -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex align-items-center justify-content-between">
                        <h4 class="card-title mb-0">
                            <i class="mdi mdi-bell-ring me-2"></i>Your Notifications
                        </h4>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="selectAll">
                            <label class="form-check-label" for="selectAll">
                                Select All
                            </label>
                        </div>
                    </div>
                </div>
                <div class="card-body p-0">
                    @if($notifications->count() > 0)
                        <div class="list-group list-group-flush">
                            @foreach($notifications as $notification)
                                <div class="list-group-item notification-item {{ $notification->is_read ? 'notification-read' : 'notification-unread' }}"
                                     data-notification-id="{{ $notification->id }}">
                                    <div class="d-flex align-items-start">
                                        <!-- Checkbox -->
                                        <div class="form-check me-3 mt-1">
                                            <input class="form-check-input notification-checkbox"
                                                   type="checkbox"
                                                   value="{{ $notification->id }}"
                                                   id="notification_{{ $notification->id }}">
                                        </div>

                                        <!-- Icon -->
                                        <div class="avatar-sm me-3 flex-shrink-0">
                                            <span class="avatar-title {{ $notification->badge_class }} rounded-circle">
                                                <i class="{{ $notification->icon_class }} font-size-16"></i>
                                            </span>
                                        </div>

                                        <!-- Content -->
                                        <div class="flex-grow-1 min-width-0">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h6 class="mb-1 font-weight-bold {{ $notification->is_read ? 'text-muted' : 'text-dark' }}">
                                                    {{ $notification->title }}
                                                </h6>
                                                <div class="d-flex align-items-center gap-2">
                                                    @if(!$notification->is_read)
                                                        <span class="badge bg-primary">New</span>
                                                    @endif
                                                    <small class="text-muted">{{ $notification->time_ago }}</small>
                                                </div>
                                            </div>

                                            <p class="mb-2 {{ $notification->is_read ? 'text-muted' : 'text-secondary' }}">
                                                {{ $notification->message }}
                                            </p>

                                            <!-- Action Buttons -->
                                            <div class="d-flex align-items-center gap-2">
                                                @if(isset($notification->data['action_url']))
                                                    <a href="{{ $notification->data['action_url'] }}"
                                                       class="btn btn-sm btn-outline-primary">
                                                        <i class="mdi mdi-eye"></i> View
                                                    </a>
                                                @endif

                                                @if(!$notification->is_read)
                                                    <button type="button"
                                                            class="btn btn-sm btn-outline-success mark-read-btn"
                                                            data-notification-id="{{ $notification->id }}">
                                                        <i class="mdi mdi-check"></i> Mark Read
                                                    </button>
                                                @endif

                                                <button type="button"
                                                        class="btn btn-sm btn-outline-danger delete-notification-btn"
                                                        data-notification-id="{{ $notification->id }}">
                                                    <i class="mdi mdi-delete"></i> Delete
                                                </button>
                                            </div>

                                            <!-- Additional Info -->
                                            @if(isset($notification->data['student_name']))
                                                <div class="mt-2 p-2 bg-light rounded">
                                                    <small class="text-muted">
                                                        <strong>Student:</strong> {{ $notification->data['student_name'] }}
                                                        @if(isset($notification->data['student_matric']))
                                                            ({{ $notification->data['student_matric'] }})
                                                        @endif
                                                        @if(isset($notification->data['request_number']))
                                                            <br><strong>Request:</strong> {{ $notification->data['request_number'] }}
                                                        @endif
                                                    </small>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Pagination -->

<!-- Replace the existing pagination section with: -->
<div class="card-footer p-0">
    @include('components.notification-pagination', ['notifications' => $notifications])
</div>
                    @else
                        <div class="text-center py-5">
                            <div class="avatar-lg mx-auto mb-4">
                                <div class="avatar-title bg-light text-muted rounded-circle">
                                    <i class="mdi mdi-bell-off font-size-24"></i>
                                </div>
                            </div>
                            <h5 class="text-muted">No notifications found</h5>
                            <p class="text-muted mb-0">
                                @if(array_filter($filters))
                                    Try adjusting your filters or
                                    <a href="{{ route('admin.notifications.index') }}">clear all filters</a>
                                @else
                                    You don't have any notifications yet.
                                @endif
                            </p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- JavaScript for Notifications Management -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const notificationCheckboxes = document.querySelectorAll('.notification-checkbox');
    const bulkDeleteBtn = document.getElementById('bulkDeleteBtn');
    const markAllReadBtn = document.getElementById('markAllReadBtn');
    const clearReadBtn = document.getElementById('clearReadBtn');

    // Select All functionality
    selectAllCheckbox.addEventListener('change', function() {
        notificationCheckboxes.forEach(checkbox => {
            checkbox.checked = this.checked;
        });
        updateBulkDeleteButton();
    });

    // Individual checkbox change
    notificationCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateSelectAllCheckbox();
            updateBulkDeleteButton();
        });
    });

    function updateSelectAllCheckbox() {
        const checkedCount = document.querySelectorAll('.notification-checkbox:checked').length;
        const totalCount = notificationCheckboxes.length;

        selectAllCheckbox.checked = checkedCount === totalCount;
        selectAllCheckbox.indeterminate = checkedCount > 0 && checkedCount < totalCount;
    }

    function updateBulkDeleteButton() {
        const checkedCount = document.querySelectorAll('.notification-checkbox:checked').length;
        bulkDeleteBtn.disabled = checkedCount === 0;
    }

    // Mark individual notification as read
    document.querySelectorAll('.mark-read-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            markNotificationAsRead(notificationId, this);
        });
    });

    // Delete individual notification
    document.querySelectorAll('.delete-notification-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const notificationId = this.dataset.notificationId;
            if (confirm('Are you sure you want to delete this notification?')) {
                deleteNotification(notificationId);
            }
        });
    });

    // Mark all as read
    markAllReadBtn.addEventListener('click', function() {
        if (confirm('Mark all notifications as read?')) {
            markAllAsRead();
        }
    });

    // Clear read notifications
    clearReadBtn.addEventListener('click', function() {
        if (confirm('Delete all read notifications? This action cannot be undone.')) {
            clearReadNotifications();
        }
    });

    // Bulk delete
    bulkDeleteBtn.addEventListener('click', function() {
        const selectedIds = Array.from(document.querySelectorAll('.notification-checkbox:checked'))
                                .map(cb => cb.value);

        if (selectedIds.length === 0) return;

        if (confirm(`Delete ${selectedIds.length} selected notifications? This action cannot be undone.`)) {
            bulkDeleteNotifications(selectedIds);
        }
    });

    // AJAX Functions
    function markNotificationAsRead(notificationId, button) {
        fetch(`/admin/notifications/${notificationId}/read`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update UI
                const notificationItem = button.closest('.notification-item');
                notificationItem.classList.remove('notification-unread');
                notificationItem.classList.add('notification-read');

                // Remove the "New" badge and mark read button
                const newBadge = notificationItem.querySelector('.badge.bg-primary');
                if (newBadge) newBadge.remove();
                button.remove();

                // Update counters
                updateNotificationCounters();

                showToast('Notification marked as read', 'success');
            } else {
                showToast('Failed to mark notification as read', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function deleteNotification(notificationId) {
        fetch(`/admin/notifications/${notificationId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove notification from DOM
                const notificationItem = document.querySelector(`[data-notification-id="${notificationId}"]`);
                notificationItem.remove();

                updateNotificationCounters();
                showToast('Notification deleted successfully', 'success');

                // Check if no notifications left
                if (document.querySelectorAll('.notification-item').length === 0) {
                    location.reload();
                }
            } else {
                showToast('Failed to delete notification', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function markAllAsRead() {
        fetch('/admin/notifications/mark-all-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('Failed to mark all notifications as read', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function clearReadNotifications() {
        fetch('/admin/notifications/clear-read', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('Failed to clear read notifications', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

    function bulkDeleteNotifications(notificationIds) {
        fetch('/admin/notifications/bulk-delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                notification_ids: notificationIds
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                showToast('Failed to delete notifications', 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showToast('An error occurred', 'error');
        });
    }

function updateNotificationCounters() {
        // Update header notification count
        const headerCount = document.getElementById('notification-count');
        const unreadItems = document.querySelectorAll('.notification-unread').length;

        if (headerCount) {
            if (unreadItems > 0) {
                headerCount.textContent = unreadItems;
                headerCount.style.display = 'inline-block';
            } else {
                headerCount.style.display = 'none';
            }
        }

        // Update page statistics
        const statsCards = document.querySelectorAll('.stats-card h4');
        if (statsCards.length >= 2) {
            // Update unread count in stats
            statsCards[1].textContent = unreadItems;
        }
    }

    function showToast(message, type = 'info') {
        // Create toast element
        const toast = document.createElement('div');
        toast.className = `alert alert-${type === 'error' ? 'danger' : type} alert-dismissible fade show position-fixed`;
        toast.style.cssText = 'top: 20px; right: 20px; z-index: 9999; min-width: 300px;';
        toast.innerHTML = `
            <i class="mdi mdi-${type === 'success' ? 'check-circle' : type === 'error' ? 'alert-circle' : 'information'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;

        document.body.appendChild(toast);

        // Auto remove after 5 seconds
        setTimeout(() => {
            if (toast.parentNode) {
                toast.remove();
            }
        }, 5000);
    }

    // Auto-refresh notifications every 30 seconds
    setInterval(() => {
        updateHeaderNotifications();
    }, 30000);

    function updateHeaderNotifications() {
        fetch('/admin/api/notifications', {
            method: 'GET',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const headerCount = document.getElementById('notification-count');
                if (headerCount) {
                    if (data.unread_count > 0) {
                        headerCount.textContent = data.unread_count;
                        headerCount.style.display = 'inline-block';
                    } else {
                        headerCount.style.display = 'none';
                    }
                }
            }
        })
        .catch(error => {
            console.error('Error updating notifications:', error);
        });
    }
});
</script>

<style>
.notification-item {
    transition: all 0.3s ease;
    border-left: 4px solid transparent;
}

.notification-item:hover {
    background-color: #f8f9fa;
    transform: translateX(2px);
}

.notification-unread {
    background-color: #f0f8ff;
    border-left-color: #007bff;
}

.notification-unread .list-group-item {
    font-weight: 500;
}

.notification-read {
    opacity: 0.8;
}

.notification-read:hover {
    opacity: 1;
}

.avatar-title {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    font-size: 18px;
}

.stats-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.min-width-0 {
    min-width: 0;
}

.form-check-input:checked {
    background-color: #007bff;
    border-color: #007bff;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-left: 0.25rem;
}

.btn-group .btn:first-child {
    margin-left: 0;
}

@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .btn-group .btn {
        margin-left: 0;
        width: 100%;
    }

    .d-flex.gap-2 {
        flex-direction: column;
    }

    .d-flex.gap-2 > * {
        margin-bottom: 0.5rem;
    }
}

/* Loading states */
.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
}

.loading {
    position: relative;
    pointer-events: none;
}

.loading::after {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 16px;
    height: 16px;
    margin: -8px 0 0 -8px;
    border: 2px solid transparent;
    border-top: 2px solid #fff;
    border-radius: 50%;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Notification type indicators */
.bg-info { background-color: #17a2b8 !important; }
.bg-success { background-color: #28a745 !important; }
.bg-warning { background-color: #ffc107 !important; }
.bg-danger { background-color: #dc3545 !important; }
.bg-primary { background-color: #007bff !important; }

/* Custom scrollbar for long notification lists */
.list-group {
    max-height: 70vh;
    overflow-y: auto;
}

.list-group::-webkit-scrollbar {
    width: 6px;
}

.list-group::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 3px;
}

.list-group::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 3px;
}

.list-group::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>
@endsection
