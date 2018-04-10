<?php

namespace pvsaintpe\search\interfaces;

/**
 * Interface PerformanceInterface
 * @package backend\components
 */
interface PerformanceInterface
{
    /**
     * @return array
     */
    public function requiredAttributes();

    /**
     * @return array
     */
    public function performanceRules();

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformance($searchClass = null, $route = null);

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceOptions($searchClass = null, $route = null);

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceDelete($searchClass = null, $route = null);

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceUpdate($searchClass = null, $route = null);

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceShare($searchClass = null, $route = null);

    /**
     * @param string|null $searchClass
     * @param string|null $route
     * @return array
     */
    public function getGridPerformanceCreate($searchClass = null, $route = null);

    /**
     * @return array
     */
    public function getPerformanceFilters();

    /**
     * @return array
     */
    public function getGridPerformanceToolbar();


}