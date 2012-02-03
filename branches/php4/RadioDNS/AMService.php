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
 * Implementation of RadioDNS_Service for AM Service
 * 
 * Implements RadioDNS_Service object, providing necessary functionality to 
 * represent AM Services (both DRM and AMSS) as a RadioDNS Service Object
 * 
 * @uses      RadioDNS_Service
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class RadioDNS_AMService extends RadioDNS_Service
{
	/**
	 * Type of AM Service (either 'drm' or 'amss')
	 * @var string
	 */
	private $type;
	/**
	 * SID value for the AM Service
	 * @var string
	 */
	private $sid;
	
	/**
	 * Constructor for RadioDNS_AMService object
	 * @param string $type
	 * @param string $sid
	 */
	public function __construct($type, $sid)
	{
		/**
		 * check for required variables
		 */
		if(!isset($type) || !isset($sid))
		{
			return NULL;
		}
		
		/**
		 * validate type value
		 */
		if($type == 'drm' || $type == 'amss')
		{
			$this->type = $type;
		}
		else
		{
			trigger_error('Invalid type value. Must be either \'drm\' (Digital Radio Mondiale) or \'amss\' (AM Signalling System).');
			return NULL;
		}
		
		/**
		 * validate sid value
		 */
		if(ereg('^[0-9A-F]{6}$', $sid))
		{
			$this->sid = $sid;
		}
		else
		{
			trigger_error('Invalid Service Identifier (SId) value. Must be a valid 6-character hexadecimal.');
			return NULL;
		}
	}
	
	/**
	 * Constructs the RadioDNS FQDN for a AM Service
	 * @return string
	 */
	public function toFQDN()
	{
		$fqdn = sprintf('%s.%s.radiodns.org', $this->sid, $this->type);
		$fqdn = strtolower($fqdn);
		
		return $fqdn;
	}
}

?>