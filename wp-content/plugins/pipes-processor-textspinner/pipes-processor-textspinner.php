<?php

/*
$Id: pipes-processor-textspinner.php 167 2014-02-27 03:05:32Z tungpham $
Plugin Name: WP Pipes Addon Text spinner Processor
Plugin URI: http://thimpress.com/shop/pipes/
Description: WP Pipes addon works follow thimpress.
Version: 2.0
Author: thimpress
Author URI: http://thimpress.com
Type: Processor
Short Name: textspinner
*/

class WPPipesPro_textspinner {

	public static function check_params_df( $params ) {
		$df         = new stdclass();
		$df->dicts  = 'custom';
		$df->random = 0;
		$df->custom = '';
		$df->lang   = 'json_vi.txt';

		foreach ( $df as $key => $val ) {
			if ( ! isset( $params->$key ) ) {
				$params->$key = $val;
			}
		}

		return $params;
	}

	public static function synonymKeyword( $keyword, $params ) {
		$path_to_dict = dirname( __FILE__ ) . DS . 'dicts' . DS . $params->dicts;
		$dictionaries = '';
		if ( isset( $params->custom ) && $params->custom != '' && strlen( $params->custom ) > 3 ) {
			$dictionaries .= "\n" . $params->custom;
		}
		if ( is_file( $path_to_dict ) ) {
			$dictionaries .= "\n" . file_get_contents( $path_to_dict );
		}
		$custom_lines = explode( "\n", $dictionaries );
		foreach ( $custom_lines as $line ) {
			if ( $line == '' ) {
				continue;
			}
			$line    = trim( $line );
			$line	 = str_replace(", ", ",", $line);
			$keyword = trim( $keyword );
			$words   = explode( ",", $line );

			if ( in_array( $keyword, $words ) ) {
				$res = $words;
				$key = array_search( $keyword, $words );
				break;
			}
		}
		//process time too long
		if ( ! @$res ) {
			return '';
		}

		$n = $params->random ? rand( 0, ( count( $res ) - 1 ) ) : 0;
		while ( $key == $n ) {
			$n = rand( 0, ( count( $res ) - 1 ) );
		}
		$newword = $res[$n];

		if ( isset( $_GET['pts1'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
			var_dump( $n );
			var_dump( $res );
		}

		return $newword;
	}

	public static function process( $data, $params ) {
		if ( ! is_object( $params ) ) {
			$params = new stdClass();
		}
		$params    = self::check_params_df( $params );
		$res       = new stdClass();
		$res->text = $data->text;
		$text      = strip_tags( $data->text );
		$text      = str_replace( array( '.', ',', '!', '?' ), ' ', $text );
		$text      = preg_replace( '/\n+|\r+|\t+/i', ' ', $text );
		$text      = trim( $text );
		$words     = self::separate_word( $text );
		if ( count( $words ) < 3 ) {
			return $res;
		}
		$joined_words = self::get_list_joined_words( $data->text, $words, $params );

		$new_text  = str_replace( $joined_words, "", $data->text );
		$new_words = self::separate_word( $new_text );

		$words_list = array();
		foreach ( $new_words AS $word ) {
			if ( in_array( $word, $words_list ) || strlen( $word ) < 4 ) {
				continue;
			}
			$words_list[] = $word;
		}
		$total_words = array_merge( $joined_words, $words_list );
		if ( count( $total_words ) < 1 ) {
			return $res;
		}

		/*$random = $params->random;
		$dicts   = $params->dicts;*/
		if ( isset( $_GET['pts2'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
			echo $res->text . '<hr />';
		}
		foreach ( $total_words AS $keyword ) {
			$replaced_word = self::synonymKeyword( $keyword, $params );
			if ( $replaced_word == '' ) {
				continue;
			}
			if ( isset( $_GET['pts2'] ) ) {
				echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
				echo "{$keyword} : {$replaced_word}";
			}
			$res->text = str_replace( $keyword, $replaced_word, $res->text );
		}
		if ( isset( $_GET['pts2'] ) ) {
			echo "\n\n<hr /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n"; //exit();
			echo $res->text;
			exit();
		}

		return $res;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->input  = array( 'text' );
		$data->output = array( 'text' );

		return $data;
	}

	public static function get_list_joined_words( $text, $words, $params ) {
		$path_to_lib  = dirname( __FILE__ ) . DS . 'libs' . DS . $params->lang;
		$json_content = file_get_contents( $path_to_lib );
		$lib          = unserialize( $json_content );
		$words_list   = array();
		$joined_w     = array();
		foreach ( $words AS $word ) {
			if ( in_array( $word, $words_list ) ) {
				continue;
			}
			$words_list[] = $word;
			if ( isset( $lib[$word] ) && count( $lib[$word] ) > 0 ) {
				foreach ( $lib[$word] as $search_w ) {
					$pos = strpos( $text, $search_w );
					if ( $pos === false ) {
						continue;
					} else {
						$text       = str_replace( $search_w, "", $text );
						$joined_w[] = $search_w;
					}
				}
			}
		}

		return $joined_w;
	}

	public static function separate_word( $text ) {
		$text  = strip_tags( $text );
		$text  = str_replace( array( '.', ',', '!', '?' ), ' ', $text );
		$text  = preg_replace( '/\n+|\r+|\t+/i', ' ', $text );
		$text  = trim( $text );
		$words = explode( " ", $text );

		return $words;
	}

	function pipes_plugin_activate() {
		add_option( 'pipes_plugin_do_activation_redirect', true );
	}
}

register_activation_hook( __FILE__, array( 'WPPipesPro_textspinner', 'pipes_plugin_activate' ) );