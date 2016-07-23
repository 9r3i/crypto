# crypto

[![Author](https://img.shields.io/badge/author-9r3i-lightgrey.svg)](https://github.com/9r3i "9r3i")
[![License](https://img.shields.io/github/license/9r3i/crypto.svg)](https://github.com/9r3i/crypto/blob/master/license.txt "License")
[![Forks](https://img.shields.io/github/forks/9r3i/crypto.svg)](https://github.com/9r3i/crypto/network "Forks")
[![Stars](https://img.shields.io/github/stars/9r3i/crypto.svg)](https://github.com/9r3i/crypto/stargazers "Stars")
[![Issues](https://img.shields.io/github/issues/9r3i/crypto.svg)](https://github.com/9r3i/crypto/issues "Issues")
[![Releases](https://img.shields.io/github/release/9r3i/crypto.svg)](https://github.com/9r3i/crypto/releases "Releases")
[![Donate](https://img.shields.io/badge/paypal-donate-yellowgreen.svg)](https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=QZKZVZPBAC538 "Donate")

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

