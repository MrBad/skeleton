<?php
namespace Classes;
class Calendar 
{
	var $sel_year;
	var $sel_day;
	var $sel_mounth;
	
	var $cal_str='';
	
	var $week_day;		// prima zi a lunii

	var $link;			// care e link-ul - parametrul de modificat - ...&data=2006-11-30 | /2006-11-30/

	var $no_days;		// numarul de zile din luna selectata;
	var $str_days = array('', 'L','M','M','J','V', 'S', 'D');
//	var $str_mounths = array('', 'Ian','Feb','Mar','Apr','Mai', 'Iun', 'Iul', 'Aug', 'Sep', 'Oct', 'Noi', 'Dec');
	var $str_mounths = array('', 'Jan','Feb','Mar','Apr','May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');
	var $check_date_callback='';		// callback catre functia de validare a datei
	
	var $year_callback='year_exists';
	var $month_callback='month_exists';
	var $all_years_callback='all_years';

	function calendar($link, $sel_date, $callback)
	{
		setlocale(LC_ALL, 'en_US');
		$parts = explode('-', $sel_date);
		foreach($parts as $key=>$val) {
			$parts[$key] = (int)$val;
		}
		list($this->sel_year, $this->sel_mounth, $this->sel_day) = $parts;
		
		$sel_ts = mktime(0,0,0,$this->sel_mounth, 1 , $this->sel_year);
		$this->week_day = date('w', $sel_ts);
		$this->no_days = date('t', $sel_ts);
		if(is_callable($callback)) {
			$this->check_date_callback = $callback;
		} else {
			die('Functia de validare a datei nu exista!');
		}
	}
	
