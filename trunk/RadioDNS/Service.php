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
 * Abstract class for all Service objects
 * 
 * Abstract class to provide core functions for all RadioDNS Service objects
 * 
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
abstract class RadioDNS_Service
{
	/**
	 * Holds a single Net_DNS DNS resolution object for the life of the object
	 * @access private
	 * @var Net_DNS_Resolver
	 */
	private $dns_resolver;
	/**
	 * Holds a previously resolved authorative FQDN for this service to avoid 
	 * the need to lookup again
	 * @access private
	 * @var unknown_type
	 */
	private $cached_authorative_fqdn;
	
	/**
	 * Called when a DNS resolver object is not held in the 
	 * {@link $dns_resolver} variable
	 */
	private function setupDNSResolver()
	{
		require_once('Net/DNS.php');
		/*
		 * hack to fix a problem with Net_DNS library using and unsetting 
		 * GLOBAL with each new resolver
		 */
		$GLOBALS['_Net_DNS_packet_id'] = mt_rand(0, 65535);
		
		$this->dns_resolver = new Net_DNS_Resolver();
	}
	
	/**
	 * Performs a CNAME DNS record lookup request using the Net_DNS_Resolver 
	 * object held in {@link $dns_resolver} to return the authorative FQDN for 
	 * a service. The retrieved value is also held in 
	 * {@link $cached_authorative_fqdn} for future requests.
	 */
	public function resolveAuthorativeFQDN()
	{
		// perform DNS resolution for CNAME record
		if(!$this->dns_resolver) { $this->setupDNSResolver(); }
		$response = $this->dns_resolver->query($this->toFQDN(), 'CNAME');
		
		// check for valid response
		if(!$response) { return FALSE; }
		if(!$response->answer[0]->cname) { return FALSE; }
		
		// cache result to avoid recurring queries for the same service
		$this->cached_authorative_fqdn = $response->answer[0]->cname;
		
		return $this->cached_authorative_fqdn;
	}
	
	/**
	 * Performs a SRV DNS record lookup for the supplied application ID and 
	 * transport protocol against the previously resolved authorative FQDN
	 * @param string $application_id
	 * @param string $transport_protocol
	 * @return array
	 */
	public function resolveApplication($application_id, $transport_protocol='TCP')
	{
		/*
		 * check for required variables
		 */ 
		if(!isset($application_id)) { return FALSE; }
		
		/*
		 * obtain authorative FQDN either from cache or new query
		 */
		$authorative_fqdn = isset($this->cached_authorative_fqdn) ? $this->cached_authorative_fqdn : $this->resolveAuthorativeFQDN();
		if(!$authorative_fqdn) { return FALSE; }
		
		$application_fqdn = sprintf('_%s._%s.%s', strtolower($application_id), strtolower($transport_protocol), $authorative_fqdn);
		
		/*
		 * perform DNS resolution for SRV record
		 */
		if(!$this->dns_resolver) { $this->setupDNSResolver(); }
		$response = $this->dns_resolver->query($application_fqdn, 'SRV');
		
		/*
		 * check for valid response
		 */
		if(!$response) { return FALSE; }
		if(count($response->answer) == 0) { return FALSE; }
		
		/*
		 * prepare results array
		 */
		$results = array();
		foreach($response->answer as $answer)
		{
			array_push($results, array(
				'target' => $response->answer[0]->target,
				'port' => $response->answer[0]->port,
				'priority' => $response->answer[0]->preference,
				'weight' => $response->answer[0]->weight,
			));
		}
		
		return $results;
	}
}

?>