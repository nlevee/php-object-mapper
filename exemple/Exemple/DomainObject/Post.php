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
	protected $id;

	/**
	 * @var string
	 */
	protected $title;

	/**
	 * @var string
	 */
	protected $text;

	/**
	 * @var \DateTime
	 */
	protected $date_create;

	/**
	 * @var \DateTime
	 */
	protected $date_update;

}