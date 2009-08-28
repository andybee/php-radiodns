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
 * Implementation of RadioDNS_Service for HD Service
 * 
 * Implements RadioDNS_Service object, providing necessary functionality to 
 * represent HD Radio Services (both DRM and AMSS) as a RadioDNS Service Object
 * 
 * @uses      RadioDNS_Service
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class RadioDNS_HDService extends RadioDNS_Service
{
	/**
	 * Transmiter identifier value
	 * @var string
	 */
	private $tx;
	/**
	 * Country code value
	 * @var string
	 */
	private $cc;
	
	/**
	 * Constructor for RadioDNS_HDService
	 * @param string $cc
	 * @param string $tx
	 */
	public function __construct($cc, $tx)
	{
		/**
		 * check for required variables
		 */
		if(!isset($cc) || !isset($tx))
		{
			return NULL;
		}
		
		/**
		 * cc value
		 */
		if(ereg('^[0-9A-F]{3}$', $cc))
		{
			$this->cc = $cc;
		}
		else
		{
			trigger_error('Invalid Country Code (CC) value. Must be a valid 3-character hexadecimal Country Code.');
			return NULL;
		}
		
		/**
		 * tx value
		 */
		if(ereg('^[0-9A-F]{5}$', $tx))
		{
			$this->tx = $tx;
		}
		else
		{
			trigger_error('Invalid Transmitter Identifier (TX) value. Must be a valid 5-character hexadecimal.');
			return NULL;
		}
	}
	
	/**
	 * Constructs the RadioDNS FQDN for a HD Radio Service
	 * @return string
	 */
	public function toFQDN()
	{
		$fqdn = sprintf('%s.%s.hd.radiodns.org', $this->tx, $this->cc);
		$fqdn = strtolower($fqdn);
		
		return $fqdn;
	}
}

?>