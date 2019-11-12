<?php
require_once './vendor/autoload.php';
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;

class Crud {

	protected $database;
	protected $dbname = 'users'; // Name of the Firebase table

	public function __construct() {
		$acc = ServiceAccount::fromJsonFile(__DIR__ . '/secret/path_to_the_secred_json_file.json');
		$firebase = (new Factory) -> withServiceAccount($acc)->create();

		$this->database = $firebase->getDatabase();
	}

	/**
	 * @param int|NULL $userID
	 * @return bool|mixed
	 * @throws \Kreait\Firebase\Exception\ApiException
	 *
	 * Getting data from the Firebase
	 */
	public function get(int $userID = NULL){
		if (empty($userID) || !isset($userID)) { return false; }

		if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userID)) {
			return $this->database->getReference($this->dbname)->getChild($userID)->getValue();
		} else {
			return false;
		}
	}

	/**
	 * @param array $data
	 * @return bool
	 * @throws \Kreait\Firebase\Exception\ApiException
	 *
	 * Adding data inside the Firebase
	 */
	public function insert(array $data) {
		if (empty($data) || !isset($data)) {return false; }

		foreach ($data as $key => $value) {
		    //You can set push() in order to get generated keys. Read more in docs.
			$this->database->getReference()->getChild($this->dbname)->getChild($key)->set($value);
		}
		return true;
	}

	/**
	 * @param int $userID
	 * @return bool
	 * @throws \Kreait\Firebase\Exception\ApiException
	 *
	 * Deleting data from the Firebase
	 */
	public function delete(int $userID) {
		if (empty($userID) || !isset($userID)) { return false; }

		if ($this->database->getReference($this->dbname)->getSnapshot()->hasChild($userID)) {
			$this->database->getReference($this->dbname)->getChild($userID)->remove();
			return true;
		} else {
			return false;
		}
	}
}

$crud = new Crud();

// Adding the data to the database
var_dump($crud->insert([
	'1' => 'Input 1',
	'2' => 'Input 2',
	'3' => 'Input 3'
]));

// var_dump($crud->get(1)); // pull the data from the database
// var_dump($crud->delete(3)); // deleting data from the database