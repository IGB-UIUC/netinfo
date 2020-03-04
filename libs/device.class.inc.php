<?php

class device {

////////////////Private Variables//////////

        private $db; //mysql database object
	private $aname;
	private $ipnumber;
	private $hardware;
	private $user;
	private $email;
	private $room;
	private $os;
	private $description;
	private $serial_number;
	private $alias = array();
	private $modifiedby;
	private $modified;
	private $property_tag;
	private $vendor;
	private $switch;
	private $port;
	private $domain;
	private $network;

	const hostname_length = 35;
        ////////////////Public Functions///////////

        public function __construct($db,$ipnumber = 0) {
                $this->db = $db;
        	if ($ipnumber != 0) {
			$this->get_device($ipnumber);
		}
	}
        public function __destruct() {
	}

	public function get_aname() { return $this->aname; }
	public function get_ipnumber() { return $this->ipnumber; }
	public function get_hardware() { return $this->hardware; }

	public function get_hardware_cisco() {
		return preg_replace('/....(?!$)/', '$0.', $this->hardware);
	}
	public function get_hardware_dashes() {
		return preg_replace('/..(?!$)/', '$0-', $this->hardware);
	}
	public function get_hardware_colon() {
		return preg_replace('/..(?!$)/', '$0:', $this->hardware);
	}
	public function get_user() { return $this->user; }
	public function get_email() { return $this->email; }
	public function get_room() { return $this->room; }
	public function get_os() { return $this->os; }
	public function get_description() { return $this->description; }
	public function get_serial_number() { return $this->serial_number; }
	public function get_alias() { return $this->alias; }
	public function get_modifiedby() { return $this->modifiedby; }
	public function get_modified() { return $this->modified; }
	public function get_property_tag() { return $this->property_tag; }
	public function get_vendor() { return $this->vendor; }
	public function get_switch() { return $this->switch; }
	public function get_port() { return $this->port; }
	public function get_domain() { return $this->domain; }
	public function get_network() { return $this->network; }

	public function delete($modified_by) {
		$sql = "UPDATE namespace ";
		$sql .= "SET aname='spare',";
		$sql .= "hardware='',";
		$sql .= "name='',";
		$sql .= "email='',";
		$sql .= "room='',";
		$sql .= "os='',";
		$sql .= "description='',";
		$sql .= "serial_number='',";
		$sql .= "alias='',";
		$sql .= "property_tag='', ";
		$sql .= "modifiedby='" . $modified_by . "' ";
		$sql .= "WHERE ipnumber='" . $this->get_ipnumber() . "' ";
		$sql .= "LIMIT 1";
		$this->db->non_select_query($sql);
		$this->get_device($this->get_ipnumber());
		return array('RESULT'=>true);
	}

	public function get_locations() {
		$sql = "SELECT macwatch.date,macwatch.mac, ";
		$sql .= "macwatch.switch, macwatch.port ";
		$sql .= "FROM macwatch ";
		$sql .= "WHERE LOWER(mac)='" . $this->get_hardware() . "' ";
		$sql .= "ORDER BY date DESC";
		
		return $this->db->query($sql);


	}
	
