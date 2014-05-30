<?php
class shopSettingsPrintformSetupAction extends waViewAction
{
    public function execute()
    {
        if ($plugin_id = waRequest::request('id')) {
            $plugins = $this->getConfig()->getPlugins();
            $plugins_count = count($plugins);
            if (isset($plugins[$plugin_id]) && !empty($plugins[$plugin_id]['printform'])) {

                $plugin = waSystem::getInstance()->getPlugin($plugin_id);
                $namespace = 'printform_'.$plugin_id;

                if ($settings = waRequest::post($namespace)) {
                    $plugin->saveSettings($settings);
                    $this->view->assign('saved',true);
                }

                $params = array();
                $params['id'] = $plugin_id;
                $params['namespace'] = $namespace;
                $params['title_wrapper'] = '%s';
                $params['description_wrapper'] = '<br><span class="hint">%s</span>';
                $params['control_wrapper'] = '<div class="name">%s</div><div class="value">%s %s</div>';
                $params['subject'] = 'printform';

                $settings_controls = $plugin->getControls($params);
                $this->getResponse()->setTitle(_w(sprintf('Plugin %s settings', $plugin->getName())));

                $this->view->assign('plugin_info', $plugins[$plugin_id]);

                $this->view->assign('plugin_id', $plugin_id);
                $this->view->assign('settings_controls', $settings_controls);
            }
            waSystem::popActivePlugin();
        }
    }
}
