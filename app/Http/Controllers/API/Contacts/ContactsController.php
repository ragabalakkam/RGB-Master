<?php

namespace App\Http\Controllers\API\Contacts;

# models
use App\Models\Roles\Role;

# facades
use Illuminate\Support\Facades\Mail;

# controllers
use App\Http\Controllers\Controller;

# requests
use App\Http\Requests\Contacts\ContactRequest;

class ContactsController extends Controller
{
    public function store(ContactRequest $request)
    {
        $receivers = Role::where('key', 'master')->first()->employees()->whereNotNull('email')->pluck('email')->toArray();

        try {
            Mail::send([], [], function ($message) use ($request, $receivers) {
                $lightGray = '#6e6e6e';
                $primary = '#3490dc';
                $light = '#f8f9fa';
                
                $font_sm = 'font-size: 0.8rem';
                $font_md = 'font-size: 1rem';
                $font_lg = 'font-size: 1.25rem';

                $body = [
                    "<div style='background-color:rgba(0,0,0,0.1);display:block;padding:0.25rem;'>" .
                    "   <img style='display:block;margin:1rem auto;width:2.5rem' src='". getConfig('url') . "/storage/". getConfig('logo') . "' />" .
                    "   <div style='$font_md;font-weight:600;margin-bottom:0.5rem;color:$lightGray;'>{$request->name} يرغب في التواصل</div>" .
                    "   <p style='$font_sm'>" . date("Y-m-d h:i:s A") . "</p>",
                    "</div>",

                    // name
                    "<div style='$font_sm;margin-bottom:3px;color:$lightGray'>الإسم</div><div style='$font_md'>" . (capitalize($request->name)) . "</div>",
                    
                    // phone
                    "<div style='$font_sm;margin-bottom:3px;color:$lightGray'>رقم الجوال</div><div style='$font_md'>" . ($request->phone) . "</div>",

                    // email
                    "<div style='$font_sm;margin-bottom:3px;color:$lightGray'>البريد الإلكتروني</div><div style='$font_md'>" . ($request->email) . "</div>",

                    // message
                    "<div style='background-color:rgba(0,0,0,0.1);padding:12px;$font_md;white-space:pre-wrap;'>{$request->message}</div>",
                ];

                $body = "<html><body dir='rtl' style='text-align:center'>" . implode('', array_map(fn($l) => "<div style='margin-bottom:1.5rem;'>$l</div>", $body)) . "</body></html>";

                $message->subject("{$request->name} يرغب في التواصل")
                    ->to($receivers)
                    ->setBody($body, 'text/html');

                if ($request->attachment)
                    $message->attach(public_path('/storage/' . $request->attachment->store('bugs', 'public')));
            });
        }
        
        catch (\Exception $e) {
            return response()->json(['errors' => 'somethingWentWrong', 'errorMsg' => (string) $e], 422);
        }
    }
}
