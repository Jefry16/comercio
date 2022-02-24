<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;
use App\Models\Product as ModelsProduct;
use App\Modules\Paginator;
use \Core\View;

class Categories extends \Core\Controller
{
    protected function before()
    {
        $this->redirectIfNotAdmin();
    }

    public function homeAction()
    {
        $paginatedCategories = Category::getAllByPage();
        var_dump($paginatedCategories);
        View::renderTemplate('Backoffice/Category/index.html', [
            'categories' => $paginatedCategories
        ]);
    }

    public function addAction()
    {
        $errors = $this->handleAdd(Category::class, 'category');

        View::renderTemplate('Backoffice/Category/add.html', [
            'errors' => $errors,
            'inputs' => $_POST
        ]);        
    }

}
