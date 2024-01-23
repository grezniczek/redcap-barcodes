<?php namespace DE\RUB\BarcodesExternalModule;

class BarcodesExternalModule extends \ExternalModules\AbstractExternalModule {

    /**
     * The name of the action tag provided by this module
     * @var string
     */
    private $at = "@BARCODES";

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
        $qr = $dm = $c128 = false;
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
                case "Code 128":
                    $js[] = $this->addCode128($tag);
                    $c128 = true;
                    $fonts[$tag["text"] ? "Code128Text" : "Code128"] = true;
                    break;
                case "EAN/UPC":
                    $tag["text"] = true;
                    $js[] = $this->addEAN13($tag);
                    $fonts["EAN13Text"] = true;
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
            if ($qr) $ih->js("js/qrcode.min.js", true);
            if ($dm) $ih->js("js/datamatrix.min.js", true);
            if ($c128) $ih->js("js/code-128-encoder.min.js", true);
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

    private function addCode128($tag) {
        if (!isset($tag["size"])) $tag["size"] = 48;
        return "DE_RUB_Barcodes.code128(".json_encode($tag).");";
    }

    private function addEAN13($tag) {
        if (!isset($tag["size"])) $tag["size"] = 128;
        return "DE_RUB_Barcodes.ean13(".json_encode($tag).");";
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



