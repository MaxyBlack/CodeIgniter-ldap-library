<?php if(!defined('BASEPATH')) exit('No direct script access allowed');

	/**
	 * CodeIgniter Ldap Class
	 *
	 * @package     CodeIgniter
	 * @subpackage  Libraries
	 * @category    Libraries
	 * @author      MaxyBlack
	 * @link        none
	 */

	class Ldap {

		private $CI;
		//CodeIgniter instance

		public function __construct()
		{
			$this->CI=&get_instance();
			// get CodeIgniter instance
			$this->CI->load->config('ldap', TRUE);
			// load Ldap config
		}

		/**
		 * Connect to Ldap server
		 *
		 * @access  public
		 * @return  void
		 */
		public function connect()
		{
			$connection=ldap_connect($this->CI->config->item('ldapserver', 'ldap'), $this->CI->config->item('ldapport', 'ldap'));
			ldap_set_option($connection, LDAP_OPT_PROTOCOL_VERSION, 3);
			return $connection;
		}

		/**
		 * Bind conection
		 *
		 * @access  public
		 * @return  void
		 */
		public function bind($connection)
		{
			$bind=ldap_bind($connection, $this->CI->config->item('basedn', 'ldap'), $this->CI->config->item('basepass', 'ldap'));
			if($bind)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		/**
		 * Serch and return numer of objects
		 *
		 * @access  public
		 * @param   connection
		 * @param   string
		 * @param   string
		 * @param   array
		 * @return  int
		 */
		public function searchCount($connection, $searchdn, $filter, $attributes=array())
		{
			$sr=ldap_search($connection, $searchdn, $filter, $attributes);
			if($sr)
			{
				return ldap_count_entries($connection, $sr);
			}
		}

		/**
		 * Serch and return result
		 *
		 * @access  public
		 * @param   connection
		 * @param   string
		 * @param   string
		 * @param   array
		 * @return  object
		 */
		public function search($connection, $searchdn, $filter, $attributes=array())
		{
			$sr=ldap_search($connection, $searchdn, $filter, $attributes);
			if($sr)
			{
				ldap_count_entries($connection, $sr);
				return ldap_get_entries($connection, $sr);
			}
			else
			{
				echo FALSE;
			}
		}

		/**
		 * Return value of object
		 *
		 * @access  public
		 * @param   connection
		 * @param   string
		 * @param   array
		 * @return  object
		 */
		public function getValue($searchdn, $filter, $attributes=array())
		{
			$sr=ldap_search($this->connect(), $searchdn, $filter, $attributes);
			return ldap_get_entries($this->connect(), $sr);

		}

		/**
		 * Add new record
		 *
		 * @access  public
		 * @param   connection
		 * @param   string
		 * @param   array
		 * @return  boolean
		 */
		public function addRecord($connection, $adddn, $record=array())
		{
			$addProcess=ldap_add($connection, $adddn, $record);

			if($addProcess)
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}

		/**
		 * Modify current record
		 *
		 * @access  public
		 * @param   connection
		 * @param   string
		 * @param   array
		 * @return  boolean
		 */
		public function modifyRecord($connection, $modifydn, $record)
		{
			if(ldap_modify($connection, $modifydn, $record))
			{
				return TRUE;
			}
			else
			{
				return FALSE;
			}
		}
		
		/**
		 * Delete current record
		 *
		 * @access  public
		 * @param   connection
		 * @param   string
		 * @param   boolean
		 * @return  boolean
		 */
		public function deleteRecord($connection, $dn, $recursive=FALSE)
		{
			if($recursive==FALSE)
			{
				return (ldap_delete($connection, $dn));
			}
			else
			{
				$sr=ldap_list($connection, $dn, "ObjectClass=*", array(""));
				$info=ldap_get_entries($connection, $sr);

				for($i=0;$i<$info['count'];$i++)
				{
					$result=myldap_delete($connection, $info[$i]['dn'], $recursive);
					if(!$result)
					{
						return ($result);
					}
				}
				return (ldap_delete($connection, $dn));
			}
		}

		/**
		 * Close connection
		 *
		 * @access  public
		 * @param   connection
		 * @return  void
		 */
		public function close($connection)
		{
			ldap_close($connection);
		}
	}
// END Ldap class

/* End of file Ldap.php */
/* Location: ./application/libraries/Ldap.php */
