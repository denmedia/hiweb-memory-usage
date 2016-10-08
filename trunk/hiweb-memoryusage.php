<?php
	/**
	 * Plugin Name: hiWeb Memory Usage
	 * Description: A small plug on the measurement of memory used on the site is the site PHP level
	 * Version: 1.3.0.0
	 */
	
	
	function hiweb_memoryUsage(){
		static $class;
		if( !is_object( $class ) ) $class = new hiweb_memoryusage();
		return $class;
	}
	
	class hiweb_memoryusage{
		
		
		public $memoryUsage = 0;
		
		public function formatBytes( $bytes, $precision = 2 ){
			$units = array( 'B', 'KB', 'MB', 'GB', 'TB' );
			
			$bytes = max( $bytes, 0 );
			$pow = floor( ( $bytes ? log( $bytes ) : 0 ) / log( 1024 ) );
			$pow = min( $pow, count( $units ) - 1 );
			$bytes /= pow( 1024, $pow );
			
			return round( $bytes, $precision ) . ' ' . $units[ $pow ];
		}
		
		public function is_ajax(){
			return defined( 'DOING_AJAX' );
		}

		function get_memory_limit( ){
			$val = trim( ini_get( 'memory_limit' ) );
			$last = strtolower( $val[ strlen( $val ) - 1 ] );
			switch( $last ){
				// The 'G' modifier is available since PHP 5.1.0
				case 'g':
					$val *= 1024;
				case 'm':
					$val *= 1024;
				case 'k':
					$val *= 1024;
			}
			return $val;
		}
		
		
		public function __construct(){
			$this->memoryUsage = memory_get_usage();
			add_action( 'shutdown', function(){
				if( !hiweb_memoryUsage()->is_ajax() ) hiweb_memoryUsage()->getHtml_memoryUsage( memory_get_peak_usage(), 0 );
			} );
		}
		
		function getHtml_memoryUsage( $usage, $hiweb_memory_usage ){
			echo '<div style="position: fixed; top: 20px; padding: 10px; background: #ffffff; right: 10px; border: 3px solid black; z-index: 10000;">Size: ' . hiweb_memoryUsage()->formatBytes( $usage - $hiweb_memory_usage ) . ' / ' . (
				strpos( ini_get( 'memory_limit' ), 'M' ) !== false ? ini_get( 'memory_limit' ) : hiweb_memoryUsage()->formatBytes( $this->get_memory_limit() )
				) . '</div>';
		}
		
	}
	
	
	hiweb_memoryUsage();