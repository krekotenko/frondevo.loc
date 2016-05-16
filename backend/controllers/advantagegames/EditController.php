<?php
namespace backend\controllers\advantagegames;

use Yii;
use backend\models\AdminOthers;
use backend\models\advantagegames\Advantagegames;

class EditController extends  \backend\controllers\AdminController
{
	public function actionIndex()
	{
        $isAjax = Yii::$app->getRequest()->isAjax;

        if ($isAjax) {
            $id1Uri = $idPageGroup = Yii::$app->getRequest()->get('id');
            $id2Uri = $idRecord = Yii::$app->getRequest()->get('id2');
			$id3Uri = $pageLang = Yii::$app->getRequest()->get('id3');
			
			if (!$pageLang) {
				$pageLang = Yii::$app->params['defLang'];
			}

			$myOthers = new AdminOthers();
			$myAdvantagegames = new Advantagegames();
			
			$myOthers->clearTempDir();
			$_SESSION['images'] = [];

			// Получаем запись
            $advantagegamesItem = $myAdvantagegames->get($idRecord, $pageLang);//echo '<pre>';print_r($advantagegamesItem);echo '</pre>';exit;

			if (!empty($advantagegamesItem)) {
				//Навигационное меню
                $langs = $myOthers->getAllLangs();
				

				// Множество текстовых полей "Перечень преимуществ"
				$list = $myOthers->getManyFieldsElementMultiLangs('advantagegames_list', $idRecord, $pageLang);
				if (!$list) {
					$list = $myOthers->getManyFieldsElementEmpty();
				}/* UpdateCode */


				$content = '';
                $navMenu = '';

                if (is_file(Yii::$app->basePath.'/views/pages/AdvantagegamesEditView.php')) {
                    require Yii::$app->basePath.'/views/pages/AdvantagegamesEditView.php';
                }

                return json_encode(['code' => '0', 'message' => '', 'content' => $content, 'navMenu' => $navMenu]);
            } else {
                return json_encode(['code' => '00202', 'message' => 'Запись не найдена', 'content' => '', 'navMenu' => '']);
            }
        } else {
            throw new HTTP_Exception_404('Нет такой страницы.');
        }
    }
}