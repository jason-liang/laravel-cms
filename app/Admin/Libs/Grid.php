<?php

namespace App\Admin\Libs;

use Encore\Admin\Grid as AdminGrid;

class Grid extends AdminGrid
{
    protected $createBtnQueryString = '';

    public function setCreateBtnQueryString($qs) {
        $this->createBtnQueryString = $qs;

        return $this;
    }
    
    public function getCreateUrl()
    {
        $queryString = $this->createBtnQueryString;

        if ($constraints = $this->model()->getConstraints()) {
            $queryString += http_build_query($constraints);
        }

        return sprintf(
            '%s/create%s',
            $this->resource(),
            $queryString ? ('?'.$queryString) : ''
        );
    }

}