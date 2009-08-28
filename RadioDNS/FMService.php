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
 * Implementation of RadioDNS_Service for FM Service
 * 
 * Implements RadioDNS_Service object, providing necessary functionality to
 * represent FM Services as a RadioDNS Service Object
 * 
 * @uses      RadioDNS_Service
 * @package   PHP-RadioDNS
 * @author    Andy Buckingham <andy.buckingham@thisisglobal.com>
 * @license   http://www.apache.org/licenses/LICENSE-2.0
 * @link      http://www.radiodns.org/docs
 */
class RadioDNS_FMService extends RadioDNS_Service
{
	/**
	 * Extended Country Code (ECC) and country code value
	 * @var string
	 */
	private $rds_cc_ecc;
	/**
	 * ISO 3166-1 alpha-2 country code value
	 * @var string
	 */
	private $iso3166_country_code;
	/**
	 * Programme Identification (PI) value
	 * @var string
	 */
	private $rds_pi;
	/**
	 * Frequency value
	 * @var float
	 */
	private $frequency;
	
	/**
	 * Constructor for RadioDNS_DABService object
	 * @param string $country
	 * @param string $pi
	 * @param float $frequency
	 */
	public function __construct($country, $pi, $frequency)
	{
		/**
		 * check for required variables
		 */
		if(!isset($country) || !isset($pi) || !isset($frequency))
		{
			return NULL;
		}
		
		/**
		 * country value
		 */
		if(strlen($country) == 2)
		{
			$this->rds_cc_ecc = NULL;
			$this->iso3166_country_code = $country;
		}
		else if(ereg('^[0-9A-F]{3}$', $country))
		{
			$this->rds_cc_ecc = $country;
			$this->iso3166_country_code = NULL;
		}
		else
		{
			trigger_error('Invalid country value. Must be either a ISO 3166-1 alpha-2 country code or valid hexadecimal value of a RDS Country Code concatanated with a RDS Extended Country Code (ECC).');
			return NULL;
		}
		
		/**
		 * pi value
		 */
		if(ereg('^[0-9A-F]{4}$', $pi) && (substr($pi, 0, 1) == substr($this->rds_cc_ecc, 0, 1) || $this->iso3166_country_code != NULL))
		{
			$this->pi = $pi;
		}
		else
		{
			trigger_error('Invalid PI value. Must be a valid hexadecimal RDS Programme Identifier (PI) code and the first character must match the first character of the combined RDS Country Code and RDS Extended Country Code (ECC) value (if supplied).');
			return NULL;
		}
		
		/**
		 * frequency value
		 */
		if((is_float($frequency) || is_int($frequency)) && $frequency >= 76 && $frequency <= 108)
		{
			$this->frequency = $frequency;
		}
		else
		{
			trigger_error('Invalid frequency value. Must be a valid float between the values 76.0 and 108.0.');
			return NULL;
		}
	}
	
	/**
	 * Constructs the RadioDNS FQDN for a FM Service
	 * @return string
	 */
	public function toFQDN()
	{
		$country = $this->rds_cc_ecc ? $this->rds_cc_ecc : $this->iso3166_country_code;
		
		$fqdn = sprintf('%s.%s.%s.fm.radiodns.org', str_pad((string) $this->frequency * 100, 5, '0', STR_PAD_LEFT), $this->pi, $country);
		$fqdn = strtolower($fqdn);
		
		return $fqdn;
	}
}

?>