<?php
namespace backend\controllers\filtersfrontoutport;

use Yii;
use backend\models\AdminOthers;
use backend\models\filtersfrontoutport\Filtersfrontoutport;

class ListController extends \backend\controllers\AdminController
{
	public function actionIndex()
	{
        $isAjax = Yii::$app->getRequest()->isAjax;

        if ($isAjax) {
			$id1Uri = $idPageGroup = Yii::$app->getRequest()->get('id');
			$id2Uri = $pageLang = Yii::$app->getRequest()->get('id2');

			if (!$pageLang) {
				$pageLang = Yii::$app->params['defLang'];
			}

			$limit = Yii::$app->getRequest()->get('limit', 20);
			$offset = 0;// limit
			$filters = Yii::$app->getRequest()->get('filters', [
				// filter
			]);
            $page = Yii::$app->getRequest()->get('page', 1);

			$myOthers = new AdminOthers();
			$myFiltersfrontoutport = new Filtersfrontoutport();
			

			// Подготавливаем данные для хлебных крошек
			$pageGroupData = $myOthers->getPageGroupDataI($idPageGroup, ['groupName']);/* UpdateCode */

			// Получаем записи
			$filtersfrontoutport = $myFiltersfrontoutport->getMany($filters, $limit, $offset, $pageLang);//echo '<pre>';print_r($filtersfrontoutport);echo '</pre>';exit;


			$content = '';

            if (is_file(Yii::$app->basePath.'/views/pages/FiltersfrontoutportListView.php')) {
                require Yii::$app->basePath.'/views/pages/FiltersfrontoutportListView.php';
            }

            $json_data = json_encode(['code' => '0', 'message' => '', 'content' => $content]);
            return $json_data;
        } else {
            throw new CHttpException(404, 'Нет такой страницы.');
        }
    }
}