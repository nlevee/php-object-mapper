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
$adapter = new \POM\Service\Mysql\Adapter("mysql:host=gary.idobs;dbname=nlevee", "nlevee", "devine");
$adapter->connect();
$cursor = $adapter->fetch("SELECT * FROM server_http_config");
foreach($cursor as $idx => $row) {
	var_dump($idx, $row);
}
unset($cursor);

$cursor = $adapter->fetch("SELECT * FROM server_http_config WHERE description LIKE :desc", [':desc' => '%CMS%']);
foreach($cursor as $idx => $row) {
	var_dump($idx, $row);
}
unset($cursor);

$cursor = $adapter->fetch("SELECT * FROM server_http_config WHERE description LIKE :desc", [':desc' => '%WEB%']);
foreach($cursor as $idx => $row) {
	var_dump($idx, $row);
}
unset($cursor);

var_dump($adapter->fetchOne("SELECT * FROM server_http_config"));

$cursor = $adapter->fetchColumn(2, "SELECT * FROM server_http_config");
foreach($cursor as $idx => $row) {
	var_dump($idx, $row);
}
unset($cursor);

var_dump($adapter);


// new post
$postMapper = new \Exemple\Mapper\Post($adapter);
$post = new \Exemple\DomainObject\Post();
$post->populate([
	'title' => 'Nouveau post PHP',
	'text' => '<p>le texte du post en HTML</p>',
	'attribut' => 'inconnu'
]);
//var_dump($post);

$post['title'] = "Le titre nouveau";
//var_dump($post);

unset($post['title']);
//var_dump($post);

//
var_dump($postMapper->fetchById(1, $post), $post);
var_dump($postMapper->removeById(2));

var_dump($postMapper->fetchById(3, $post), $post);
var_dump($postMapper->remove($post));

/*@END@*/
echo 'finish';