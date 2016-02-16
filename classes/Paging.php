<?php
namespace Classes;
//
//		Paging class - split sql results into pages
//
//		
//		ver 0.32- removed order by in count query, to speed up //25 ian 2010
//		ver 0.31- fixed another bug in subselects// 28/10/2007	
//		ver 0.3 - fixed group by - must find another clean method // 28/10/2007	
//		ver 0.2 - fixed subselects // 05/10/2007
//			

class Paging {
	private $query='';
	private $page=0;
	private $items_per_page=10;
	public $items=0;
	private $pages=0;
	private $c_pages=10;

	/**
	 * Constructor
	 *
	 * @return Paging
	 */
	public function __constructor() {
		if ($_SERVER['SERVER_NAME'] == 'x') {
			$this->c_pages = 4;
		}
	}
	
	/**
	 * Seteaza numarul de inregistrari pe pagina
	 *
	 * @param int $items
	 */
	public function setItemsPerPage($items) {
		if(is_numeric($items)) {
			$this->items_per_page = (int) $items;
		}
	} 
	/**
	 * Seteaza query-ul ce va fi paginat
	 *
	 * @param string $query
	 * @return boolean
	 */
	public function setQuery($query) {
		$sql = Mysql::getInstance();
		$this->query = $query;
		if ($this->items_per_page == 0) {
			return false;
		}
		$this->query = trim(preg_replace("/[\t\\s\r\n]+/", ' ', $this->query));
		$this->query = preg_replace('/LIMIT [0-9]+\s*\t*,\s*\t*[0-9]+$/si', '', $this->query);
		
		// extract subselects //
		$cnt_query = $this->query;
		$subselects = array();
		preg_match_all('/\(\s*\t*SELECT.*?\)/', $cnt_query, $subselects);

		for($i=0; $i < count($subselects[0]); $i++) {
			$cnt_query = str_replace($subselects[0][$i], '{['.$i.']}', $cnt_query);
		}
		// use count //
		$cnt_query = preg_replace('/select\s*\t*(.*?)\s*\t*from[\s\t]/si', 'select count(1) as items from ', $cnt_query);
		$cnt_query = preg_replace("'ORDER BY.*?$'si", '', $cnt_query);

		for($i=0; $i < count($subselects[0]); $i++) {
			$cnt_query = str_replace('{['.$i.']}', $subselects[0][$i], $cnt_query);
		}


		if($sql->Query($cnt_query)) {
			if($sql->rows > 1) {
				// this is a group by effect //
				$this->items = $sql->rows;
			} else if($sql->rows==1){
				$sql->GetRow(0);
				$this->items = $sql->data->items;
			}
		}
		
		$this->pages = ceil($this->items / $this->items_per_page);
		return true;
	}

	/**
	 * Seteaza pagina curenta
	 *
	 * @param int $page
	 */
	public function setPage($page) {
		if(is_numeric($page)) {
//			if ($page >= $this->pages) {
//				Utils::Redirect('/');
//			}
			if(/*$page <= $this->pages && */$page >= 0) {
				$this->page = (int) $page;
			}
			
			if ($this->page >= $this->pages && $this->pages > 0) {
				$uri = isset($_SERVER['REDIRECT_URL'])  ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];
				$redir = preg_replace("'(.*)page([0-9]+)\.html$'i", "$1page".(int)($this->pages - 1).".html", $uri);
				$redir = preg_replace("'page0.html$'i", '', $redir);
				Utils::Redirect($redir);
			}
		}
	}
	
	/**
	 * 
	 */
	public function setPages($pages) {
		if(is_numeric($pages)) {
			$this->pages = (int) $pages;
		}
	}
	
	/**
	 * Intoarce numarul de pagini
	 *
	 * @return int
	 */
	public function getPages() {
		return $this->pages;
	}

	/**
	 * Returneaza query-ul ce acum are limita
	 *
	 * @return string
	 */
	public function getQuery() {
		if ($this->items_per_page > 0) {
			$this->query .= " LIMIT " . $this->page * $this->items_per_page . "," . $this->items_per_page;	
		}
		return $this->query;
	}

	/**
	 * Intoarce codul html pentru pagini
	 *
	 * @param int $type - 0 pt url de forma ?pg=2... 1 pt url de forma /page2.html
	 * @return string
	 */
	public function getPagesStr($type=0) {
		$str='';

		$uri = isset($_SERVER['REDIRECT_URL'])  ? $_SERVER['REDIRECT_URL'] : $_SERVER['REQUEST_URI'];

		$chr = '?';
		if(strstr($uri, '?')) {
			$chr = '&';
		}
		if($type==0) {
			if(!preg_match('@pg=[0-9]+@', $uri)) {
				$uri .= $chr."pg=0";
			}
		} else {
			if(!preg_match('@page[0-9]+\.html@', $uri)) {
				$uri .= 'page0.html';
			}
		}
		
		if($this->pages > 1) {
			if($this->page - $this->c_pages <=0) {
				$start_page = 0;
				$end_page = min($this->pages, $start_page+2 * $this->c_pages);
			} else if($this->page+$this->c_pages >= $this->pages) {
				$end_page = $this->pages;
				$start_page = max(0, $end_page-2 * $this->c_pages);
			} else {
				$start_page = $this->page - $this->c_pages;
				$end_page = $this->page + $this->c_pages;
			}
		
			
		
			for($i=$start_page; $i<$end_page; $i++){
				if($i == $start_page){
					$str = '<div class="pg"><div class="title">Pagina:</div><ul>';
					if($start_page > 0) {
						if($type==0) {
							$str .= '<li><a href="'.preg_replace('@[&\?]pg=[0-9]+@', '', $uri).'">«</a></li>';
						} else {
							$str .= '<li><a href="'.preg_replace('@page[0-9]+\.html@', '', $uri).'">«</a></li>';
						}
					}
				}
				if($this->page == $i) {
					$str .= '<li class="current">'.($i+1).'</li>';
				}
				else {
					if($type==0) {
						$str .= '<li><a href="'.preg_replace('@([&\?])pg=[0-9]+@', ($i > 0 ? "\\1pg=$i" : ''), $uri).'">'.($i+1).'</a></li>';
					} else {
						$str .= '<li><a href="'.preg_replace('@page[0-9]+\.html@', ($i > 0 ? "page$i.html" : ''), $uri).'">'.($i+1).'</a></li>';
					}
				}
				if($i==$end_page-1){
					if($end_page < $this->pages) {
						if($type==0) {
							$str .= '<li><a href="'.preg_replace('@([&\?])pg=[0-9]+@', '\\1pg='.($this->pages-1), $uri).'">»</a></li>';
						} else {
							$str .= '<li><a href="'.preg_replace('@page[0-9]+\.html@', 'page'.($this->pages-1).'.html', $uri).'">»</a></li>';
						}
					}
					$str .='</ul></div><div class="clearboth"></div>';
				}
			}
		}
		return $str;
	}
}
