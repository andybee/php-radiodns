<?php

/**
 * PHP-RadioDNS
 * A PHP library that facilitates the resolution of an authoritative Fully 
 * Qualified Domain Name (FQDN) from the broadcast parameters of an audio 
 * service.
 * From this FQDN it is then possible to discover the advertisement of IP-based
 * applications provided in relation to the queried audio service.
 * For more information about RadioDNS, please see the official documentation: 
 * http://radiodns.org/docs
 * 
 * Copyright 2009 Global Radio UK Limited
 * 
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 * 
 *   http://www.apache.org/licenses/LICENSE-2.0
 * 
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.    
 * See the License for the specific language governing permissions and 
 * limitations under the License.
 * 
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @copyright Copyright (c) 2009, Global Radio UK Limited
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @version   1.0
 * @link      http://www.radiodns.org/docs
 */


/**
 * Test case for running unit tests related to the FM Service class functions
 * 
 * @uses      PHPUnit_Framework_TestCase
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class FMServiceTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		// load RadioDNS library
		require_once '../RadioDNS.php';
		
		// values for FM service testing taken from 95.8 Capital FM (London, UK)
		$this->cc_ecc = 'CE1';
		$this->iso3166 = 'GB';
		$this->pi = 'C586';
		$this->frequency = 95.8;
		
		// FQDN correct values (correct as of 2009-08-23 16:49:14)
		$this->fqdn_cc_ecc = '09580.c586.ce1.fm.radiodns.org';
		$this->fqdn_iso3166 = '09580.c586.gb.fm.radiodns.org';
	}
	
	public function testValidCC_ECCArguments()
	{
		$service = new RadioDNS_FMService($this->cc_ecc, $this->pi, $this->frequency);
		
		$this->assertEquals('RadioDNS_FMService', get_class($service));
	}

	public function testValidISO3166Arguments()
	{
		$service = new RadioDNS_FMService($this->iso3166, $this->pi, $this->frequency);
		
		$this->assertEquals('RadioDNS_FMService', get_class($service));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNoArguments()
	{
		$service = new RadioDNS_FMService();
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortCountryArgument()
	{
		$service = new RadioDNS_FMService(substr($this->cc_ecc, 0, 1), $this->pi, $this->frequency);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongCountryArgument()
	{
		$service = new RadioDNS_FMService(str_pad($this->cc_ecc, 4, '0'), $this->pi, $this->frequency);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexCC_ECCArgument()
	{
		$random_position = rand(0, strlen($this->cc_ecc) - 1);
		$non_hex_cc_ecc = sprintf('%sX%s', substr($this->cc_ecc, 0, $random_position), substr($this->cc_ecc, $random_position + 1));
		
		$service = new RadioDNS_FMService($non_hex_cc_ecc, $this->pi, $this->frequency);
	}

	public function testLowerCaseCC_ECCArgument()
	{
		$lowercase_ecc = strtolower($this->cc_ecc);
		
		$service = new RadioDNS_FMService($lowercase_ecc, $this->pi, $this->frequency);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortPIArgument()
	{
		$service = new RadioDNS_FMService($this->cc_ecc, substr($this->pi, 0, 3), $this->frequency);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongPIArgument()
	{
		$service = new RadioDNS_FMService($this->cc_ecc, str_pad($this->pi, 5, '0'), $this->frequency);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexPIArgument()
	{
		$random_position = rand(0, strlen($this->pi) - 1);
		$non_hex_pi = sprintf('%sX%s', substr($this->pi, 0, $random_position), substr($this->pi, $random_position + 1));
		
		$service = new RadioDNS_FMService($this->cc_ecc, $non_hex_pi, $this->frequency);
	}

	public function testLowerCasePIArgument()
	{
		$lowercase_pi = strtolower($this->pi);
		
		$service = new RadioDNS_FMService($this->cc_ecc, $lowercase_pi, $this->frequency);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLowFrequencyArgument()
	{
		$service = new RadioDNS_FMService($this->cc_ecc, $this->pi, 75.9);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooHighFrequencyArgument()
	{
		$service = new RadioDNS_FMService($this->cc_ecc, $this->pi, 108.1);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonFloatFrequencyArgument()
	{
		$service = new RadioDNS_FMService($this->cc_ecc, $this->pi, 'A');
	}
	
	public function testCC_ECCFQDNOutput()
	{
		$service = new RadioDNS_FMService($this->cc_ecc, $this->pi, $this->frequency);
		
		$this->assertEquals($this->fqdn_cc_ecc, $service->toFQDN());
	}

	public function testISO3166FQDNOutput()
	{
		$service = new RadioDNS_FMService($this->iso3166, $this->pi, $this->frequency);
		
		$this->assertEquals($this->fqdn_iso3166, $service->toFQDN());
	}
}

?>
