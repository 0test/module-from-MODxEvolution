<?php
if(IN_MANAGER_MODE!='true' && !$modx->hasPermission('exec_module')) die('ERROR');

//	Конфигурация модуля
//	&sectionId=ID;integer;0

$Template=new Template;
$bigAction = $_GET['a'];
$moduleId = $_GET['id'];
$FullTableName = $modx->getFullTableName('site_content');

switch($_REQUEST['action']){
	default:	//	Действия при загрузке модуля
		$section=$params['sectionId'];	//	Получаем из конфига id раздела
		$result = $modx->db->select('id,pagetitle,introtext', $FullTableName, 'parent='.$section, '', 30);
		if($modx->db->getRecordCount($result)>= 1){
			while($row = $modx->db->getRow( $result )){
				if($class){$class="gridAltItem";}else{$class="gridItem";}	//	Оформление ячеек, "зёбра"			
				$Template->lang['phpwork'] .='<tr class="'.$class.'">';
				$Template->lang['phpwork'] .='<td >'.$row["id"].'</td>';
				$Template->lang['phpwork'] .='<td>'.$row["pagetitle"].'</td>';
				$Template->lang['phpwork'] .='<td >'.$row["introtext"].'</td>';
				$Template->lang['phpwork'] .='<td ><a href="index.php?&a=' . $bigAction. '&id='.$moduleId . '&editDoc='. $row['id'].'&action=edit" data-id="'.$row["id"].'">' . $Template->lang['edit'] . '</a></td>';
				$Template->lang['phpwork'] .='</tr>';
			}
		}
		else{
			$Template->lang['phpwork'] = '';
		}
		$tpl = Template::parseTemplate($Template->getTpl(dirname( __FILE__ ).'/templates/main.html'),$modx->config);
		$tpl = Template::parseTemplate($tpl ,$Template->lang);
		echo $tpl;
		
	break;
		
	case 'edit':

		if($_POST){
			$fields = array(
			"pagetitle" => $modx->db->escape($_POST["pagetitle"]),
			"introtext" => $modx->db->escape($_POST["introtext"])
			);
			$result = $modx->db->update($fields, $FullTableName, "id=" . $_GET['editDoc']);
			if($result){
				$Template->lang['phpwork'] = $Template->lang['save_success'];
			}
			else{
				$Template->lang['phpwork'] = $Template->lang['save_error'];
			}
		}
		else{
			$result = $modx->db->select('id,pagetitle,introtext', $FullTableName, 'id='.$modx->db->escape($_GET['editDoc']));
			if($modx->db->getRecordCount($result)>= 1){
				
				while($row = $modx->db->getRow( $result )){
					if($class){$class="gridAltItem";}else{$class="gridItem";}	//	Оформление ячеек, "зёбра"			
					$Template->lang['phpwork'] .='<tr class="'.$class.'">';
					$Template->lang['phpwork'] .='<td>'.$Template->lang['header'].'</td>';
					$Template->lang['phpwork'] .='<td><input name="pagetitle" type="text" maxlength="255" value="'.$row["pagetitle"].'" class="inputBox" onchange="documentDirty=true;" spellcheck="true"></td>';
					$Template->lang['phpwork'] .='</tr>';
					$Template->lang['phpwork'] .='<tr class="'.$class.'">';
					$Template->lang['phpwork'] .='<td>'.$Template->lang['table_header2'].'</td>';
					$Template->lang['phpwork'] .='<td><textarea id="introtext" name="introtext" class="inputBox" rows="3" cols="" onchange="documentDirty=true;">'.$row["introtext"].'</textarea></td>';				
					$Template->lang['phpwork'] .='</tr>';
				}
			}
		}
		$tpl = Template::parseTemplate($Template->getTpl(dirname( __FILE__ ).'/templates/edit.html'),$modx->config);
		$tpl = Template::parseTemplate($tpl ,$Template->lang);
		echo $tpl;		
	break;
}

class Template{
	public $lang;
	function __construct(){
		global $modx;
		$lang = $modx->config['manager_language'];
		if (file_exists( dirname(__FILE__) .  '/lang/'.$lang.'.php')){
			include_once(dirname(__FILE__) .  '/lang/'.$lang.'.php');
		} else {
			include_once(dirname(__FILE__) .  '/lang/english.php');
		}
		$this->lang = $_field;
	}
	
	function getTpl($file){
		ob_start();
		include($file);
		$tpl = ob_get_contents();  
		ob_end_clean(); 
		return $tpl;
	}
	
	static function parseTemplate($tpl,$field){
		foreach($field as $key=>$value)  $tpl = str_replace('[+'.$key.'+]',$value,$tpl);
		return $tpl;
	}
}
?>