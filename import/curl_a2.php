<?php

//DBCONNECTR
$link = mysql_connect('localhost', 'newwa', 'newwa');
if (!$link) {
    die('Could not connect: ' . mysql_error());
}
//echo 'Connected successfully';
mysql_select_db('newwa'); 

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
      

             
      
      
                 
 

 function get_product_list()
 {      global $crm_login, $crm_pass, $add_product, $upd_product, $elco_to_itop_cat;

              
      //          echo $res;        return;




             
                  $dom = new DOMDocument();
                  $dom->load('p.xml');
//                  echo $dom->saveXML();
                  
                  //$doc->load('book.xml');
                  
                  
              //    $element = $dom->getElementsByTagName('Предложение')->item(0);
              //    foreach($element->childNodes as $node){
              //          $data[$node->nodeName] = $node->nodeValue;
              //      }


                  
                  
                  $root = $dom->documentElement;
                  $nodeList = $root->childNodes;
                  
                  $i=0;
                  
                  
                  
                  
                  
                                    
                  $arr=   xml2array($root);
                 
                  $arr=current($arr);
                  
                  

                  $qc="SELECT currency_value FROM `SC_currency_types` WHERE `CID`=6";
                  $cres=mysql_query($qc);
                  $crow=mysql_fetch_array($cres);
                  $currency=$crow['currency_value'];
                  
                  
                 
                 foreach ($arr as $a)      // Цыкл обработки массива категорий
                 {
                     
               
                     $ELKOcode=$a['ELKOcode'][0];
                     $productName=$a['productName'][0];
                     $price=$a['price'][0];
                     $categoryCode=$a['categoryCode'][0];
                     $stockQuantity=$a['stockQuantity'][0];
                     $varranty=$a['warranty'][0];
                     $vendorCode=$a['vendorCode'][0];
                     $vendorName=$a['vendorName'][0];
                     

                     
                     $q="SELECT COUNT(*) AS c, productID FROM SC_products WHERE import_xml_article = ".$ELKOcode['cdata'];               //НАДО ДОПИСАТЬ ШО ТОЛЬКО ДЛЯ ЕЛКО
                     //echo $q."\n\r\n\r";
                     $res=mysql_query($q);
                     $row=mysql_fetch_array($res);
                     if($row['c']==0)
                                {// Если этого товара еще нет

                                    //Проверка - если элковский прайс наличие есть на складе
                                    if($stockQuantity['cdata']!="0"){ 
                                    
                                        //Находим айди категории в которую надо вставить товар
                                        $q_cat="SELECT ss_category_id FROM Code_import_category WHERE import_category_id LIKE '%".trim($categoryCode['cdata'])."%' ";
                                        $res_cat=mysql_query($q_cat);
                                        if(mysql_num_rows($res_cat)!=0)
                                        {
                                            $row_cat=mysql_fetch_array($res_cat);
                                            $cat_i=$row_cat['ss_category_id'];
                                        }else{$cat_i='';}
                                        
                                    
                                    $qib="INSERT INTO SC_products (`categoryID`, `brandID`, `customers_rating`, `Price`, `in_stock`, `customer_votes`, `items_sold`, `enabled`, `list_price`, `product_code`, `sort_order`, `default_picture`, `date_added`, `date_modified`, `viewed_times`, `add2cart_counter`, `eproduct_filename`, `eproduct_available_days`, `eproduct_download_times`, `weight`, `free_shipping`, `min_order_amount`, `shipping_freight`, `classID`, `name_en`, `brief_description_en`, `description_en`, `meta_title_en`, `meta_description_en`, `meta_keywords_en`, `ordering_available`, `slug`, `name_ru`, `brief_description_ru`, `description_ru`, `meta_title_ru`, `meta_description_ru`, `meta_keywords_ru`, `vkontakte_update_timestamp`, `id_1c`, `import_id`, `import_article`, `import_xml_article`, `brand_id`, `brand_name`, `varranty`) VALUES
                                        
                                   
                                   
                                   ( '1',
                                    0, 0, '".$price['cdata']."', '".str_replace("> ","",$stockQuantity['cdata'])."', 0, 0, 0, 0, '".$ELKOcode['cdata']."', 0,
                                     NULL, '".date("Y-m-d H:i:s")."', '".date("Y-m-d H:i:s")."', 0, 0, '', 5, 5, 0, 0, 1, 0, 0,
                                    '', '', '', NULL, '', '', 1, '', '".$productName['cdata']."', NULL, '', NULL, ' ', '', NULL, NULL, 
                                    2, '".$ELKOcode['cdata']."', '".$ELKOcode['cdata']."', '', '".$vendorName['cdata']."', '".$varranty['cdata']."')
                                   ";
                                   

                  //                  $qib_res=mysql_query($qib);
                                    
                                    //echo $qib."\n\r\n\r";
                                    
                                    
                                    $q_id=mysql_insert_id();        
                                    $add_product[]="http://itop.com.ua/published/SC/html/scripts/index.php?ukey=product_settings&productID=".$q_id."&categoryID=1";
                                       
                                             
                    //                mysql_query("UPDATE SC_categories SET products_count_admin=products_count_admin+1, products_count=products_count+1");
                                   
                                   


                                    
                                    
                                    }    
                                 
                                }
                                else
                                {                       //DELETE FROM b_catalog_price WHERE PRODUCT_ID='".$row['ID']."';                   
                                    
                                    if($stockQuantity['cdata']!="0"){  


                                     //Находим айди категории в которую надо вставить товар
                                        $q_cat="SELECT ss_category_id FROM Code_import_category WHERE import_category_id LIKE '%".trim($categoryCode['cdata'])."%' ";
                                        $res_cat=mysql_query($q_cat);
                                        if(mysql_num_rows($res_cat)!=0)
                                        {
                                            $row_cat=mysql_fetch_array($res_cat);
                                            $cat_i=$row_cat['ss_category_id'];
                                        }else{$cat_i='';}            
                                     
                      //               $q_id="UPDATE SC_products SET varranty='".$varranty['cdata']."', Price='".$price['cdata']."' , date_modified='".date("Y-m-d H:i:s")."', in_stock=GREATEST('".str_replace("> ","",$stockQuantity['cdata'])."', stockQuantity), import_id=2 WHERE productID='".$row['productID']."' AND product_synch=1 AND categoryID IN (SELECT categoryID FROM SC_categories WHERE category_synch=1)  ";
                                    //echo $q_id."\n\r\n\r";   
                                   
                                      if(($categoryCode['cdata']!="HDA") AND ($categoryCode['cdata']!="KEY") AND ($categoryCode['cdata']!="MOU") AND ($categoryCode['cdata']!="MEM") AND ($categoryCode['cdata']!="WCA") ) 
                                      {mysql_query($q_id);   }else{
                                      //echo "++++++".$categoryCode['cdata']."\n\r\n\r";   
                                      }
                                    
                                     

                                     
                                     
                                     
                                     

                                     $upd_product[]="http://itop.com.ua/published/SC/html/scripts/index.php?ukey=product_settings&productID=".$row['productID']."&categoryID=".$cat_i;
                                       
                                    }
                                    else 
                                    {   
                                    //$q_id="UPDATE SC_products SET varranty='".$varranty['cdata']."', Price='".$price['cdata']."' , date_modified='".date("Y-m-d H:i:s")."', in_stock=GREATEST('".str_replace("> ","",$stockQuantity['cdata'])."', stockQuantity), import_id=2 WHERE productID='".$row['productID']."' AND product_synch=1 AND categoryID IN (SELECT categoryID FROM SC_categories WHERE category_synch=1) ";
                                        //echo $q_id."\n\r\n\r";   
                                        if(($categoryCode['cdata']!="HDA") AND ($categoryCode['cdata']!="KEY") AND ($categoryCode['cdata']!="MOU") AND ($categoryCode['cdata']!="MEM") AND ($categoryCode['cdata']!="WCA") ) 
                                        {mysql_query($q_id); }else{
                                        //echo "++++++".$categoryCode['cdata']."\n\r\n\r";
                                        }
                                    }
                                    
                                }
                                $i++;

                 }
                
             
 } ;

 
 
 
 
 
 
 
 
 
          //echo serialize ($upd_product)."\n\r";
 
 
 
 
 
 

    
      get_product_list();

    // echo     "+".get_acive_product("90257")."\n\r"    ;
    // echo     "+".get_description_product("90257")."\n\r"    ;                
     
     
                      
          //date_default_timezone_set('Europe/Moscow');
     

        $file_add="add_".date("YmdHis").".txt";

        
        if( !file_exists($file_add)) {
        $fp = fopen($file_add, "w");
        foreach  ($add_product AS $ar) {fwrite($fp, $ar."\n\r");}
        fclose ($fp);
        }
        
        

        $file_upd="upd_".date("YmdHis").".txt";

        if( !file_exists($file_upd)) {
        $fp = fopen($file_upd, "w"); 
        foreach  ($upd_product AS $ar) {fwrite($fp, $ar."\n\r");}
        fclose ($fp);
        }
          
     
           
     
     mysql_close($link);     

?>
