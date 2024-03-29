<?php
namespace backend\controllers\works;

use Yii;
use backend\models\AdminOthers;
use backend\models\works\Works;
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
			$myWorks = new Works();
			$myImagick = new Imagick();

			// Сортировка в list-таблице
			if (!$id2Uri && isset($_POST['list'])) {
				$listIndexes = $_POST['list'];
				$newIndexes = json_decode($listIndexes);

				$orderField = 'order';

				$myOthers->sortTable('works', 'id', $newIndexes, $orderField);
				exit;
			}

			$hostName = Yii::$app->params['hostName'];
			$admPanelUri = Yii::$app->homeUrl;


			// Чекбокс "Отображать страницу"
			$_POST['base']['show'] = isset($_POST['base']['show']) ? $_POST['base']['show'] : 0;

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
				$row = $myWorks->update($idRecord, $_POST['base'], $_POST['content'], $pageLang);

				if ($row !== false) {
					$json_data = json_encode(['code' => '0', 'message' => 'Запись успешно сохранена']);
				} else {
					$json_data = json_encode(['code' => '00204', 'message' => 'Не удалось сохранить запись']);
				}
			} else {
				// Добавляем запись
				$row = $myWorks->add($_POST['base'], $_POST['content'], $pageLang);

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
				$pathToFolder = $_SERVER['DOCUMENT_ROOT'].'/frontend/web/';

				if ($uploader[0] == 'uploader0') { // одно изображение
					// если после добавления тут же удалили, то не продолжаем
					//if (isset($_POST['images'][$uploader[1].'-one-'.$name]) && $_POST['images'][$uploader[1].'-one-'.$name]['deleted']) continue;

					$fileName = $idRecord.'-'.$uploader[1].$fileExtension;//echo '<pre>';print_r($fileName);echo '</pre>';exit;
					$fileNameOriginal = Yii::$app->params['pics']['works']['path']."original-".$fileName;

					$fileNameGeneral = Yii::$app->params['pics']['works']['path']."general-".$fileName;
					$imgGeneralWidth = Yii::$app->params['pics']['works']['sizes']['general']['width'];
					$imgGeneralHeight = Yii::$app->params['pics']['works']['sizes']['general']['height'];

					$fileNamePreview = Yii::$app->params['pics']['works']['path']."preview-".$fileName;
					$imgPreviewWidth = Yii::$app->params['pics']['works']['sizes']['preview']['width'];
					$imgPreviewHeight = Yii::$app->params['pics']['works']['sizes']['preview']['height'];

					$fileNameBigSBK = Yii::$app->params['pics']['works']['path']."bigsbk-".$fileName;
					$imgBigSBKWidth = Yii::$app->params['pics']['works']['sizes']['bigsbk']['width'];
					$imgBigSBKHeight = Yii::$app->params['pics']['works']['sizes']['bigsbk']['height'];

					$fileNameMediumSBK = Yii::$app->params['pics']['works']['path']."mediumsbk-".$fileName;
					$imgMediumSBKWidth = Yii::$app->params['pics']['works']['sizes']['mediumsbk']['width'];
					$imgMediumSBKHeight = Yii::$app->params['pics']['works']['sizes']['mediumsbk']['height'];


					$fileNameGeneralPRTF =Yii::$app->params['pics']['works']['path']."generalprtf-".$fileName;
					$imgGeneralPRTFWidth = Yii::$app->params['pics']['works']['sizes']['generalprtf']['width'];
					$imgGeneralPRTFHeight = Yii::$app->params['pics']['works']['sizes']['generalprtf']['height'];

					$fileNameGeneralBG =Yii::$app->params['pics']['works']['path']."generalbg-".$fileName;
					$imgGeneralBGWidth = Yii::$app->params['pics']['works']['sizes']['generalbg']['width'];
					$imgGeneralBGHeight = Yii::$app->params['pics']['works']['sizes']['generalbg']['height'];


					$fileNameMediumBG =Yii::$app->params['pics']['works']['path']."mediumbg-".$fileName;
					$imgMediumBGWidth = Yii::$app->params['pics']['works']['sizes']['mediumbg']['width'];
					$imgMediumBGHeight = Yii::$app->params['pics']['works']['sizes']['mediumbg']['height'];


					$fileNameSmallBG =Yii::$app->params['pics']['works']['path']."smallbg-".$fileName;
					$imgSmallBGWidth = Yii::$app->params['pics']['works']['sizes']['smallbg']['width'];
					$imgSmallBGGHeight = Yii::$app->params['pics']['works']['sizes']['smallbg']['height'];


					$fileNameGeneralMP =Yii::$app->params['pics']['works']['path']."generalmp-".$fileName;
					$imgGeneralMPWidth = Yii::$app->params['pics']['works']['sizes']['generalmp']['width'];
					$imgGeneralMPHeight = Yii::$app->params['pics']['works']['sizes']['generalmp']['height'];


					$fileNameBigMP =Yii::$app->params['pics']['works']['path']."bigmp-".$fileName;
					$imgBigMPWidth = Yii::$app->params['pics']['works']['sizes']['bigmp']['width'];
					$imgBigMPHeight = Yii::$app->params['pics']['works']['sizes']['bigmp']['height'];


					$fileNameMediumMP =Yii::$app->params['pics']['works']['path']."mediummp-".$fileName;
					$imgMediumMPWidth = Yii::$app->params['pics']['works']['sizes']['mediummp']['width'];
					$imgMediumMPMHeight = Yii::$app->params['pics']['works']['sizes']['mediummp']['height'];



					$fileNameSmallMP =Yii::$app->params['pics']['works']['path']."smallmp-".$fileName;
					$imgSmallMPWidth = Yii::$app->params['pics']['works']['sizes']['smallmp']['width'];
					$imgSmallMPMHeight = Yii::$app->params['pics']['works']['sizes']['smallmp']['height'];

					$fileNameGeneralADD =Yii::$app->params['pics']['works']['path']."generaladd-".$fileName;
					$imgGeneralADDWidth = Yii::$app->params['pics']['works']['sizes']['generaladd']['width'];
					$imgGeneralADDHeight = Yii::$app->params['pics']['works']['sizes']['generaladd']['height'];

					// Копируем файл оригинал
					//copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameOriginal);

					if ($_SERVER['REMOTE_ADDR'] == '0') {
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameGeneral);
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNamePreview);
						$newRow = $myOthers->addImgOneMultiLangsI('works', $uploader[1], $fileName, $imgTitle, $imgGeneralWidth, $imgGeneralHeight, $imgPreviewWidth, $imgPreviewHeight, $idRecord, 'idWorks', $pageLang);
					} else {
						/*// Загружаем как есть
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameMedium);
						$newRow = $myOthers->addImgOne('works', $uploader[1], $fileName, $imgTitle, $imgWidth, $imgHeight, $idRecord);*/

						/*// Создаём файл нужного размера по ширине
						$h = $myImagick->makeResizeImageByWidth(200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('works', $uploader[1], $fileName, $imgTitle, 200, $h, $idRecord);*/

						/*// Создаём файл нужного размера по высоте
						$w = $myImagick->makeResizeImageByHeight(200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('works', $uploader[1], $fileName, $imgTitle, $w, 200, $idRecord);*/

						/*// Создаём файл нужного размера по минимальной стороне
						$sizes = $myImagick->makeResizeImageByMinSide(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('works', $uploader[1], $fileName, $imgTitle, $sizes[0], $sizes[1], $idRecord);*/

						/*// Создаём файл нужного размера без обрезания
						$myImagick->makeResizeImage(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('works', $uploader[1], $fileName, $imgTitle, 200, 200, $idRecord);*/

						/*// Создаём файл нужного размера с оптимальным обрезанием
						$myImagick->makeResizeImageWithOptimalCrop(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgOne('works', $uploader[1], $fileName, $imgTitle, 200, 200, $idRecord);*/
						if ($uploader[1] == "imageprtf") {
							$myImagick->makeResizeImageWithOptimalCrop($imgGeneralPRTFWidth, $imgGeneralPRTFHeight,$pathToFolder.$fileNameGeneralPRTF, $tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1, 0, 0, \imagick::COMPRESSION_NO, 100);
							$newRow = $myOthers->addImgOneMultiLangs('works', $uploader[1], $fileName, $imgTitle, $imgGeneralPRTFWidth, $imgGeneralPRTFHeight,$idRecord, 'idWorks', $pageLang);
						//загрузка изображений для background


						} else if ($uploader[1] == "imagebg"){
							$myImagick->makeResizeImageWithOptimalCrop($imgGeneralBGWidth, $imgGeneralBGHeight, $pathToFolder.$fileNameGeneralBG,$tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1.2, 0, 0, \imagick::COMPRESSION_NO, 100);
							// Создаём файл нужного размера с оптимальным обрезанием (превью)
							$myImagick->makeResizeImageWithOptimalCrop($imgMediumBGWidth, $imgMediumBGHeight, $pathToFolder.$fileNameMediumBG,$tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1.2, 0, 0, \imagick::COMPRESSION_NO, 100);
							// Создаём файл нужного размера с оптимальным обрезанием (превью)
							$myImagick->makeResizeImageWithOptimalCrop($imgSmallBGWidth, $imgSmallBGGHeight, $pathToFolder.$fileNameSmallBG, $tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED,  1.2, 0, 0, \imagick::COMPRESSION_NO, 100);
							$newRow = $myOthers->addImgOneMultiLangsII('works', $uploader[1], $fileName, $imgTitle, $imgGeneralBGWidth,$imgGeneralBGHeight, $imgMediumBGWidth, $imgMediumBGHeight, $imgSmallBGWidth, $imgSmallBGGHeight, $idRecord, 'idWorks', $pageLang);

						} else if ($uploader[1] == "addpage"){
							$myImagick->makeResizeImageWithOptimalCrop($imgGeneralADDWidth, $imgGeneralADDHeight, $pathToFolder.$fileNameGeneralADD,$tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1, 0, 0, \imagick::COMPRESSION_NO, 100);
							$newRow = $myOthers->addImgOneMultiLangs('works', $uploader[1], $fileName, $imgTitle, $imgGeneralADDWidth, $imgGeneralADDHeight, $idRecord, 'idWorks', $pageLang);	}


						else if ($uploader[1] == "mainpage"){
							$myImagick->makeResizeImageWithOptimalCrop($imgGeneralMPWidth, $imgGeneralMPHeight, $pathToFolder.$fileNameGeneralMP,$tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1, 0, 0, \imagick::COMPRESSION_NO, 100);
							// Создаём файл нужного размера с оптимальным обрезанием (превью)
							$myImagick->makeResizeImageWithOptimalCrop($imgBigMPWidth, $imgBigMPHeight, $pathToFolder.$fileNameBigMP,$tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1, 0, 0, \imagick::COMPRESSION_NO, 100);

							$myImagick->makeResizeImageWithOptimalCrop($imgMediumMPWidth, $imgMediumMPMHeight, $pathToFolder.$fileNameMediumMP,$tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1, 0, 0, \imagick::COMPRESSION_NO, 100);
							// Создаём файл нужного размера с оптимальным обрезанием (превью)
							$myImagick->makeResizeImageWithOptimalCrop($imgSmallMPWidth, $imgSmallMPMHeight, $pathToFolder.$fileNameSmallMP, $tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED,  1, 0, 0, \imagick::COMPRESSION_NO, 100);
							$newRow = $myOthers->addImgOneMultiLangsIII('works', $uploader[1], $fileName, $imgTitle, $imgGeneralMPWidth, $imgGeneralMPHeight, $imgBigMPWidth, $imgBigMPHeight, $imgMediumMPWidth, $imgMediumMPMHeight, $imgSmallMPWidth, $imgSmallMPMHeight, $idRecord, 'idWorks', $pageLang);



						} else {
							$myImagick->makeResizeImageWithOptimalCrop($imgGeneralWidth, $imgGeneralHeight, $pathToFolder.$fileNameGeneral,$tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED,  1, 0, 0, \imagick::COMPRESSION_NO, 100);
							// Создаём файл нужного размера с оптимальным обрезанием (превью)
							$myImagick->makeResizeImageWithOptimalCrop($imgPreviewWidth, $imgPreviewHeight, $pathToFolder.$fileNamePreview, $tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1, 0, 0, \imagick::COMPRESSION_NO, 100);
							$newRow = $myOthers->addImgOneMultiLangsI('works', $uploader[1], $fileName, $imgTitle, $imgGeneralWidth, $imgGeneralHeight, $imgPreviewWidth, $imgPreviewHeight, $idRecord, 'idWorks', $pageLang);
							$myImagick->makeResizeImageWithOptimalCrop($imgBigSBKWidth, $imgBigSBKHeight, $pathToFolder.$fileNameBigSBK,$tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1, 0, 0, \imagick::COMPRESSION_NO, 100);
							// Создаём файл нужного размера с оптимальным обрезанием (превью)
							$myImagick->makeResizeImageWithOptimalCrop($imgMediumSBKWidth, $imgMediumSBKHeight, $pathToFolder.$fileNameMediumSBK, $tmp_dir.'/'.$name, $format, \imagick::FILTER_UNDEFINED, 1, 0, 0, \imagick::COMPRESSION_NO, 100);
							$newRow = $myOthers->addImgOneMultiLangsI('works', $uploader[1], $fileName, $imgTitle, $imgGeneralWidth, $imgGeneralHeight, $imgPreviewWidth, $imgPreviewHeight, $idRecord, 'idWorks', $pageLang);
						}

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
					$fileNameOriginal = "p/works/original-".$fileName;
					$fileNameMedium = "p/works/medium-".$fileName;

					// Копируем файл оригинал
					copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameOriginal);

					if ($_SERVER['REMOTE_ADDR'] == '127.0.0.1') {
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameMedium);
						$newRow = $myOthers->addImgManyMultiLangs('works_'.$uploader[1], 'idWorks', $idRecord, $fileName, $imgTitle, $imgWidth, $imgHeight, $pageLang);
					} else {
						/*// Загружаем как есть
						copy($tmp_dir.'/'.$name, $pathToFolder.$fileNameMedium);
						$newRow = $myOthers->addImgMany('works_'.$uploader[1], 'idWorks', $idRecord, $fileName, $imgTitle, $imgWidth, $imgHeight);*/

						/*// Создаём файл нужного размера по ширине
						$h = $myImagick->makeResizeImageByWidth(200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('works_'.$uploader[1], 'idWorks', $idRecord, $fileName, $imgTitle, 200, $h);*/

						/*// Создаём файл нужного размера по высоте
						$w = $myImagick->makeResizeImageByHeight(200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('works_'.$uploader[1], 'idWorks', $idRecord, $fileName, $imgTitle, $w, 200);*/

						/*// Создаём файл нужного размера по минимальной стороне
						$sizes = $myImagick->makeResizeImageByMinSide(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('works_'.$uploader[1], 'idWorks', $idRecord, $fileName, $imgTitle, $sizes[0], $sizes[1]);*/

						/*// Создаём файл нужного размера без обрезания
						$myImagick->makeResizeImage(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('works_'.$uploader[1], 'idWorks', $idRecord, $fileName, $imgTitle, 200, 200);*/

						/*// Создаём файл нужного размера с оптимальным обрезанием
						$myImagick->makeResizeImageWithOptimalCrop(200, 200, $fileNameMedium, $tmp_dir.'/'.$name, $format, imagick::FILTER_HAMMING, 0.8, 0, 1, imagick::COMPRESSION_LZW, 87);
						$newRow = $myOthers->addImgMany('works_'.$uploader[1], 'idWorks', $idRecord, $fileName, $imgTitle, 200, 200);*/
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
							$myOthers->updateImgOneMultiLangs('works', $key[0], $item['imgTitle'], $idRecord, 'idWorks', $pageLang);
						} else {
							$myOthers->deleteImgOneMultiLangs('works', $key[0], $idRecord);
						}
					} else { // несколько изображений
						if (!$item['deleted']) {
							$myOthers->updateImgManyMultiLangs('works_'.$key[0], $item['imgTitle'], $item['picking'], $key[1], $pageLang);
						} else {
							$myOthers->deleteImgMany('works_'.$key[0], $key[1]);
						}
					}
				}
			}

			// Множество текстовых полей "Пункты результата"
			$resultlist1 = ArrayHelper::getValue($_POST, 'resultlist1', []);
			$myOthers->updateManyFieldsElementIMultiLangsSimple('works_resultlist1', $idRecord, $resultlist1, $pageLang);

			// Группа чекбоксов "Выбор ссылок отображаемых в футере"
			$linksIds = ArrayHelper::getValue($_POST, 'linksIds', []);
			$myOthers->updateChGrIds('works_links', 'idWorks', 'idLinks', $idRecord, $linksIds);/* UpdateCodeBottom */

			exit($json_data);
		} else {
			throw new HTTP_Exception_404('Нет такой страницы.');
		}
	}
}