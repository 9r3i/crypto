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
 * ~ version 1.1 - may 23rd 2016
 *   + change parameter $i, to string of openssl method
 *     the reason is the different of php version
 *   + fix forgotten catch in try and throw exception
 * ~ version 1.2 - may 30th 2016
 *   + create new function check
 *     to check require functions and defined constants
 *     because some server does not load openssl extension properly
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

/* deema namespace */
namespace deema;

class crypto{
  public $version='1.2';
  public $methods;
  public $errors=array();
  private $method;
  private $encode;
  function __construct($i=null,$e=false){
    if($this->check()){
      $this->methods=\openssl_get_cipher_methods();
      $this->method=isset($i)&&in_array($i,$this->methods)?$i:'AES-128-CBC';
      $this->encode=is_bool($e)&&$e?true:false;
    }
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
    if($this->check()){
      $n=\openssl_cipher_iv_length($this->method);
      $o=\openssl_random_pseudo_bytes($n);
      $t=\openssl_encrypt((string)$s,$this->method,$c,OPENSSL_RAW_DATA,$o);
      return $this->encode?base64_encode($o.$t):$o.$t;
    }return false;
  }
  public function decrypt($s=null,$c=null){
    if($this->check()){
      if($this->encode){try{
        $s=base64_decode((string)$s,true);
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
