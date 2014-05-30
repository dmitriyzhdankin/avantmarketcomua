<?php

/**
 * Html view action for getting one photo
 * @see photosFrontendLoadPhotoController
 * @see photosFrontendLoadListAction
 */
class photosFrontendPhotoAction extends photosFrontendViewAction
{
    /**
     * private hash of ONE photo (not album private hash)
     * @var string|null
     */
    private $private_hash = null;
    /**
     * @var photosPhotoModel
     */
    private $photo_model;

    /**
     * hash of collection
     * @var string
     */
    private $hash;

    /**
     * album
     * @var array
     */
    private $album;

    /**
     * photo
     * @var array
     */
    private $photo;

    public function __construct($params = null)
    {
        parent::__construct($params);
        $this->setThemeTemplate('photo.html');
    }

    public function execute()
    {
        $url = waRequest::param('url');
        $this->album = waRequest::param('album');
        $this->hash = waRequest::param('hash');
        if (!$url) {
            throw new waException(_w('Page not found', 404));
        }
        $this->photo_model = new photosPhotoModel();

        $this->photo = $this->getPhoto($url);
        if (!$this->photo) {
            throw new waException(_w('Page not found'), 404);
        }
        $this->photo = photosPhoto::escapeFields($this->photo);

        if ($this->album && $this->album['status'] <= 0) {
            $this->album['full_url'] = photosCollection::frontendAlbumHashToUrl($this->hash);
        }

        // delegate work to special render helper
        $render_helper = new photosPhotoRenderHelper($this->photo, $this->private_hash);
        $result = $render_helper->workUp(array(
            'album' => $this->album,
            'hash' => $this->hash
        ));

        waRequest::setParam('title', $this->photo['name']);
        waRequest::setParam('nofollow', $this->isNeedNofollow());
        waRequest::setParam('breadcrumbs', $this->getBreadcrumbs());
        waRequest::setParam('disable_sidebar', true);

        // pull out work's up result
        $this->view->assign('photo',        $result['photo']);
        $this->view->assign('albums',       $result['blocks']['albums']);
        $this->view->assign('tags',         $result['blocks']['tags']);
        $this->view->assign('exif',         $result['blocks']['exif']);
        $this->view->assign('author',       $result['blocks']['author']);
        $this->view->assign('stack_nav',    $result['blocks']['stack_nav']);
        $this->view->assign('photo_stream', $result['blocks']['photo_stream']);

        // if we are not in album, than $album is null
        $this->view->assign('album', $this->album);

        /**
         * Add extra widgets to photo page
         * @event frontend_photo
         * @param string[array]mixed $photo photo data
         * @return array[string][string]string $return[%plugin_id%]['bottom'] In bottom, under photo - any widget
         * @return array[string][string]string $return[%plugin_id%]['sidebar']
         * @return array[string][string]string $return[%plugin_id%]['top_left']
         * @return array[string][string]string $return[%plugin_id%]['top_right']
         */
        $this->view->assign('frontend_photo', wa()->event('frontend_photo', $this->photo));

        $version = wa()->getVersion();
        $this->getResponse()->addJs('js/common.js?v='.$version, true);
        $this->getResponse()->addJs('js/photo.stream.slider.js?v='.$version, true);
        $this->getResponse()->addJs('js/frontend.photo.js?v='.$version, true);
    }

    private function getPhoto($url)
    {
        $photo = $this->photo_model->getByField('url', $url);
        if (!$photo) {
            $this->private_hash = photosPhotoModel::parsePrivateUrl($url);
            $photo = $this->photo_model->getByField('hash', $this->private_hash);
            $parent = $this->photo_model->getStackParent($photo);
            $this->hash = photosPhotoModel::getPrivateHash($parent ? $parent : $photo);
        }
        return $photo;
    }

    private function isNeedNofollow()
    {
        if ($this->album && $this->album['status'] == 1) {
            return false;
        }
        if (empty($this->hash)) {
            $album_photos_model = new photosAlbumPhotosModel();
            return (bool)$album_photos_model->countByField('photo_id', $this->photo['id']);
        }
        return true;
    }

    private function getBreadcrumbs()
    {
        if ($this->album) {
            $album_model = new photosAlbumModel();
            return $album_model->getBreadcrumbs($this->album['id'], true, true);
        }
        return array();
    }
}