<?php
namespace backend\controllers\advantagepsdhmtl5;

use Yii;
use backend\models\AdminOthers;
use backend\models\advantagepsdhmtl5\Advantagepsdhmtl5;
use backend\models\Imagick;
use Yii\helpers\ArrayHelper;

class UpdateController extends  \backend\controllers\AdminController
{
	public function actionIndex()
	{
		$isAjax = Yii::$app->getRequest()->isAjax;

		if ($isAjax) {//echo '<pre>';print_r($_POST);echo '</pre>';exit;
			$id1Uri = $idPageGroup = Yii::$app->getRequest()->get('id');
			$id2Uri = $idRecord = Yii::$app->getRequest()->get('id2');
			$newDoc = Yii::$app->getRequest()->get('newDoc', 0);
			$pageLang = Yii::$app->getRequest()->get('pageLang');
			
			if (!$pageLang) {
				$pageLang = Yii::$app->params['defLang'];
			}
			
			$_POST['content'] = isset($_POST['content']) ? $_POST['content'] : [];

			$myOthers = new AdminOthers();
            $myAdvantagepsdhmtl5 = new Advantagepsdhmtl5();
            $myImagick = new Imagick();
			
            $hostName = Yii::$app->params['hostName'];
			$admPanelUri = Yii::$app->homeUrl;


			

			// Многострочные поля без HTML
			if (isset($_POST['noHTML'])) {
				foreach ($_POST['noHTML'] as $item) {
					$_POST['content'][$item] = strip_tags($_POST['content'][$item]);
				}
			}/* UpdateCodeTop */
			
			if (!isset($_POST['base'])) {
				$_POST['base'] = [];
			}
			
			if (!$newDoc) {
				// Обновляем запись
				$row = $myAdvantagepsdhmtl5->update($idRecord, $_POST['base'], $_POST['content'], $pageLang);

				if ($row !== false) {
					$json_data = json_encode(['code' => '0', 'message' => 'Запись успешно сохранена']);
				} else {
					$json_data = json_encode(['code' => '00204', 'message' => 'Не удалось сохранить запись']);
				}
			} else {
				// Добавляем запись
				$row = $myAdvantagepsdhmtl5->add($_POST['base'], $_POST['content'], $pageLang);

				if (isset($row[0]) && $row[0] >= 0) {
					$json_data = json_encode(['code' => '0', 'message' => 'Запись успешно добавлена', 'id' => $hostName.$admPanelUri.'edit#formedit/'.$idPageGroup.'/'.$row[1].'/'.$pageLang]);
				} else {
					$json_data = json_encode(['code' => '00204', 'message' => 'Не удалось добавить запись']);
				}
				
				$idRecord = $row[1];
			}

			//$justAddedImages = array();
			// Изображения
			foreach ($_SESSION['images'] as $item) { //только что добавленные
				//$justAddedImages[] = $item['name'];
				$tmp_dir = $_SERVER['DOCUMENT_ROOT'].'/temp';
				
				$imgTitle = $item['value'];
				$uploader = $item['id'];
				$fileExtension = $item['fileExtension'];
				$name = $item['name'];
				$imgWidth = $item['imgWidth'];
				$imgHeight = $item['imgHeight'];
				$format = $item['format'];
				
				$uploader = explode('-', $uploader);
				$pathToFolder = '../../frontend/web/';

				if ($uploader[0] == 'uploader0') { // одно изображение
					// если после добавления тут же удалили, то не продолжаем
					//if (isset($_POST['images'][$uploader[1].'-one-'.$name]) && $_POST['images'][$uploader[1].'-one-'.$name]['deleted']) continue;
					
					$fileName = $idRecord.'-'.$uploader[1].$fileExtension;//echo '<pre>';print_r($fileName);echo '</pre>';exit;
					$fileNameOriginal = "p/advantagepsdhmtl5/original-".$fileName;
					$fileNameMedium = "p/advantagepsdhmtl5/medium-".$fileName;

					// Копируем файл оригинал
					copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameOriginal);

					if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameMedium);
						$newRow = $myOthers->addImgOneMultiLangs('advantagepsdhmtl5', $uploader[1], $fileName, $imgTitle, $imgWidth, $imgHeight, $idRecord, 'idAdvantagepsdhmtl5', $pageLang);
					} else {
						/*// Загружаем как есть
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameMedium);
						$newRow = $myOthers->addImgOne('advantagepsdhmtl5', $uploader[1], $fileName, $imgTitle, $imgWidth, $imgHeight, $idRecord);*/
						
						/*// Создаём файл нужного размера по ширине
						$h = $myImagick->makeResizeImageByWidth(200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('advantagepsdhmtl5', $uploader[1], $fileName, $imgTitle, 200, $h, $idRecord);*/
						
						/*// Создаём файл нужного размера по высоте
						$w = $myImagick->makeResizeImageByHeight(200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('advantagepsdhmtl5', $uploader[1], $fileName, $imgTitle, $w, 200, $idRecord);*/
						
						/*// Создаём файл нужного размера по минимальной стороне
						$sizes = $myImagick->makeResizeImageByMinSide(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('advantagepsdhmtl5', $uploader[1], $fileName, $imgTitle, $sizes[0], $sizes[1], $idRecord);*/
						
						/*// Создаём файл нужного размера без обрезания
						$myImagick->makeResizeImage(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('advantagepsdhmtl5', $uploader[1], $fileName, $imgTitle, 200, 200, $idRecord);*/
						
						/*// Создаём файл нужного размера с оптимальным обрезанием
						$myImagick->makeResizeImageWithOptimalCrop(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('advantagepsdhmtl5', $uploader[1], $fileName, $imgTitle, 200, 200, $idRecord);*/
					}

					/*if ($newRow[1] >= 0)
					{   
						return json_encode(array('code' => '0', 'message' => 'Изображение успешно добавлено', 'id' => $uploader[1].'-one',
								'filepath' => '/'.$fileNameOriginal));
					}
					else
					{
						return json_encode(array('code' => '00307', 'message' => 'Ошибка сохранения в БД'));
					}*/
				} else { // несколько изображений
					// если после добавления тут же удалили, то не продолжаем
					//if (isset($_POST['images'][$uploader[1].'-'.$nextId.'-'.$name]) && $_POST['images'][$uploader[1].'-'.$nextId.'-'.$name]['deleted']) continue;
					
					$fileName = $idRecord.'-'.$uploader[1].'-'.microtime(true).$fileExtension;
					$fileNameOriginal = "p/advantagepsdhmtl5/original-".$fileName;
					$fileNameMedium = "p/advantagepsdhmtl5/medium-".$fileName;

					// Копируем файл оригинал
					copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameOriginal);

