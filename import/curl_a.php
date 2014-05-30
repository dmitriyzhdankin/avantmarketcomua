<?php

//DBCONNECTR
$link = mysql_connect('localhost', 'newwa', 'newwa');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
//echo 'Connected successfully';
mysql_select_db('newwa'); 
		  mysql_query('SET character_set_database = utf8');
		  mysql_query('SET NAMES utf8');
//DBCONNECTRdom

function xml2array($domnode)
{
    $nodearray = array();
    $domnode = $domnode->firstChild;
    while (!is_null($domnode))
    {
        $currentnode = $domnode->nodeName;
        switch ($domnode->nodeType)
        {
            case XML_TEXT_NODE:
                if(!(trim($domnode->nodeValue) == "")) $nodearray['cdata'] = $domnode->nodeValue;
            break;
            case XML_ELEMENT_NODE:
                if ($domnode->hasAttributes() )
                {
                    $elementarray = array();
                    $attributes = $domnode->attributes;
                    foreach ($attributes as $index => $domobj)
                    {
                        $elementarray[$domobj->name] = $domobj->value;
                    }
                }
            break;
        }
        if ( $domnode->hasChildNodes() )
        {
            $nodearray[$currentnode][] = xml2array($domnode);
            if (isset($elementarray))
            {
                $currnodeindex = count($nodearray[$currentnode]) - 1;
                $nodearray[$currentnode][$currnodeindex]['@'] = $elementarray;
            }
        } else {
            if (isset($elementarray) && $domnode->nodeType != XML_TEXT_NODE)
            {
                $nodearray[$currentnode]['@'] = $elementarray;
            }
        }
        $domnode = $domnode->nextSibling;
    }
    return $nodearray;
}

  $upd_product[]="";
  $add_product[]="";

 function get_product_list($filename)
 {      global $crm_login, $crm_pass, $add_product, $upd_product, $elco_to_itop_cat;
	              $dom = new DOMDocument();
                  $dom->load($filename);
				  $root = $dom->documentElement;
                  $nodeList = $root->childNodes;
                  $i=0;
				  $arr=xml2array($root);
				  $products = array();
				   foreach($arr['Worksheet'][0]['Table'][0]['Row'] AS $key=>$product ){
					 if($product['Cell'][0] && $product['Cell'][2] && $product['Cell'][4] && $product['Cell'][6] && $product['Cell'][8] && $product['Cell'][10]){
					 $products[$key] = array();  
					 foreach($product['Cell'] AS $xkey=>$xproduct ){   				         
					  switch ($xkey) {
					  case '0':
					  $products[$key]['name'] = $xproduct['Data'][0]['cdata'];
					  break;
					  case '2':
					  $products[$key]['sku'] = $xproduct['Data'][0]['cdata'];
					  break;
                      case '4':
					  $products[$key]['purchase_price'] = floatval($xproduct['Data'][0]['cdata']);
					  break;
                      case '6':					  
					  $products[$key]['price'] = floatval($xproduct['Data'][0]['cdata']);
					  break;
                      case '8':
					  $products[$key]['count1'] = intval($xproduct['Data'][0]['cdata']);					  
					  break;
                      case '10':
					  $products[$key]['count2'] = intval($xproduct['Data'][0]['cdata']);					  
					  break;
					   }
					  }

					 }
				   }
				  
				 $db_products = array();

				 foreach($products as $product){
				  if($product['sku'] && $product['sku'] != ''){
					  $qc="SELECT s.purchase_price AS sku_price, s.sku, s.id AS sku_id, p.price, p.id, p.name FROM shop_product_skus AS s
					  LEFT JOIN shop_product AS p ON (p.id = s.product_id)
					  WHERE s.sku='".$product['sku']."'"; 
                  $cres=mysql_query($qc);
                  $crow=mysql_fetch_array($cres);				  
				  if($crow ){ 
				         $db_products[$crow['sku']] = array();
				         $db_products[$crow['sku']]['id'] = intval($crow['id']);
						 $db_products[$crow['sku']]['sku_id'] = intval($crow['sku_id']);
						 $db_products[$crow['sku']]['name'] = $crow['name'];
						 $db_products[$crow['sku']]['purchase_price'] = $product['purchase_price'];
						 $db_products[$crow['sku']]['price'] = $product['price'];
						 $db_products[$crow['sku']]['count1'] = $product['count1'];
						 $db_products[$crow['sku']]['count2'] = $product['count2'];
						  }
				     }
				  }
                 $pU = 0; $sU = 0; $c1 = 0; $c2 = 0;
				 foreach($db_products as $db_key => $db_product){							 
					 
					 $updateProduct = mysql_query('UPDATE shop_product SET price='.$db_product['price'].',
					 count='.($db_product['count1'] + $db_product['count2']).' WHERE id = '.$db_product['id'].'');
					 $updateSKU = mysql_query('UPDATE shop_product_skus SET price='.$db_product['price'].', purchase_price='.$db_product['purchase_price'].'
					 WHERE sku = "'.$db_key.'" AND product_id = '.$db_product['id'].' AND id = '.$db_product['sku_id'].' ');
					 
					 $isCount1Query = mysql_query('SELECT count FROM  shop_product_stocks 
					 WHERE product_id = "'.$db_product['id'].'" AND sku_id = '.$db_product['sku_id'].' AND stock_id = 3');
					 $isCount1Res = mysql_fetch_array($isCount1Query);
					 if(!$isCount1Res){
					  $updateProduct = mysql_query('INSERT INTO shop_product_stocks (count,product_id,sku_id,stock_id) VALUES
					('.$db_product['count1'].',"'.$db_product['id'].'" ,'.$db_product['sku_id'].',3)');
					 } else {
					 $updateProduct = mysql_query('UPDATE  shop_product_stocks SET count='.$db_product['count1'].'
					 WHERE product_id = "'.$db_product['id'].'" AND sku_id = '.$db_product['sku_id'].' AND stock_id = 3');
					 }
					 $isCount2Query = mysql_query('SELECT count FROM  shop_product_stocks 
					 WHERE product_id = "'.$db_product['id'].'" AND sku_id = '.$db_product['sku_id'].' AND stock_id = 1');
					 $isCount2Res = mysql_fetch_array($isCount2Query);
					 if(!$isCount2Res){
					 $updateProduct = mysql_query('INSERT INTO shop_product_stocks (count,product_id,sku_id,stock_id) VALUES
					('.$db_product['count2'].',"'.$db_product['id'].'" ,'.$db_product['sku_id'].',1) ');
					 } else {
					  $updateProduct = mysql_query('UPDATE  shop_product_stocks SET count='.$db_product['count2'].'
					 WHERE product_id = "'.$db_product['id'].'" AND sku_id = '.$db_product['sku_id'].' AND stock_id = 1 ');
					 }
					 
				 }
              } 
 
     
	  
	  $filename = $_GET['f'];
	  if($filename && $filename != '' && file_exists('../wa-data/public/site/'.$filename)){
	  get_product_list('../wa-data/public/site/'.$filename);
	  } else { echo 'Файл задан не верно!'; }
     mysql_close($link);     

?>
