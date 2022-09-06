<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

# models
use App\Models\Notification;

# events
// use App\Events\NotificationEvent;

# facades
use Illuminate\Support\Facades\DB;

class NotificationsController extends Controller
{
  public function index()
  {
    $notifications = auth()->user()->notifications()->orderBy('created_at', 'DESC')->limit(100)->get();
    return response()->json($notifications, 200);
  }

  public function store(Request $request)
  {
    if (is_image($request->image))
      $request->imageSrc = $request->image->store('public', 'Notifications');

    $notification = Notification::create($request->all());
    // event(new NotificationEvent($notification));
    return $notification;
  }

  public function show($id)
  {
    //
  }

  // mark as seen
  public function update($notificationID)
  {
    if ($notificationID == 'all')
      DB::table('notifications')->where('read_at', null)->update(['read_at' => now()]);
    else if ($notificationID)
      DB::table('notifications')->where('read_at', null)->where('id',  $notificationID)->update(['read_at' => now()]);
    return response()->json(null, 201);
  }

  public function destroy($id)
  {
    //
  }
}