					if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameMedium);
						$newRow = $myOthers->addImgManyMultiLangs('advantagepsdhmtl5_'.$uploader[1], 'idAdvantagepsdhmtl5', $idRecord, $fileName, $imgTitle, $imgWidth, $imgHeight, $pageLang);
					} else {
						/*// Загружаем как есть
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameMedium);
						$newRow = $myOthers->addImgMany('advantagepsdhmtl5_'.$uploader[1], 'idAdvantagepsdhmtl5', $idRecord, $fileName, $imgTitle, $imgWidth, $imgHeight);*/
						
						/*// Создаём файл нужного размера по ширине
						$h = $myImagick->makeResizeImageByWidth(200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('advantagepsdhmtl5_'.$uploader[1], 'idAdvantagepsdhmtl5', $idRecord, $fileName, $imgTitle, 200, $h);*/
						
						/*// Создаём файл нужного размера по высоте
						$w = $myImagick->makeResizeImageByHeight(200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('advantagepsdhmtl5_'.$uploader[1], 'idAdvantagepsdhmtl5', $idRecord, $fileName, $imgTitle, $w, 200);*/
						
						/*// Создаём файл нужного размера по минимальной стороне
						$sizes = $myImagick->makeResizeImageByMinSide(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('advantagepsdhmtl5_'.$uploader[1], 'idAdvantagepsdhmtl5', $idRecord, $fileName, $imgTitle, $sizes[0], $sizes[1]);*/
						
						/*// Создаём файл нужного размера без обрезания
						$myImagick->makeResizeImage(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('advantagepsdhmtl5_'.$uploader[1], 'idAdvantagepsdhmtl5', $idRecord, $fileName, $imgTitle, 200, 200);*/
						
						/*// Создаём файл нужного размера с оптимальным обрезанием
						$myImagick->makeResizeImageWithOptimalCrop(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('advantagepsdhmtl5_'.$uploader[1], 'idAdvantagepsdhmtl5', $idRecord, $fileName, $imgTitle, 200, 200);*/
					}

					/*if ($newRow[1] >= 0)
					{   
						return json_encode(array('code' => '0', 'message' => 'Изображение успешно добавлено', 'id' => $uploader[1].'_'.$newRow[0],
								'filepath' => '/'.$fileNameOriginal));
					}
					else
					{
						return json_encode(array('code' => '00307', 'message' => 'Ошибка сохранения в БД'));
					}*/
				}
			}
			
			$images = ArrayHelper::getValue($_POST, 'images', []);
			if (isset($images)) {
				foreach ($images as $key => $item) { //существующие, а также только что добавленные
					$key = explode('-', $key);
					
					// только что добавленные изображения игнорируем
					//if (isset($key[2]) && in_array($key[2], $justAddedImages)) continue;
					
					if ($key[1] == 'one') { // одно изображение
						if (!$item['deleted']) {
							$myOthers->updateImgOneMultiLangs('advantagepsdhmtl5', $key[0], $item['imgTitle'], $idRecord, 'idAdvantagepsdhmtl5', $pageLang);
						} else {
							$myOthers->deleteImgOneMultiLangs('advantagepsdhmtl5', $key[0], $idRecord);
						}
					} else { // несколько изображений
						if (!$item['deleted']) {
							$myOthers->updateImgManyMultiLangs('advantagepsdhmtl5_'.$key[0], $item['imgTitle'], $item['picking'], $key[1], $pageLang);
						} else {
							$myOthers->deleteImgMany('advantagepsdhmtl5_'.$key[0], $key[1]);
						}
					}
				}
			}

			// Множество текстовых полей "Перечень преимуществ"
			$list = ArrayHelper::getValue($_POST, 'list', []);
			$myOthers->updateManyFieldsElementIMultiLangsSimple('advantagepsdhmtl5_list', $idRecord, $list, $pageLang);/* UpdateCodeBottom */

			exit($json_data);
		} else {
			throw new HTTP_Exception_404('Нет такой страницы.');
		}
	}
}