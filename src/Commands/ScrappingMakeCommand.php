<?php

namespace Hanafalah\ModuleIcd\Commands;


class ScrappingMakeCommand extends EnvironmentCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-icd:scrapping {version} {releaseId} {--code=}';


    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command is used for add data from who';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $version = $this->argument('version');
        $releaseId = $this->argument('releaseId');
        switch ($version) {
            case 10:
            default:
                $icd_schema = app(config('app.contracts.Icd10'));
                $icd_schema->setup()->oauth();
                $icd_schema->setIcdModel($this->Icd10Model())
                    ->setVersion('Icd10_' . $releaseId);
                $icd_schema->setYearReleaseId($releaseId);

                $code = $this->option('code');
                $codes = explode(',', $code);
                foreach ($codes as $code) {
                    $icd  = $icd_schema->getRelease10($releaseId, $code);

                    if (isset($icd->parent) && count($icd->parent) > 0) {
                        $parent = $icd->parent[0];
                        $parent = explode('/', $parent);
                        $parent_model = $this->Icd10Model()->where('code', end($parent))->first();
                    }

                    $icd_schema->installIcd($icd, $parent_model ?? null);
                }
                break;
        }
    }
}
