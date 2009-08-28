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
 * Implementation of RadioDNS_Service for DAB Service
 * 
 * Implements RadioDNS_Service object, providing necessary functionality to 
 * represent DAB Digital Radio Services (including data services) as a RadioDNS
 * Service Object
 * 
 * @uses      RadioDNS_Service
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class RadioDNS_DABService extends RadioDNS_Service
{
	/**
	 * Extended Country Code (ECC) value
	 * @var string
	 */
	private $ecc;
	/**
	 * Ensemble Identifier (EId) value
	 * @var string
	 */
	private $eid;
	/**
	 * Service Identifer (SId) value
	 * @var string
	 */
	private $sid;
	/**
	 * Service Component Identifer within the Service (SCIdS) value
	 * @var string
	 */
	private $scids;
	/**
	 * X-PAD Applicaton Type (AppTy) and User Applicaton type value
	 * @var string
	 */
	private $xpad;
	/**
	 * Packet Address (PA) value
	 * @var integer
	 */
	private $pa;
	
	/**
	 * Constructor for RadioDNS_DABService object
	 * @param string $ecc
	 * @param string $eid
	 * @param string $sid
	 * @param string $scids
	 * @param string|integer $data
	 */
	public function __construct($ecc, $eid, $sid, $scids, $data = NULL)
	{
		/**
		 * check for required variables
		 */
		if(!isset($ecc) || !isset($eid) || !isset($sid) || !isset($scids))
		{
			return NULL;
		}
		
		/**
		 * validate ecc value
		 */
		if(ereg('^[0-9A-F]{3}$', $ecc))
		{
			$this->ecc = $ecc;
		}
		else
		{
			trigger_error('Invalid Extended Country Code (ECC) value. Must be a valid 3-character hexadecimal.');
			return NULL;
		}
		
		/**
		 * validate eid value
		 */
		if(ereg('^[0-9A-F]{4}$', $eid))
		{
			$this->eid = $eid;
		}
		else
		{
			trigger_error('Invalid Ensembled Identifier (EId) value. Must be a valid 4-character hexadecimal.');
			return NULL;
		}
		
		/**
		 * validate sid value
		 */
		if(ereg('^[0-9A-F]{4}$|^[0-9A-F]{8}$', $sid))
		{
			$this->sid = $sid;
		}
		else
		{
			trigger_error('Invalid Service Identifier (SId) value. Must be a valid 4 or 8-character hexadecimal.');
			return NULL;
		}
		
		/**
		 * validate scids value
		 */
		if(ereg('^[0-9A-F]{1}$|^[0-9A-F]{3}$', $scids))
		{
			$this->scids = $scids;
		}
		else
		{
			trigger_error('Invalid Service Component Identifer within the Service (SCIdS) value. Must be a valid 3-character hexadecimal.');
			return NULL;
		}
		
		/**
		 * identify data value type and validate
		 */
		if($data != NULL) {
			if(ereg('^[0-9A-F]{2}-[0-9A-F]{3}$', $data))
			{
				$this->xpad = $data;
				$this->pa = NULL;
			}
			else if(is_int($data) && $data >= 0 && $data <= 1023)
			{
				$this->xpad = NULL;
				$this->pa = $data;
			}
			else
			{
				trigger_error('Invalid data value. Must be either a valid X-PAD Applicaton Type (AppTy) and User Applicaton type (UAtype) hexadecimal or Packet Address integer.');
				return NULL;
			}
		}
	}
	
	/**
	 * Constructs the RadioDNS FQDN for a DAB Digital Radio Service
	 * @return string
	 */
	public function toFQDN()
	{
		$fqdn = sprintf('%s.%s.%s.%s.dab.radiodns.org', $this->scids, $this->sid, $this->eid, $this->ecc);
		if($this->xpad || $this->pa)
		{
			$fqdn = sprintf('%s.%s', $this->xpad ? $this->xpad : $this->pa, $fqdn);
		}
		$fqdn = strtolower($fqdn);
		
		return $fqdn;
	}
}

?>