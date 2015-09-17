# Introduction #

A PHP library that facilitates the resolution of an authoritative Fully Qualified Domain Name (FQDN) from the broadcast parameters of an audio service.

From this FQDN it is then possible to discover the advertisement of IP-based applications provided in relation to the queried audio service.

For more information about RadioDNS, please see the official documentation: http://radiodns.org/docs

# Installation #

This library depends on the Net\_DNS library. See http://pear.php.net/package/Net_DNS/ for download and installation details.

# Getting Started #

Include the radiodns.php file and utilise the RadioDNS object which provides utility functions to lookup audio services.

```
require_once 'php-radiodns/RadioDNS.php';

$rdns = new RadioDNS();
$rsp = $rdns->lookupFMService('CE1', 'C586', 95.8);

print_r($rsp);
```

## See Also ##

  * [PyRadioDNS](http://code.google.com/p/pyradiodns/)
  * [perl-RadioDNS](http://code.google.com/p/perl-radiodns/)