	public function update($aname,$hardware,$user,$email,$room,$description,$serial_number,$property_tag,$os,$modified_by) {
		$message = "";
		$error = 0;
		if (($aname == $this->get_aname()) && ($hardware == $this->get_hardware()) && ($user == $this->get_user()) &&
			($email == $this->get_email()) && ($room == $this->get_room()) && ($description == $this->get_description()) &&
			($serial_number == $this->get_serial_number()) && ($property_tag == $this->get_property_tag()) &&
			($os == $this->get_os())) 
		{
			$error = 1;
			$message .= "<div class='alert'>No changes were made</div>";	

		}
		else {
			if (!$this->verify_hostname($aname)) {
				$message .= "<div class='alert alert-error'>Invalid hostname. ";
				$message .= "Hostname can contain only lowercase letters, numbers, and hyphens. ";
				$message .= "Maximum length is " . $this::hostname_length . " characters. It can not contain the word 'spare'.</div>";
				$error = 1;
			}
			elseif (!$this->unique_aname($aname)) {
				$message .= "<div class='alert alert-error'>Hostname " . $aname . " already exists on domain <strong>" . $this->get_domain() . "</strong></div>";
				$error = 1;
		
			}
			if (!$this->verify_hardware($hardware)) {
				$message .= "<div class='alert alert-error'>Invalid Hardware Address. ";
				$message .= "Hardware address can contain only numbers and lowercase letters from a-f. ";
				$message .= "Must contain 12 characters.</div>";	
				$error = 1;
			}
			elseif (!$this->unique_hardware($hardware)) {
				$message .= "<div class='alert alert-error'>Hardware Address ";
				$message .= $hardware . " already exists on network <strong>" . $this->get_network() . "</strong></div";
				$error = 1;
			}
			if (!$this->verify_user($user)) {
				$message .= "<div class='alert alert-error'>Please enter the user's full name</div>";
				$error = 1;
			}
			if (!$this->verify_email($email)) {
				$message .= "<div class='alert alert-error'>Please enter the user's email address</div>";
				$error = 1;
			}
			if (!$this->verify_description($description)) {
				$message .= "<div class='alert alert-error'>Please enter a description</div>";
				$error = 1;
			}
			if (!$this->verify_room($room)) {
				$message .= "<div class='alert alert-error'>Please enter a room number.  Maximum of 5 characters (ie 2414a)</div>";
				$error = 1;
			}
		}
		if ($error == 0) {
			
			$sql = "UPDATE namespace SET ";
			$sql .= "aname='" . $aname . "',";
			$sql .= "hardware='" . $hardware . "',";
        	        $sql .= "name='" . $user . "',";
                	$sql .= "email='" . $email . "',";
		        $sql .= "room='" . $room . "',";
        		$sql .= "os='" . $os . "',";
                	$sql .= "description='" . $description . "',";
	                $sql .= "serial_number='" . $serial_number . "',";
	                $sql .= "property_tag='" . $property_tag . "', ";
			$sql .= "modifiedby='" . $modified_by . "' ";
	        	$sql .= "WHERE ipnumber='" . $this->get_ipnumber() . "' ";
        	        $sql .= "LIMIT 1";
			$result = $this->db->non_select_query($sql);
			$this->get_device($this->get_ipnumber());
			$message = "<div class='alert alert-success'>Device Successfully Updated</div>";
			return array('RESULT'=>$result,'MESSAGE'=>$message);
		}
		else {
			return array('RESULT'=>false,'MESSAGE'=>$message);
		}


	}
	public function add_alias($alias,$modified_by) {
		$message = "";
		$result = true;
		$number_periods = substr_count($alias,".");
		if ($number_periods > 1) {
                        $message = "<div class='alert alert-error'>Invalid Alias Name. ";
                        $message .= "Alias can contain only one subdomain.</div>";
                        $result = false;

		}
		elseif ($number_periods == 1) {
			$hostnames = explode(".",$alias);
			foreach ($hostnames as $host) {
				if (!$this->verify_hostname($host)) {
		                        $message = "<div class='alert alert-error'>Invalid Alias Name. ";
                		        $message .= "Alias can contain only lowercase letters, numbers, and hyphens. here</div>";
		                        $result = false;

				}

			}

		}
		elseif ((!$number_periods) && (!$this->verify_hostname($alias))) {
			$message = "<div class='alert alert-error'>Invalid Alias Name. ";
			$message .= "Alias can contain only lowercase letters, numbers, and hyphens.</div>";
			$result = false;
		}
		
		if (!$this->unique_alias($alias)) {
			$message = "<div class='alert alert-error'>Hostname " . $alias . " already exists on domain <strong>" . $this->get_domain() . "</strong></div>";
			$result = false;

		}
		if ($result) {
			$alias_array = $this->get_alias();
			array_push($alias_array,$alias);
			$alias_string = implode(",",$alias_array);
			$sql = "UPDATE namespace SET alias='" . $alias_string . "', ";
			$sql .= "modifiedby='" . $modified_by . "' ";
			$sql .= "WHERE ipnumber='" . $this->get_ipnumber() . "' LIMIT 1";
			$result = $this->db->non_select_query($sql);
			if ($result) {
				$message = "<div class='alert alert-success'>Alias " . $alias . " successfully added</div>";
				$this->get_device($this->get_ipnumber());
			}
			else {
				$message = "<div class='alert alert-error'>Error adding alias</div>";
			}
			
		}
		return array('RESULT'=>$result,'MESSAGE'=>$message);
	}

