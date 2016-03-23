<?php 
namespace backend\controllers\settings\settings;

use Yii;
use backend\models\AdminOthers;

/**
 * Privateoffice controller
 */

class PrivateofficeController extends \backend\controllers\AdminController {
            
    public function actionIndex()
    {
        $isAjax = Yii::$app->getRequest()->isAjax;

        if (!$isAjax) {
            throw new BadRequestHttpException();
        } else {
            $id1Uri = Yii::$app->getRequest()->get('id');
            $settingsPageUri = Yii::$app->getRequest()->get('id2');
            $pageLang = Yii::$app->getRequest()->get('id3');
            
            $admPanelUri = Yii::$app->homeUrl;
            $defLang = Yii::$app->params['defLang'];

            //Хлебные крошки
            $myOthers = new AdminOthers();
            $pageGroupData = $myOthers->getPageGroupData($id1Uri);
            $settingsPageName = $myOthers->getSettingsPageName($settingsPageUri);

            $content = '';
            $navMenu = '';

            if (is_file(Yii::$app->basePath.'/views/pages/settings/PrivateOfficeListView.php')) {
                require Yii::$app->basePath.'/views/pages/settings/PrivateOfficeListView.php';
            }

            return json_encode(['code' => '0', 'message' => '', 'content' => $content, 'navMenu' => $navMenu]);
        }
    }
}        
