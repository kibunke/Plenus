<?php
namespace CommonBundle\Entity;

class MCrypt
{
    /**
     * Un string Hash a partir de la clase pasada como parámetro
     *
     * @var string
     */
    private $securekey;
    
    /**
     * Un array random
     *
     * @var array
     */
    private $iv;
    
    /**
     * Inicializa la instancia de la clase con el valor hash de la clase pasada como parámeto y el vector IV.
     * Estas variables serán utilizadas luego para la encriptación o desencriptación.
     *
     * @param string $textkey
     */
    function __construct($textkey='sucutrule')
    {
        $this->securekey = hash('sha256',$textkey,true);
        $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_256, MCRYPT_MODE_ECB);    
        $this->iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    }
    
    /**
     * Esta función recibe como parámetro un string y lo devuelve encriptado con MIME base64
     *
     * @param string $input
     * @return string
     * @link http://ar2.php.net/manual/en/function.base64-encode.php
     */
    function encriptar($input) {
        return base64_encode(mcrypt_encrypt(MCRYPT_RIJNDAEL_256, $this->securekey, $input, MCRYPT_MODE_ECB, $this->iv));
    }
    
    /**
     * Esta función recibe como parámetro un string encriptado anteriormente con la función encriptar y devuelve el string original
     *
     * @param string $input
     * @return string
     */
    function desencriptar($input) {
        return trim(mcrypt_decrypt(MCRYPT_RIJNDAEL_256, $this->securekey, base64_decode($input), MCRYPT_MODE_ECB, $this->iv));
    }

}
?>