	public function delete_alias($alias,$modified_by) {
		$message = "";
		$alias_array = $this->get_alias();
		$alias_index = array_search($alias,$alias_array);
		unset($alias_array[$alias_index]);	
		$alias_string = implode(",",$alias_array);
		$sql = "UPDATE namespace SET alias='" . $alias_string . "', ";
		$sql .= "modifiedby='" . $modified_by . "' ";
		$sql .= "WHERE ipnumber='" . $this->get_ipnumber() . "' LIMIT 1";
		$result = $this->db->non_select_query($sql);
		if ($result) {
			$message = "<div class='alert alert-success'>Alias " . $alias . " successfully deleted</div>";
			$this->get_device($this->get_ipnumber());
		}
		else {
			$message = "<div class='alert alert-error'>Error removing alias</div>";
		}
		return array('RESULT'=>$result,'MESSAGE'=>$message);

	}
	////////////////Private Functions//////////

	private function get_device($ipnumber) {
		$sql = "SELECT namespace.aname, namespace.ipnumber, ";
		$sql .= "LOWER(namespace.hardware) as hardware , namespace.name, ";
		$sql .= "namespace.email, namespace.room, namespace.os, namespace.description, ";
		$sql .= "namespace.serial_number, namespace.alias, namespace.modifiedby, namespace.modified, ";
		$sql .= "namespace.property_tag,networks.name as network,domains.name as domain, ";
		$sql .= "a.switch, a.port, a.vendor ";
		$sql .= "FROM namespace ";
		$sql .= "LEFT JOIN networks ON namespace.network_id=networks.id ";
		$sql .= "LEFT JOIN domains ON networks.domain_id=domains.id ";
		$sql .= "LEFT JOIN ( ";
		$sql .= "SELECT MAX(macwatch.date) as last_seen, macwatch.switch as switch, macwatch.vendor as vendor, ";
		$sql .= "macwatch.port as port,LOWER(macwatch.mac) as mac FROM macwatch GROUP BY mac) as a ";
		$sql .= "ON a.mac=LOWER(namespace.hardware) ";
		$sql .= "WHERE ipnumber='" . $ipnumber . "' LIMIT 1";
		$result = $this->db->query($sql);
		if (count($result)) {
			$this->aname = $result[0]['aname'];
			$this->ipnumber = $result[0]['ipnumber'];
			$this->hardware = strtolower($result[0]['hardware']);
			$this->user = $result[0]['name'];
			$this->email = $result[0]['email'];
			$this->room = $result[0]['room'];
			$this->os = $result[0]['os'];
			$this->description = $result[0]['description'];
			$this->serial_number = $result[0]['serial_number'];
			if (strlen($result[0]['alias']) > 0) {
				$this->alias = explode(",",$result[0]['alias']);
			}
			else {
				$this->alias = array();
			}
			$this->modifiedby = $result[0]['modifiedby'];
			$this->modified = $result[0]['modified'];
			$this->property_tag = $result[0]['property_tag'];
			$this->vendor = $result[0]['vendor'];
			$this->switch = $result[0]['switch'];
			$this->port = $result[0]['port'];
			$this->domain = $result[0]['domain'];
			$this->network = $result[0]['network'];

		}
	}

