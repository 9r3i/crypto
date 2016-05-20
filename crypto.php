<?php
/* crypto
 * ~ openssl encryption
 * class-uri : https://github.com/9r3i/crypto
 * license   : https://github.com/9r3i/crypto/blob/master/license.txt
 * 
 * authored by 9r3i
 * github.com/9r3i
 * 
 * ~ version 1.0 - september 21st 2015
 * 
 * Usage:
 * $cr = new crypto($method,$encode);
 * $cr->encrypt($string,$key);
 * $cr->decrypt($string,$key);
 * 
 * $method -> integer from 0 to 181 as methods are got by $cr->methods;
 * $encode -> boolean, encoded by base64_encode function
 * $string -> string, raw binary string to encrypt or decrypt
 * $key    -> string of personal key or password
 * 
 * PS: this class can use OPENSSL_ZERO_PADDING or OPENSSL_RAW_DATA
 *     but i prefer OPENSSL_RAW_DATA in this case 
 */
class crypto{
  public $version='1.0';
  public $methods;
  private $method;
  private $encode;
  function __construct($i=0,$e=false){
    $this->methods=openssl_get_cipher_methods();
    $this->method=isset($i,$this->methods[$i])?$this->methods[$i]:$this>methods[0];
    $this->encode=isset($e)&&$e?true:false;
  }
  public function encrypt($s,$c){
    $n=openssl_cipher_iv_length($this->method);
    $o=openssl_random_pseudo_bytes($n);
    $t=openssl_encrypt($s,$this->method,$c,OPENSSL_RAW_DATA,$o);
    return $this->encode?base64_encode($o.$t):$o.$t;
  }
  public function decrypt($s,$c){
    if($this->encode){$s=base64_decode($s,true);
      if($s===false){throw new Exception('Decryption failure');}
    }
    $n=openssl_cipher_iv_length($this->method);
    $o=mb_substr($s,0,$n,'8bit');
    $t=mb_substr($s,$n,null,'8bit');
    return openssl_decrypt($t,$this->method,$c,OPENSSL_RAW_DATA,$o);
  }
}
