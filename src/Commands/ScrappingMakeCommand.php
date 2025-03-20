<?php

namespace Zahzah\ModuleIcd\Commands;

use Zahzah\ModuleIcd\Contracts\ICD10;

class ScrappingMakeCommand extends EnvironmentCommand{
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
        switch($version){
            case 10 :
            default : 
                $icd_schema = app(ICD10::class);
                $icd_schema->oauth();
                $icd_schema->setIcdModel($this->ICD10Model())
                           ->setVersion('ICD10_'.$releaseId);
                $icd_schema->setYearReleaseId($releaseId);

                $code = $this->option('code');
                $codes = explode(',',$code);
                foreach ($codes as $code) {
                    $icd  = $icd_schema->getRelease10($releaseId,$code);
                    
                    if (isset($icd->parent) && count($icd->parent) > 0){
                        $parent = $icd->parent[0];
                        $parent = explode('/',$parent);
                        $parent_model = $this->ICD10Model()->where('code',end($parent))->first();
                    }
                    
                    $icd_schema->installICD($icd,$parent_model ?? null);
                }
            break;
        }
    }
}