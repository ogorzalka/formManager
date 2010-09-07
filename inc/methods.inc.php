<?php
class FormManager {
	public static $errors = array();
	public static $totalErrors = 0;
	public static $htmlForm = '';
		
	// est-ce un tableau associatif ou pas ?
	static function isAssoc($arr) {
	    return array_keys($arr) !== range(0, count($arr) - 1);
	}
	
	public static function success() {
		if(!empty($_POST) && self::getErrorNumber() == 0) {
			return true;
		}
		return false;
	}
	
	public static function getErrorNumber() {
		return self::$totalErrors;
	}
	
	public static function getErrors() {
		if (self::$totalErrors > 0) {
			$txtErrors = implode(', ',self::$errors);
			self::$htmlForm = str_replace(
				array('%totalErrors%','%listErrors%'),
				array(self::$totalErrors,$txtErrors), 
				self::$htmlForm
			);
		} else {
			self::$htmlForm = preg_replace('/\{errors\}(.*)\{\/errors\}/','', self::$htmlForm);
		}
		
		self::$htmlForm = preg_replace('/\{\/?errors\}/','', self::$htmlForm);
	}
	
	public static function showErrors($text) {
		self::$htmlForm .= '{errors}<p class="error">'.$text.'<br />';
		self::$htmlForm .= '%listErrors%</p>{/errors}';
	}
	
    private static function isValidEmail($email) {
    	if(preg_match("/[.+a-zA-Z0-9_-]+@[a-zA-Z0-9-]+.[a-zA-Z]+/", $email) > 0) {
    		return true;
		} else {
			return false;
		}
	}

	public static function openForm($method, $action, $attr=array()) {
		$htmlAttr = '';
		// on insère les attribut additionnels aux différents champs
		foreach ($attr as $key=>$value) {
			$htmlAttr .= " $key=\"$value\"";
		}
		self::$htmlForm .= '<form method="'.$method.'" action="'.$action.'"'.$htmlAttr.'>'."\n";
	}

	public static function closeForm() {
		self::$htmlForm .= '</form>'."\n";
	}

	public static function openSection($attr=array()) {
		$htmlAttr = '';
		foreach ($attr as $key=>$value) {
			$htmlAttr .= " $key=\"$value\"";
		}
		self::$htmlForm .= '<fieldset'.$htmlAttr.'>'."\n";
	}
	
	public static function closeSection() {
		self::$htmlForm .= '</fieldset>'."\n";
	}
	
	public static function insert($elem,$extra,$name,$attr=array(),$required=false) {
		if ($elem == 'input' && $extra == 'submit') {
			$htmlAttr = " type=\"$extra\" value=\"$name\"";
		}
		else if ($elem != 'select') {
			$htmlAttr = " type=\"$extra\" value=\"".( (!empty($_POST[$name])) ? $_POST[$name] : '' )."\" name=\"$name\"";
		}
		 else {
			$htmlAttr = " name=\"$name\"";
		}
		
		if ($required || $extra == 'email') {
			// si le champs requis n'a pas été remplis
			if (
				(!empty($_POST) && $_POST[$name]=='') ||
				($extra == 'email') && !empty($_POST) && !self::isValidEmail($_POST[$name])
			) {
					// si une classe est déjà spécifiée, on ajoute la classe erreur à cette classe
					if (!empty($attr['class']) && $attr['class'] != '') {
						$attr['class'] .= ' error';
					} 
					// sinon on ajoute juste l'attribut de classe
					else {
						$htmlAttr .= " class=\"error\"";
					}
					self::$errors[] = $attr['errorLabel'];
					self::$totalErrors++;
			}
		}
		
		// si aucun ID de pour le champs n'a été spécifié, on l'ajoute manuellement
		if (empty($attr['id']) || $attr['id'] == '') {
			$attr['id'] = "form_$name";
		}
		
		// on insère les attribut additionnels aux différents champs
		foreach ($attr as $key=>$value) {
			if ( !in_array($key, array('label', 'p.class', 'errorLabel')) ) {
				$htmlAttr .= " $key=\"$value\"";
			}
		}
		
		if ($elem == "select") {
			$htmlAttr .= ">"."\n";

			foreach ($extra as $key=>$value) {
				$value = ((self::isAssoc($extra)) ? $key : $value);
				$htmlAttr .= "<option value=\"$value\"".( (!empty($_POST) && $_POST[$name] == $value) ? ' selected="selected"' : '' ).">$value</option>"."\n";
			}
			$htmlAttr .= "</select";
		}

		// on affiche le champs
		$output = '<p'.( ( !empty($attr['p.class']) ) ? ' class="'.$attr['p.class'].'">' : '>' )."\n";
		if (!empty($attr['label'])) {
			$output .= "\t".'<label for="'.$attr['id'].'">'.$attr['label'].'</label>'."\n";
		}
		$output .= "\t".'<'.$elem.$htmlAttr.'>'."\n";
		$output .= '</p>'."\n";
		self::$htmlForm .= $output;
	}
	
	public static function printForm() {
		return self::$htmlForm;
	}
}