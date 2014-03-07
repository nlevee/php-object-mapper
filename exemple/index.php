<?php
/**
 * @author: Nicolas Levée
 * @version 070320141527
 */

require_once "SplClassLoader.php";

// declaration des autoload
SplClassLoader::AutoloadRegister("POM", __DIR__ . "/../src");
SplClassLoader::AutoloadRegister("Exemple", __DIR__ . "/");

// decalaration de l'adapter