<?php $con=mysql_connect("localhost","fonarik_fonarik2","221976");
	      if($con){ mysql_select_db("fonarik_fonarik2",$con); $menu=""; }
	      else{ $menu="fail"; }
		  mysql_query('SET character_set_database = utf8');
		  mysql_query('SET NAMES utf8');
		  $sql = "SELECT * FROM wp_posts AS p 
					LEFT JOIN wp_postmeta AS m ON p.ID = m.post_id 
					LEFT JOIN wp_terms AS t ON t.slug = 'avantmarket'
					LEFT JOIN wp_term_taxonomy AS tt ON tt.term_id = t.term_id
					LEFT JOIN wp_term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id					
					WHERE post_type = 'nav_menu_item' 
					AND post_status = 'publish' 
					AND m.meta_key = '_menu_item_menu_item_parent' 
					AND m.meta_value = 0
					AND p.ID = tr.object_id
					ORDER BY p.menu_order";
					
		  $rsd = mysql_query($sql);
		  
		  while($menu_item = mysql_fetch_array($rsd)) {	
		  
		  $sql_meta = 'SELECT meta_value, meta_key FROM wp_postmeta WHERE post_id = '.$menu_item['ID'].'';
		  $rsd_meta = mysql_query($sql_meta);
		  $menu_meta = array();
		  while($metaarr = mysql_fetch_array($rsd_meta)) {
			  $menu_meta[$metaarr['meta_key']] = $metaarr['meta_value'];
		  }
		  
		  $menu .= '<li><a href="'.$menu_meta['_menu_item_url'].'">'.$menu_item['post_title'];
		  if($menu_item['post_excerpt'] && $menu_item['post_excerpt'] != ''){
		  $menu .= '&nbsp;<img src="'.$menu_item['post_excerpt'].'" alt="'.$menu_item['post_title'].'"  />';
		  }
		  $menu .= '</a>';
		  
		  $sqlsub = "SELECT * FROM wp_posts AS p 
					LEFT JOIN wp_postmeta AS m ON p.ID = m.post_id 
					WHERE post_type = 'nav_menu_item' 
					AND post_status = 'publish' 
					AND meta_key = '_menu_item_menu_item_parent' 
					AND meta_value = ".$menu_item['ID']."
					ORDER BY menu_order";
		  	
		  $rsdsub = mysql_query($sqlsub);
		  if(mysql_num_rows($rsdsub) > 0){
	      $menu .=  '<ul class="sub">';
		  while($submenu_item = mysql_fetch_array($rsdsub)) {	
		   
		  $subsql_meta = 'SELECT meta_value, meta_key FROM wp_postmeta WHERE post_id = '.$submenu_item['ID'].'';
		  $subrsd_meta = mysql_query($subsql_meta);
		  $submenu_meta = array();
		  while($submetaarr = mysql_fetch_array($subrsd_meta)) {
			  $submenu_meta[$submetaarr['meta_key']] = $submetaarr['meta_value'];
		  }
		  
		  $menu .= '<li><a href="'.$submenu_meta['_menu_item_url'].'">'.$submenu_item['post_title'];		  
		  $menu .= '</a></li>';
		   
		  }
		  $menu .=  '</ul>';
		  }
		  $menu .= '</li>';
      	}
		  $this->assign('wa_menu', $menu);
		  
		 $menu2="";
		  
		 $sql2 = "SELECT * FROM wp_posts AS p 
					LEFT JOIN wp_postmeta AS m ON p.ID = m.post_id 
					LEFT JOIN wp_terms AS t ON t.slug = 'avantmarket2'
					LEFT JOIN wp_term_taxonomy AS tt ON tt.term_id = t.term_id
					LEFT JOIN wp_term_relationships AS tr ON tr.term_taxonomy_id = tt.term_taxonomy_id					
					WHERE post_type = 'nav_menu_item' 
					AND post_status = 'publish' 
					AND m.meta_key = '_menu_item_menu_item_parent' 
					AND m.meta_value = 0
					AND p.ID = tr.object_id
					ORDER BY p.menu_order";
					
		  $rsd2 = mysql_query($sql2);
		  
		  while($menu_item2 = mysql_fetch_array($rsd2)) {	
		  
		  $sql_meta2 = 'SELECT meta_value, meta_key FROM wp_postmeta WHERE post_id = '.$menu_item2['ID'].'';
		  $rsd_meta2 = mysql_query($sql_meta2);
		  $menu_meta2 = array();
		  while($metaarr2 = mysql_fetch_array($rsd_meta2)) {
			  $menu_meta2[$metaarr2['meta_key']] = $metaarr2['meta_value'];
		  }
		  
		  $menu2 .= '<li><a href="'.$menu_meta2['_menu_item_url'].'">'.$menu_item2['post_title'];
		  if($menu_item['post_excerpt'] && $menu_item['post_excerpt'] != ''){
		  $menu2 .= '&nbsp;<img src="'.$menu_item2['post_excerpt'].'" alt="'.$menu_item2['post_title'].'"  />';
		  }
		  $menu2 .= '</a>';
		  
		  $sqlsub2 = "SELECT * FROM wp_posts AS p 
					LEFT JOIN wp_postmeta AS m ON p.ID = m.post_id 
					WHERE post_type = 'nav_menu_item' 
					AND post_status = 'publish' 
					AND meta_key = '_menu_item_menu_item_parent' 
					AND meta_value = ".$menu_item2['ID']."
					ORDER BY menu_order";
		  	
		  $rsdsub2 = mysql_query($sqlsub2);
		  if(mysql_num_rows($rsdsub2) > 0){
	      $menu2 .=  '<ul class="sub">';
		  while($submenu_item2 = mysql_fetch_array($rsdsub2)) {	
		   
		  $subsql_meta2 = 'SELECT meta_value, meta_key FROM wp_postmeta WHERE post_id = '.$submenu_item2['ID'].'';
		  $subrsd_meta2 = mysql_query($subsql_meta2);
		  $submenu_meta2 = array();
		  while($submetaarr2 = mysql_fetch_array($subrsd_meta2)) {
			  $submenu_meta2[$submetaarr2['meta_key']] = $submetaarr2['meta_value'];
		  }
		  
		  $menu2 .= '<li><a href="'.$submenu_meta2['_menu_item_url'].'">'.$submenu_item2['post_title'];		  
		  $menu2 .= '</a></li>';
		   
		  }
		  $menu2 .=  '</ul>';
		  }
		  $menu2 .= '</li>';
      	}
		  $this->assign('wa_menu2', $menu2);
		  
		  
		  
		  $sql_banner = "SELECT DISTINCT p.post_title, p.guid, p.ID, p.post_date, m.meta_value FROM wp_posts AS p
		  LEFT JOIN wp_postmeta AS m ON p.ID = m.post_id 
		  WHERE p.post_status = 'publish' AND p.post_type = 'slideshow'
		  AND m.meta_key = 'meta_options' ORDER BY p.post_date";					
		  $rsd_banner = mysql_query($sql_banner);
		  $b = 1; $controller = ''; $slider = ''; $slider_small='';
		  while($banner = mysql_fetch_array($rsd_banner)) {
			  $banner_meta = unserialize($banner['meta_value']);
			  $sql_file = 'SELECT guid FROM wp_posts WHERE ID = '.$banner_meta['image_id'].' LIMIT 0,1';
			  $rsd_file = mysql_query($sql_file);
			  $file = mysql_fetch_assoc($rsd_file);		
			  if($banner_meta['type'] == '2'){	  
			  $slider .='<div id="slide'.$b.'">
			 <a href="'.$banner_meta['link'].'"><img src="'.$file['guid'].'" alt="" title="" width="984"/></a>
			 <div class="slid_text"> <h3 class="slid_title"><span>'.$banner_meta['slogan1'].'</span></h3>			
			 <p><span><a href="'.$banner_meta['link'].'" style="color:#999;">'.$banner_meta['slogan2'].'</a></span></p></div>	  
            </div>';
			  $controller .= '<div class="control"><span>'.$b.'</span></div>';
			  $b++;
			  } else if($banner_meta['type'] == '3'){
				$slider_small .= '<div class="grid_4">
                 <a href="'.$banner_meta['link'].'" class="button_block best_price">
                 <img src="'.$file['guid'].'" alt="Banner 1" width="312" height="101"/>
                 </a></div>';
			  }

		  }
		  
		  $this->assign('wa_banner', $slider); 
		  $this->assign('wa_small_banner', $slider_small); 
		  $this->assign('wa_controller', $controller);?>