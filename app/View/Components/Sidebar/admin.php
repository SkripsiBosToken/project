<?php

namespace App\View\Components\Sidebar;

use Closure;
use App\Http\Controllers\GuestController;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class admin extends Component
{
    public $setting;

    public function __construct(GuestController $guestController)
    {
        //
        $this->setting = $guestController->setting();
    }

    /**
     * Get the view / contents that represent the component.
     */
    public function render(): View|Closure|string
    {
        return view('components.sidebar.admin', ['setting' => $this->setting]);
    }
}
