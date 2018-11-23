<?php
/* 9r3i\crypto
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
 * ~ version 1.3.2 - april 21st 2017
 *   + add function getMethods
 *   + change version to constant
 *   + change methods as private property
 *   + change function check as private function
 * ~ version 1.3.3 - november 23rd 2018
 *   + default change to aes-128-cbc (be lower-cased)
 *   + all methods change to lower case as php version 7.2.x
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
  const version='1.3.3';      // constant of class version
  public $errors;             // array of errors
  private $methods;           // array of available methods
  private $method;            // string of encryption method; default: aes-128-cbc
  private $encode;            // bool of encode; default: false
  private $checked=false;     // bool of system compatibility
  function __construct($i=null,$e=false){
    $this->errors=array();
    if($this->check()){
      $this->methods=\openssl_get_cipher_methods();
      $this->method=is_string($i)
        &&in_array(strtolower($i),$this->methods)
        ?strtolower($i):'aes-128-cbc';
      $this->encode=is_bool($e)&&$e?true:false;
      $this->checked=true;
    }return $this;
  }
  public function getMethods(){
    return $this->methods;
  }
  public function getMethod($i=null){
    return $this->method;
  }
  public function setMethod($i=null){
    $this->method=is_string($i)
      &&in_array(strtolower($i),$this->methods)
      ?strtolower($i):'aes-128-cbc';
    return true;
  }
  public function setEncode($e=false){
    $this->encode=is_bool($e)&&$e?true:false;
    return true;
  }
  public function encrypt($s=null,$c=null){
    if($this->checked&&is_string($s)){
      $c=is_string($c)?$c:null;
      $n=\openssl_cipher_iv_length($this->method);
      $o=\openssl_random_pseudo_bytes($n);
      $t=\openssl_encrypt((string)$s,$this->method,$c,OPENSSL_RAW_DATA,$o);
      return $this->encode?\base64_encode($o.$t):$o.$t;
    }return false;
  }
  public function decrypt($s=null,$c=null){
    if($this->checked&&is_string($s)){
      if($this->encode){try{
        $s=\base64_decode((string)$s,true);
        if($s===false){throw new \Exception('Decryption failure');}
      }catch(\Exception $e){
        return false;
      }}
      $c=is_string($c)?$c:null;
      $n=\openssl_cipher_iv_length($this->method);
      $o=\mb_substr((string)$s,0,$n,'8bit');
      $t=\mb_substr((string)$s,$n,null,'8bit');
      return \openssl_decrypt($t,$this->method,$c,OPENSSL_RAW_DATA,$o);
    }return false;
  }
  private function check(){
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
    }return $r;
  }
}
