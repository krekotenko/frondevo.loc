<?php
namespace vendor\pagination;
/*
 * Класс Pagination для генерации постраничной навигации
 */
use vendor\UrlProvider\TextPagesUrlProvider;

class Pagination
{
    /**
     *
     * @var Ссылок навигации на страницу
     *
     */
    private $max = 6;
    /**
     *
     * @var Ключ для GET, в который пишется номер страницы
     *
     */
    private $index = 'page';
    /**
     *
     * @var Текущая страница
     *
     */
    private $current_page;
    /**
     *
     * @var Общее количество записей
     *
     */
    private $total;
    /**
     *
     * @var Записей на страницу
     *
     */
    private $limit;

    /**
     * Запуск необходимых данных для навигации
     * @param type $total <p>Общее количество записей</p>
     * @param type $currentPage <p>Номер текущей страницы</p>
     * @param type $limit <p>Количество записей на страницу</p>
     * @param type $index <p>Ключ для url</p>
     * @param type $urlpage <p>Ссылка на страницу для формирования ссылок в кнопках</p>
     * @param type $filterUri <p>Uri фильтра</p>
     */
    public function __construct($total, $currentPage, $limit, $index, $urlpage, $filterUri = '')
    {
        # Устанавливаем общее количество записей
        $this->total = $total;
        # Устанавливаем количество записей на страницу
        $this->limit = $limit;
        # Устанавливаем ключ в url
        $this->index = $index;
        # Устанавливаем ссылку на страницу
        $this->urlpage = $urlpage;
        # Устанавливаем количество страниц
        $this->amount = $this->amount();
        # Устанавливаем номер текущей страницы
        $this->setCurrentPage($currentPage);
        # Uri фильтра
        $this->filterUri = $filterUri;
    }

    /**
     *  Для вывода ссылок
     * @return HTML-код со ссылками навигации
     */
    public function get()
    {
        # Для записи ссылок
        $links = null;
        # Получаем ограничения для цикла
        $limits = $this->limits();

        $html = '<ul class="pager"
<li class="pager-item mob-only active">
                            <span>&lt;</span>
                        </li>>';
        # Генерируем ссылки
        for ($page = $limits[0]; $page <= $limits[1]; $page++) {
            # Если текущая это текущая страница, ссылки нет и добавляется класс active
            if ($page == $this->current_page) {
                $links .= '<li class="pager-item desk-only active"><span>' . $page . '</span></li>';
            } else {
                # Иначе генерируем ссылку
                $links .= $this->generateHtml($page);
            }
        }
        # Если ссылки создались
        if (!is_null($links)) {
            # Если текущая страница не первая
            if ($this->current_page > 1)
                # Создаём ссылку "На первую"
                $links = $this->generateHtml(1, '&lt;') . $links;
            # Если текущая страница не первая
            if ($this->current_page < $this->amount)
                # Создаём ссылку "На последнюю"
                $links .= $this->generateHtml($this->amount, '&gt;');
            # Если текущая страница не первая
            if ($this->current_page != 1)
                # Создаём ссылку "На первую"
                $links = $this->generateHtml(1, '&lt;','mob') . $links;
            # Если текущая страница не первая
            if ($this->current_page != $this->amount)
                # Создаём ссылку "На последнюю"
                $links .= $this->generateHtml($this->amount, '&gt;','mob');

        }
        $html .= $links;
        # Возвращаем html
        return $html;
    }

