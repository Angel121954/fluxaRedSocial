@forelse($projects as $project)
<x-project-card :project="$project" />
@empty
<div class="empty-state">
    <p>No hay proyectos disponibles</p>
</div>
@endforelse

@if($projects->hasMorePages())
<div class="flex flex-col items-center gap-3 py-8">
    <button class="border border-[#0d8e91] text-[#0d8e91] hover:bg-[#0d8e91] hover:text-white transition-all px-8 py-2.5 rounded-full text-sm font-medium">
        Cargar más
    </button>
    <p class="text-xs text-gray-400">Mostrando {{ $projects->count() }} de {{ $projects->total() }} proyectos</p>
</div>
@endif