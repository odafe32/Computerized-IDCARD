@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <!-- Page Title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0 font-size-18">Manage Students</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Students</li>
                    </ol>
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

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6">
            <div class="card stats-card">
                <div class="card-body">
                    <div class="d-flex">
                        <div class="flex-grow-1">
                            <p class="text-truncate font-size-14 mb-2">Total Students</p>
                            <h4 class="mb-2">{{ $stats['total'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-primary fw-bold font-size-12 me-2">
                                    <i class="mdi mdi-account-multiple me-1 align-middle"></i>All Students
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-primary rounded-3">
                                <i class="mdi mdi-account-group font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Active Students</p>
                            <h4 class="mb-2">{{ $stats['active'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-success fw-bold font-size-12 me-2">
                                    <i class="mdi mdi-check-circle me-1 align-middle"></i>Active Accounts
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-success rounded-3">
                                <i class="mdi mdi-account-check font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Inactive Students</p>
                            <h4 class="mb-2">{{ $stats['inactive'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-warning fw-bold font-size-12 me-2">
                                    <i class="mdi mdi-account-off me-1 align-middle"></i>Inactive Accounts
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-warning rounded-3">
                                <i class="mdi mdi-account-off font-size-24"></i>
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
                            <p class="text-truncate font-size-14 mb-2">Suspended Students</p>
                            <h4 class="mb-2">{{ $stats['suspended'] }}</h4>
                            <p class="text-muted mb-0">
                                <span class="text-danger fw-bold font-size-12 me-2">
                                    <i class="mdi mdi-account-cancel me-1 align-middle"></i>Suspended Accounts
                                </span>
                            </p>
                        </div>
                        <div class="avatar-sm">
                            <span class="avatar-title bg-light text-danger rounded-3">
                                <i class="mdi mdi-account-cancel font-size-24"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h4 class="card-title mb-0">Students List</h4>
                        <a href="{{ route('admin.users.create') }}" class="btn btn-primary waves-effect waves-light">
                            <i class="mdi mdi-plus me-1"></i>Add New Student
                        </a>
                    </div>

                    <!-- Filters -->
                    <form method="GET" class="mb-4">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="search" class="form-label">Search</label>
                                    <input type="text"
                                           class="form-control"
                                           id="search"
                                           name="search"
                                           value="{{ $filters['search'] ?? '' }}"
                                           placeholder="Search by name, email, or matric number...">
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="status" class="form-label">Status</label>
                                    <select class="form-select" id="status" name="status">
                                        <option value="">All Status</option>
                                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Active</option>
                                        <option value="inactive" {{ ($filters['status'] ?? '') === 'inactive' ? 'selected' : '' }}>Inactive</option>
                                        <option value="suspended" {{ ($filters['status'] ?? '') === 'suspended' ? 'selected' : '' }}>Suspended</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="department" class="form-label">Department</label>
                                    <select class="form-select" id="department" name="department">
                                        <option value="">All Departments</option>
                                        @foreach($departments as $dept)
                                            <option value="{{ $dept }}" {{ ($filters['department'] ?? '') === $dept ? 'selected' : '' }}>
                                                {{ $dept }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <div class="form-group">
                                    <label for="sort" class="form-label">Sort By</label>
                                    <select class="form-select" id="sort" name="sort">
                                        <option value="created_at" {{ ($filters['sort'] ?? 'created_at') === 'created_at' ? 'selected' : '' }}>Date Created</option>
                                        <option value="name" {{ ($filters['sort'] ?? '') === 'name' ? 'selected' : '' }}>Name</option>
                                        <option value="matric_no" {{ ($filters['sort'] ?? '') === 'matric_no' ? 'selected' : '' }}>Matric Number</option>
                                        <option value="last_login_at" {{ ($filters['sort'] ?? '') === 'last_login_at' ? 'selected' : '' }}>Last Login</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-1">
                                <div class="form-group">
                                    <label class="form-label">&nbsp;</label>
                                    <div class="d-grid">
                                        <button type="submit" class="btn btn-outline-primary">
                                            <i class="mdi mdi-magnify"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <input type="hidden" name="order" value="{{ $filters['order'] ?? 'desc' }}">
                    </form>

                    <!-- Students Table -->
                    <div class="table-responsive">
                        <table class="table table-nowrap table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Student</th>
                                    <th>Matric Number</th>
                                    <th>Department</th>
                                    <th>Status</th>
                                    <th>Last Login</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($students as $student)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="flex-shrink-0 me-3">
                                                    <img src="{{ $student->photo_url }}"
                                                         alt="{{ $student->name }}"
                                                         class="avatar-sm rounded-circle">
                                                </div>
                                                <div class="flex-grow-1">
                                                    <h5 class="font-size-14 mb-1">
                                                        <a href="{{ route('admin.users.show', $student) }}" class="text-dark">
                                                            {{ $student->name }}
                                                        </a>
                                                    </h5>
                                                    <p class="text-muted mb-0">{{ $student->email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $student->matric_no }}</span>
                                        </td>
                                        <td>{{ $student->department }}</td>
                                        <td>
                                            @php
                                                $statusClasses = [
                                                    'active' => 'bg-success',
                                                    'inactive' => 'bg-warning',
                                                    'suspended' => 'bg-danger'
                                                ];
                                            @endphp
                                            <span class="badge {{ $statusClasses[$student->status] ?? 'bg-secondary' }}">
                                                {{ ucfirst($student->status) }}
                                            </span>
                                        </td>
                                        <td>
                                            @if($student->last_login_at)
                                                <span class="text-muted">{{ $student->last_login_at->diffForHumans() }}</span>
                                            @else
                                                <span class="text-muted">Never</span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="dropdown">
                                                <button class="btn btn-link font-size-16 shadow-none py-0 text-muted dropdown-toggle"
                                                        type="button" data-bs-toggle="dropdown" data-bs-display="static" aria-expanded="false">
                                                    <i class="mdi mdi-dots-horizontal"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.users.show', $student) }}">
                                                            <i class="mdi mdi-eye font-size-16 text-success me-1"></i> View
                                                        </a>
                                                    </li>
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('admin.users.edit', $student) }}">
                                                            <i class="mdi mdi-pencil font-size-16 text-success me-1"></i> Edit
                                                        </a>
                                                    </li>
                                                    <li><hr class="dropdown-divider"></li>

                                                    @if($student->status !== 'active')
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.users.activate', $student) }}" class="d-inline">
                                                                @csrf @method('PUT')
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="mdi mdi-check-circle font-size-16 text-success me-1"></i> Activate
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    @if($student->status !== 'inactive')
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.users.deactivate', $student) }}" class="d-inline">
                                                                @csrf @method('PUT')
                                                                <button type="submit" class="dropdown-item">
                                                                    <i class="mdi mdi-account-off font-size-16 text-warning me-1"></i> Deactivate
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    @if($student->status !== 'suspended')
                                                        <li>
                                                            <form method="POST" action="{{ route('admin.users.suspend', $student) }}" class="d-inline">
                                                                @csrf @method('PUT')
                                                                <button type="submit" class="dropdown-item"
                                                                        data-confirm="Are you sure you want to suspend this student?">
                                                                    <i class="mdi mdi-account-cancel font-size-16 text-danger me-1"></i> Suspend
                                                                </button>
                                                            </form>
                                                        </li>
                                                    @endif

                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.users.reset-password', $student) }}" class="d-inline">
                                                            @csrf
                                                            <button type="submit" class="dropdown-item"
                                                                    data-confirm="Are you sure you want to reset this student's password?">
                                                                <i class="mdi mdi-lock-reset font-size-16 text-info me-1"></i> Reset Password
                                                            </button>
                                                        </form>
                                                    </li>
                                                    <li>
                                                        <form method="POST" action="{{ route('admin.users.destroy', $student) }}" class="d-inline">
                                                            @csrf @method('DELETE')
                                                            <button type="submit" class="dropdown-item text-danger"
                                                                    data-confirm="Are you sure you want to delete this student? This action cannot be undone.">
                                                                <i class="mdi mdi-trash-can font-size-16 text-danger me-1"></i> Delete
                                                            </button>
                                                        </form>
                                                    </li>
                                                </ul>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <div class="d-flex flex-column align-items-center">
                                                <i class="mdi mdi-account-search font-size-48 text-muted mb-2"></i>
                                                <h5>No Students Found</h5>
                                                <p class="text-muted">No students match your current filters.</p>
                                                @if(array_filter($filters))
                                                    <a href="{{ route('admin.users.index') }}" class="btn btn-outline-primary btn-sm">
                                                        Clear Filters
                                                    </a>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($students->hasPages())
                        <div class="row mt-4">
                            <div class="col-sm-6">
                                <div class="dataTables_info">
                                    Showing {{ $students->firstItem() }} to {{ $students->lastItem() }} of {{ $students->total() }} entries
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="dataTables_paginate paging_simple_numbers float-end">
                                    {{ $students->links() }}
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    object-fit: cover;
}

/* Ensure dropdowns are not clipped inside responsive tables */
.table-responsive {
    overflow: visible;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.table th {
    border-top: none;
    font-weight: 600;
    text-transform: uppercase;
    font-size: 12px;
    letter-spacing: 0.5px;
}

.dropdown-toggle::after {
    display: none;
}
</style>
@endsection
