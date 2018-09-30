<?php

//Welcome to our page!


class bluxedropdisplayModuleFrontController extends ModuleFrontController
{
    public function initContent()
    {
        parent::initContent();
        $this->setTemplate('module:bluxedrop/views/templates/front/display.tpl');
    }
}