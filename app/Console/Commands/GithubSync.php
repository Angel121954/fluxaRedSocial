<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GithubSync extends Command
{
    protected $signature = 'github:sync {user? : ID del usuario específico}';

    protected $description = 'Sincroniza datos públicos de GitHub (username, public_repos) para usuarios con token';

    public function handle(): int
    {
        $query = User::whereNotNull('github_token');

        if ($userId = $this->argument('user')) {
            $query->where('id', $userId);
        }

        $users = $query->get();
        $synced = 0;

        foreach ($users as $user) {
            try {
                $response = Http::withToken($user->github_token)
                    ->accept('application/vnd.github.v3+json')
                    ->get('https://api.github.com/user');

                if ($response->successful()) {
                    $data = $response->json();

                    $user->forceFill([
                        'github_username' => $data['login'] ?? $user->github_username,
                        'github_public_repos' => $data['public_repos'] ?? 0,
                        'github_synced_at' => now(),
                    ])->save();

                    $synced++;
                    $this->info("Synced {$user->username} (@{$data['login']}): {$data['public_repos']} public repos");
                } else {
                    $this->warn("Failed for {$user->username}: HTTP {$response->status()}");
                }
            } catch (\Exception $e) {
                $this->error("Error for {$user->username}: {$e->getMessage()}");
                Log::error('github:sync error', ['user_id' => $user->id, 'error' => $e->getMessage()]);
            }
        }

        $this->info("Synced {$synced}/{$users->count()} users");

        return Command::SUCCESS;
    }
}
