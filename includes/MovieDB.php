<?php 

class MovieDB {

	// variable for method & request
	public $method;
	public $request;
	private $db;

	// put values in variables & MysqliDb
	public function __construct($method, $request) {
		$this->method = $method;
		$this->request = $request;
		
		$connection = array(
			'user'    => DB_USER,
			'pass'    => DB_PASSWORD,
			'db'      => DB_DATABASE,
			'host'		=> DB_SERVER
		);
	  $this->db = new SafeMySQL($connection);
	}


	// explode the URL, check for the action (which is the first argument) and insert in new 'resource' variable
	public function serve() {
		$paths = explode('/', $this->request);
		array_shift($paths);
		$resource = array_shift($paths);

		switch ($resource) {
			case 'show':
				$showType = array_shift($paths);
				if (empty($showType)) {
					$this->showAllItems();
				} else {
					$this->showSelectedType($showType);
				}
				break;
			case 'movie':
			case 'tvshow':
			case 'documentary':
				$identifier = array_shift($paths);
				if (empty($identifier)) {
					header('HTTP/1.1 400 Bad Request');
				} else {
					$this->getItem($resource, $identifier);
				}
				break;
			case 'info':
				break;
			default:
				header('HTTP/1.1 404 Not Found');
				break;
		}
	}

	public function showAllItems() {
		$shows = $this->db->getAll('SELECT * FROM movieDatabase LIMIT 10');
		echo json_encode($shows);
	}

	public function showSelectedType($showType) {
		switch ($showType) {
			case 'movies':
				$shows = $this->db->getAll('SELECT * FROM movieDatabase WHERE type_movie = \'Feature\' LIMIT 10');
				echo json_encode($shows);
				break;
			case 'tvshows':
				$shows = $this->db->getAll('SELECT * FROM movieDatabase WHERE type_movie = \'TV Series\' LIMIT 10');
				echo json_encode($shows);
				break;
			default:
				header('HTTP/1.1 404 Not Found');
				break;
		}
	}

	public function getItem($showType, $id) {
		$selectedShow;
		if ($showType == 'movie') {
			$selectedShow = 'Feature';
		} else if ($showType == 'tvshow') {
			$selectedShow = 'TV Series';
		} else {
			$selectedShow = 'Documentary';
		}

		$result = $this->db->getAll('SELECT * FROM movieDatabase WHERE id = ?i AND type_movie = ?s', $id, $selectedShow);
		if (sizeof($result) > 0) {
			echo json_encode($result);
		} else {
			header('HTTP/1.1 404 Not Found');
		}		
	}
}

?>