<?php

/**
 * Class for formatting results into JSON or XML
 */
class format {
    public function convert($type, $input) {
        switch($type):
            case 'xml':
                return $this->convertToXML($input);
                break;
            case 'json':
                return $this->convertToJSON($input);
                break;
            case 'phparray1337':
            	return $input;
            	break;
            default:
                return $this->convertToJSON($input);
                break;
        endswitch;
    }

    private function convertToXML($input, $root = 'result') {
    	return "<?xml version=\"1.0\" ?>\n<$root>\t" . $this->toXML($input, 0) . "\n</$root>";
	}

	private function toXML($input, $depth) {
		$output = '';

		$tab = "";
		for ($i = 0;$i<=$depth;$i++) {
			$tab .= "\t";
		}

		foreach($input as $key=>$value) {
			if (is_array($value)) {
				$output .= "\n{$tab}<$key>{$tab}\t" . $this->toXML($value, ($depth+1)) . "\n{$tab}</$key>";
			} else {
				$output .= "\n{$tab}<$key>$value</$key>";
			}
		}

		return $output;
	}

	private function convertToJSON($input) {
		return json_encode($input);
	}
}
