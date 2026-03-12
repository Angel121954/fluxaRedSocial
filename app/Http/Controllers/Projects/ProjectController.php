<?php

namespace App\Http\Controllers\Projects;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Technology;
use Illuminate\Support\Facades\Auth;
use Cloudinary\Cloudinary;
use Illuminate\Support\Facades\Log;

class ProjectController extends Controller
{
    protected Cloudinary $cloudinary;

    public function __construct()
    {
        $this->cloudinary = new Cloudinary([
            'cloud' => [
                'cloud_name' => env('CLOUDINARY_CLOUD_NAME'),
                'api_key'    => env('CLOUDINARY_KEY'),
                'api_secret' => env('CLOUDINARY_SECRET'),
            ],
        ]);
    }

    public function index()
    {
        /** @var \App\Models\User $user */
        $user = Auth::user();

        $projects = Project::with('media', 'technologies')
            ->where('user_id', $user->id)
            ->latest()
            ->paginate(10);

        return view('projects.index', compact('projects'));
    }

    public function create()
    {
        return view('projects.create');
    }

    public function store(Request $request)
    {
        Log::info('Iniciando creacion de proyecto', [
            'user_id'     => Auth::id(),
            'title'       => $request->title,
            'privacy'     => $request->privacy ?? 'public',
            'techs_count' => is_array($request->techs) ? count($request->techs) : 0,
            'media_count' => $request->hasFile('media') ? count($request->file('media')) : 0,
            'ip'          => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        $validated = $request->validate([
            'title'      => 'required|string|min:3|max:100',
            'content'    => 'required|string|min:10|max:500',
            'privacy'    => 'nullable|in:public,followers,private',
            'techs'      => 'nullable|array',
            'techs.*'    => 'string|max:50',
            'media'      => 'nullable|array|max:6',
            'media.*'    => 'file|mimes:jpg,jpeg,png,webp,gif,mp4,webm|max:10240',
        ], [
            'title.required' => 'El titulo es requerido',
            'title.string'   => 'El titulo debe ser cadena de texto',
            'title.min'      => 'El titulo debe de tener un minimo de 3 caracteres',
            'title.max'      => 'El titulo debe de tener un maximo de 100 caracteres',

            'content.required' => 'La descripcion del proyecto es requerida',
            'content.string'   => 'La descripcion debe de ser cadena de texto',
            'content.min'      => 'La descripcion debe de tener un minimo de 10 caracteres',
            'content.max'      => 'La descripcion debe de tener un maximo de 500 caracteres',

            'privacy.in' => 'El tipo de privacidad seleccionado no es valido',

            'techs.array'    => 'Las tecnologias deben enviarse como una lista',
            'techs.*.string' => 'Cada tecnologia debe ser texto',
            'techs.*.max'    => 'Cada tecnologia puede tener maximo 50 caracteres',

            'media.array' => 'Los archivos deben enviarse como una lista',
            'media.max'   => 'Solo puedes subir maximo 6 archivos',

            'media.*.file'  => 'Cada archivo debe ser un archivo valido',
            'media.*.mimes' => 'Los archivos deben ser jpg, jpeg, png, webp, gif, mp4 o webm',
            'media.*.max'   => 'Cada archivo puede pesar maximo 10MB',
        ]);

        Log::info('Validacion superada, creando registro en base de datos', [
            'user_id' => Auth::id(),
            'title'   => $validated['title'],
            'privacy' => $validated['privacy'] ?? 'public',
        ]);

        $project = Project::create([
            'user_id' => Auth::id(),
            'title'   => $validated['title'],
            'content' => $validated['content'],
            'privacy' => $validated['privacy'] ?? 'public',
        ]);

        Log::info('Proyecto guardado en base de datos', [
            'project_id' => $project->id,
            'user_id'    => $project->user_id,
            'title'      => $project->title,
        ]);

        if (!empty($validated['techs'])) {
            $techIds = Technology::whereIn('name', $validated['techs'])->pluck('id');
            $project->technologies()->sync($techIds);

            Log::info('Tecnologias asociadas al proyecto', [
                'project_id' => $project->id,
                'techs'      => $validated['techs'],
                'ids_found'  => $techIds->toArray(),
            ]);
        }

        if ($request->hasFile('media')) {
            foreach ($request->file('media') as $position => $file) {
                $mime         = $file->getMimeType();
                $mediaType    = match (true) {
                    str_starts_with($mime, 'video/') => 'video',
                    $mime === 'image/gif'             => 'gif',
                    default                          => 'image',
                };
                $resourceType = $mediaType === 'video' ? 'video' : 'image';

                Log::info('Subiendo archivo a Cloudinary', [
                    'project_id'    => $project->id,
                    'position'      => $position,
                    'media_type'    => $mediaType,
                    'resource_type' => $resourceType,
                    'original_name' => $file->getClientOriginalName(),
                    'size_kb'       => round($file->getSize() / 1024, 2),
                    'mime_type'     => $mime,
                ]);

                try {
                    $uploaded = $this->cloudinary->uploadApi()->upload(
                        $file->getRealPath(),
                        [
                            'folder'        => 'projects',
                            'resource_type' => $resourceType,
                        ]
                    );

                    Log::info('Archivo subido exitosamente a Cloudinary', [
                        'project_id' => $project->id,
                        'position'   => $position,
                        'public_id'  => $uploaded['public_id'],
                        'secure_url' => $uploaded['secure_url'],
                        'format'     => $uploaded['format'] ?? null,
                        'bytes'      => $uploaded['bytes'] ?? null,
                        'width'      => $uploaded['width'] ?? null,
                        'height'     => $uploaded['height'] ?? null,
                    ]);
                } catch (\Exception $e) {
                    Log::error('Error al subir archivo a Cloudinary', [
                        'project_id'    => $project->id,
                        'position'      => $position,
                        'original_name' => $file->getClientOriginalName(),
                        'error'         => $e->getMessage(),
                        'trace'         => $e->getTraceAsString(),
                    ]);

                    throw $e;
                }

                $project->media()->create([
                    'media_url' => $uploaded['secure_url'],
                    'public_id' => $uploaded['public_id'],
                    'type'      => $mediaType,
                    'position'  => $position,
                ]);
            }
        }

        Log::info('Proyecto publicado correctamente', [
            'project_id'  => $project->id,
            'user_id'     => $project->user_id,
            'title'       => $project->title,
            'media_total' => $project->media->count(),
            'techs_total' => $project->technologies->count(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Proyecto publicado correctamente.',
            'project' => $project->load('media', 'technologies'),
        ]);
    }

    public function show(Project $project)
    {
        $project->load('media', 'technologies');
        return view('explore.explore', compact('project'));
    }

    public function edit(Project $project)
    {
        $this->authorize('update', $project);
        $project->load('media', 'technologies');
        return view('projects.edit', compact('project'));
    }

    public function update(Request $request, Project $project)
    {
        $this->authorize('update', $project);

        $validated = $request->validate([
            'title'   => 'required|string|min:3|max:100',
            'content' => 'required|string|min:10|max:500',
            'privacy' => 'nullable|in:public,followers,private',
            'techs'   => 'nullable|array',
            'techs.*' => 'string|max:50',
        ]);

        $project->update([
            'title'   => $validated['title'],
            'content' => $validated['content'],
            'privacy' => $validated['privacy'] ?? $project->privacy,
        ]);

        $techIds = !empty($validated['techs'])
            ? Technology::whereIn('name', $validated['techs'])->pluck('id')
            : collect();

        $project->technologies()->sync($techIds);

        return redirect()->route('projects.show', $project)
            ->with('success', 'Proyecto actualizado.');
    }

    public function destroy(Project $project)
    {
        $this->authorize('delete', $project);

        foreach ($project->media as $media) {
            if (!empty($media->public_id)) {
                $resourceType = $media->type === 'video' ? 'video' : 'image';
                $this->cloudinary->uploadApi()->destroy(
                    $media->public_id,
                    ['resource_type' => $resourceType]
                );
            }
        }

        $project->delete();

        return redirect()->route('projects.index')
            ->with('success', 'Proyecto eliminado.');
    }
}
