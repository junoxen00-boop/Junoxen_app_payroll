@extends('layouts.admin')

@section('title', 'Notifications')

@section('content')

<div class="container-fluid">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3>Notifications</h3>

        <form action="{{ route('notifications.readAll') }}" method="POST">
            @csrf
            <button class="btn btn-primary">
                Mark All as Read
            </button>
        </form>
    </div>

    @if(session('success'))
        <div class="alert alert-success">
            {{ session('success') }}
        </div>
    @endif

    @forelse($notifications as $notification)

        <div class="card mb-3 {{ $notification->is_read ? '' : 'border-primary' }}">
            <div class="card-body">

                <div class="d-flex justify-content-between">

                    <div>
                        <h5>{{ $notification->title }}</h5>

                        <p class="mb-1">
                            {{ $notification->message }}
                        </p>

                        <small class="text-muted">
                            {{ $notification->created_at->diffForHumans() }}
                        </small>
                    </div>

                    @if(!$notification->is_read)

                        <form method="POST"
                              action="{{ route('notifications.read', $notification) }}">

                            @csrf
                            @method('PATCH')

                            <button class="btn btn-success btn-sm">
                                Mark Read
                            </button>

                        </form>

                    @endif

                </div>

            </div>
        </div>

    @empty

        <div class="alert alert-info">
            No notifications found.
        </div>

    @endforelse

    <div class="mt-3">
        {{ $notifications->links() }}
    </div>

</div>

@endsection