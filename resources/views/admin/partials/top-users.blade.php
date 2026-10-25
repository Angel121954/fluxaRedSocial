<div class="card">
    <div class="card-header">
        <span class="card-title">Usuarios más activos</span>
    </div>
    <div class="users-table-head">
        <span class="th">Usuario</span>
        <span class="th">Proyectos</span>
    </div>
    <div>
        @forelse ($usuariosMasActivos as $usuario)
        <div class="user-row">
            <img class="u-avatar" src="{{ $usuario->avatar_url }}" alt="{{ $usuario->name }}">
            <div class="u-info">
                <div class="u-name">{{ $usuario->name }}</div>
                <div class="u-handle">&#64;{{ $usuario->username }}</div>
            </div>
            <div class="u-count">{{ $usuario->projects_count }}</div>
        </div>
        @empty
        <div class="user-row">
            <div class="u-info">
                <div class="u-sub">Sin usuarios aún</div>
            </div>
        </div>
        @endforelse
    </div>
</div>
