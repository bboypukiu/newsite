<?php

/*
$Id: pipes-source-links_finder.php 167 2014-02-27 03:05:32Z tungpham $
Plugin Name: WP Pipes Addon Engine Link Finder
Plugin URI: http://wpbriz.com
Description: WP Pipes addon works follow WPPIPES.
Version: 1.3
Author: WPPipes
Author URI: http://wpbriz.com
Type: Source
Short Name: links_finder
*/


class WPPipesEngine_links_finder {
	public static function getData( $params ) {
		if ( isset( $_GET['e'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			ogb_pr( $params, 'Params: ' );
		}
		$data = self::getItemsList( $params );
		if ( isset( $_GET['e1'] ) ) {
			echo "\n\n<br /><i><b>File:</b>" . __FILE__ . ' <b>Line:</b>' . __LINE__ . "</i><br />\n\n";
			echo 'Total: ' . count( $data );
			ogb_pr( $data, 'Data: ' );
		}

		return $data;
	}

	public static function getItemsList( $params ) {
		$urls = explode( "\n", $params->url );
		$data = array();
		foreach ( $urls as $url ) {
			$url = trim($url);
			$cache_path = self::getPath( $url );
			if ( ! self::need_update( $cache_path ) && ( $params->pcache ) ) {
				$rows   = self::get_cache( $cache_path );
				$result = $rows;
			} else {
				$result = self::getItems( $url, $params );
				self::update_cache( $cache_path, $result );

			}
			$data = array_merge( $data, $result );
		}

		return $data;
	}

	public static function getItems( $url, $params ) {
		$rows = array();
		if ( $url == '' || ! filter_var( $url, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED ) ) {
			return $rows;
		}
		$domain = parse_url( $url, PHP_URL_HOST );
		if ( $params->absolute_host != '' ) {
			$domain = $params->absolute_host;
		}
		$domain        = trim( $domain, '/' );
		$original_host = explode( $domain, $url );
        $params->custom_ck = isset( $params->custom_ck ) ? $params->custom_ck : 'location.href=1;';
		switch ( $params->linkfinder_curl ) {
            case 6:
                $html[1] = ogbFile::get_curl5( $url, $params->custom_ck );
                break;
			case 5:
				$html[1] = file_get_contents( $url );
				break;
			case 4:
				$html = ogbFile::get_curl4( $url );
				break;
			case 3:
				$html = ogbFile::get_curl3( $url );
				break;
			case 2:
				$html = ogbFile::get_curl2( $url );
				break;
			default:
				$html = ogbFile::get_curl1( $url );
		}

		$html = $html[1];

		$format = $params->format;
		$format = str_replace( "/", "\/", $format );
		$format = str_replace( "(*)", "[^\/]*", $format );

		$dom = new DOMDocument;
		libxml_use_internal_errors( true );
		$dom->loadHTML( $html );
		if ( $params->queries != '' ) {
			$queries              = explode( '\n', $params->queries );
			$newdoc               = new DOMDocument;
			$newdoc->formatOutput = true;
			$newdoc->loadXML( "<root></root>" );
			foreach ( $queries as $query ) {
				$query           = str_replace( "\\", "", $query );
				$xpath           = new DOMXpath( $dom );
				$elements        = $xpath->query( $query );
				$length_elements = $elements->length;
				if ( $length_elements == 0 ) {
					continue;
				}
				for ( $t = 0; $t < $length_elements; $t ++ ) {
					$node = $elements->item( $t );
					$node = $newdoc->importNode( $node, true );
					$newdoc->documentElement->appendChild( $node );
				}
			}
			$links = $newdoc->getElementsByTagName( 'a' );
		} else {
			$links = $dom->getElementsByTagName( 'a' );
		}
		$length = $links->length;
		if ( $length == 0 ) {
			return $rows;
		}
		$list_href   = array();
		$limit_items = 0;

		for ( $i = 0; $i < $length; $i ++ ) {
			if ( $limit_items >= $params->limit_items && isset( $params->limit_items ) && $params->limit_items > 0 ) {
				break;
			}
			$item = new stdClass();
			$href = $links->item( $i )->getAttribute( 'href' );
			if ( ! filter_var( $href, FILTER_VALIDATE_URL, FILTER_FLAG_HOST_REQUIRED ) ) {
				$href = "/" . $href;
				$href = str_replace( "//", "/", $href );
				$href = $original_host[0] . $domain . $href;
			}
			if ( preg_match( "/{$format}/i", $href, $result ) ) {
				$condition = true;
			} else {
				$condition = false;
			}
			if ( $condition ) {
				$item->link    = $href;
				$item->src_url = $item->link;
				$title         = preg_replace( '/(?:\s\s+|\n|\t)/', '', $links->item( $i )->nodeValue );
				if ( ( $title != '' || $params->force_title==0 ) && ! in_array( $href, $list_href ) ) {
					$title          = $params->utf8decode ? utf8_decode( $title ) : $title;
					$item->title    = $title;
					$item->src_name = $item->title;
					$list_href[]    = $href;
					$rows[]         = $item;
					$limit_items ++;
				}
			}
		}

		return $rows;
	}

	public static function getDataFields() {
		$data         = new stdClass();
		$data->output = array( 'title', 'link' );
		$id           = filter_input( INPUT_GET, 'id' );
		$path         = OGRAB_EDATA . 'item-' . $id . DS . 'row-default.dat';
		if ( ! is_file( $path ) ) {
			return $data;
		}
		$default = file_get_contents( $path );
		$default = unserialize( $default );

		$default_oe = $default->so;
		foreach ( $data->output as $key => $value ) {
			if ( is_array( $default_oe->$value ) ) {
				$data->output[$key] = $value . '<br /><p class="text-muted small">Array</p>';
			} else {
				$default_oe->$value = str_replace( "'", "", $default_oe->$value );
				$default_oe->$value = str_replace( '"', '', $default_oe->$value );
				$data->output[$key] = $value . '<br /><p data-toggle="tooltip" data-original-title="' . ( $default_oe->$value != '' ? strip_tags( $default_oe->$value ) : 'null' ) . '" class="text-muted small">' . ( $default_oe->$value != '' ? strip_tags( $default_oe->$value ) . '</p>' : 'null</p>' );
			}
		}

		return $data;
	}

	public static function get_default_item() {
		$id                  = filter_input( INPUT_POST, 'id' );
		$path                = OGRAB_EDATA . 'item-' . $id . DS . 'row-default.dat';
		$value_default       = filter_input( INPUT_POST, 'val_default' );
		$input               = explode( '||', $value_default );
		$params              = new stdClass();
		$params->url         = $input[1];
		$params->format      = $input[0];
		$params->limit_items = $input[2];
		$rows                = self::getItems( $input[1], $params );
		$row                 = new stdclass();
		$row->title          = $rows[0]->title; # the title
		$row->link           = $rows[0]->link; # a single link
		if ( ! is_file( $path ) ) {
			$source = new stdClass();
		} else {
			$source = ogb_common::get_default_data( '', $id );
		}
		$source->so = $row;
		$cache      = serialize( $source );

		if ( isset( $_GET['x2'] ) ) {
			//echo "\n\n<br /><i><b>File:</b>".__FILE__.' <b>Line:</b>'.__LINE__."</i><br />\n\n";
			echo '<br>Path: ' . $path;
		}
		ogbFile::write( $path, $cache );
		exit();
	}

	/**
	 * Process cache
	 */
	public static function getPath( $url ) {
		return OGRAB_ECACHE . md5( $url );
	}

	public static function need_update( $path ) {
		/*if ( isset( $_GET['u'] ) ) {
			return true;
		}*/
		if ( ! is_file( $path ) ) {
			return true;
		}
		$cache_mtime = filemtime( $path );
		$diff        = time() - $cache_mtime;
		if ( isset( $_GET['x'] ) ) {
			echo '<br /><br /><i><b>File</b> ' . __FILE__ . ' <b>Line</b> ' . __LINE__ . "</i><br />\n";
			echo 'last Update  - s: ';
			var_dump( $diff );
			if ( $diff > 600 ) {
				$a = $diff / 60;
				echo ' - m: ';
				var_dump( $a );
			}
			if ( $a > 60 ) {
				$a = $diff / 60;
				echo ' - h: ';
				var_dump( $a );
			}
		}
		$time = 3600;

		return $diff > $time;
	}

	//--- Begin Get feed item ---
	public static function update_cache( $path, $rows ) {
		$data = serialize( $rows );
		//$cache = self::get_cache( $path );
		$a = ogbFile::write( $path, $data );

		return $a;
	}

	public static function get_cache( $path ) {
		if ( ! is_file( $path ) ) {
			return array();
		}
		$cache_conten = file_get_contents( $path );
		$rows         = unserialize( $cache_conten );

		return $rows;
	}

}