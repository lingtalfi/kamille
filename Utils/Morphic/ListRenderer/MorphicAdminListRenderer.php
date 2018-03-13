<?php


namespace Kamille\Utils\Morphic\ListRenderer;


use Kamille\Mvc\Theme\Theme;
use Kamille\Utils\Morphic\Helper\MorphicHelper;
use QuickPdo\Util\QuickPdoListInfoUtil;

class MorphicAdminListRenderer
{
    private $widgetRendererIdentifier;


    public function __construct()
    {
        $this->widgetRendererIdentifier = null;
    }


    public static function create()
    {
        return new static();
    }


    public function setWidgetRendererIdentifier($widgetRendererIdentifier)
    {
        $this->widgetRendererIdentifier = $widgetRendererIdentifier;
        return $this;
    }


    public function renderByConfig(array $config, array $params = null)
    {


        //--------------------------------------------
        // PERSISTENCE LAYER
        //--------------------------------------------
        if (null === $params || empty($params)) {
            if (array_key_exists("viewId", $config)) {
                $params = MorphicHelper::getListParameters($config['viewId']);
            } else {
                $params = [];
            }
        }

        //--------------------------------------------
        //
        //--------------------------------------------
        $util = QuickPdoListInfoUtil::create()
            ->setQuerySkeleton($config['querySkeleton'])
            ->setQueryCols($config['queryCols']);
        if (null !== $config['allowedFilters']) {
            $util->setAllowedFilters($config['allowedFilters']);
        }
        if (null !== $config['allowedSort']) {
            $util->setAllowedSorts($config['allowedSort']);
        }
        if (array_key_exists('realColumnMap', $config)) {
            $util->setRealColumnMap($config['realColumnMap']);
        }
        if (array_key_exists('having', $config)) {
            $util->setHaving($config['having']);
        }
        $info = $util->execute($params);


        $rows = $info['rows'];
        $renderer = Theme::getWidgetRenderer($this->widgetRendererIdentifier);
        $renderer->setModel([
            'title' => $config['title'],
            'rows' => $rows,
            'module' => $config['module'],
            'viewId' => $config['viewId'],
            'table' => $config['table'],
            'headers' => $config['headers'],
            'headersVisibility' => (array_key_exists("headersVisibility", $config)) ? $config['headersVisibility'] : [],
            'page' => $info['page'],
            'nbPages' => $info['nbPages'],
            'nipp' => $info['nipp'],
            'nbItems' => $info['nbItems'],
            'nippChoices' => $config['nippChoices'],
            //
            'sort' => $info['symbolicSorts'],
//            'realSort' => $info['sort'],

            'filters' => $info['symbolicFilters'],
            'listActions' => $config['listActions'],
            'rowActions' => (array_key_exists("rowActions", $config)) ? $config['rowActions'] : [],
            'context' => (array_key_exists("context", $config)) ? $config['context'] : [],
            'deadCols' => (array_key_exists("deadCols", $config)) ? $config['deadCols'] : [],
            'colTransformers' => (array_key_exists("colTransformers", $config)) ? $config['colTransformers'] : [],
            'colSizes' => (array_key_exists("colSizes", $config)) ? $config['colSizes'] : [],
        ]);
        return $renderer->render();
    }

}