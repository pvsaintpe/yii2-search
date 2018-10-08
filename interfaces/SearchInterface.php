<?php

namespace pvsaintpe\search\interfaces;

use pvsaintpe\search\components\View;
use yii\data\ActiveDataProvider;

/**
 * Interface SearchInterface
 * @package pvsaintpe\search\interfaces
 */
interface SearchInterface
{
    /**
     * @param array $filters
     * @return $this
     */
    public function setSearchFilters($filters = []);

    /**
     * @return array
     */
    public function getSearchFilters();

    /**
     * @return string
     */
    public function getSheetTitle();

    /**
     * @return bool
     */
    public function getShowTitle();

    /**
     * @return bool
     */
    public function getShowTableTitle();

    /**
     * @return bool
     */
    public function getShowPageSummary();

    /**
     * @return array
     */
    public function getSafeAttributes();

    /**
     * @return array
     */
    public function getAfterSummaryColumns();

    /**
     * @return mixed
     */
    public function getDisableColumns();

    /**
     * @return mixed
     */
    public function getEnableColumns();

    /**
     * @return array
     */
    public function getGridColumns();

    /**
     * @param string $attribute
     * @return array
     */
    public function getFilter($attribute);
    /**
     * @return array
     */
    public function getFilters();

    /**
     * @param array $filters
     * @return $this
     */
    public function setFilters($filters = []);

    /**
     * @return array
     */
    public function getPageAttributes();

    /**
     * @return array
     */
    public function getPagination();

    /**
     * @return int
     */
    public function getPaginationSize();

    /**
     * @return array
     */
    public function getSort();

    /**
     * @param mixed $sort
     */
    public function setSort($sort);

    /**
     * @return void
     */
    public function modifyQuery();

    /**
     * @return ActiveDataProvider
     */
    public function getDataProvider();

    /**
     * @param $order
     * @return $this
     */
    public function setDefaultOrder($order = null);

    /**
     * @return array
     */
    public function getGridToolbar();

    /**
     * @return array
     */
    public function getGridReset();

    /**
     * @param null|integer $id
     * @param string $class
     * @return array
     */
    public function getGridExport($id = null, $class = 'btn-md');

    /**
     * @param $permissionPrefix
     */
    public function setPermissionPrefix($permissionPrefix);

    /**
     * @return mixed
     */
    public function getPermissionPrefix();

    /**
     * @param View|\yii\base\View $view
     */
    public function setView($view);

    /**
     * @return View
     */
    public function getView();

    /**
     * @param $viewPath
     */
    public function setViewPath($viewPath);

    /**
     * @return mixed
     */
    public function getViewPath();

    /**
     * @param $updatePath
     */
    public function setUpdatePath($updatePath);

    /**
     * @return mixed
     */
    public function getUpdatePath();

    /**
     * @return mixed
     */
    public function getDisableAttributes();

    /**
     * @return mixed
     */
    public function getEnableAttributes();

    /**
     * @return \pvsaintpe\search\components\ActiveQuery
     */
    public function getRawQuery();

    /**
     * @return int
     */
    public function getPage();

    /**
     * @param null|integer $page
     * @return $this
     */
    public function setPage($page = null);
}
