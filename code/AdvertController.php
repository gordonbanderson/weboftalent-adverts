<?php
	class Advert_Controller extends Controller {


		/**
		 * @var integer Size of output chunks in kb while in PHP fread mode.
		 */
		protected static $chunck_size_kb = 32;
		
		/**
		 * @var boolean Flag use X-Sendfile header mode instead of PHP fread mode.
		 */
		protected static $use_x_sendfile = false;
		
		/**
		 * @var boolean Flag use SilverStripe send file method.
		 */
		protected static $use_ss_sendfile = false;

		/*
		When a URL of the following form is clicked,
		http://SERVER/advert/26b0c28f4566dc54cc66770bdda86017851bd3dbbd4ac5868c63e003903dc8dc9b88b209bba54f1b5e6f4c3cf1295e58298e7d5a58b6c8ff02df9130ffd4ac30
		a search is made for the digital signature in the adverts (this is indexed in the database)

		If a record is found it's link is obtained and a redirection made, firstly recording the clickthrough in the database.

		If a record is not found a 404 is returned
		*/
		public function click($args) {
	    	$params = $args->allParams();
	    	$digsig = $params['DigitalSignature'];

	    	$advert = Advert::get()->filter('DigitalSignature', $digsig)->first(); // should only be the one but make sure
	    	
	    	if (!$advert) {
	    		$this->httpError(404, 'Advert "'.$digsig.'"" not found');
	    	} else {
	    		// record the click
	    		$advert->Clickthroughs = $advert->Clickthroughs + 1;
	    		$advert->write();
	    		// documentation here states temporary directs are used, http://doc.silverstripe.org/framework/en/topics/controller
	    		// this means the browser wont store the redirect and thus bypass the clickthrough recording
	    		return $this->redirect($advert->WebsiteLink);
	    	}

	    	
		}


		public function image($args) {
	    	$params = $args->allParams();
	    	$digsig = $params['DigitalSignature'];

	    	$advert = Advert::get()->filter('DigitalSignature', $digsig)->first(); // should only be the one but make sure
	    	if (!$advert) {
	    		$this->httpError(404, 'Advert "'.$digsig.'"" not found');
	    	} else {
	    		// record the click
	    		$advert->Impressions = $advert->Impressions + 1;
	    		$advert->write();
	    		// documentation here states temporary directs are used, http://doc.silverstripe.org/framework/en/topics/controller
	    		// this means the browser wont store the redirect and thus bypass the clickthrough recording

	    		return $this->fileFound($advert->AdvertImage());
	    	}
		}
	







	/**
	 * Use X-Sendfile headers to send files to the browser.
	 * This is quicker than pushing files through PHP but
	 * requires either Lighttpd or mod_xsendfile for Apache
	 * @link http://tn123.ath.cx/mod_xsendfile/ 
	 */
	static function use_x_sendfile_method() {
		self::use_default_sendfile_method();
		self::$use_x_sendfile = true;
	}
	
	/**
	 * Use internal SilverStripe to send files to the browser.
	 * This is the least efficient method but is useful for 
	 * testing. Not recommend for production
	 * environments.
	 */
	static function use_ss_sendfile_method() {
		self::use_default_sendfile_method();
		self::$use_ss_sendfile = true;
	}
	
	/**
	 * Use the default chuncked file method to send files to the browser.
	 * This is the default method.
	 */
	static function use_default_sendfile_method() {
		self::$use_ss_sendfile = false;
		self::$use_x_sendfile = false;
	}
	
	/**
	 * Set the size of upload chunk in bytes.
	 * @param int $kilobytes
	 */
	static function set_chunk_size($kilobytes) {
		$kilobytes = max(0, (int)$kilobytes);
		if(!$kilobytes) user_error("Invalid download chunk size", E_USER_ERROR);
		self::$chunck_size_kb = $kilobytes;
	}
	
	/**
	 * Set the Apache access file name (.htaccess by default)
	 * as determined by the AccessFileName Apache directive.
	 * @param string $filename
	 */
	static function set_access_filename($filename) {
		self::$htaccess_file = $filename;
	}
	
	/**
	 * Get the Apache access file name
	 * @return string
	 */
	static function get_access_filename() {
		return self::$htaccess_file;
	}



	/**
	 * File found response
	 *
	 * @param $file File to send
	 * @param $alternate_path string If supplied, return the file from this path instead, for
	 * example, resampled images.
	 */
	function fileFound(File $file, $alternate_path = null) {
		
		// File properties
		$file_name = $file->Name;
		$file_path = Director::getAbsFile($alternate_path ? $alternate_path : $file->FullPath);
		$file_size = filesize($file_path);
		
		// Testing mode - return an HTTPResponse
		if(self::$use_ss_sendfile) {
			if(ClassInfo::exists('SS_HTTPRequest')) {
				return SS_HTTPRequest::send_file(file_get_contents($file_path), $file_name);
			} else {
				return HTTPRequest::send_file(file_get_contents($file_path), $file_name);
			}
		}
		
		// Normal operation:
		$mimeType = HTTP::get_mime_type($file_name);
		header("Content-Type: {$mimeType}; name=\"" . addslashes($file_name) . "\"");
		header("Content-Disposition: attachment; filename=" . addslashes($file_name));
		header("Cache-Control: max-age=1, private");
		header("Content-Length: {$file_size}");
		header("Pragma: ");
		
		if(self::$use_x_sendfile) {
			session_write_close();
			header('X-Sendfile: '.$file_path);
			exit();
		} elseif($filePointer = @fopen($file_path, 'rb')) {
			session_write_close();
			$this->flush();
			// Push the file while not EOF and connection exists
			while(!feof($filePointer) && !connection_aborted()) {
				//error_log("Sending $chunck_size_kb kb");
				print(fread($filePointer, 1024 * self::$chunck_size_kb));
				$this->flush();
			}
			fclose($filePointer);
			exit();
		} else {
			// Edge case - either not found anymore or can't read
			return $this->fileNotFound();
		}
	}
	
	/**
	 * Flush the output buffer to the server (if possible).
	 * @see http://nz.php.net/manual/en/function.flush.php#93531
	 */
	function flush() {
		if(ob_get_length()) {
			@ob_flush();
			@flush();
			@ob_end_flush();
		}
		@ob_start();
	}

}