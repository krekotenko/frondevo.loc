<?php
namespace backend\controllers\settings;

use Yii;
use backend\models\AdminOthers;
use backend\models\settings\Settings;

/**
 * Del controller
 */

class DelController extends \backend\controllers\AdminController {
        
    public function actionIndex()
    {
        $isAjax = Yii::$app->getRequest()->isAjax;

        if (!$isAjax) {
            throw new BadRequestHttpException();
        }
        else {
            $id1Uri = Yii::$app->getRequest()->get('id');
            $idSettingsPage = Yii::$app->getRequest()->get('id2');

            $mySettings = new Settings();

            $settingsPageAdminClass = $mySettings->getSettingsPageAdminClass($idSettingsPage);

            if($settingsPageAdminClass) {   
                $controller = Yii::$app->createControllerByID($settingsPageAdminClass.'del');
                $ret = $controller->runAction('index');
                echo $ret;
            }
        }
    }
}
