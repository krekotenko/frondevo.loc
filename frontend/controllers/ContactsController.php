<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 28.03.2016
 * Time: 14:10
 */

namespace frontend\controllers;
use frontend\models\Root;
use frontend\models\Works;
use vendor\UrlProvider\TextPagesUrlProvider;


class ContactsController extends CommonController

{

    private $myWorks;
    public function init() {
        parent::init();
        $this->myRoot = new Root($this->lang);
        $this->myWorks = new Works($this->lang);

    }

    public function actionIndex() {
        $data = [];
        $forLayout = [];

        // Добираем статические данные страницы

        $pageData = $this->myRoot->getPageContentByAlias($this->pageContent['alias'], [], []);
        $data['pageData'] = $pageData;

// Языковое меню
        $langMenu = [];
        $pagesContent = $this->myRoot->getPagesContent();
        $options = [];
        $options['joinUris'] = 1;
        $options['items'] = $pagesContent;
        // укр
        $urlProvider = new TextPagesUrlProvider('ua', $options);
        $pageUaUrl = $urlProvider->getContactsUrl();
        $langMenu['ua'] = [
            'link' => $pageUaUrl,
            'text' => 'Укр'
        ];
        // eng
        $urlProvider = new TextPagesUrlProvider('en', $options);
        $pageEnUrl = $urlProvider->getContactsUrl();
        $langMenu['en'] = [
            'link' => $pageEnUrl,
            'text' => 'Eng'
        ];
        // рус
        $urlProvider = new TextPagesUrlProvider('ru', $options);
        $pageRuUrl = $urlProvider->getContactsUrl();
        $langMenu['ru'] = [
            'link' => $pageRuUrl,
            'text' => 'Рус'
        ];
        $forLayout['langMenu'] = $langMenu;
        $data = array_merge($this->data, $data);
        $forLayout = array_merge($this->forLayout, $forLayout);//echo '<pre>';print_r($data);echo '</pre>';exit;

        // Список ссылок для плашки сссылок в футере
        $links = $this->myWorks->getLinks($this->pageContent['alias']);
        $forLayout['links'] = $links;


        $data['pageData'] = $pageData;

        return [
            'view' => 'contacts',
            'data' => $data,
            'layout' => $this->layout,
            'forLayout' => $forLayout,
        ];
    }

}