<?php

/*
 * This file is part of Webasyst framework.
 *
 * Licensed under the terms of the GNU Lesser General Public License (LGPL).
 * http://www.webasyst.com/framework/license/
 *
 * @link http://www.webasyst.com/
 * @author Webasyst LLC
 * @copyright 2011 Webasyst LLC
 * @package installer
 */

class installerPluginsAction extends installerItemsAction
{
    protected $module = 'plugins';
    public function execute()
    {
        parent::execute();
        $messages = installerMessage::getInstance()->handle(waRequest::get('msg'));
        $this->view->assign('systemplugins', installerHelper::getSystemPlugins($messages, $this->update_counter));
        if (waRequest::get('subject') == 'systemplugins') {
            $this->view->assign('selected_type', waRequest::get('slug'));
        }
        $this->view->assign('messages', $messages);
        $this->getConfig()->setCount($this->update_counter ? $this->update_counter : null);
        $this->view->assign('update_counter', $this->update_counter);
    }
}
//EOF