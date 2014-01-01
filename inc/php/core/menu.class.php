<?php

class menu {

	private $menu;
	private $build;

	public function __CONSTRUCT() {
		$menu = array();
		$build = false;
	}

	private function buildMenu($childid = 0) {
		if(!ctype_digit($childid) || empty($childid)) $childid = 0;

		$page = array();
		$category = array();

		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."pages`");
		while($row = $query->fetch_assoc()) $page[$row['id']] = $row;
		$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."categories`");
		while($row = $query->fetch_assoc()) $category[$row['id']] = $row;

		if($childid == 0):
			$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."menu` WHERE parent_id IS NULL ORDER BY `order` ASC") or debug::addLine("ERROR", "Could not fetch menu information, reason: ".mysqli_error(cl::g("mysql")), __FILE__, __LINE__);
		else:
			$query = cl::g("mysql")->query("SELECT * FROM `".SQL_PREFIX."menu` WHERE parent_id = '$childid' ORDER BY `order` ASC") or debug::addLine("ERROR", "Could not fetch menu information, reason: ".mysqli_error(cl::g("mysql")), __FILE__, __LINE__);
		endif;

		if(!$query) return false;
		if($query->num_rows == 0) return array();

		$result = array();

		while($row = $query->fetch_assoc()):
			$result[] = array(
							'id'	=> $row['id'],
							'type'	=> $row['id_type'],
							'title' => ($row['id_type'] == "page") ? $page[$row['id']]['title'] : $category[$row['id']]['title'],
							'url'	=> ($row['id_type'] == "page") ? $page[$row['id']]['url'] : (($category[$row['id']]['page_link'] > 0) ? $page[$category[$row['id']]['page_link']]['url'] : ''),
							'submenu' => $this->buildMenu($row['id'])
						);
		endwhile;

		$this->build = true;

		return $result;
	}

	public function getMenu() {
		echo "<pre>";

		return ($this->build) ? $this->menu : $this->buildMenu();
	}
	
}