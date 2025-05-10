<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Services\Stripe\SyncStripeService;

class SyncStripeDataCommand extends Command
{
    protected $signature = 'stripe:sync-data';

    protected $description = 'Sincroniza os dados do Stripe para todos os usuários com email.';

    public function handle()
    {
        $this->info('Iniciando sincronização de todos os usuários...');

        $users = User::whereNotNull('email')->get();

        foreach ($users as $user) {
            try {
                SyncStripeService::syncUser($user);
                $this->info("Usuário {$user->id} sincronizado com sucesso.");
            } catch (\Exception $e) {
                $this->error("Erro ao sincronizar usuário {$user->id}: {$e->getMessage()}");
            }
        }

        $this->info('Sincronização de Stripe concluída.');
    }
}
