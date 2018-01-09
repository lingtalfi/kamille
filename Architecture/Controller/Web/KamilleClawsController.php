<?php


namespace Kamille\Architecture\Controller\Web;


use Kamille\Architecture\Response\Web\HttpResponse;
use Kamille\Utils\Claws\Claws;
use Kamille\Utils\Claws\Renderer\ClawsRenderer;

class KamilleClawsController extends KamilleController
{



    protected $claws;
    protected $clawsRenderer;
    protected $clawsReturn;


    public function renderClaws()
    {
        $this->prepareClaws();
        return $this->doRenderClaws();
    }


    //--------------------------------------------
    //
    //--------------------------------------------
    protected function getClaws()
    {
        if (null === $this->claws) {
            $this->claws = new Claws();
        }
        return $this->claws;
    }



    protected function prepareClaws() // override me
    {

    }

    protected function doRenderClaws()
    {
        if (null !== $this->clawsReturn) {
            return $this->clawsReturn;
        }
        $claws = $this->getClaws();
        $renderer = $this->getClawsRenderer();
        $content = $renderer->setClaws($claws)->render();
        return HttpResponse::create($content);
    }

    //--------------------------------------------
    //
    //--------------------------------------------
    private function getClawsRenderer()
    {
        if (null === $this->clawsRenderer) {
            $this->clawsRenderer = new ClawsRenderer();
        }
        return $this->clawsRenderer;
    }



}