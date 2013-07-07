<?php
function formatDate($timestamp)
{
	@$output = '';
	@$fulldate = date( "j # Y в H:i", $timestamp );
	@$mon = date("m", $timestamp );
		switch( $mon )
		{
			case  1: { $mon='Января'; } break;
			case  2: { $mon='Февраля'; } break;
			case  3: { $mon='Марта'; } break;
			case  4: { $mon='Апреля'; } break;
			case  5: { $mon='Мая'; } break;
			case  6: { $mon='Июня'; } break;
			case  7: { $mon='Июля'; } break;
			case  8: { $mon='Августа'; } break;
			case  9: { $mon='Сентября'; } break;
			case 10: { $mon='Октября'; } break;
			case 11: { $mon='Ноября'; } break;
			case 12: { $mon='Декабря'; } break;
		}
		/*$dayofweek = date("D", $timestamp );
		switch ($dayofweek)
		{
			case 'Sun': { $dayofweek = 'SOSкресение'; } break;
			case 'Mon': { $dayofweek = 'Понедельник'; } break;
			case 'Tue': { $dayofweek = 'Вторник'; } break;
			case 'Wed': { $dayofweek = 'Среда'; } break;
			case 'Thu': { $dayofweek = 'Четверг'; } break;
			case 'Fri': { $dayofweek = 'Девчатница'; } break;
			case 'Sat': { $dayofweek = 'Субкота'; } break;
		}*/
		$fulldate = str_replace( '#', $mon, $fulldate );
		/*$fulldate = str_replace( '@', $dayofweek, $fulldate );*/
		return $output.$fulldate;
}

function countcomments($toKey)
{
 $countcom = mysql_query("SELECT COUNT(`id`) FROM `comments` WHERE `keyID` = '$toKey'");
 $countcom = mysql_fetch_row($countcom);
 $countcom = max($countcom);
 return $countcom;
}

function makepagenav($start, $count, $total, $range = 0)
{
		$pg_cnt = ceil($total / $count);
		if ($pg_cnt <= 1) { return ""; }
		$idx_back = $start - $count;
		$idx_next = $start + $count;
		$cur_page = ceil(($start + 1) / $count);
		$res = "<li><span class=\"pages\">Страница " . $cur_page . " из " . $pg_cnt . ":</span></li>";
		if ($idx_back >= 0)
		{
			if ($cur_page > ($range + 1))
			{
				$res .= "<li><a href='?page=1'>1</a></li><li><span>...</span></li>";
			}
		}
		$idx_fst = max($cur_page - $range, 1);
		$idx_lst = min($cur_page + $range, $pg_cnt);
		if ($range == 0)
		{
			$idx_fst = 1;
			$idx_lst = $pg_cnt;
		}
		for ($i = $idx_fst; $i <= $idx_lst; $i++)
		{
			$offset_page = $i;
			if ($i == $cur_page)
			{
				$res .= "<li><span class=\"current\">".$i."</span></li>";
			}
			else
			{
				$res .= "<li><a href='?page=".$offset_page."'>".$i."</a></li>";
			}
		}
		if ($idx_next < $total)
		{
			if ($cur_page < ($pg_cnt - $range))
			{
				$res .= "<li><span>...</span></li><li><a href='?page=".($pg_cnt)."'>".$pg_cnt."</a></li>";
			}
		}    
		return "<div><ol>".$res."</ol></div>";
	}

function makepagenavsearch($start, $count, $total, $range = 0)
{	
	    $w = $_GET['search'];
		$pg_cnt = ceil($total / $count);
		if ($pg_cnt <= 1) { return ""; }
		$idx_back = $start - $count;
		$idx_next = $start + $count;
		$cur_page = ceil(($start + 1) / $count);
		$res = "<li><span class=\"pages\">Страница " . $cur_page . " из " . $pg_cnt . ":</span></li>";
		if ($idx_back >= 0)
		{
			if ($cur_page > ($range + 1))
			{
				$res .= "<li><a href='?page=1&search=" . $w . "'>1</a></li><li><span>...</span></li>";
			}
		}
		$idx_fst = max($cur_page - $range, 1);
		$idx_lst = min($cur_page + $range, $pg_cnt);
		if ($range == 0)
		{
			$idx_fst = 1;
			$idx_lst = $pg_cnt;
		}
		for ($i = $idx_fst; $i <= $idx_lst; $i++)
		{
			$offset_page = $i;
			if ($i == $cur_page)
			{
				$res .= "<li><span class=\"current\">".$i."</span></li>";
			}
			else
			{
				$res .= "<li><a href='?page=".$offset_page."&search=" . $w . "'>".$i."</a></li>";
			}
		}
		if ($idx_next < $total)
		{
			if ($cur_page < ($pg_cnt - $range))
			{
				$res .= "<li><span>...</span></li><li><a href='?page=".($pg_cnt)."&search=" . $w . "'>".$pg_cnt."</a></li>";
			}
		}    
		return "<div><ol>".$res."</ol></div>";
	}

function is_banned() {
    header("content-type:text/html;charset=utf8;");
    $myip = md5($_SERVER['REMOTE_ADDR']);
    $c = mysql_result(mysql_query("SELECT COUNT(*) FROM `bans` WHERE `ip` = '".$myip."' AND `time_end` > '".(time())."'"),0);
    if ($c>0) {
        die('
        ПОШЕЛ НАХУЙ, ТЫ ЗАБАНЕН
        ');
    }
}

?>