	private function verify_hostname($hostname) {
		$valid = 1;
		if (!preg_match('/^[a-z0-9-]+$/',$hostname)) {
			$valid = 0;
		}
		elseif (preg_match('/spare/',$hostname)) {
			$valid = 0;
		}
		elseif (strlen($hostname) > $this::hostname_length) {
			$valid = 0;
		}
		elseif (strpos($hostname,"spare")) {
			$valid = 0;
		}
		return $valid;
	}
	private function verify_hardware($hardware) {
		$valid = 1;
		if (!preg_match('/^[a-f0-9]{12}$/',$hardware)) {
			$valid = 0;
		}
		return $valid;
	}

	private function verify_email($email) {
		$email = strtolower($email);
		$valid = 1;
		if (strpos($email,"@")) {
			list($prefix,$hostname) = explode("@",$email);
			if (!preg_match("/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,4})$/",
				$email)) {
                        	$valid = 0;     
                	}
                	if (($hostname != "") && (!checkdnsrr($hostname,"MX"))) {
                        	$valid = 0;
                	}
		}
		else {
			$valid = 0;
		}
		return $valid;

	}

	private function unique_aname($aname) {
		$sql = "SELECT count(1) as count FROM namespace ";
		$sql .= "LEFT JOIN networks ON namespace.network_id=networks.id ";
                $sql .= "LEFT JOIN domains ON networks.domain_id=domains.id ";	
		$sql .= "WHERE (aname='" . $aname . "' AND ";
		$sql .= "domains.name='" . $this->get_domain() . "' AND ";
		$sql .= "namespace.ipnumber<>'" . $this->get_ipnumber() . "') ";
		$sql .= "OR (FIND_IN_SET('" . $aname . "',namespace.alias) AND ";
		$sql .= "domains.name='" . $this->get_domain() . "') ";
		$result = $this->db->query($sql);
		if ($result[0]['count']) {
			return false;
		}
		return true;
	}

	private function unique_alias($alias) {
                $alias = strtolower($alias);
                $sql = "SELECT count(1) as count FROM namespace ";
		$sql .= "LEFT JOIN networks ON namespace.network_id=networks.id ";
		$sql .= "LEFT JOIN domains ON networks.domain_id=domains.id ";
                $sql .= "WHERE (aname='" . $alias . "' AND ";
		$sql .= "domains.name='" . $this->get_domain() . "') ";
                $sql .= "OR (FIND_IN_SET('" . $alias . "',alias) AND ";
		$sql .= "domains.name='" . $this->get_domain() . "')";
                $result = $this->db->query($sql);
                if ($result[0]['count']) {
                        return false;
                }
                return true;


	}

	private function unique_hardware($hardware) {
		$hardware = strtolower($hardware);
		if ($hardware == "000000000000") {
			return true;
		}
		else {
			$sql = "SELECT count(1) as count FROM namespace ";
			$sql .= "LEFT JOIN networks ON namespace.network_id=networks.id ";
			$sql .= "WHERE hardware='" . $hardware . "' AND ";
			$sql .= "ipnumber<>'" . $this->get_ipnumber() . "' AND ";
			$sql .= "networks.name='" . $this->get_network() . "'";
			$result = $this->db->query($sql);
			if ($result[0]['count']) {
				return false;
			}
			else {
				return true;
			}
		}
	}

	private function verify_user($user) {
		$user = trim(rtrim($user));
		$valid = 1;	
		if ($user == "") {
			$valid = 0;
		}
		return $valid;
	}
	
	private function verify_description($description) {
		$description = trim(rtrim($description));
		$valid = 1;
		if ($description == "") {
			$valid = 0;

		}
		return $valid;
	}

	private function verify_room($room) {
		$room = trim(rtrim($room));
		$valid = 1;
		if ($room == "") {
			$valid = 0;
		}
		elseif (strlen($room) > 5) {
			$valid = 0;
		}
		return $valid;

	}
}


?>
