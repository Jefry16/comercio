<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;
use App\Models\Product as ModelsProduct;
use App\Models\Variant;
use App\Modules\Message;
use \Core\View;

class Products extends \Core\Controller
{
    protected function before()
    {
        $this->redirectIfNotAdmin();
    }

    public function homeAction()
    {
        $paginatedProducts = ModelsProduct::getAllByPage();

        View::renderTemplate('Backoffice/Product/index.html', [
            'pagination' => $paginatedProducts
        ]);
    }

    public function addAction()
    {
        $categories = Category::getAll();

        $errors = $this->handleAdd(ModelsProduct::class, 'product');

        View::renderTemplate('Backoffice/Product/add.html', [
            'categories' => $categories,
            'errors' => $errors,
            'inputs' => $_POST
        ]);
    }

    public function variantsAction()
    {
        $errors = $this->handleAdd(Variant::class, 'variant');

        $variants = Variant::getAll();

        View::renderTemplate('Backoffice/Product/variants.html', [
            'variants' => $variants,
            'inputs' => $_POST,
            'errors' => $errors
        ]);
    }
}
