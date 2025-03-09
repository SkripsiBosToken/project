<?php

namespace App\View\Components\Sidebar;

use Closure;
use App\Http\Controllers\GuestController;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class customer extends Component
{
    public $categories;

    public function __construct(GuestController $guestController)
    {
        //
        $this->categories = $guestController->getCategories();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar.customer', ['categories' => $this->categories]);
    }
}
