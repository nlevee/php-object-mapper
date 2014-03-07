<?php
/**
 * @author: Nicolas Levée
 * @version 070320141532
 */

namespace Exemple\DomainObject;

use POM\DomainObjectAbstract;

/**
 * Class Post
 * @package Exemple\DomainObject
 */
class Post extends DomainObjectAbstract {


	/**
	 * @var string
	 */
	public $id;

	/**
	 * @var string
	 */
	public $title;

	/**
	 * @var string
	 */
	public $text;

	/**
	 * @var \DateTime
	 */
	public $date_create;

	/**
	 * @var \DateTime
	 */
	public $date_update;


	/**
	 * Valide les donnée de l'objet
	 * @return bool
	 */
	public function validate() {
		return true;
	}
}