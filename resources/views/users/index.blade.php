@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <div>
            <h2 class="fw-bold">User Management</h2>
            <p class="text-muted">
                Manage all Admin, Manager and Employee accounts.
            </p>
        </div>

    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    <div class="card shadow-sm">

        <div class="card-body p-0">

            <div class="table-responsive">

                <table class="table table-hover mb-0">

                    <thead class="table-light">

                        <tr>

                            <th>#</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Role</th>
                            <th>Password</th>
                            <th>Action</th>

                        </tr>

                    </thead>

                    <tbody>

                    @forelse($users as $index => $user)

                        <tr>

                            <td>{{ $index + 1 }}</td>

                            <td>{{ $user->name }}</td>

                            <td>{{ $user->email }}</td>

                            <td>{{ $user->role->name }}</td>

                            <td>

                                @if($user->must_change_password)

                                    <span class="badge bg-warning text-dark">
                                        Temporary
                                    </span>

                                @else

                                    <span class="badge bg-success">
                                        Active
                                    </span>

                                @endif

                            </td>

                            <td>

                                <a href="{{ route('users.edit', $user) }}"
                                   class="btn btn-sm btn-primary">

                                    Edit

                                </a>

                            </td>

                        </tr>

                    @empty

                        <tr>

                            <td colspan="6" class="text-center">

                                No users found.

                            </td>

                        </tr>

                    @endforelse

                    </tbody>

                </table>

            </div>

        </div>

    </div>

</div>

@endsection