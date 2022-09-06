<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;

# requests
use App\Http\Requests\ReportBugRequest;

# models
use App\Models\Roles\Role;
use App\Models\Clients\Client;
use App\Models\Apps\AppClient;

# facades
use Illuminate\Support\Facades\Mail;
use Exception;

class BugsController extends Controller
{
    public function store(ReportBugRequest $request)
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

                $client = Client::find($request->client_id);
                $client_app = AppClient::find($request->client_app_id);

                $tax_number = $client->tax_number;
                $url = $client_app->domain;
                $referer = $request->headers->get('referer') ?? $url;

                $body = [
                    "<div style='background-color:rgba(0,0,0,0.1);display:block;padding:0.25rem;'>" .
                    "   <img style='display:block;margin:1rem auto;width:2.5rem' src='$url/storage/". getConfig('logo') . "' />" .
                    "   <div style='$font_md;font-weight:600;margin-bottom:0.5rem;color:$lightGray;'>مشكلة في برنامج آر جي بي</div>" .
                    "   <div style='$font_lg;font-weight:600;'>" . parseName($client->name) . "</div>" .
                    "   <p style='$font_sm'>" . date("Y-m-d h:i:s A") . "</p>",
                    "</div>",

                    // name
                    "<div style='$font_sm;margin-bottom:3px;color:$lightGray'>الإسم</div><div style='$font_md'>" . (capitalize($request->name ?? 'لا يوجد')) . "</div>",
                    
                    // phone
                    "<div style='$font_sm;margin-bottom:3px;color:$lightGray'>رقم الجوال</div><div style='$font_md'>" . ($request->phone ?? 'لا يوجد') . "</div>",

                    // email
                    "<div style='$font_sm;margin-bottom:3px;color:$lightGray'>البريد الإلكتروني</div><div style='$font_md'>" . ($request->email ?? 'لا يوجد') . "</div>",
                    
                    // device
                    "<div style='$font_sm;margin-bottom:3px;color:$lightGray'>الجهاز</div><div style='$font_md'>" .
                        ($request->device_type ? "{$request->device_type} | {$request->device_number}" : 'لم تتم إضافة معلومات الجهاز') .
                    "</div>",

                    // referer
                    "<div style='$font_sm;margin-bottom:3px;color:$lightGray'>رابط الصفحة التي تم الإبلاغ من خلالها</div>
                    <div style='$font_md'><a style='text-decoration:none;' href='$referer'>$referer</a></div>",

                    // message
                    "<div style='background-color:rgba(0,0,0,0.1);padding:12px;$font_md;white-space:pre-wrap;'>{$request->message}</div>",

                    // open client app
                    "<a style='display:block;text-decoration:none;' href='$url'>
                        <p style='padding:8px 12px;margin:20px auto -12px auto;background:$primary;color:$light;border-radius:50px;display:block;width:max-content;'>
                            فتح التطبيق
                        </p>
                        <p>$url</p>
                    </a>",

                    // modfiy app settings
                    $client->id ? "<a
                        style='padding:8px 12px;margin:20px auto;background:$primary;color:$light;text-decoration:none;border-radius:50px;display:block;width:max-content;'
                        href='". config('master.url') ."/master/clients/{$client->id}?app_id={$client_app->id}'
                    >
                        تعديل إعدادات / صلاحيات التطبيق من خلال RGB ماستر
                    </a>" : null,

                    // address
                    "<div style='$font_sm;'>جوال {$client->phone} - رقم ضريبي $tax_number</div>",
                    "<div style='$font_sm;margin-top:-0.75rem;'>" . parseName($client->full_address, 'ar') . "</div>",
                ];

                $body = "<html><body dir='rtl' style='text-align:center'>" . implode('', array_map(fn($l) => "<div style='margin-bottom:1.5rem;'>$l</div>", $body)) . "</body></html>";

                $message->subject("مشكلة في برنامج آر جي بي | " . parseName($client->name))
                    ->to($receivers)
                    ->setBody($body, 'text/html');

                if ($request->attachment)
                    $message->attach(public_path('/storage/' . $request->attachment->store('bugs', 'public')));
            });
        }
        
        catch (Exception $e) {
            return response()->json(['errors' => 'somethingWentWrong', 'errorMsg' => (string) $e], 422);
        }
    }
}