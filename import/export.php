<?php

//DBCONNECTR
$link = mysql_connect('localhost', 'newwa', 'newwa');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}

mysql_select_db('newwa'); 
		  mysql_query('SET character_set_database = utf8');
		  mysql_query('SET NAMES utf8');

function stockCount($count){
 if($count > 0 && $count < 6) { return 1; }
 else if($count > 5 && $count < 11) { return 6; }	
 else if($count > 10) { return 11; }
 else { return 0; }	
}

function exportCSV($dir, $limit, $where)
{
	             $products = array();
                 $qc="SELECT s.purchase_price AS sku_price, s.sku, p.price, p.name, p.summary, p.description, p.count, p.url, s.id AS sku_id, p.id, sc.name AS category_name FROM shop_product_skus AS s
					  LEFT JOIN shop_product AS p ON (p.id = s.product_id) 
					  LEFT JOIN shop_category AS sc ON (sc.id = p.category_id)
					  WHERE p.status = 1 AND p.category_id != '' ".$where."
					  ORDER BY sc.name ASC
					  ".$limit." "; 
                  $cres=mysql_query($qc);
                  $crow=mysql_fetch_array($cres);	
				  $i = 1;	
				  
				   $products[0][0] =  iconv("UTF-8", "Windows-1251",'Артикул');
				   $products[0][1] =  iconv("UTF-8", "Windows-1251",'Название');
				   $products[0][2] =  iconv("UTF-8", "Windows-1251",'Ссылка');				   
				   $products[0][3] =  iconv("UTF-8", "Windows-1251",'Краткое описание');
				   $products[0][4] =  iconv("UTF-8", "Windows-1251",'Полное описание');
				   $products[0][5] =  iconv("UTF-8", "Windows-1251",'Цена');
				   $products[0][6] =  iconv("UTF-8", "Windows-1251",'Оптовая цена');
				   $products[0][7] =  iconv("UTF-8", "Windows-1251",'Склад 1');
				   $products[0][8] =  iconv("UTF-8", "Windows-1251",'Склад 2');
				   $products[0][9] =  iconv("UTF-8", "Windows-1251",'Суммарно на складах');
				   $products[0][10] =  iconv("UTF-8", "Windows-1251",'Категория');
				   $products[0][11] =  iconv("UTF-8", "Windows-1251",'Изображения');
				  		   
				   while($row=mysql_fetch_array($cres)){
				   $isCount1Query = mysql_query('SELECT count FROM  shop_product_stocks 
					 WHERE product_id = "'.$row['id'].'" AND sku_id = '.$row['sku_id'].' AND stock_id = 3');
				   $isCount1Res = mysql_fetch_array($isCount1Query);

				   $isCount2Query = mysql_query('SELECT count FROM  shop_product_stocks 
					 WHERE product_id = "'.$row['id'].'" AND sku_id = '.$row['sku_id'].' AND stock_id = 1');
				   $isCount2Res = mysql_fetch_array($isCount2Query);
				   
				   $cat = ''.$row['category_name'];
				  $catQuery = mysql_query('SELECT sc.name FROM shop_category_products AS scp
				     LEFT JOIN shop_category AS sc ON (sc.id = scp.category_id)
					 WHERE scp.product_id = "'.$row['id'].'" ');
					 while($catRes=mysql_fetch_array($catQuery)){
				       if($catRes['name'] != $row['category_name']) { $cat .= ', '.$catRes['name']; }
					 }
				   
				   $images = '';
				   $imageQuery = mysql_query('SELECT DISTINCT id, product_id FROM shop_product_images 
					 WHERE product_id = "'.$row['id'].'" ');
					 $im = 0;
					 while($imageRes=mysql_fetch_array($imageQuery)){
					   $f1 = substr(strip_tags($imageRes['product_id']), -2);
					   $f2 = substr(strip_tags($imageRes['product_id']),0, -2);
					   if(strlen($f2) < 2) { $f2 = '0'.$f2; }
					   if($im > 0 ) { $images .= ' , '; }
				       $images .= 'http://avantmarket.com.ua/wa-data/public/shop/products/'.$f1.'/'.$f2.'/'.$imageRes['product_id'].'/images/'.$imageRes['id'].'/'.$imageRes['id'].'.750x0.jpg';
					   $im++;
					 }
					 
				   $products[$i][0] = iconv("UTF-8", "Windows-1251",$row['sku']);
				   $products[$i][1] = iconv("UTF-8", "Windows-1251",$row['name']);
				   $products[$i][2] = 'http://avantmarket.com.ua/'.$row['url'];				   
				   $products[$i][3] = iconv("UTF-8", "Windows-1251",str_replace("&nbsp;"," ",strip_tags($row['summary'])));
				   $products[$i][4] = iconv("UTF-8", "Windows-1251",str_replace("&nbsp;"," ",strip_tags($row['description'])));
				   $products[$i][5] = number_format($row['price'], 2, ',', '');
				   $products[$i][6] = number_format($row['sku_price'], 2, ',', '');
				   $products[$i][7] = stockCount($isCount1Res['count']);
				   $products[$i][8] = stockCount($isCount2Res['count']);
				   $products[$i][9] = stockCount($row['count']);
				   $products[$i][10] = iconv("UTF-8", "Windows-1251",$cat);
				   $products[$i][11] = $images;
				   
				   $i++;
				  }	 
				  
				  
				
				  if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) { 
				    if( filetype($dir.$file) == 'file' && strstr($dir.$file, '.csv')  ) {
						   unlink($dir.$file); 
						 }
				    }
				    closedir($dh);
                  }
                  
                
				  
				  $filename = 'avantmarket-products-'.date("m.d.y.h");
				  
				  $fp = fopen( $dir.$filename.'.csv', 'w');
				  foreach ($products as $fields) {
                    fputcsv($fp, $fields, ';');
                  }
                if($fp) { echo 'File: http://avantmarket.com.ua/csv/'.$filename.'.csv, products: '.(count($products)-1).'<br/>'; fclose($fp); }				  
      } 
	  
	  
	 exportCSV("../csv/", "", " AND p.category_id != 736 AND  p.category_id != 727");
	   
     mysql_close($link);     

?>
