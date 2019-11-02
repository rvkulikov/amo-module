<?php
namespace rvkulikov\amo\module\services\init;

/**
 *
 */
interface ModuleInitializer_Interface
{
    /**
     * @param ModuleInitializer_Cfg $cfg
     *
     * @return ModuleInitializer_Res
     */
    public function initialize(ModuleInitializer_Cfg $cfg);
}