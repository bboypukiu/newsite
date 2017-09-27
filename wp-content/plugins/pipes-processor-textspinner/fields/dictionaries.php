<?php
/**
 * @package          WP Pipes plugin
 * @version          $Id:$
 * @author           thimpress.com
 * @copyright    (c) 2007-2013 thimpress.com. All rights reserved.
 * @license          http://www.gnu.org/licenses/gpl-2.0.html
 */


require_once OBGRAB_ADMIN.DS.'includes'.DS.'form'.DS.'fields'.DS.'list.php';

class JFormFieldDictionaries extends JFormFieldList
{
	protected $type = 'Dictionaries';

	protected function getOptions()
	{
		$lists = $this->getfilelist();

		return $lists;
	}
	public static function getfilelist() {
		$path = dirname(dirname(__FILE__)) . DS . 'dicts';
	 	$default = new stdClass();
		$default->text = 'Custom Only';
		$default->value = 'custom';
		$list = array();
		$list[] = $default;
		if ( ! is_dir( $path ) ) {
			return $list;
		}
		require_once(OBGRAB_HELPERS . 'filesystem.php');
		$files = PIPES_Helper_FileSystem::files( $path );
		if ( ! is_array( $files ) ) {
			return $list;
		}
		foreach($files as $file){
			$extension = JFile::getExt( $file );
			$dict = new stdClass();
			if($extension == 'txt'){
				$dict->value = $file;
				$dict->text = ogbFile::getName($file);
				$list[] = $dict;
			}
		}
		return $list;
	}
}

