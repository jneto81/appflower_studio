<?php



/**
 * This class defines the structure of the 'afs_notification' table.
 *
 *
 *
 * This map class is used by Propel to do runtime db structure discovery.
 * For example, the createSelectSql() method checks the type of a given column used in an
 * ORDER BY clause to know whether it needs to apply SQL to make the ORDER BY case-insensitive
 * (i.e. if it's a text column type).
 *
 * @package    propel.generator.plugins.appFlowerStudioPlugin.lib.model.map
 */
class afsNotificationTableMap extends TableMap
{

	/**
	 * The (dot-path) name of this class
	 */
	const CLASS_NAME = 'plugins.appFlowerStudioPlugin.lib.model.map.afsNotificationTableMap';

	/**
	 * Initialize the table attributes, columns and validators
	 * Relations are not initialized by this method since they are lazy loaded
	 *
	 * @return     void
	 * @throws     PropelException
	 */
	public function initialize()
	{
		// attributes
		$this->setName('afs_notification');
		$this->setPhpName('afsNotification');
		$this->setClassname('afsNotification');
		$this->setPackage('plugins.appFlowerStudioPlugin.lib.model');
		$this->setUseIdGenerator(true);
		// columns
		$this->addColumn('MESSAGE', 'Message', 'VARCHAR', true, 255, null);
		$this->addColumn('MESSAGE_TYPE', 'MessageType', 'VARCHAR', true, 255, null);
		$this->addColumn('USER', 'User', 'VARCHAR', true, 128, null);
		$this->addColumn('IP', 'Ip', 'VARCHAR', true, 255, null);
		$this->addColumn('CREATED_AT', 'CreatedAt', 'TIMESTAMP', false, null, null);
		$this->addPrimaryKey('ID', 'Id', 'INTEGER', true, null, null);
		// validators
	} // initialize()

	/**
	 * Build the RelationMap objects for this table relationships
	 */
	public function buildRelations()
	{
	} // buildRelations()

	/**
	 *
	 * Gets the list of behaviors registered for this table
	 *
	 * @return array Associative array (name => parameters) of behaviors
	 */
	public function getBehaviors()
	{
		return array(
			'symfony' => array('form' => 'true', 'filter' => 'true', ),
			'symfony_behaviors' => array(),
			'symfony_timestampable' => array('create_column' => 'created_at', ),
		);
	} // getBehaviors()

} // afsNotificationTableMap
