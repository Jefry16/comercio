<?php

namespace App\Controllers\Backoffice;

use App\Models\Category;
use App\Models\Option;
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
        echo 'here i will show products tabs, such as: best selling, most visisted and so on....';
    }

    public function listingAction()
    {
        $paginatedProducts = ModelsProduct::getAllByPage();

        View::renderTemplate('Backoffice/Product/index.html', [
            'pagination' => $paginatedProducts,
        ]);
    }

    public function addAction()
    {

        $errors = $this->handleAdd(ModelsProduct::class, 'product');

        View::renderTemplate('Backoffice/Product/add.html', [
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

    public function optionAction()
    {
        //$options = new Option($_POST);

        //This is an array of options. None of the coming options should be in;
        $optionNames = Option::getAllOptionsById($_POST['optionid']);

        //These are the coming options. It must have at least a none empty value in order to save that value
        $comingOptions = explode(',', $_POST['name']);

        $optionsToBeSaved = [];

        foreach ($comingOptions as $option) {

            if (!in_array($option, $optionNames)  && $option != '') {
                $optionsToBeSaved[] = $option;
            }
        }

        if (count($optionsToBeSaved) > 0) {
            Option::addMultipleOptions($optionsToBeSaved, $_POST['optionid']);
        } else {
            Message::set('No option was added. This is because either the options existed already or they were empty.');
            header('Location:' . $_SERVER['HTTP_REFERER'], true, 303);
        }
    }
}
