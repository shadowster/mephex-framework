<?php



/**
 * Generates a PDO connection based on a credential factory and
 * credential names.
 * 
 * @author mlight
 */
class Mephex_Db_Sql_Pdo_ConnectionFactory
implements Mephex_Db_Sql_Base_ConnectionFactory
{
	/**
	 * The credential factory that should be used for generating credentials
	 * based on connection names.
	 *
	 * @var Mephex_Db_Sql_Base_CredentialFactory
	 */
	protected $_credential_factory;



	/**
	 * @param Mephex_Db_Sql_Base_CredentialFactory $credential_factory -
	 *		the credential factory that should be used for generating
	 *		credentials based on connection names
	 */
	public function __construct(
		Mephex_Db_Sql_Base_CredentialFactory $credential_factory
	)
	{
		$this->_credential_factory	= $credential_factory;
	}



	/**
	 * Generates a database connection of the given name.
	 * 
	 * @param string $name - the name of the connection to generate
	 * @return Mephex_Db_Sql_Pdo_Connection
	 * @see Mephex_Db_Sql_Base_ConnectionFactory#getConnection
	 */
	public function getConnection($name)
	{
		$credential_factory	= $this->_credential_factory;

		try
		{
			// try to get a 'write' credential (which can be used for
			// writing and reading)
			$write_credential	= $credential_factory->getCredential(
				"{$name}.write"
			);
			
			try
			{
				// try to get a 'read' credential (which can only be used for
				// reading)
				$read_credential	= $credential_factory->getCredential(
					"{$name}.read"
				);
			}
			catch(Mephex_Config_OptionSet_Exception_UnknownKey $read_ex)
			{
				// if a 'read' credential could not be loaded, we use
				// a null credential (which causes the 'write' connection to be 
				// used)
				$read_credential	= $write_credential;
			}
		}
		catch(Mephex_Config_OptionSet_Exception_UnknownKey $write_ex)
		{
			try
			{
				// if a 'write' credential could not be loaded (which also means
				// a 'read' credential was not loaded), attempt to load a general
				// credential
				$write_credential	= $credential_factory->getCredential(
					"{$name}"
				);
				$read_credential	= $write_credential;
			}
			catch(Mephex_Config_OptionSet_Exception_UnknownKey $general_ex)
			{
				// if a general credential could not be loaded, throw the
				// exception generated by attemptingto load the 'write' credential
				throw $write_ex;
			}
		}
		
		return $this->connectUsingCredential(
			new Mephex_Db_Sql_Pdo_Credential(
				new Mephex_Db_Sql_Base_Quoter_Mysql(),
				$write_credential,
				$read_credential
			)
		);
	}
	
	
	
	
	/**
	 * Generates a connection using the given credentials.
	 * 
	 * @param Mephex_Db_Sql_Pdo_Credential $credential - the credential to use
	 *		for making the DB connection
	 * @return Mephex_Db_Sql_Pdo_Connection
	 */
	protected function connectUsingCredential(
		Mephex_Db_Sql_Pdo_Credential $credential
	)
	{
		return new Mephex_Db_Sql_Pdo_Connection($credential);
	}
}