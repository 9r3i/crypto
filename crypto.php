<?php
/* crypto
 * ~ an openssl encryption
 * class-uri : https://github.com/9r3i/crypto
 * license   : https://github.com/9r3i/crypto/blob/master/license.txt
 * 
 * authored by 9r3i
 * github.com/9r3i
 * 
 * ~ version 1.0 - september 21st 2015
 * ~ version 1.1 - may 23rd 2016
 *   + change parameter $i (variable $method), to string of openssl method
 *     the reason is the different of php version
 *   + fix forgotten catch in try and throw exception
 * ~ version 1.2 - may 30th 2016
 *   + create new function check
 *     to check require functions and defined constants
 *     because some server does not load openssl extension properly
 * ~ version 1.3 - july 23rd 2016
 *   + add variable checked
 *     to make sure the functions are checked
 *   + add function setMethod, getMethod and setEncode
 *     there is some reason for executing in the middle of called class
 * ~ version 1.3.1 - july 23rd 2016
 *   + fix function setMethod as to get through cipher methods
 * 
 * 
 * Usage:
 * $cr = new \crypto($method,$encode);
 * $cr->encrypt($string,$key);
 * $cr->decrypt($string,$key);
 * $cr->check(); // check server compatiblity as version 1.2
 * $cr->setMethod('AES-192-CBC'); // set new method as version 1.3
 * 
 * 
 * $method -> string of method (as version 1.1), as methods are got by $cr->methods;
 * $encode -> boolean, encoded by base64_encode function
 * $string -> string, raw binary string to encrypt or decrypt
 * $key    -> string of personal key or password
 * 
 * PS: this class can use OPENSSL_ZERO_PADDING or OPENSSL_RAW_DATA
 *     but i prefer OPENSSL_RAW_DATA in this case 
 */

class crypto{
  public $version='1.3.1';
  public $methods;
  public $errors=array();
  private $method;
  private $encode;
  private $checked=false;
  function __construct($i=null,$e=false){
    if($this->check()){
      $this->methods=\openssl_get_cipher_methods();
      $this->method=isset($i)&&in_array($i,$this->methods)?$i:'AES-128-CBC';
      $this->encode=is_bool($e)&&$e?true:false;
      $this->checked=true;
    }
  }
  public function getMethod($i=null){
    return $this->method;
  }
  public function setMethod($i=null){
    $this->method=isset($i)&&in_array($i,\openssl_get_cipher_methods())?$i:'AES-128-CBC';
    return true;
  }
  public function setEncode($e=false){
    $this->encode=is_bool($e)&&$e?true:false;
    return true;
  }
  public function check(){
    $r=true;
    $f=array(
      'openssl_get_cipher_methods',
      'openssl_cipher_iv_length',
      'openssl_random_pseudo_bytes',
      'openssl_encrypt',
      'openssl_decrypt',
      'mb_substr',
    );
    foreach($f as $t){
      if(!function_exists('\\'.$t)){
        $this->errors[]='function '.$t.' does not exist';
        $r=false;
      }
    }
    if(!defined('OPENSSL_RAW_DATA')){
      $this->errors[]='OPENSSL_RAW_DATA is not defined';
      $r=false;
    }
    return $r;
  }
  public function encrypt($s=null,$c=null){
    if($this->checked){
      $n=\openssl_cipher_iv_length($this->method);
      $o=\openssl_random_pseudo_bytes($n);
      $t=\openssl_encrypt((string)$s,$this->method,$c,OPENSSL_RAW_DATA,$o);
      return $this->encode?\base64_encode($o.$t):$o.$t;
    }return false;
  }
  public function decrypt($s=null,$c=null){
    if($this->checked){
      if($this->encode){try{
        $s=\base64_decode((string)$s,true);
        if($s===false){throw new \Exception('Decryption failure');}
      }catch(\Exception $e){
        return false;
      }}
      $n=\openssl_cipher_iv_length($this->method);
      $o=\mb_substr((string)$s,0,$n,'8bit');
      $t=\mb_substr((string)$s,$n,null,'8bit');
      return \openssl_decrypt($t,$this->method,$c,OPENSSL_RAW_DATA,$o);
    }return false;
  }
}
