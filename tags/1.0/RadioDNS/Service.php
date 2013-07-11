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
	 * Holds a previously resolved authoritative FQDN for this service to avoid 
	 * the need to lookup again
	 * @access private
	 * @var unknown_type
	 */
	private $cached_authoritative_fqdn;
	
	/**
	 * Performs a CNAME DNS record lookup request using the Net_DNS_Resolver 
	 * object held in {@link $dns_resolver} to return the authoritative FQDN for 
	 * a service. The retrieved value is also held in 
	 * {@link $cached_authoritative_fqdn} for future requests.
	 */
	public function resolveAuthoritativeFQDN()
	{
		// perform DNS resolution for CNAME record
		$response = dns_get_record($this->toFQDN(), DNS_CNAME);
		
		// check for valid response
		if(!$response) { return FALSE; }
		if(!$response[0]['target']) { return FALSE; }
		if($response[0]['type'] != 'CNAME') { return FALSE; }
		
		// cache result to avoid recurring queries for the same service
		$this->cached_authoritative_fqdn = $response[0]['target'];
		
		return $this->cached_authoritative_fqdn;
	}
	
	/**
	 * Performs a SRV DNS record lookup for the supplied application ID and 
	 * transport protocol against the previously resolved authoritative FQDN
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
		 * obtain authoritative FQDN either from cache or new query
		 */
		$authoritative_fqdn = isset($this->cached_authoritative_fqdn) ? $this->cached_authoritative_fqdn : $this->resolveAuthoritativeFQDN();
		if(!$authoritative_fqdn) { return FALSE; }
		
		$application_fqdn = sprintf('_%s._%s.%s', strtolower($application_id), strtolower($transport_protocol), $authoritative_fqdn);
		
		/*
		 * perform DNS resolution for SRV record
		 */
		$response = dns_get_record($application_fqdn, DNS_SRV);
		
		/*
		 * check for valid response
		 */
		if(!$response) { return FALSE; }
		if(count($response) == 0) { return FALSE; }
		
		/*
		 * prepare results array
		 */
		$results = array();
		foreach($response as $answer)
		{
			if($answer['type'] != 'SRV') { continue; }
			array_push($results, array(
				'target' => $answer['target'],
				'port' => $answer['port'],
				'priority' => $answer['pri'],
				'weight' => $answer['weight'],
			));
		}
		
		return $results;
	}
}

?>
