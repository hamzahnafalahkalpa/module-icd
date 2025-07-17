<?php

namespace Hanafalah\ModuleIcd\Commands;

class InstallMakeCommand extends EnvironmentCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-icd:install';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for initial installation of icd module';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $provider = 'Hanafalah\ModuleIcd\ICDServiceProvider';

        $this->callSilent('vendor:publish', [
            '--provider' => $provider,
            '--tag'      => 'migrations'
        ]);
        $this->info('✔️  Created migrations');

        $this->comment('hanafalah/module-icd installed successfully.');
    }
}
