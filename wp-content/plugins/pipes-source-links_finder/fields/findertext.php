<?php
/**
 * Created by PhpStorm.
 * User: TuyenHung
 * Date: 3/10/14
 * Time: 10:32 PM
 */
require_once OBGRAB_ADMIN . 'includes' . DS . 'form' . DS . 'fields' . DS . 'text.php';

/**
 * Field to select a user id from a modal list.
 *
 * @subpackage  Form
 * @since       1.6.0
 */
class JFormFieldFindertext extends JFormFieldText {
	/**
	 * The form field type.
	 *
	 * @var    string
	 * @since  1.6.0
	 */
	public $type = 'Findertext';

	protected function getInput() {
		$html   = parent::getInput();
		$script = '<script type="text/javascript">
                    function write_default_data(format){
                    	if(format==""){
                    		alert("You did not input the format");
                    		return;
                    	}
                        var urls = document.getElementById("engine_params_url");
                        var url = urls.value.split("\n")[0];
                        validate(url);
                        var limit_items = document.getElementById("engine_params_limit_items");
                        var input = format + "||" + url;
                        if(limit_items.value != ""){
                        	input = input +  "||" + limit_items.value;
                        }
                        call_function_from_addon("engine", "pipes-source-links_finder", "get_default_item", input);
                    }
                    function validate(url) {
						var pattern = /(ftp|http|https):\/\/(\w+:{0,1}\w*@)?(\S+)(:[0-9]+)?(\/|\/([\w#!:.?+=&%@!\-\/]))?/;
						if (pattern.test(url)) {
							return true;
						}
							alert("Url is not valid!");
							return false;

					}
                </script>';

		return $html . $script;
	}

}