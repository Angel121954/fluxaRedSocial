@extends('admin.layouts.admin')

@section('title', 'Panel Administrativo - Fluxa')

@section('admin-content')
<div class="page-header">
    <div>
        <h1 class="page-title">Panel Administrativo</h1>
        <p class="page-sub">Bienvenido de nuevo, {{ auth()->user()->name }}. Aquí tienes un resumen general de Fluxa.</p>
    </div>
</div>

@include('admin.partials.stats-cards')

<div class="content-grid">
    <div class="col-left">
        @include('admin.partials.growth-chart')
        @include('admin.partials.donut-charts')
    </div>
    <div class="col-right">
        @include('admin.partials.activity')
        @include('admin.partials.top-users')
    </div>
</div>
@endsection