<?php

namespace App\View\Components\Banner;

use Closure;
use App\Http\Controllers\GuestController;
use Illuminate\Contracts\View\View;
use Illuminate\View\Component;

class event extends Component
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
        return view('components.banner.event', ['setting' => $this->setting]);
    }
}
