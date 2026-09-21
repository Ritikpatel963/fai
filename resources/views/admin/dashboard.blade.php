@extends('admin.layouts.app')

@section('title', 'Dashboard')
@section('page', 'index')

@section('content')
<div class="page-head">
    <div>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Admin</a></li>
                <li class="breadcrumb-item active">Dashboard</li>
            </ol>
        </nav>
        <h1>Dashboard</h1>
    </div>
</div>

<div class="kpi-grid mb-3">
    <article class="kpi-card">
        <span class="kpi-icon bg-primary"><i class="bi bi-file-text"></i></span>
        <p>Total Blogs</p>
        <strong>0</strong>
        <small>Published & Draft</small>
    </article>
    <article class="kpi-card">
        <span class="kpi-icon bg-success"><i class="bi bi-folder"></i></span>
        <p>Total Resources</p>
        <strong>0</strong>
        <small>Active resources</small>
    </article>
    <article class="kpi-card">
        <span class="kpi-icon bg-info"><i class="bi bi-envelope"></i></span>
        <p>Contact Leads</p>
        <strong>0</strong>
        <small>New inquiries</small>
    </article>
</div>
@endsection
