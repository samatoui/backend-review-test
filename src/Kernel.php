<?php

namespace App;

use Symfony\Bundle\FrameworkBundle\Kernel\MicroKernelTrait;
use Symfony\Component\HttpKernel\Kernel as BaseKernel;

class Kernel extends BaseKernel
{
    use MicroKernelTrait;

    /**
     * @return array
     */
    protected function getKernelParameters(): array
    {
        $parameters = parent::getKernelParameters();

        $parameters['kernel.spools_dir'] = realpath($this->getSpoolDir()) ?: $this->getSpoolDir();

        return $parameters;
    }

    /**
     * @return string
     */
    public function getSpoolDir(): string
    {
        return $this->getProjectDir() . '/var/spool';
    }
}
