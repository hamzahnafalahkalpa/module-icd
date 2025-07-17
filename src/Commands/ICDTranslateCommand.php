<?php

namespace Hanafalah\ModuleIcd\Commands;

use Stichoza\GoogleTranslate\GoogleTranslate;

class ICDTranslateCommand extends EnvironmentCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'module-icd:translate {code} {to} {--from=} {--childs}';

    protected $__tr;

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
        $code = $this->argument('code');
        $this->__tr = new GoogleTranslate();
        $this->__tr->setSource($this->option('from') ?? null)->setTarget($this->argument('to'));
        $icd = $this->ICDModel()->where('code', $code)->first();
        $this->translateICD($icd);
    }

    private function translateICD($icd)
    {
        $icd->local_name = $this->__tr->translate($icd->name);
        $icd->save();
        if ($this->option('childs') && isset($icd->childs) && count($icd->childs) > 0) {
            foreach ($icd->childs as $child) {
                $this->translateICD($child);
            }
        }
    }
}
