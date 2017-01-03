# form-bundle
Bundle Symfony pour la gestion des formulaires, prend en charge Google Recaptcha

## Méthode de la classe FormManager
/**
/*   Initialisation de la classe
/*   $arrayKeyVar : Le nom des varaibles souhaitées, ex : array('nom','prenom','mail')
/*   $data_received : Le tableau $_POST ou $_GET reçu
/**/
public function __construct($arrayKeyVar,$data_received)

/**
/*   Verifie que toutes les variables requise par le constructeur existent (retourne true si tout existe, sinon retourne false, utiliser getError pour voir le tableau des variables)
/*/
public function testCorrespondance()

/**
/*   Vérifie qu'aucune variable n'est vide (retourne true si aucune n'est vide, sinon retourne false, utiliser getError pour voir le tableau des variables)
/*/
public function testBlank()

/**
/* sectionne tout les chaines de chaque champs selon la longueur maximale définie par $charMax
/**/
public function setFormatSizeDataReceived($charMax)

/**
/* Vérifie le captcha de Google 
/*/
public function verifyCaptcha($input,$secret)

/**
/* Retourne les variables modifiés par les méthodes comme setFormatSizeDataReceived
/*/
public function getDataReceived()

/**
/* Retourne les variables concernées par l'erreur en cours
/*/
public function getError()

## Comment utiliser ?
Utiliser le bundle avec : use AE\FormBundle\Helper\FormManager;

Exemple de code ci-dessous :

$form = new FormManager(array('nom','mail','tel','company','g-recaptcha-response'),$_POST);  

if (!$form->testCorrespondance())
{
    throw new \Exception('Correspond Error, miss : ' . print_r($form->getError(),true));
    exit;
}

if (!$form->testBlank())
{
    throw new \Exception('Blank input Error, field : ' . print_r($form->getError(),true));
    exit;
}

if (!$form->verifyCaptcha($_POST['g-recaptcha-response'],'SECRET_ICI'))
{
    throw new \Exception('Captcha error : ' . print_r($form->getError(),true));
    exit;
}

$form->setFormatSizeDataReceived(50);

$tab = $form->getDataReceived();
