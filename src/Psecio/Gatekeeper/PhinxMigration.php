<?php

namespace Psecio\Gatekeeper;
use Phinx\Migration\AbstractMigration;

/**
 * Custom migration extending the base Phinx version to allow
 * 	for custom table prefix handling
 */
abstract class PhinxMigration extends AbstractMigration
{
	/**
	 * Default table prefix (empty)
	 * @var string
	 */
	protected $prefix = '';

	/**
	 * Initialize, set the prefix if provided
	 */
	public function init()
	{
		if (isset($_SERVER['DB_TABLE_PREFIX'])) {
			$this->setPrefix($_SERVER['DB_TABLE_PREFIX']);
		}
	}

	/**
	 * Set the current prefix value
	 *
	 * @param string $prefix Table prefix value
	 */
	public function setPrefix($prefix)
	{
		$this->prefix = $prefix;
	}

	/**
	 * Get the current prefix value
	 * 	If defined, returns the value plus an underscore ("_")
	 *
	 * @return string Formatted prefix or empty string
	 */
	public function getPrefix()
	{
		return (strlen($this->prefix) > 0) ? $this->prefix.'_' : '';
	}

	/**
	 * Get the current table name value
	 *
	 * @return string Name of current migration's table
	 */
	public function getTableName()
	{
		return $this->getPrefix().$this->tableName;
	}
}