	function build()
	{
		$prev_year=$this->sel_year-1;
		$prev_mounth=$this->sel_mounth-1;
		$next_year=$this->sel_year+1;
		$next_mounth=$this->sel_mounth+1;
		
		$ts = mktime(0,0,0,$this->sel_mounth, $this->sel_day, $this->sel_year);
		$prev_year_exists = call_user_func($this->year_callback, $this->sel_year-1);
		$prev_month_exists = call_user_func($this->month_callback, $this->sel_year-($this->sel_mounth-1 == 0 ? 1:0), $this->sel_mounth-1 == 0 ? 12:$this->sel_mounth-1);
		$next_year_exists = call_user_func($this->year_callback, $this->sel_year+1);
		$next_month_exists = call_user_func($this->month_callback, $this->sel_year+($this->sel_mounth+1 > 12 ? 1:0), $this->sel_mounth+1 > 12 ? 1:$this->sel_mounth+1);
		
		$all_years = call_user_func($this->all_years_callback);
		
		$this->cal_str	.= '<table border="0" cellspacing="1" cellpadding="0" class="calendar"><tr class="header">'."\n";
		// prev year //
		$this->cal_str	.= $prev_year_exists ? '<td class="prev"><a href="/news/archive-from-'
						. str_pad($this->sel_day,2,0,STR_PAD_LEFT)
						. '-'.str_pad($this->sel_mounth,2,0,STR_PAD_LEFT)
						. '-'.$prev_year
						.'/">&laquo;</a></td>'."\n" : '<td class="prev">&laquo;</td>';
						
		// prev mounth //
		$this->cal_str	.= $prev_month_exists ?'<td class="prev"><a href="/news/archive-from-' 
						.  str_pad($this->sel_day,2,0,STR_PAD_LEFT)
						.  '-'.str_pad($prev_mounth==0 ? 12 : $prev_mounth, 2, 0, STR_PAD_LEFT)
						.  '-'.($prev_mounth==0 ? $prev_year : $this->sel_year)						
						.'/">&lsaquo;</a></td>'."\n" : '<td class="prev">&lsaquo;</td>';
						
		// title //
		$this->cal_str	.= '<td class="title" colspan="3">' . "\n";
		$this->cal_str	.= '<form method="post" action="">' . "\n";
//		$this->cal_str	.= '<select onChange="this.form.submit()" name="data[month]">' . "\n";
		$this->cal_str	.= '<select id="cal_month" name="data[month]">' . "\n";
		$mstr = array_shift($this->str_mounths);
		for ($i=0; $i < count($this->str_mounths); $i++) {
			$this->cal_str  .= '<option value="'.($i+1).'"'.($this->sel_mounth==($i+1) ? ' selected="selected"':'')
							.(! call_user_func($this->month_callback, $this->sel_year, $i+1) ? ' disabled="disabled"' : '')
							.'>'.$this->str_mounths[$i].'</option>'. "\n";
		}
		$this->cal_str	.=  '</select>'. "\n";
//		$this->cal_str	.=  '<select onChange="this.form.submit()" name="data[year]">'. "\n";
		$this->cal_str	.=  '<select id="cal_year" name="data[year]">'. "\n";
		for ($i=0; $i < count($all_years); $i++) {
			$this->cal_str .= '<option value="'.$all_years[$i].'" '.($this->sel_year==$all_years[$i] ? 'selected="selected"':'').'>'.$all_years[$i].'</option>'. "\n";
		}
		$this->cal_str	.=  '</select>'. "\n";
		$this->cal_str	.=  '<input type="hidden" name="data[day]" value="'.$this->sel_day.'"/>'. "\n";
		$this->cal_str	.=  '</form>'. "\n";
		
		
		// next mounth //
		$this->cal_str	.= $next_month_exists ? '<td class="next"><a href="/news/archive-from-'
						.  str_pad($this->sel_day,2,0,STR_PAD_LEFT)
						.  '-'.str_pad($next_mounth==13 ? 1 : $next_mounth, 2, 0, STR_PAD_LEFT)
						.  '-'.($next_mounth==13?$this->sel_year+1:$this->sel_year)
						.  '/">&rsaquo;</a></td>'."\n" : '<td class="next">&rsaquo;</td>';
						
		// next year //
		$this->cal_str	.= $next_year_exists ? '<td class="next"><a href="/news/archive-from-' 
						.	str_pad($this->sel_day,2,0,STR_PAD_LEFT)
						.	'-'.str_pad($this->sel_mounth,2,0,STR_PAD_LEFT) 
						.	'-'.$next_year
						.	'/">&raquo;</a></td>'."\n":'<td class="next">&raquo;</td>';
		
		$this->cal_str .= '</tr>'."\n";
		$this->cal_str .= '<tr>';

		
		// zilele din sapt //
		for($i = 1; $i <= 7; $i++) {
			$this->cal_str .='<td class="dname">' . $this->str_days[$i] . '</td>'."\n";
		}
		$this->cal_str .= '</tr><tr>';
		$e =  $this->week_day == 0 ? $this->week_day=7 : $this->week_day; // ziua zero este ziua 7 - conversie din cal us in ro
		for($j=1; $j < $e; $j++) {
			$this->cal_str .='<td class="na">&nbsp;</td>'."\n";
		}
		
		// zilele - numeric //
		for($i=1; $i <= $this->no_days; $i++) {
			
			$date_exists = call_user_func($this->check_date_callback,
								$this->sel_year.'-'.str_pad($this->sel_mounth,2,0,STR_PAD_LEFT).'-'.str_pad($i,2,0,STR_PAD_LEFT));

			$this->cal_str .= '<td class="'.($date_exists ? (($i==$this->sel_day)? 'selected':'av'):'na').'">'."\n";

//			if($i==$this->sel_day) {
//				$this->cal_str .= $i;
//			} else {
				if($date_exists) {
					//
					//	Aceasta data este disponibila in calendar
					//

					$this->cal_str	.= '<a href="/news/archive-from-'
									.	str_pad($i,2,0,STR_PAD_LEFT)
									.	'-'.str_pad($this->sel_mounth,2,0,STR_PAD_LEFT)
									.	'-'.$this->sel_year
									.'/">'.$i.'</a>';
				} 
				else {
					//
					//	Aceasta data nu este in calendar, deci nu pun href
					//
					$this->cal_str .= $i;
				}
//			}
			
			$this->cal_str .-"</td>"."\n";
			if($j == 7) {
				$this->cal_str .= '</tr><tr>';
				$j=0;
			}
			$j++;
		}
		while($j <= 7 && $j > 1) {
			$this->cal_str .='<td class="na">&nbsp;</td>'."\n";
			$j++;
		}
		$this->cal_str .="</tr></table>"."\n";
	}

