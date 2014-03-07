<?php
/**
 * @author: Nicolas Levée
 * @version 070320141527
 */
error_reporting(true);

require_once "SplClassLoader.php";

// declaration des autoload
SplClassLoader::AutoloadRegister("POM", __DIR__ . "/../src");
SplClassLoader::AutoloadRegister("Exemple", __DIR__ . "/");

// decalaration de l'adapter
$adapter = new \POM\Service\Mysql\Adapter();

// new post
$post = new \Exemple\DomainObject\Post();
$post->initFromArray([
	'title' => 'Nouveau post PHP',
	'text' => '<p>le texte du post en HTML</p>',
	'attribut' => 'inconnu'
]);
var_dump($post);

$post['title'] = "Le titre nouveau";
var_dump($post);

unset($post['title']);
var_dump($post);

/*@END@*/
echo 'finish';