<?php

namespace iEducar\Modules\AuditoriaGeral\Model;

use Portabilis_Date_Utils;

trait JsonToHtmlTable
{
    public static function transformJsonToHtmlTable($json)
    {
        $dataJson = json_decode($json);
        $htmlTable = '<table class=\'tablelistagem auditoria-tab\' width=\'100%\' border=\'0\' cellpadding=\'4\' cellspacing=\'1\'>
                        <tr>
                            <td class=\'formdktd\' valign=\'top\' align=\'left\' style=\'font-weight:bold;\'>Campo</td>
                            <td class=\'formdktd\' valign=\'top\' align=\'left\' style=\'font-weight:bold;\'>Valor</td>
                        <tr>';

        foreach ($dataJson as $key => $value) {
            if (Portabilis_Date_Utils::isDateValid($value)) {
                $value = date('d/m/Y', strtotime($value));
            }
            $htmlTable .= '<tr>';
            $htmlTable .= "<td class='formlttd'>$key</td>";
            $htmlTable .= "<td class='formlttd'>$value</td>";
            $htmlTable .= '</tr>';
        }

        $htmlTable .= '</table>';

        return $htmlTable;
    }
}
