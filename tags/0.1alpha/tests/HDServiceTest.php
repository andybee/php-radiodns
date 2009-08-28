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
 * @version   0.1alpha
 * @link      http://www.radiodns.org/docs
 */

require_once 'PHPUnit/Framework.php';

/**
 * Test case for running unit tests related to the HD Service class functions
 * 
 * @uses      PHPUnit_Framework_TestCase
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class HDServiceTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		// load RadioDNS library
		require_once '../RadioDNS.php';
		
		// values for HD service testing taken from ??? (???, ???)
		$this->cc = '567';
		$this->tx = '01234';
		
		// FQDN correct values (correct as of 2009-08-23 16:49:14)
		$this->fqdn = '01234.567.hd.radiodns.org';
	}
	
	public function testValidArguments()
	{
		$service = new RadioDNS_HDService($this->cc, $this->tx);
		
		$this->assertEquals('RadioDNS_HDService', get_class($service));
	}

	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNoArguments()
	{
		$service = new RadioDNS_HDService();
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortCCArgument()
	{
		$service = new RadioDNS_HDService(substr($this->cc, 0, 2), $this->tx);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongCCArgument()
	{
		$service = new RadioDNS_HDService(str_pad($this->cc, 4, '0'), $this->tx);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexCCArgument()
	{
		$random_position = rand(0, strlen($this->cc) - 1);
		$non_hex_cc = sprintf('%sX%s', substr($this->cc, 0, $random_position), substr($this->cc, $random_position + 1));
		
		$service = new RadioDNS_HDService($non_hex_cc, $this->tx);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortTXArgument()
	{
		$service = new RadioDNS_HDService($this->cc, substr($this->tx, 0, 4));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongTXArgument()
	{
		$service = new RadioDNS_HDService($this->cc, str_pad($this->tx, 6, '0'));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexTXArgument()
	{
		$random_position = rand(0, strlen($this->tx) - 1);
		$non_hex_tx = sprintf('%sX%s', substr($this->tx, 0, $random_position), substr($this->tx, $random_position + 1));
		
		$service = new RadioDNS_HDService($this->cc, $non_hex_tx);
	}
	
	public function testFQDNOutput()
	{
		$service = new RadioDNS_HDService($this->cc, $this->tx);
		
		$this->assertEquals($this->fqdn, $service->toFQDN());
	}
}

?>