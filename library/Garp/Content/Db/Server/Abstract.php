<?php
/**
 * Garp_Content_Db_Server_Abstract
 * 
 * @author David Spreekmeester | grrr.nl
 * @modifiedby $LastChangedBy: $
 * @version $Revision: $
 * @package Garp
 * @subpackage Content
 * @lastmodified $Date: $
 */
abstract class Garp_Content_Db_Server_Abstract implements Garp_Content_Db_Server_Protocol {

	const COMMAND_DUMP = "mysqldump -u'%s' -p'%s' --add-drop-table --host='%s' --databases %s";
	
	const COMMAND_CREATE_BACKUP_PATH = "mkdir -p -m 770 %s";

	const PATH_CONFIG_APP = '/configs/application.ini';

	/**
	 * @var String $_environment The environment this server runs in.
	 */
	protected $_environment;

	/**
	 * @var Zend_Config_Ini $_appConfigParams 	Application configuration parameters (application.ini)
	 *											for this particular environment.
	 */
	protected $_appConfigParams;

	/**
	 * @var		String		Absolute path to write backup files to.
	 */
	protected $_backupPath;


	/**
	 * @param String $_environment The environment this server runs in.
	 */
	public function __construct($environment) {
		$this->setEnvironment($environment);
		$this->setAppConfigParams($this->_fetchAppConfigParams());
		$this->setBackupPath($this->getBackupPath());
	}


	/**
	 * Backs up the database and writes it to a file on the server itself.
	 */
	public function backup() {
		$commands = array(
			$this->_renderCreateBackupPath(),
			$this->_renderDumpShellCommand()
		);

		foreach ($commands as $command) {
			$this->_shellExec($command);
		}
	}
	
	/**
	 * @return String
	 */
	public function getEnvironment() {
		return $this->_environment;
	}
	
	/**
	 * @return Zend_Config_Ini
	 */
	public function getAppConfigParams() {
		return $this->_appConfigParams;
	}

		
	/**
	 * @return Zend_Config_Ini
	 */
	public function getDbConfigParams() {
		$appConfigParams = $this->getAppConfigParams();
		return $appConfigParams->resources->db->params;
	}
		
	public function setAppConfigParams(Zend_Config_Ini $appConfigParams) {
		$this->_appConfigParams = $appConfigParams;
	}
	
	/**
	 * @param String $environment The id of the environment
	 */
	public function setEnvironment($environment) {
		$this->_environment = $environment;
	}

	/**
	 * @param String $path
	 */
	public function setBackupPath($path) {
		$this->_backupPath = $path;
	}

	protected function _renderDumpSqlCommand() {
		$appConfigParams = $this->getAppConfigParams();

		$dbConfig = $appConfigParams->resources->db->params;
		
		$dumpCommand = sprintf(
			self::COMMAND_DUMP,
			$dbConfig->username,
			$dbConfig->password,
			$dbConfig->host,
			$dbConfig->dbname
		);
		
		return $dumpCommand;
	}
	
	
	protected function _renderDumpShellCommand() {
		$dumpSqlCommand 	= $this->_renderDumpSqlCommand();
		$backupPath 		= $this->getBackupPath();
		$dbConfigParams 	= $this->getDbConfigParams();
		$dbName 			= $dbConfigParams->dbname;
		$environment		= $this->getEnvironment();
		$date				= date('Y-m-d-Hi');

		$shellCommand		= $dumpSqlCommand . ' > ' . $backupPath . '/'
							. $dbName . '-' . $environment . '-' . $date . '.sql';

		return $shellCommand;
	}
	
	
	protected function _renderCreateBackupPath() {
		$backupPath = $this->getBackupPath();
		return sprintf(self::COMMAND_CREATE_BACKUP_PATH, $backupPath);
	}

	protected function _fetchAppConfigParams() {
		$config = new Zend_Config_Ini(APPLICATION_PATH . self::PATH_CONFIG_APP, $this->getEnvironment());
		return $config;
	}

}