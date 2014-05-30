<?php
class installerHelper
{
    /**
     *
     * @var waAppSettingsModel
     */
    private static $model;
    /**
     *
     * @var waInstallerApps
     */
    private static $installer;

    /**
     *
     * @return waInstallerApps
     */
    public static function &getInstaller()
    {
        if (!self::$model) {
            self::$model = new waAppSettingsModel();
        }
        if (!self::$installer) {
            self::$installer = new waInstallerApps(self::$model->get('webasyst', 'license', false), wa()->getLocale(), 600, waRequest::get('refresh') ? true : false);
        }
        return self::$installer;
    }

    public static function getHash()
    {
        return self::getInstaller()->getHash();
    }

    public static function checkUpdates(&$messages)
    {
        try {
            self::getInstaller()->checkUpdates();
        } catch (Exception $ex) {
            $text = $ex->getMessage();
            $message = array('text' => $text, 'result' => 'fail');
            if (strpos($text, "\n")) {
                $texts = array_filter(array_map('trim', explode("\n", $message['text'])), 'strlen');
                while ($message['text'] = array_shift($texts)) {
                    $messages[] = $message;
                }
            } else {
                $messages[] = $message;
            }
        }

    }

    public static function getApps(&$messages, &$update_counter = null, $filter = array())
    {
        if ($update_counter !== null) {
            $update_counter = is_array($update_counter) ? array_merge(array_fill_keys(array('total', 'applicable', 'payware'), 0), $update_counter) : intval($update_counter);
        }
        $app_list = array();

        try {
            $app_list = self::getInstaller()->getApplicationsList(false, array(), wa()->getDataPath('images', true), $messages);
            self::$model->ping();
            if ($update_counter !== null) {
                $minimize = is_array($update_counter) ? true : false;
                $update_counter = waInstallerApps::getUpdateCount($app_list, $minimize, $update_counter);
                self::$model->ping();

            }

        } catch (Exception $ex) {
            if ($messages === null) {
                throw $ex;
            } else {
                $messages[] = array('text' => $ex->getMessage(), 'result' => 'fail');
            }
        }

        foreach ($app_list as $key => & $item) {
            if ($item['slug'] == 'developer') {
                $item['downloadable'] = $item['current'] ? true : false;
                break;
            }
        }
        unset($item);

        if ($filter) {
            foreach ($app_list as $key => & $item) {

                if (!empty($filter['enabled']) && empty($item['enabled']) && empty($item['current'])) { //not present
                    unset($app_list[$key]);
                    continue;
                }

                if (!empty($filter['extras'])) {
                    $extras = $filter['extras'];
                    if (empty($item['current'][$extras]) || empty($item['extras'][$extras])) { //themes not supported or not available
                        unset($app_list[$key]);
                        continue;
                    }
                }

            }
            unset($item);
        }
        return $app_list;
    }

    public static function getSystemPlugins(&$messages, &$update_counter = null, $filter = array())
    {
        if ($update_counter !== null) {
            $update_counter = is_array($update_counter) ? array_merge(array_fill_keys(array('total', 'applicable', 'payware'), 0), $update_counter) : intval($update_counter);
        }
        try {
            $items = self::getInstaller()->getSystemList();
            $types = array();

            $icons = array(
                'sms'      => 'icon16 mobile',
                'payment'  => 'icon16 dollar',
                'shipping' => 'icon16 box',
            );
            $translate = array(
                'sms'      => _w('SMS'),
                'payment'  => _w('Payment'),
                'shipping' => _w('Shipping'),
            );

            foreach ($items as $id => $item) {
                if (!empty($item['subject']) && ($item['subject'] == 'systemplugins')) {
                    $t = $item['type_slug'];
                    if (empty($types[$t])) {
                        $types[$t] = array(
                            'name'    => isset($translate[$t]) ? $translate[$t] : null,
                            'icon'    => isset($icons[$t]) ? $icons[$t] : null,
                            'plugins' => array(),
                        );
                    }
                    $types[$t]['plugins'][$item['id']] = $item;

                }
            }
            $minimize = is_array($update_counter) ? true : false;
            foreach ($types as & $items) {
                if ($update_counter !== null) {
                    $update_counter = waInstallerApps::getUpdateCount($items['plugins'], $minimize, $update_counter);
                    self::$model->ping();
                }
            }

            self::$model->ping();
            unset($items);
        } catch(Exception $ex) {
            if ($messages === null) {
                throw $ex;
            } else {
                $messages[] = array('text' => $ex->getMessage(), 'result' => 'fail');
            }
            $types = array();
        }

        return $types;
    }

    public static function isDeveloper()
    {
        $result = false;
        $paths = array();
        $paths[] = dirname(__FILE__).'/.svn';
        $paths[] = dirname(__FILE__).'/.git';
        $root_path = wa()->getConfig()->getRootPath();
        $paths[] = $root_path.'/.svn';
        $paths[] = $root_path.'/.git';
        foreach ($paths as $path) {
            if (file_exists($path)) {
                $result = true;
                break;
            }
        }
        return $result;
    }

    /**
     *
     * Search first entry condition
     * @param array $items
     * @param array $filter
     * @return mixed
     */
    public static function search($items, $filter)
    {
        $match = null;
        foreach ($items as & $item) {
            $matched = true;
            foreach ($filter as $field => $value) {
                if ($value && ($item[$field] != $value)) {
                    $matched = false;
                    break;
                }
            }
            if ($matched) {
                $match = $item;
                break;
            }
        }
        return $match;
    }

    /**
     *
     * Compare arrays by specified fields
     * @param array $a
     * @param array $b
     * @param array $fields
     * @return bool
     */
    public static function equals($a, $b, $fields = array('vendor', 'edition'))
    {
        $equals = true;
        foreach ($fields as $field) {
            if (empty($a[$field]) && empty($b[$field])) {
                /*do nothing*/
            } elseif ($a[$field] != $b[$field]) {
                $equals = false;
                break;
            }
        }

        return $equals;
    }
}
