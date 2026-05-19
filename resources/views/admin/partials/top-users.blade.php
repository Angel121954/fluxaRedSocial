<div class="card">
    <div class="card-header">
        <span class="card-title">Usuarios más activos</span>
    </div>
    <div class="users-table-head">
        <span class="th">Usuario</span>
        <span class="th">Proyectos</span>
    </div>
    <div>
        @forelse ($topUsers as $user)
        <div class="user-row">
            <img class="u-avatar" src="{{ $user->avatar_url }}" alt="{{ $user->name }}">
            <div class="u-info">
                <div class="u-name">{{ $user->name }}</div>
                <div class="u-handle">@ {{ $user->username }}</div>
            </div>
            <div class="u-count">{{ $user->projects_count }}</div>
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