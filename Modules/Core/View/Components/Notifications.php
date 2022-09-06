<?php

namespace Modules\Core\View\Components;

use Illuminate\View\Component;

class Notifications extends Component
{
  /**
   * Create a new component instance.
   *
   * @return void
   */
  public function __construct()
  {
    //
  }


  /**
   * Get the view / contents that represent the component.
   *
   * @return \Illuminate\View\View|string
   */
  public function render()
  {
    $notifications = notify()->pullAll();
    $has_notifications = $notifications->isNotEmpty();

    return view('core::components._notifications', compact(
      'notifications',
      'has_notifications',
    ));
  }
}
