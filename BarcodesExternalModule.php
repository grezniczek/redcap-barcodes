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
        foreach ($tags as $tag) {
        }
		
        if (count($js)) {
            print "<script>$(function() { " . join("; ", $js) . " });</script>";
        }
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
                        $tags[] = array(
                            "field" => $fieldName,
                            "type" => $match["t"],
                        );
                    }
                }
            }
        }
        return $tags;
    }


    #endregion
}