	function get_calendar()
	{
		return $this->cal_str;
	}
}

//
//	cache-ul pt luni
//
$available_days=array();

//
//	Callback-ul pentru validarea datei X
//		nota: selectul se face o singura data - 1 query per luna, nu la fiecare data verificata!!!
//
function date_has_items($data) {
	global $available_days;
	$sql = Mysql::getInstance();
	if (empty($available_days)) {
		$available_days = array();
	}
	
	$sql->SetFetchType(FETCH_ASSOC);
	if(empty($available_days)) {
		if(preg_match('@^([0-9]{4})-([0-9]{2})-[0-9]{2}$@', $data, $match)){
			$year = $match[1];
			$mounth = $match[2];
			$ts_start = mktime(0,0,0,$mounth,0,$year);
			$ts_end = mktime(0,0,0,$mounth+1,1,$year);
//			$query="SELECT DISTINCT(FROM_UNIXTIME(added_ts, '%Y-%m-%d')) as data FROM items 
//					WHERE (added_ts between $ts_start AND $ts_end) 
//					AND added_ts < unix_timestamp()";
//			$query="SELECT DISTINCT(CONCAT(Y, '-', M, '-', D)) as data FROM items 
//					WHERE (added_ts between $ts_start AND $ts_end) 
//					AND added_ts < unix_timestamp()";
//			$query="SELECT DISTINCT(CONCAT(Y, '-', M, '-', D)) as data FROM items 
//					WHERE Y=".$year." AND M=".$mounth."  
//					AND added_ts < unix_timestamp()";
			$query="SELECT DISTINCT(CONCAT(Y, '-', M, '-', D)) as data FROM items_dates 
					WHERE Y=".$year." AND M=".$mounth." ";
			if($sql->Query($query)) {
				for($i=0; $i<$sql->rows; $i++) {
					$sql->GetRow($i);
					$available_days[]=$sql->data['data'];
				}
			}
		}
	}
	$sql->SetFetchType(FETCH_OBJ);
	if(in_array($data, $available_days)) {
		return true;
	}
	return false;
}

function year_exists($year) {
	$sql = Mysql::getInstance();
	$ts_start = mktime(0,0,0,0,0,$year);
	$ts_end = mktime(0,0,0,0,0,$year+1);
	$query="SELECT count(1) FROM items 
			WHERE Y=" . (int) $year;
	$query="SELECT num_items FROM items_dates
			WHERE Y=" . (int) $year;
	
	return $sql->QueryItem($query) > 0 ? true:false;
}
function month_exists($year, $month) {
	$sql = Mysql::getInstance();
	$ts_start = mktime(0,0,0,$month,0,$year);
	$ts_end = mktime(0,0,0,$month+1,0,$year);
	$query="SELECT count(1) FROM items 
			WHERE Y=".(int) $year." AND M=". (int) $month;
	$query="SELECT num_items FROM items_dates 
			WHERE Y=".(int) $year." AND M=". (int) $month;
	return $sql->QueryItem($query) > 0 ? true:false;
}

function all_years(){
	$sql = Mysql::getInstance();
	$years = array();
//	$query="SELECT DISTINCT(FROM_UNIXTIME(added_ts, '%Y')) as year FROM items";
	$query="SELECT DISTINCT(Y) as year FROM items";
	$query="SELECT DISTINCT(Y) as year FROM items_dates";
	if ($sql->Query($query)) {
		for ($i=0; $i < $sql->rows; $i++) {
			$sql->GetRow($i);
			$years[] = $sql->data->year;
		}
	}
	return $years;
}


/*
$data = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');
$calendar = new calendar($_SERVER['SCRIPT_URI'].'?'.$_SERVER['QUERY_STRING'], $data, 'check_date');
$calendar->build();
echo $calendar->get_calendar();
*/
?>