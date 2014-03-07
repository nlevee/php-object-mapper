<?php
/**
 * @author: Nicolas Levée
 * @version 060320141144
 */

namespace POM;

/**
 * Interface MapperInterface
 * @package POM
 */
interface MapperInterface {

	/**
	 * charge les donnée $data dans l'objet $object
	 * @param DomainObjectInterface $object
	 * @param array $data
	 * @return void
	 */
	public function populate(DomainObjectInterface &$object, array $data);

	/**
	 * Charge les données dans $object de l'element trouvé via son id,
	 * renvoi true/false selon la réussite de la requete
	 * @param mixed $id
	 * @param DomainObjectInterface $object
	 * @return bool
	 */
	public function fetchById($id, DomainObjectInterface &$object);

	/**
	 * Supprime un element de la DB via son ID,
	 * renvoi true/false selon la réussite de la requete
	 * @param mixed $id
	 * @return bool
	 */
	public function removeById($id);

	/**
	 * Sauvegarde l'objet dans la DB, l'$objet est mis a jour selon les modif appliqué par la DB (insert: id...)
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return bool
	 */
	public function save(DomainObjectInterface &$object);

	/**
	 * Insert l'objet dans la DB, l'$objet est mis a jour selon les modif appliqué par la DB (insert: id...)
	 * si l'objet existe déjà on renvoi un exception
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return bool
	 */
	public function insert(DomainObjectInterface &$object);

	/**
	 * Update l'objet dans la DB, l'$objet est mis a jour selon les modif appliqué par la DB (insert: id...)
	 * si l'objet n'existe pas déjà on renvoi un exception
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return mixed
	 */
	public function update(DomainObjectInterface $object);

	/**
	 * Supprime l'objet de la DB, si l'objet n'existe pas déjà on renvoi un exception
	 * renvoi true/false selon la réussite de la requete
	 * @param DomainObjectInterface $object
	 * @return mixed
	 */
	public function remove(DomainObjectInterface $object);

}