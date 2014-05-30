<?php

class shopFrontendPageAction extends waPageAction
{
    public function execute()
    {
        $this->setLayout(new shopFrontendLayout());
		$csvFiles = $this->dirFiles("./csv/");
        $xmlFiles = $this->dirFiles("./xml/");
		$xlsFiles = $this->dirFiles("./wa-data/public/site/Excel/");
		$this->view->assign('csvFiles', $csvFiles);
		$this->view->assign('xmlFiles', $xmlFiles);
		$this->view->assign('xlsFiles', $xlsFiles);
        parent::execute();
		
		
    }

    public function display($clear_assign = true)
    {
        /**
         * @event frontend_nav
         * @return array[string]string $return[%plugin_id%] html output for navigation section
         */
        $this->view->assign('frontend_nav', wa()->event('frontend_nav'));

        try {
            return parent::display(false);
        } catch (waException $e) {
            if ($e->getCode() == 404) {
                $url = $this->getConfig()->getRequestUrl(false, true);
                if (substr($url, -1) !== '/' && substr($url, -9) !== 'index.php') {
                    $this->redirect($url.'/');
                }
            }

			
            wa()->event('frontend_error', $e);
            $this->view->assign('error_message', $e->getMessage());
            $code = $e->getCode();
            $this->view->assign('error_code', $code);
            $this->getResponse()->setStatus($code ? $code : 500);
            $this->setThemeTemplate('error.html');
            return $this->view->fetch($this->getTemplate());
        }
    }
	
	protected function dirFiles($dir) {
	$files = array();
    	if (is_dir($dir)) {
			
        if ($dh = opendir($dir)) {
		$i = 0;
        while (($file = readdir($dh)) !== false) {
			if( filetype($dir . $file) == 'file' && ( strstr($file, '.xml') or strstr($file, '.csv') or strstr($file, '.xls') )  ) {
				$files[$i]['name'] = $file;
				$files[$i]['time'] = gmdate("Y-m-d H:i",filemtime($dir . $file));
				$files[$i]['mtime'] = filemtime($dir . $file);
				$files[$i]['size'] = number_format(filesize($dir . $file)/1024, 1, '.', ' ');
				$files[$i]['path'] = str_replace('.', '', $dir );
				$i++;
			}
        }
        closedir($dh);
        }
        }
	$return_files[0] = array();	
	foreach($files as $file){
		$mtime = $file['mtime'];
		if(!isset($oldmtime) or $mtime > $oldmtime) $return_files[0] = $file;
		$oldmtime = $file['mtime'];
	}
    return $return_files;
   }
}