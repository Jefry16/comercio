<?php

namespace App\Controllers\Frontend;

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

    public function view()
    {
        var_dump(ModelsProduct::findBySlug($this->route_params['slug']));
    }
}