    /**
     * Для генерации HTML-кода ссылки
     * @param integer $page - номер страницы
     *
     * @return
     */
    private function generateHtml($page, $text = null,$device = null)
    {   if($text == '&gt;'&& $device == null){
        if (!$text)
            # Указываем, что текст - цифра страницы
            $text = $page;
        $currentURI =  $this->urlpage;
        $currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);
        # Формируем HTML код ссылки и возвращаем
        $_GET['page'] = isset($_GET['page'])?$_GET['page']:1;
        $page = $_GET['page'] + 1 ;
        return
            '<li class="pager-item desk-only"><a href="' . $currentURI .'/'.$this->filterUri. $this->index . $page . '">' . $text . '</a></li>';
    }
        if($text == '&lt;'&& $device == null){
            if (!$text)
                # Указываем, что текст - цифра страницы
                $text = $page;
            $currentURI =  $this->urlpage;
            $currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);
            # Формируем HTML код ссылки и возвращаем
            $page = $_GET['page'] - 1 ;
            return
                '<li class="pager-item desk-only"><a href="' . $currentURI .'/'.$this->filterUri. $this->index . $page . '">' . $text . '</a></li>';
        }
        if($text == '&gt;' && $device == 'mob'){
        if (!$text)
            # Указываем, что текст - цифра страницы
            $text = $page;
        $currentURI =  $this->urlpage;
        $currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);
        # Формируем HTML код ссылки и возвращаем
        $_GET['page'] = isset($_GET['page'])?$_GET['page']:1;
        $page = $_GET['page'] + 1 ;
        return
            '<li class="pager-item mob-only"><a href="' . $currentURI .'/'.$this->filterUri. $this->index . $page . '">' . $text . '</a></li>';
        }
        if($text == '&lt;'&& $device == 'mob'){
            if (!$text)
                # Указываем, что текст - цифра страницы
                $text = $page;
            $currentURI =  $this->urlpage;
            $currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);
            # Формируем HTML код ссылки и возвращаем
            $page = $_GET['page'] - 1 ;
            return
                '<li class="pager-item mob-only"><a href="' . $currentURI .'/'.$this->filterUri. $this->index . $page . '">' . $text . '</a></li>';
        }

        if($page==1 && !$text){
            $text = $page;
            $currentURI =  $this->urlpage;
            $currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);
            # Формируем HTML код ссылки и возвращаем
            return
                '<li class="pager-item desk-only"><a href="' . $currentURI.'/'.$this->filterUri.'">' . $text . '</a></li>';
        }elseif($page==1 && $text){
            $currentURI =  $this->urlpage;
            $currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);
            # Формируем HTML код ссылки и возвращаем
            return
                '<li class="pager-item desk-only"><a href="' . $currentURI.'/'.$this->filterUri.'">' . $text . '</a></li>';
        }
        # Если текст ссылки не указан
        if (!$text)
            # Указываем, что текст - цифра страницы
            $text = $page;
        $currentURI =  $this->urlpage;
        $currentURI = preg_replace('~/page-[0-9]+~', '', $currentURI);
        # Формируем HTML код ссылки и возвращаем
        return
            '<li class="pager-item desk-only"><a href="' . $currentURI .'/'.$this->filterUri. $this->index . $page . '">' . $text . '</a></li>';
    }

    /**
     *  Для получения, откуда стартовать
     *
     * @return массив с началом и концом отсчёта
     */
    private function limits()
    {
        # Вычисляем ссылки слева (чтобы активная ссылка была посередине)
        $left = $this->current_page - round($this->max / 2);

        # Вычисляем начало отсчёта
        $start = $left > 0 ? $left : 1;
        # Если впереди есть как минимум $this->max страниц
        if ($start + $this->max <= $this->amount) {
            # Назначаем конец цикла вперёд на $this->max страниц или просто на минимум
            $end = $start > 1 ? $start + $this->max : $this->max;
        } else {
            # Конец - общее количество страниц
            $end = $this->amount;
            # Начало - минус $this->max от конца
            $start = $this->amount - $this->max > 0 ? $this->amount - $this->max : 1;
        }
        # Возвращаем
        return
            array($start, $end);
    }

    /**
     * Для установки текущей страницы
     *
     * @return
     */
    private function setCurrentPage($currentPage)
    {
        # Получаем номер страницы
        $this->current_page = $currentPage;
        # Если текущая страница больше нуля
        if ($this->current_page > 0) {
            # Если текущая страница меньше общего количества страниц
            if ($this->current_page > $this->amount)
                # Устанавливаем страницу на последнюю
                $this->current_page = $this->amount;
        } else
            # Устанавливаем страницу на первую
            $this->current_page = 1;
    }

    /**
     * Для получения общего числа страниц
     *
     * @return число страниц
     */
    private function amount()
    {
        # Делим и возвращаем
        return ceil($this->total / $this->limit);
    }
}