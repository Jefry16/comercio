<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;
use App\Models\Product as ModelsProduct;
use App\Models\Variant;
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

    public function addVariantAction()
    {
        $errors = $this->handleAdd(Variant::class, 'Variant');

        View::renderTemplate('Backoffice/Product/variants.html', [
            'inputs' => $_POST,
            'errors' => $errors
        ]);
    }

    public function variantsAction()
    {
        View::renderTemplate('Backoffice/Product/variants.html');
    }
}
