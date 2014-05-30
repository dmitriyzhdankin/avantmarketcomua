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


function exportXML($dir, $limit, $where)
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
				  
				  $doc = new DOMDocument('1.0', 'utf-8');
                  $doc->formatOutput = true;
				  
				   $root = $doc->createElement('products');
				   $root = $doc->appendChild($root);
				  
				  		   
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
					 
				   $product = $root->appendChild($doc->createElement('product'));		   
				   			   
				   $sku = $doc->createElement('sku');
                   $sku = $product->appendChild($sku);
                   $skutext = $doc->createTextNode(iconv("UTF-8", "UTF-8",$row['sku']));
                   $skutext = $sku->appendChild($skutext);
					 
				   $name = $doc->createElement('name');
                   $name = $product->appendChild($name);
                   $nametext = $doc->createTextNode($row['name']);
                   $nametext = $name->appendChild($nametext);				 
					 
				   $url = $doc->createElement('url');
                   $url = $product->appendChild($url);
                   $urltext = $doc->createTextNode('http://avantmarket.com.ua/'.$row['url']);
                   $urltext = $url->appendChild($urltext);
				   
				   $shortdescription = $doc->createElement('shortdescription');
                   $shortdescription = $product->appendChild($shortdescription);
                   $shortdescriptiontext = $doc->createTextNode(str_replace("&#13;","",str_replace("&nbsp;"," ",strip_tags($row['summary']))));
                   $shortdescriptiontext = $shortdescription->appendChild($shortdescriptiontext);				   
				   
				   $description = $doc->createElement('description');
                   $description = $product->appendChild($description);
                   $descriptiontext = $doc->createTextNode(str_replace("&#13;","",str_replace("&nbsp;"," ",strip_tags($row['description']))));
                   $descriptiontext = $description->appendChild($descriptiontext);
				   
				   $price = $doc->createElement('price');
                   $price = $product->appendChild($price);
                   $pricetext = $doc->createTextNode(number_format($row['price'], 2, ',', ''));
                   $pricetext = $price->appendChild($pricetext);

				   
				   $sku_price = $doc->createElement('sku_price');
                   $sku_price = $product->appendChild($sku_price);
                   $sku_pricetext = $doc->createTextNode(number_format($row['sku_price'], 2, ',', ''));
                   $sku_pricetext = $sku_price->appendChild($sku_pricetext);
				   
				   $count1 = $doc->createElement('count1');
                   $count1 = $product->appendChild($count1);
                   $count1text = $doc->createTextNode(stockCount($isCount1Res['count']));
                   $count1text = $count1->appendChild($count1text);
				   
				   $count2 = $doc->createElement('count2');
                   $count2 = $product->appendChild($count2);
                   $count2text = $doc->createTextNode(stockCount($isCount2Res['count']));
                   $count2text = $count2->appendChild($count2text);
				   
				   $count = $doc->createElement('count');
                   $count = $product->appendChild($count);
                   $counttext = $doc->createTextNode(stockCount($row['count']));
                   $counttext = $count->appendChild($counttext);
				   
				   $category = $doc->createElement('category');
                   $category = $product->appendChild($category);
                   $categorytext = $doc->createTextNode($cat);
                   $categorytext = $category->appendChild($categorytext);
				   
				   $img = $doc->createElement('images');
                   $img = $product->appendChild($img);
                   $imgtext = $doc->createTextNode($images);
                   $imgtext = $img->appendChild($imgtext);			   
				   
				   $i++;
				  }	 
				  
				  if ($dh = opendir($dir)) {
                    while (($file = readdir($dh)) !== false) { 
				    if( filetype($dir.$file) == 'file' && strstr($dir.$file, '.xml')  ) {
						   unlink($dir.$file); 
						 }
				    }
				    closedir($dh);
                  }
				  
				  $strxml = $doc->saveXML();
				  $filename = 'avantmarket-products-'.date("m.d.y.h");
				  
				  $fp = fopen( $dir.$filename.'.xml', 'w');
				  fwrite($fp, $strxml);
				  
                  if($fp) { echo 'File: http://avantmarket.com.ua/xml/'.$filename.'.xml, products: '.$i.'<br/>'; fclose($fp); }
				 
				  fclose($handle);
				  
				   
      } 
	  
	  
	 exportXML("../xml/", "", " AND p.category_id != 736 AND  p.category_id != 727");
	   
     mysql_close($link);     

?>
