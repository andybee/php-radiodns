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
 * Require the individual class declarations that make up the PHP-RadioDNS
 * library.
 */
require_once('RadioDNS/Service.php');
require_once('RadioDNS/AMService.php');
require_once('RadioDNS/DABService.php');
require_once('RadioDNS/FMService.php');
require_once('RadioDNS/HDService.php');

/**
 * RadioDNS utility class
 * 
 * The RadioDNS class provides a set of utility functions that instantiate a 
 * service, query it and return an associative array containing the results.
 * 
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class RadioDNS
{
	/**
	 * Holds an array of arrays depicting the currently known RadioDNS-
	 * advertised applications as of 2009-08-28
	 * @var array
	 */
	protected $KNOWN_APPLICATIONS = array
	(
		array('radioepg', 'TCP'),
		array('radiotag', 'TCP'),
		array('radiovis', 'TCP')
	);
	
	/**
	 * Lookup the broadcast parameters of a AM (DRM or AMSS) service. Returns a
	 * results associative array.
	 * @param string $type
	 * @param string $sid
	 * @return array
	 */
	public function lookupAMService($type = NULL, $sid = NULL)
	{
		$service = new RadioDNS_AMService($type, $sid);
		
		return $this->lookupService($service);
	}
	
	/**
	 * Lookup the broadcast parameters of a DAB Digital Radio service, 
	 * including those with X-PAD Applicaton Type (AppTy) and User Applicaton 
	 * type (UAtype) hexadecimal or Packet Address integer values. Returns a 
	 * results associative array.
	 * @param string $ecc
	 * @param string $eid
	 * @param string $sid
	 * @param string $scids
	 * @param string $data
	 * @return array
	 */
	public function lookupDABService($ecc = NULL, $eid = NULL, $sid = NULL, $scids = NULL, $data = NULL)
	{
		$service = new RadioDNS_DABService($ecc, $eid, $sid, $scids, $data);
		
		return $this->lookupService($service);
	}
	
	/**
	 * Lookup the broadcast parameters of a FM service. Returns a results 
	 * associative array.
	 * @param string $country
	 * @param string $pi
	 * @param float $frequency
	 * @return unknown_type
	 */
	public function lookupFMService($country = NULL, $pi = NULL, $frequency = NULL)
	{
		$service = new RadioDNS_FMService($country, $pi, $frequency);
		
		return $this->lookupService($service);
	}
	
	/**
	 * Lookup the broadcast parameters of a HD Radio service. Returns a results 
	 * associatve array.
	 * @param string $tx
	 * @param string $cc
	 * @return unknown_type
	 */
	public function lookupHDService($tx = NULL, $cc = NULL)
	{
		$service = new RadioDNS_HDService($tx, $cc);
		
		return $this->lookupService($service);
	}
	
	/**
	 * Takes a RadioDNS Service object. Returns a results associative array.
	 * @access private
	 * @param RadioDNS_Service $service
	 * @return array
	 */
	private function lookupService(RadioDNS_Service $service)
	{
		$results['authorative_fqdn'] = $service->resolveAuthorativeFQDN();
		if(!$results['authorative_fqdn']) { return FALSE; }
		
		$results['applications'] = array();
		foreach($this->KNOWN_APPLICATIONS as $application)
		{
			list($application_id, $transport) = $application;
			$application_result = $service->resolveApplication($application_id, $transport);
			
			$results['applications'][$application_id]['supported'] = (bool) $application_result;
			if($application_result)
			{
				$results['applications'][$application_id]['servers'] = $application_result;
			}
		}
		
		return $results;
	}
}

?>