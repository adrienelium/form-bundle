<?php
namespace AE\FormBundle\Helper;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\Yaml\Yaml;

class FormManager
{
    var $listVar;
    var $listReceived;
    
    var $errorField;
    
    /**
    *   Initialisation de la classe
    *   $arrayKeyVar : Le nom des varaibles souhaitées, ex : array('nom','prenom','mail')
    *   $data_received : Le tableau $_POST ou $_GET reçu
    **/
    public function __construct($arrayKeyVar,$data_received)
    {  
        $this->listReceived = $data_received;
        $this->listVar = $arrayKeyVar;
        
        $this->errorField = array();
    }
    
    /**
    *   Verifie que toutes les variables requise par le constructeur existent (retourne true si tout existe, sinon retourne false, utiliser getError pour voir le tableau des variables)
    */
    public function testCorrespondance()
    {
        
        $arrayManquant = true;
        $this->errorField = array();
        
        
        if (count($this->listReceived) == 0)
        {
            $arrayManquant = false;
            $this->errorField = $this->listVar;
        }
        
        foreach ($this->listVar as $key)
        {
            $valid = true;
            foreach ($this->listReceived as $nomvar => $value)
            {
                if ($nomvar == $key)
                {
                    $valid = true;
                    break;
                }
                else
                {
                    $valid = false;
                }
            }

            if (!$valid)
            {
                array_push($this->errorField,$key);
                $arrayManquant = false;
            }
        }
        
        //echo 'Array Manquant : '.$arrayManquant;exit;

        return $arrayManquant;
    }
    
    /**
    *   Vérifie qu'aucune variable n'est vide (retourne true si aucune n'est vide, sinon retourne false, utiliser getError pour voir le tableau des variables)
    */
    public function testBlank()
    {
        $valid = false;
        $this->errorField = array();
        
        if (count($this->listReceived) == 0)
            $this->errorField = $this->listVar;
        
        foreach ($this->listReceived as $key => $value)
        {
            if ($value == '')
            {
                array_push($this->errorField,$key);
            }
        }
        
        if (count($this->errorField) == 0)
        {
            $valid = true;
        }
        
        return $valid;
    }
    
    /**
    * sectionne tout les chaines de chaque champs selon la longueur maximale définie par $charMax
    **/
    public function setFormatSizeDataReceived($charMax)
    {
        foreach ($this->listReceived as $key => $value)
        {
            $this->listReceived[$key] = substr($value,0,$charMax);
        }
    }
    
    /**
    * Vérifie le captcha de Google 
    */
    public function verifyCaptcha($input,$secret)
    {
        if ($input == '')
        {
            $this->errorField = 'user input empty';
            return false;
        }
        
        if ($secret == "")
        {
            $this->errorField = 'secret empty';
            return false;
        }
        
        $postfields = array(
            'secret' => $secret,
            'response' => $input,
            'remoteip' => $_SERVER["REMOTE_ADDR"]
        );
        
        $curl = curl_init('https://www.google.com/recaptcha/api/siteverify');
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_COOKIESESSION, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $postfields);
        
        $return = curl_exec($curl);
        curl_close($curl);
        
        $response = json_decode($return, true);

        $status = $response["success"];
        
        if ($status == true)
        {
            return true;
        }
        else
        {
            if (isset($response["error-codes"]))
                $error = $response["error-codes"];
            else
                $error = "Token problem";
            
            $this->errorField = 'Error code => '.$error;
            return false;
        }
    }
    
    /**
    * Retourne les variables modifiés par les méthodes comme setFormatSizeDataReceived
    */
    public function getDataReceived()
    {
        $tab = array();
        foreach ($this->listVar as $value)
        {
            foreach ($this->listReceived as $key => $var)
            {
                if ($key == $value)
                {
                    $tab[$key] = $var;
                }
            }
        }
        
        return $tab;
    }
    
    /**
    * Retourne les variables concernées par l'erreur en cours
    */
    public function getError()
    {
        return $this->errorField;
    }
    
}