@extends('layouts.app')

@section('content')
<h1>Admin Dashboard</h1>
<p>Welcome to the Admin Dashboard!</p>

<div class="mt-3">
    <a href="{{ route('admin.userManagement') }}" class="btn btn-primary">Manage Users</a>
</div>
@endsection
