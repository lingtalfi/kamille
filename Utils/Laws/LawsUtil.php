<?php


namespace Kamille\Utils\Laws;


use Kamille\Architecture\ApplicationParameters\ApplicationParameters;
use Kamille\Ling\Z;
use Kamille\Mvc\Layout\HtmlLayout;
use Kamille\Mvc\LayoutProxy\LawsLayoutProxy;
use Kamille\Mvc\Loader\FileLoader;
use Kamille\Mvc\Position\Position;
use Kamille\Mvc\Renderer\PhpLayoutRenderer;
use Kamille\Mvc\Widget\Widget;
use Kamille\Utils\Laws\Exception\LawsUtilException;


class LawsUtil
{


    public static function renderLawsViewById($viewId, array $config = [])
    {
        $appDir = ApplicationParameters::get("app_dir");
        $file = $appDir . "/config/laws/$viewId.conf.php";
        if (file_exists($file)) {
            $conf = [];
            include $file;
            $conf = array_replace_recursive($conf, $config);
            return self::renderLawsView($conf);
        }
        throw new LawsUtilException("laws config file not found: $file");
    }


    /**
     * $variables: allow us to change config on the fly (i.e. from the controller).
     *      it can contain the following keys:
     *          - layout: yourConf
     *          - widgets
     *              - widgetId: yourConf
     *          - positions:
     *              - positionName: yourConf
     *
     *
     */
    public static function renderLawsView(array $config)
    {



        $layoutTemplate = $config['layout']['name'];
        $positions = (array_key_exists('positions', $config)) ? $config['positions'] : [];
        $widgets = (array_key_exists('widgets', $config)) ? $config['widgets'] : [];
        $layoutConf = (array_key_exists('conf', $config['layout'])) ? $config['layout']['conf'] : [];

        $theme = ApplicationParameters::get("theme");
        $wloader = FileLoader::create()->addDir(Z::appDir() . "/theme/$theme/widgets");
        $ploader = FileLoader::create()->addDir(Z::appDir() . "/theme/$theme/positions");


        $commonRenderer = PhpLayoutRenderer::create();
        $proxy = LawsLayoutProxy::create();

        //--------------------------------------------
        // POSITIONS
        //--------------------------------------------
        foreach ($positions as $positionName => $pInfo) {
            $tplName = $pInfo['name'];
            $pVars = (array_key_exists('conf', $pInfo)) ? $pInfo['conf'] : [];

            $proxy->bindPosition($positionName, Position::create()
                ->setTemplate($tplName)
                ->setLoader($ploader)
                ->setVariables($pVars)
                ->setRenderer($commonRenderer));
        }
        $commonRenderer->setLayoutProxy($proxy);

        //--------------------------------------------
        // LAYOUT
        //--------------------------------------------
        $layout = HtmlLayout::create()
            ->setTemplate($layoutTemplate)
            ->setLoader(FileLoader::create()
                ->addDir(Z::appDir() . "/theme/$theme/layouts")
            )
            ->setRenderer($commonRenderer);


        //--------------------------------------------
        // WIDGETS
        //--------------------------------------------
        foreach ($widgets as $id => $widgetInfo) {
            $name = $widgetInfo['name'];
            $conf = (array_key_exists('conf', $widgetInfo)) ? $widgetInfo['conf'] : [];

            $layout
                ->bindWidget($id, Widget::create()
                    ->setTemplate($name)
                    ->setVariables($conf)
                    ->setLoader($wloader)
                    ->setRenderer($commonRenderer)
                );
        }

        return $layout->render($layoutConf);

    }

}