<?php

namespace Hanafalah\ModuleIcd;

use Illuminate\Database\Eloquent\Model;
use Stichoza\GoogleTranslate\GoogleTranslate;
use Hanafalah\ModuleIcd\Contracts\ModuleIcd as ContractsModuleIcd;
use Hanafalah\LaravelSupport\Supports\PackageManagement;
use Hanafalah\ModuleIcd\Concerns\Base\Authentication;
use Hanafalah\ModuleIcd\Concerns\Base\HasConfig;
use Hanafalah\ModuleIcd\Concerns\Base\HasEndPoint;

class ModuleIcd extends PackageManagement implements ContractsModuleIcd
{
    use Authentication, HasConfig, HasEndPoint;

    protected $__icd_model;
    protected $__icd_version;
    protected $__year_release;
    protected $__translate;

    public function __construct()
    {
        $this->setup();
        $config    = $this->__module_icd_config;
        $this->__translate = (new GoogleTranslate())
            ->setSource($config['lang'])
            ->setTarget($config['translate']['to']);
    }

    public function installICD(object $icd, ?Model $parent_model = null)
    {
        $icd_id  = $this->getIdFromUrl($icd->{"@id"});
        $icd_model = $this->__icd_model->updateOrCreate([
            'code'      => $icd_id,
            'version'   => $this->__icd_version
        ], [
            'parent_id' => isset($parent_model) ? $parent_model->getKey() : null,
            'name'      => $icd->title->{'@value'}
        ]);

        $icd_model->lang        = $icd->title->{'@language'};
        $icd_model->local_name  = $this->__translate->translate($icd_model->name);

        if (isset($icd->releaseDate)) $icd_model->relase_date = $icd->releaseDate;
        if (isset($icd->inclusion)) {
            $inclusions = [];
            foreach ($icd->inclusion as $inclusion) $inclusions[] = $inclusion->label->{'@value'};
            $icd_model->setAttribute('inclusions', $inclusions);
        }
        if (isset($icd->exclusion)) {
            $exclusions = [];
            foreach ($icd->exclusion as $exclusion) $exclusions[] = $exclusion->label->{'@value'};
            $icd_model->setAttribute('exclusions', $exclusions);
        }

        if (isset($icd->codingHint)) {
            $codingHints = [];
            foreach ($icd->codingHint as $codingHint) $codingHints[] = $codingHint->label->{'@value'};
            $icd_model->setAttribute('coding_hints', $codingHints);
        }

        if (isset($icd->classKind))  $icd_model->class_kind = $icd->classKind;
        $icd_model->save();
        if (isset($icd->child) && count($icd->child) > 0) {
            foreach ($icd->child as $child) {
                $icd_id  = $this->getIdFromUrl($child);
                if (method_exists($this, 'getRelease10')) {
                    $icd = $this->getRelease10($this->__year_release, $icd_id);
                }
                if (isset($icd)) $this->installICD($icd, $icd_model);
            }
        }
        return $icd_model;
    }

    public function setYearReleaseId(string $year): self
    {
        $this->__year_release = $year;
        return $this;
    }

    public function setVersion(string $version): self
    {
        $this->__icd_version = $version;
        return $this;
    }

    public function setIcdModel(Model $icd): self
    {
        $this->__icd_model = $icd;
        return $this;
    }

    protected function getIdFromUrl(string $url)
    {
        $url = explode('/', $url);
        return end($url);
    }

    protected function setup(): self
    {
        $this->setICDConfig()
            ->setAuthorization();
        return $this;
    }
}
