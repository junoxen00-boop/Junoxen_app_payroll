@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    @if(session('status')=='profile-updated')
        <div class="alert alert-success alert-dismissible fade show">
            Profile updated successfully.
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif


    <div class="card shadow-sm mb-4">

        <div class="card-header">

            <h4 class="mb-0">

                <i class="bi bi-person-circle me-2"></i>

                My Profile

            </h4>

        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('profile.update') }}">

                @csrf
                @method('PATCH')

                <div class="row">

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Full Name

                        </label>

                        <input
                            type="text"
                            name="name"
                            class="form-control"
                            value="{{ old('name',$user->name) }}"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Email Address

                        </label>

                        <input
                            type="email"
                            name="email"
                            class="form-control"
                            value="{{ old('email',$user->email) }}"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Role

                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $user->role->name }}"
                            readonly>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Employee ID

                        </label>

                        <input
                            type="text"
                            class="form-control"
                           value="{{ optional($user->employee)->employee_id ?? 'N/A' }}"
                            readonly>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-check-circle"></i>

                    Save Profile

                </button>

            </form>

        </div>

    </div>



    <div class="card shadow-sm mb-4">

        <div class="card-header">

            <h4 class="mb-0">

                <i class="bi bi-key me-2"></i>

                Change Password

            </h4>

        </div>

        <div class="card-body">

            <form method="POST"
                  action="{{ route('password.update') }}">

                @csrf

                                <div class="row">

                    <div class="col-md-12 mb-3">

                        <label class="form-label">

                            Current Password

                        </label>

                        <input
                            type="password"
                            name="current_password"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            New Password

                        </label>

                        <input
                            type="password"
                            name="password"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-6 mb-3">

                        <label class="form-label">

                            Confirm Password

                        </label>

                        <input
                            type="password"
                            name="password_confirmation"
                            class="form-control"
                            required>

                    </div>

                </div>

                <button
                    type="submit"
                    class="btn btn-primary">

                    <i class="bi bi-key-fill me-1"></i>

                    Update Password

                </button>

            </form>

        </div>

    </div>


    <div class="card shadow-sm border-danger">

    <div class="card-header bg-danger text-white">

        <h4 class="mb-0">
            <i class="bi bi-trash me-2"></i>
            Delete Account
        </h4>

    </div>

    <div class="card-body">

        <p class="text-danger mb-3">
            <strong>Warning:</strong>
            Deleting your account is permanent and cannot be undone.
        </p>

        <form method="POST"
              action="{{ route('profile.destroy') }}"
              onsubmit="return confirm('Are you sure you want to permanently delete your account?');">

            @csrf
            @method('DELETE')

            <div class="row align-items-end">

                <div class="col-md-8">

                    <label class="form-label">
                        Confirm Password
                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required>

                </div>

                <div class="col-md-4">

                    <button
                        type="submit"
                        class="btn btn-danger w-100">

                        <i class="bi bi-trash"></i>

                        Delete Account

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

      

@endsection