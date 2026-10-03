@extends('layouts.admin')

@section('content')

@if(session('success'))
<div class="alert alert-success">
    {{ session('success') }}
</div>
@endif

<div class="card shadow-sm">

    <div class="card-header">
        <h3 class="mb-0">Application Settings</h3>
    </div>

    <div class="card-body">

        <form action="{{ route('settings.update') }}"
              method="POST"
              enctype="multipart/form-data">

            @csrf

            <h5 class="mb-3">Company Information</h5>

            <div class="row">

                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Name</label>
                    <input
                        type="text"
                        name="company_name"
                        class="form-control"
                        value="{{ old('company_name', $setting->company_name) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Email</label>
                    <input
                        type="email"
                        name="company_email"
                        class="form-control"
                        value="{{ old('company_email', $setting->company_email) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Phone</label>
                    <input
                        type="text"
                        name="company_phone"
                        class="form-control"
                        value="{{ old('company_phone', $setting->company_phone) }}">
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Company Website</label>
                    <input
                        type="text"
                        name="company_website"
                        class="form-control"
                        value="{{ old('company_website', $setting->company_website) }}">
                </div>

                <div class="col-12 mb-3">
                    <label class="form-label">Company Address</label>
                    <textarea
                        name="company_address"
                        rows="3"
                        class="form-control">{{ old('company_address', $setting->company_address) }}</textarea>
                </div>

                <div class="col-md-6 mb-4">
                    <label class="form-label">Company Logo</label>

                    <input
                        type="file"
                        name="company_logo"
                        class="form-control">

                    @if($setting->company_logo)

                        <div class="mt-3">
                            <img
                                src="{{ asset('storage/'.$setting->company_logo) }}"
                                width="120"
                                class="img-thumbnail">
                        </div>

                    @endif

                </div>

            </div>

            <hr>

            <h5 class="mb-3">System Settings</h5>

            <div class="row">

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Time Zone
                    </label>

                    <input
                        type="text"
                        name="timezone"
                        class="form-control"
                        value="{{ old('timezone', $setting->timezone) }}">

                </div>

                <div class="col-md-6 mb-3">

                    <label class="form-label">
                        Date Format
                    </label>

                    <select
                        name="date_format"
                        class="form-select">

                        <option value="d-m-Y"
                        @selected($setting->date_format=='d-m-Y')>
                            DD-MM-YYYY
                        </option>

                        <option value="m-d-Y"
                        @selected($setting->date_format=='m-d-Y')>
                            MM-DD-YYYY
                        </option>

                        <option value="Y-m-d"
                        @selected($setting->date_format=='Y-m-d')>
                            YYYY-MM-DD
                        </option>

                    </select>

                </div>

            </div>

            <hr>

            <h5 class="mb-3">Email Settings</h5>

            <div class="row">

                <div class="col-md-4 mb-3">
                    <label class="form-label">Sender Name</label>

                    <input
                        type="text"
                        name="sender_name"
                        class="form-control"
                        value="{{ old('sender_name', $setting->sender_name) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Sender Email</label>

                    <input
                        type="email"
                        name="sender_email"
                        class="form-control"
                        value="{{ old('sender_email', $setting->sender_email) }}">
                </div>

                <div class="col-md-4 mb-3">
                    <label class="form-label">Reply-To Email</label>

                    <input
                        type="email"
                        name="reply_to_email"
                        class="form-control"
                        value="{{ old('reply_to_email', $setting->reply_to_email) }}">
                </div>

                <div class="col-12 mb-4">

                    <div class="form-check">

                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="email_notifications"
                            value="1"
                            @checked($setting->email_notifications)>

                        <label class="form-check-label">
                            Enable Email Notifications
                        </label>

                    </div>

                </div>

            </div>

            <button
                class="btn btn-primary">

                Save Settings

            </button>


        </form>

    </div>

</div>

@endsection