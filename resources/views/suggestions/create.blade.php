@extends('layouts.app')

@section('title', 'Crear Sugerencia Fluxa')

@push('styles')
@vite('resources/css/admin/suggestions.css')
@endpush

@section('content')

<x-topbar :profile="$profile" />

<div class="adm-wrap">

    <header class="adm-header">
        <div>
            <h1 class="adm-title">Enviar Sugerencia</h1>
            <p class="adm-subtitle">Comparte tus ideas para mejorar Fluxa.</p>
        </div>
    </header>

    <div class="adm-card">
        <form method="POST" action="{{ route('suggestions.store') }}" class="adm-form" enctype="multipart/form-data">
            @csrf

            <div class="adm-form-group">
                <label for="description" class="adm-label">Descripción</label>
                <textarea
                    id="description"
                    name="description"
                    class="adm-textarea"
                    rows="5"
                    placeholder="Describe tu sugerencia..."
                    required
                    minlength="10"
                    maxlength="1000">{{ old('description') }}</textarea>
                @error('description')
                <span class="adm-error">{{ $message }}</span>
                @enderror
                <span class="adm-hint">Mínimo 10 caracteres, máximo 1000.</span>
            </div>

            <div class="adm-form-group">
                <label for="image" class="adm-label">Imagen (opcional)</label>
                <div class="adm-upload-wrap">
                    <input
                        type="file"
                        id="image"
                        name="image"
                        class="adm-upload-input"
                        accept="image/*"
                        onchange="previewSuggestionImage(this)">
                    <div class="adm-upload-zone" id="uploadZone">
                        <svg class="adm-upload-icon" width="32" height="32" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        <p class="adm-upload-text">Arrastra una imagen o <span class="adm-upload-link">busca</span></p>
                        <p class="adm-upload-sub">PNG, JPG o WEBP (máx. 5MB)</p>
                    </div>
                    <div class="adm-upload-preview" id="uploadPreview">
                        <img id="previewImg" src="" alt="Vista previa">
                    </div>
                </div>
                @error('image')
                <span class="adm-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="adm-form-actions">
                <a href="{{ route('explore.index') }}" class="adm-btn-secondary">Cancelar</a>
                <button type="submit" class="adm-btn-primary">Enviar Sugerencia</button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
@vite('resources/js/admin/index.js')
@endpush