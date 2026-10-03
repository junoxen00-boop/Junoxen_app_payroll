@extends('layouts.admin')

@section('content')

<div class="container-fluid">

    <h2 class="mb-4">Edit User</h2>

    <div class="card shadow-sm">
        <div class="card-body">

            <form method="POST" action="{{ route('users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="mb-3">
                    <label class="form-label">Full Name</label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="{{ old('name', $user->name) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="{{ old('email', $user->email) }}"
                        required>
                </div>

                <div class="mb-3">
                    <label class="form-label">

                        Reset Password

                        <small class="text-muted">
                            (Leave blank if you don't want to change it)
                        </small>

                    </label>

                    <input
                        type="password"
                        name="password"
                        class="form-control">
                </div>

                <div class="mb-3">
                    <label class="form-label">

                        Confirm Password

                    </label>

                    <input
                        type="password"
                        name="password_confirmation"
                        class="form-control">
                </div>

                <button class="btn btn-primary">
                    Update User
                </button>

                <a href="{{ route('users.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

            </form>

        </div>
    </div>

</div>

@endsection