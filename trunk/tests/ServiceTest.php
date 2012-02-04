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


/**
 * Test case for running unit tests related to the abstract Service class 
 * functions
 * 
 * @uses      PHPUnit_Framework_TestCase
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class ServiceTest extends PHPUnit_Framework_TestCase
{
	public function setUp()
	{
		// load RadioDNS library
		require_once '../RadioDNS.php';
		
		/*
		// DRM/AMSS AM test service (none currently listed within RadioDNS name server as of 2009-08-27)
		$this->am_service = new RadioDNS_AMService(NULL, NULL);
		$this->am_authoritative_fqdn = NULL;
		$this->am_applications = NULL;
		*/
		
		// DAB Digital Radio test service (95.8 Capital FM, London, UK - last observed: 2012-02-03)
		$this->dab_service = new RadioDNS_DABService('CE1', 'C185', 'C586', 0);
		$this->dab_authoritative_fqdn = 'rdns.musicradio.com';
		$this->dab_applications = array(
			'radioepg' => array(
			array(
					'target' => 'epg.musicradio.com',
					'port' => 80,
					'priority' => 0,
					'weight' => 100
				)
			),
						'radiotag' => array(
			array(
					'target' => 'tag.musicradio.com',
					'port' => 80,
					'priority' => 0,
					'weight' => 100
				)
			),
			'radiovis' => array(
				array(
					'target' => 'vis.musicradio.com',
					'port' => 61613,
					'priority' => 0,
					'weight' => 100
				)
			)
		);
		
		// FM test service (95.8 Capital FM, London, UK - last observed: 2012-02-03)
		$this->fm_service = new RadioDNS_FMService('CE1', 'C586', 95.8);
		$this->fm_authoritative_fqdn = 'rdns.musicradio.com';
		$this->fm_applications = array(
			'radioepg' => array(
			array(
					'target' => 'epg.musicradio.com',
					'port' => 80,
					'priority' => 0,
					'weight' => 100
				)
			),
			'radiotag' => array(
			array(
					'target' => 'tag.musicradio.com',
					'port' => 80,
					'priority' => 0,
					'weight' => 100
				)
			),
			'radiovis' => array(
			array(
					'target' => 'vis.musicradio.com',
					'port' => 61613,
					'priority' => 0,
					'weight' => 100
			)
			)
		);
		
		/*
		// HD Radio test service (none currently listed with RadioDNS name server as of 2009-08-27)
		$this->hd_service = new RadioDNS_HDService(NULL, NULL);
		$this->fm_authoritative_fqdn = NULL;
		$this->fm_applications = NULL:
		*/
	}
	
	public function no_testAMAuthoritativeFQDNResolution()
	{
		$this->assertEquals($this->am_authoritative_fqdn, $this->am_service->resolveAuthoritativeFQDN());
	}
	
	public function testDABAuthoritativeFQDNResolution()
	{
		$this->assertEquals($this->dab_authoritative_fqdn, $this->dab_service->resolveAuthoritativeFQDN());
	}
	
	public function testFMAuthoritativeFQDNResolution()
	{
		$this->assertEquals($this->fm_authoritative_fqdn, $this->fm_service->resolveAuthoritativeFQDN());
	}
	
	public function no_testHDAuthoritativeFQDNResolution()
	{
		$this->assertEquals($this->hd_authoritative_fqdn, $this->hd_service->resolveAuthoritativeFQDN());
	}
	
	public function providerApplications()
    {
		return array
		(
			array('radioepg', 'TCP'),
			array('radiotag', 'TCP'),
			array('radiovis', 'TCP')
		);
    }
    
	/**
	 * @dataProvider providerApplications
	 */
	public function no_testAMApplicationResolution($application_id, $transport_protocol)
	{
		$response = $this->am_service->resolveApplication($application_id, $transport_protocol);
		if(array_key_exists($application_id, $this->am_applications))
		{
			$this->assertEquals($this->am_applications[$application_id], $response);
		}
		else
		{
			$this->assertFalse($response);
		}
	}
	
    /**
	 * @dataProvider providerApplications
	 */
	public function testDABApplicationResolution($application_id, $transport_protocol)
	{
		$response = $this->dab_service->resolveApplication($application_id, $transport_protocol);
		if(array_key_exists($application_id, $this->dab_applications))
		{
			$this->assertEquals($this->dab_applications[$application_id], $response);
		}
		else
		{
			$this->assertFalse($response);
		}
	}
	
	/**
	 * @dataProvider providerApplications
	 */
	public function testFMApplicationResolution($application_id, $transport_protocol)
	{
		$response = $this->fm_service->resolveApplication($application_id, $transport_protocol);
		if(array_key_exists($application_id, $this->fm_applications))
		{
			$this->assertEquals($this->fm_applications[$application_id], $response);
		}
		else
		{
			$this->assertFalse($response);
		}
	}
	
	/**
	 * @dataProvider providerApplications
	 */
	public function no_testHDApplicationResolution($application_id, $transport_protocol)
	{
		$response = $this->hd_service->resolveApplication($application_id, $transport_protocol);
		if(array_key_exists($application_id, $this->hd_applications))
		{
			$this->assertEquals($this->hd_applications[$application_id], $response);
		}
		else
		{
			$this->assertFalse($response);
		}
	}
}

?>
