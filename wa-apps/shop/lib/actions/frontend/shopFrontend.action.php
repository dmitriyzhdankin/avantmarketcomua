<?php

/**
 * Class shopFrontendAction
 * @method shopConfig getConfig()
 */
class shopFrontendAction extends waViewAction
{
    public function __construct($params = null)
    {
        parent::__construct($params);

        if (!waRequest::isXMLHttpRequest()) {
            $this->setLayout(new shopFrontendLayout());
        }
    }

    public function getStoreName()
    {
        $title = waRequest::param('title');
        if (!$title) {
            $title = $this->getConfig()->getGeneralSettings('name');
        }
        if (!$title) {
            $app = wa()->getAppInfo();
            $title = $app['name'];
        }
        return htmlspecialchars($title);
    }

    protected function setCollection(shopProductsCollection $collection)
    {
        $collection->setOptions(array('filters' => true));
        $getLimit = waRequest::get(limit);	
		$count = $collection->count();
		 
        if($getLimit && $getLimit != ''){ 
		if($getLimit != 'all') {
		$limit = $getLimit;
        $page = waRequest::get('page', 1, 'int');
         if ($page < 1) {
            $page = 1;
         }
          $offset = ($page - 1) * $limit;
		  $pages_count = ceil((float)$count / $limit);
		} else {
			$limit = $count;
			$page = 1;
			$offset = 0;
			$pages_count = 0;
		}
		} else {
		$limit = 20;
		$page = waRequest::get('page', 1, 'int');
         if ($page < 1) {
            $page = 1;
         }
          $offset = ($page - 1) * $limit;
		  $pages_count = ceil((float)$count / $limit);
		 }

        $products = $collection->getProducts('*', $offset, $limit);

        $this->view->assign('pages_count', $pages_count);

        $this->view->assign('products', $products);
    }

    public function execute()
    {
        if (wa()->getRouting()->getCurrentUrl()) {
            throw new waException('Page not found', 404);
        }
        $title = waRequest::param('title');
        if (!$title) {
            $app = wa()->getAppInfo();
            $title = $app['name'];
        }
        $this->getResponse()->setTitle($title);
        $this->getResponse()->setMeta('keywords', waRequest::param('meta_keywords'));
        $this->getResponse()->setMeta('description', waRequest::param('meta_description'));

        /**
         * @event frontend_homepage
         * @return array[string]string $return[%plugin_id%] html output for head section
         */
        $this->view->assign('frontend_homepage', wa()->event('frontend_homepage'));

        $this->setThemeTemplate('home.html');

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
}
