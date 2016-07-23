# crypto

[![Donate](https://camo.githubusercontent.com/11b2f47d7b4af17ef3a803f57c37de3ac82ac039/68747470733a2f2f696d672e736869656c64732e696f2f62616467652f70617970616c2d646f6e6174652d79656c6c6f772e737667)](https://www.paypal.com/cgi-bin/webscr?cmd=_donations&business=5VLYA8SDV3CTG&lc=ID&item_name=Software%20Developer&currency_code=USD&bn=PP%2dDonationsBF%3abtn_donateCC_LG%2egif%3aNonHosted "Donate")

An openssl encryption

This is a class, an openssl encryption, a simple one. Hope it can help you for encryption.

To use this class, please remind the license. Thank you.

--9r3i


# Usage
```php
$method = 'AES-256-XTS'; // what methods are available using $cr->methods; as version 1.1 become a string
$encode = true;
$string = file_get_contents(__FILE__); // get content to be encrypted
$key = 'my_password'; // a password key

/* call the class */
$cr = new \crypto($method,$encode);
$cr->encrypt($string,$key); // encrypting
$cr->decrypt($string,$key); // decrypting
/* additional */
$cr->check(); // check server compatiblity as version 1.2
$cr->setMethod('AES-192-CBC'); // set new method as version 1.3
$cr->encrypt($string,$key); // encrypting with another method
```

