<?php

namespace App\Http\Controllers\API;

# facades
use Illuminate\Support\Facades\Storage;

# controllers
use App\Http\Controllers\Controller;

# requests
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Requests\Users\UpdateUserImageRequest;

class UsersController extends Controller
{
  public function update(UpdateUserRequest $request)
  {
    $user = auth()->user();
    $request->merge(['name' => capitalize($request->name)]);
    $user->update($request->all());
    return response()->json($user, 200);
  }

  public function changeProfilePicture(UpdateUserImageRequest $request)
  {
    $user = auth()->user();

    $src = $request->image->store('profile-pictures', 'public');

    if (isset($user->image)) {
      Storage::disk('public')->delete($user->image->src);
      $user->image->update(['src' => $src]);
    } else $user->image()->create(['src' => $src]);

    return response()->json($src);
  }

  public function getPermissions()
  {
    $actions = [];
    $permissions = collect();

    foreach (auth()->user()->roles as $role) {
      $permissions = $permissions->merge($role->permissions);
    }

    $collection = $permissions->whereNull('module');
    $collection = $collection->merge($permissions->whereNotNull('module')->filter(function ($perm) {
      return hasModule($perm['module']);
    }));
    
    foreach ($collection as $permisssion) {
      $actions = array_unique([...$actions, ...$permisssion->actions->pluck('name')->toArray()]);
    }

    return $actions;
  }
}
