<?php namespace DE\RUB\BarcodesExternalModule;

class BarcodesExternalModule extends \ExternalModules\AbstractExternalModule {

    private $at = "@BARCODES";

    #region Font Data

    private $code39_text = "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";

    #endregion

    #region Hooks

    function redcap_survey_page($project_id, $record, $instrument, $event_id, $group_id, $survey_hash, $response_id, $repeat_instance = 1) {
        $tags = $this->getBarcodes($project_id, $instrument);
        if (count($tags)) {
            $this->injectJS($tags);
        }
    }

    function redcap_data_entry_form($project_id, $record, $instrument, $event_id, $group_id, $repeat_instance = 1) {
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
        $qr = $dm = false;
        $fonts = [];
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
                case "Code 39":
                    $tag["extended"] = false;
                    $js[] = $this->addCode39($tag);
                    $fonts[$tag["text"] ? "Code39Text" : "Code39"] = true;
                    break;
                case "Code 39 Extended":
                    $tag["extended"] = true;
                    $js[] = $this->addCode39($tag);
                    $fonts[$tag["text"] ? "Code39ExtendedText" : "Code39Extended"] = true;
                    break;
            }
        }

        if (count($js)) {
            $js[] = "$('form#form').trigger('change');";
            require_once "classes/InjectionHelper.php";
            $ih = InjectionHelper::init($this);
            foreach ($fonts as $font => $_) {
                $ih->css("css/$font.css", false);
            }
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

    private function addCode39($tag) {
        if (!isset($tag["size"])) $tag["size"] = 48;
        return "DE_RUB_Barcodes.code39(".json_encode($tag).");";
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
                        $tag = [
                            "field" => $fieldName,
                            "text" => false,
                        ];
                        $s = $match["t"];
                        $params = explode(",", $s);
                        $t = trim($params[0]);
                        if (ends_with($t, " Text")) {
                            $t = substr($t, 0, length($t) - 5);
                            $tag["text"] = true;
                        }
                        $tag["type"] = $t;
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



