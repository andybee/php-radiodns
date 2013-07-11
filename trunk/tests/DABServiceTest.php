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
 * Test case for running unit tests related to the DAB Digital Radio Service 
 * class functions
 * 
 * @uses      PHPUnit_Framework_TestCase
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class DABServiceTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		// load RadioDNS library
		require_once '../RadioDNS.php';
		
		// values for DAB service testing taken from 95.8 Capital FM (London, UK)
		$this->ecc = 'CE1';
		$this->eid = 'C185';
		$this->sid = 'C586';
		$this->scids = '0';
		// spoof data values for testing
		$this->xpad = 'A0-E25';
		$this->pa = 342;
		
		// FQDN correct values (correct as of 2009-08-23 16:49:14)
		$this->fqdn_short_sid_short_scids = '0.c586.c185.ce1.dab.radiodns.org';
		$this->fqdn_long_sid_short_scids = '0.c5860000.c185.ce1.dab.radiodns.org';
		$this->fqdn_short_sid_long_scids = '000.c586.c185.ce1.dab.radiodns.org';
		$this->fqdn_long_sid_long_scids = '000.c5860000.c185.ce1.dab.radiodns.org';
		$this->fqdn_xpad = 'a0-e25.0.c586.c185.ce1.dab.radiodns.org';
		$this->fqdn_pa = '342.0.c586.c185.ce1.dab.radiodns.org';
	}
	
	public function testValidShortSIDShortSCIDSArguments()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids);
		
		$this->assertEquals('RadioDNS_DABService', get_class($service));
	}
	
	public function testValidLongSIDShortSCIDSArguments()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, str_pad($this->sid, 8, '0'), $this->scids);
		
		$this->assertEquals('RadioDNS_DABService', get_class($service));
	}
	
	public function testValidShortSIDLongSCIDSArguments()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, str_pad($this->scids, 3, '0'));
		
		$this->assertEquals('RadioDNS_DABService', get_class($service));
	}
	
	public function testValidLongSIDLongSCIDSArguments()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, str_pad($this->sid, 8, '0'), str_pad($this->scids, 3, '0'));
		
		$this->assertEquals('RadioDNS_DABService', get_class($service));
	}
	
	public function testValidXPADDataArguments()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, $this->xpad);
		
		$this->assertEquals('RadioDNS_DABService', get_class($service));
	}
	
	public function testValidPAArguments()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, $this->pa);
		
		$this->assertEquals('RadioDNS_DABService', get_class($service));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNoArguments()
	{
		$service = new RadioDNS_DABService();
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortECCArgument()
	{
		$service = new RadioDNS_DABService(substr($this->ecc, 0, 2), $this->eid, $this->sid, $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongECCArgument()
	{
		$service = new RadioDNS_DABService(str_pad($this->ecc, 4, '0'), $this->eid, $this->sid, $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexECCArgument()
	{
		$random_position = rand(0, strlen($this->ecc) - 1);
		$non_hex_ecc = sprintf('%sX%s', substr($this->ecc, 0, $random_position), substr($this->ecc, $random_position + 1));
		
		$service = new RadioDNS_DABService($non_hex_ecc, $this->eid, $this->sid, $this->scids);
	}

	public function testLowerCaseECCArgument()
	{
		$lowercase_ecc = strtolower($this->ecc);

		$service = new RadioDNS_DABService($lowercase_ecc, $this->eid, $this->sid, $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortEIDArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, substr($this->eid, 0, 3), $this->sid, $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongEIDArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, str_pad($this->eid, 5, '0'), $this->sid, $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexEIDArgument()
	{
		$random_position = rand(0, strlen($this->eid) - 1);
		$non_hex_eid = sprintf('%sX%s', substr($this->eid, 0, $random_position), substr($this->eid, $random_position + 1));
		
		$service = new RadioDNS_DABService($this->ecc, $non_hex_eid, $this->sid, $this->scids);
	}

	public function testLowerCaseEIDArgument()
	{
		$lowercase_eid = strtolower($this->eid);

		$service = new RadioDNS_DABService($this->ecc, $lowercase_eid, $this->sid, $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortSIDArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, substr($this->sid, 0, 3), $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testInterimLengthSIDArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, str_pad($this->sid, 6, '0'), $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongSIDArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, str_pad($this->sid, 9, '0'), $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexSIDArgument()
	{
		$random_position = rand(0, strlen($this->sid) - 1);
		$non_hex_sid = sprintf('%sX%s', substr($this->sid, 0, $random_position), substr($this->sid, $random_position + 1));
		
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $non_hex_sid, $this->scids);
	}

	public function testLowerCaseSIDArgument()
	{
		$lowercase_sid = strtolower($this->sid);

		$service = new RadioDNS_DABService($this->ecc, $this->eid, $lowercase_sid, $this->scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testInterimSCIDSArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, str_pad($this->scids, 2, '0'));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongSCIDSArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, str_pad($this->scids, 4, '0'));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testNonHexSCIDSArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, 'X');
	}

	public function testLowerCaseSCIDSArgument()
	{
		$lowercase_scids = strtolower($this->scids);

		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $lowercase_scids);
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooShortXPADArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, substr($this->xpad, 0, 3));
	}
	
	/**
     * @expectedException PHPUnit_Framework_Error
     */
	public function testTooLongXPADArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, str_pad($this->sid, 6, '0'));
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testNonHexXPADArgument()
	{
		$random_position = rand(0, 1) ? rand(0, 1) : rand(3, 5);
		$non_hex_xpad = sprintf('%sX%s', substr($this->xpad, 0, $random_position), substr($this->xpad, $random_position + 1));
		
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, $non_hex_xpad);
	}
	
	public function testLowerCaseXPADArgument()
	{
		$lowercase_xpad = strtolower($this->xpad);
		
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, $lowercase_xpad);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testTooLowPAArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, -1);
	}
	
	/**
	 * @expectedException PHPUnit_Framework_Error
	 */
	public function testTooHighPAArgument()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, 1024);
	}
	
	public function testShortSIDShortSCIDSFQDNOutput()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids);
		
		$this->assertEquals($this->fqdn_short_sid_short_scids, $service->toFQDN());
	}
	
	public function testLongSIDShortSCIDSFQDNOutput()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, str_pad($this->sid, 8, '0'), $this->scids);
		
		$this->assertEquals($this->fqdn_long_sid_short_scids, $service->toFQDN());
	}
	
	public function testShortSIDLongSCIDSFQDNOutput()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, str_pad($this->scids, 3, '0'));
		
		$this->assertEquals($this->fqdn_short_sid_long_scids, $service->toFQDN());
	}
	
	public function testLongSIDLongSCIDSFQDNOutput()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, str_pad($this->sid, 8, '0'), str_pad($this->scids, 3, '0'));
		
		$this->assertEquals($this->fqdn_long_sid_long_scids, $service->toFQDN());
	}
	
	public function testXPADDataFQDNOutput()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, $this->xpad);
		
		$this->assertEquals($this->fqdn_xpad, $service->toFQDN());
	}
	
	public function testPADataFQDNOutput()
	{
		$service = new RadioDNS_DABService($this->ecc, $this->eid, $this->sid, $this->scids, $this->pa);
		
		$this->assertEquals($this->fqdn_pa, $service->toFQDN());
	}
}

?>
