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
 * Test case for running unit tests related to the AM Service class functions
 * 
 * @uses      PHPUnit_Framework_TestCase
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class AMServiceTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		// load RadioDNS library
		require_once '../RadioDNS.php';
		
		// spoof value for AM service testing
		$this->sid = 'FFFFFF';
		
		// FQDN correct values (correct as of 2009-08-23 16:49:14)
		$this->fqdn_drm = 'ffffff.drm.radiodns.org';
		$this->fqdn_amss = 'ffffff.amss.radiodns.org';
	}
	
	public function testValidDRMTypeArgument()
	{
		$service = new RadioDNS_AMService('drm', $this->sid);
		
		$this->assertEquals('RadioDNS_AMService', get_class($service));
	}
	
	public function testValidAMSSTypeArgument()
	{
		$service = new RadioDNS_AMService('amss', $this->sid);
		
		$this->assertEquals('RadioDNS_AMService', get_class($service));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNoArguments()
	{
		$service = new RadioDNS_AMService();
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testInvalidTypeArgument()
	{
		$service = new RadioDNS_AMService('unknown', $this->sid);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortSIDArgument()
	{
		$service = new RadioDNS_AMService('drm', substr($this->sid, 0, 5));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongSIDArgument()
	{
		$service = new RadioDNS_AMService('drm', str_pad($this->sid, 7, '0'));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexSIDArgument()
	{
		$random_position = rand(0, strlen($this->sid) - 1);
		$non_hex_sid = sprintf('%sX%s', substr($this->sid, 0, $random_position), substr($this->sid, $random_position + 1));
		
		$service = new RadioDNS_AMService('drm', $non_hex_sid);
	}
	
	public function testLowerCaseSIDArgument()
	{
		$lowercase_sid = strtolower($this->sid);
		
		$service = new RadioDNS_AMService('drm', $lowercase_sid);
	}
	
	public function testDRMFQDNOutput()
	{
		$service = new RadioDNS_AMService('drm', $this->sid);
		
		$this->assertEquals($this->fqdn_drm, $service->toFQDN());
	}

	public function testAMSSFQDNOutput()
	{
		$service = new RadioDNS_AMService('amss', $this->sid);
		
		$this->assertEquals($this->fqdn_amss, $service->toFQDN());
	}
}

?>
