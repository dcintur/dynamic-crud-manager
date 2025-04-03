<?php

namespace App\View\Components;

use Illuminate\View\Component;
use App\Models\DynamicPage;

class AdvancedFilter extends Component
{
    public $page;
    public $filters;

    public function __construct(DynamicPage $page)
    {
        $this->page = $page;
        $this->filters = request('filters', []);
    }

    public function render()
    {
        return view('components.advanced-filter');
    }
}