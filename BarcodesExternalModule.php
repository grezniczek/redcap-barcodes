<?php namespace DE\RUB\BarcodesExternalModule;

class BarcodesExternalModule extends \ExternalModules\AbstractExternalModule {

    private $at = "@BARCODES";

    #region Hooks

    function redcap_survey_page_top($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance = 1) {
        $tags = $this->getBarcodes($project_id, $instrument);
        if (count($tags)) {
            $this->injectJS($tags);
        }
    }

    function redcap_data_entry_form_top($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance = 1) {
        $tags = $this->getBarcodes($project_id, $instrument);
        if (count($tags)) {
            $this->injectJS($tags);
        }
    }

    #endregion

    #region Browser Logic

    /**
     * Injects JavaScript into data entry forms and survey pages that removes the File Upload links for fields with the action tag
     * @param Array $tags 
     * @return void 
     */
    private function injectJS($tags) {
        $js = [];
        $qr = false;
        foreach ($tags as $tag) {
            switch ($tag["type"]) {
                case "QR":
                    $js[] = $this->addQRCode($tag);
                    $qr = true;
                    break;
                case "DM":
                    $js[] = $this->addDatamatrix($tag);
                    $dm = true;
                    break;
            }
        }

        if (count($js)) {
            require_once "classes/InjectionHelper.php";
            $ih = InjectionHelper::init($this);
            if ($qr) $ih->js("js/qrcode.min.js");
            if ($dm) $ih->js("js/datamatrix.min.js");
            $ih->js("js/barcodes.js");
            print "<script>$(function() { " . join("; ", $js) . " });</script>";
        }
    }

    private function addQRCode($tag) {
        if (!isset($tag["size"])) $tag["size"] = 128;
        if (!isset($tag["link"])) $tag["link"] = false;
        return "DE_RUB_Barcodes.qr(".json_encode($tag).");";
    }

    private function addDatamatrix($tag) {
        if (!isset($tag["size"])) $tag["size"] = 128;
        if (!isset($tag["link"])) $tag["link"] = false;
        return "DE_RUB_Barcodes.dm(".json_encode($tag).");";
    }


    #endregion

    #region Server Logic 

    /**
     * Parses @BARCODES action tags
     * @param mixed $pid 
     * @param mixed $instrument 
     * @return array [[ field, type ]]
     */
    private function getBarcodes($pid, $instrument) {
        global $Proj;
        $tags = array();
        if ($Proj->project_id == $pid && array_key_exists($instrument, $Proj->forms)) {
            // Check field metadata for action tag
            // https://regex101.com/r/T8UtE6/1
            $re = "/{$this->at}\s{0,}=\s{0,}(?<q>[\"'])(?<t>.*)(?P=q)/m";
            foreach ($Proj->forms[$instrument]["fields"] as $fieldName => $_) {
                $meta = $Proj->metadata[$fieldName];
                // Only text
                if ($meta["element_type"] == "text") {
                    $misc = $Proj->metadata[$fieldName]["misc"];
                    preg_match_all($re, $misc, $matches, PREG_SET_ORDER, 0);
                    foreach ($matches as $match) {
                        $s = $match["t"];
                        $params = explode(",", $s);
                        $tag = [ 
                            "field" => $fieldName,
                            "type" => trim($params[0])
                        ];
                        for ($i = 1; $i < count($params); $i++) {
                            $p = trim($params[$i]);
                            if (ctype_digit($p)) {
                                $tag["size"] = $p * 1;
                            }
                            if ($p === "L") {
                                $tag["link"] = true;
                            }
                        }
                        $tags[] = $tag;
                    }
                }
            }
        }
        return $tags;
    }


    #endregion
}



