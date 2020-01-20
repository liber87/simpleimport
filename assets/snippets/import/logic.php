<?php
	define('MODX_API_MODE', true);
	define('IN_MANAGER_MODE', false);	
	include_once("./../../../index.php");	
	$modx->db->connect();	
	if (empty ($modx->config)) {
		$modx->getSettings();
	}
	if ($_FILES['file'])
	{	
		
		$uploaddir = MODX_BASE_PATH.'/assets/import/';
		$uploadfile = $uploaddir.'import.xlsx';
		copy($_FILES['file']['tmp_name'], $uploadfile);
		include_once(MODX_BASE_PATH."assets/lib/simplexlsx.class.php");	
		$xlsx = new SimpleXLSX($uploadfile);		
		$arr = $xlsx->rows(1);				
		$text = "<?php".PHP_EOL.'$xls='.var_export($arr,1).';';
		$f=fopen(MODX_BASE_PATH."assets/import/var.php",'w');
		fwrite($f,$text);
		fclose($f);
		echo 'ok';
		$_SESSION['start'] =  microtime(true);
		$_SESSION['prods'] =  array();
		$_SESSION['prods3'] =  array();
		$_SESSION['begin'] =  0;
		$_SESSION['found']=0;
		$_SESSION['notfound']=0;	
		$_SESSION['count']=count($arr)-1;	
		
		exit();
	}
	if (isset($_GET['import']))
	{
		include_once(MODX_BASE_PATH."assets/lib/MODxAPI/modResource.php");
		require_once(MODX_BASE_PATH."assets/import/var.php");
		$codes = array_column($xls, 2);				
		for($c=$_SESSION['begin'];$c<($_SESSION['begin']+50);$c++)
		{
			if ($c>$_SESSION['count']) break;
			$doc = new modResource($modx);		
			
			/* Заполняем массив для вставки*/			
			$fields = array(
			'pagetitle'=>$xls[$c][1],
			'alias'=>$modx->stripAlias($xls[$c][1]),
			'template'=>5,
			'published'=>1,
			'parent'=>2,
			'articul' => $xls[$c][0],
			'price' => $xls[$c][2],
			'img' => $xls[$c][3],
			'dots' => $val[4]
			);
			
			/* Находим есть ли такая позиция, в данном случае по артикулу*/			
			$sql = 'select contentid from '.$modx->getFullTableName('site_tmplvar_contentvalues').' 
			where value = "%'.trim($xls[$c][0]).'%" and tmplvarid = 21';
			$id = $modx->db->getValue($sql);
			
			if ($id) 
			{
				$doc->edit($articles[$val[0]]);
				foreach($fields as $key => $val) $doc->set($key, $val);
				$_SESSION['found'] = $_SESSION['found']+1;
			}
			else 
			{
				$_SESSION['notfound'] = $_SESSION['notfound']+1;
				$doc->create($fields);
			}
			$doc->save(true, false);
		}
		
		
		$percent = (($_SESSION['begin']/$_SESSION['count'])*100);
		$_SESSION['begin'] = $_SESSION['begin']+50;
		if ($_SESSION['begin']<$_SESSION['count']) 
		$status = 'continue';		
		else 
		{
			$status = 'complete';
			$percent = 100;
			$_SESSION['begin']=$_SESSION['count'];
			
			
			$modx->clearCache('full');
		}
		
		$time = microtime(true)-$_SESSION['start'];
		header('Content-Type: application/json');		
		
		$data = array('time'=>round($time,2),'begin'=>$_SESSION['begin'],'found'=>$_SESSION['found'],'notfound'=>$_SESSION['notfound'],'count'=>$_SESSION['count'],'status'=>$status,'percent'=>round($percent,2));
		echo json_encode($data);
		exit();
	}
