<?php


namespace Kamille\Utils\Morphic\Helper;


use Bat\SessionTool;
use QuickPdo\QuickPdo;
use QuickPdo\QuickPdoStmtTool;
use SokoForm\Form\SokoFormInterface;

class MorphicHelper
{


    public static function getFormContextValue($key, array $context)
    {
        if (array_key_exists($key, $context)) {
            return $context[$key];
        }
        throw new \Exception("Bad assertion: expected key $key to be set in the form context");
    }


    /**
     * Use this for persistence, in conjunction with getListParameters
     */
    public static function setListParameters($viewId, array $params)
    {
        SessionTool::start();

        if (false === array_key_exists("morphic-persistence", $_SESSION)) {
            $_SESSION["morphic-persistence"] = [];
        }
        $_SESSION["morphic-persistence"][$viewId] = $params;
    }


    /**
     * Use this for persistence, in conjunction with setListParameters
     * This returns an array, which might be empty if no entry is found for
     * the given viewId
     */
    public static function getListParameters($viewId)
    {
        $ret = [];
        if (false === array_key_exists("morphic-persistence", $_SESSION)) {
            $_SESSION["morphic-persistence"] = [];
        }
        if (array_key_exists($viewId, $_SESSION["morphic-persistence"])) {
            return $_SESSION["morphic-persistence"][$viewId];
        }
        return $ret;
    }


    public static function getFeedFunction($table)
    {
        return self::getFeedFunctionByQuery("select * from $table");
    }

    public static function getFeedFunctionByQuery($query)
    {
        return function (SokoFormInterface $form, array $ric) use ($query) {
            $markers = [];
            $values = array_intersect_key($_GET, array_flip($ric));
            $q = $query;
            QuickPdoStmtTool::addWhereEqualsSubStmt($values, $q, $markers);
            $row = QuickPdo::fetch("$q", $markers);
            if ($row) {
                $form->inject($row);
            }
        };
    }


    public static function price($number)
    {
        return str_replace(',', '.', $number);
    }

}