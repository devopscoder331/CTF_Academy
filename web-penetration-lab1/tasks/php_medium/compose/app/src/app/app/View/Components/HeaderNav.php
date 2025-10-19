<?php

namespace App\View\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class HeaderNav extends Component
{
    public $user;
    public $page;

    /**
     * Create a new component instance.
     */
    public function __construct($user = null, $page = null)
    {
        $this->user = $user;
        $this->page = $page;
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.header-nav');
    }
}
