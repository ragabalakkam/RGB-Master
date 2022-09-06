<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

# controllers
use App\Http\Controllers\API\Apps\AppsController;

# requests
use App\Http\Requests\Apps\AppRequest;

# models
use App\Models\Apps\RGBOnline;

class AppsSeeder extends Seeder
{
	public static function run()
	{
		$apps =
		[
			// afaqstore (online)
			[
				'model'									=> RGBOnline::class,
				'online'								=> true,
				'name'                  => localize(['RGB (Online)', 'RGB (أونلاين)']),
				'description'           => localize([
					"The customer service and technical support program, provided by Afaq Al-Khaleej Est. for Information Technology, is a program that provides the organization with solutions to give its customers a distinguished support service with the ability to send text messages, pictures or videos related to the customer’s problem and its solution through technical support and customer service staff or one of the specialized supervisors. The process is done by opening the support ticket for the customer and then automatically transferring it to the concerned employee to follow up with the customer and update the ticket information until it is closed by solving the customer’s problem.",
					"برنامج خدمة العملاء والدعم الفني المقدم من شركة آفاق الخليج لتقنيات المعلومات هو برنامج يوفر للمؤسسة حلول لمنح عملاءها خدمة دعم متميزة بإمكانية إرسال الرسائل النصية او الصور او مقاطع الفيديو المتعلقة بمشكلة العميل وحلها عن طريق موظفي الدعم الفني وخدمة العملاء او احد المشرفين المختصين. تتم العملية بفتح تذكرة الدعم للعميل ومن ثم تحويلها اوتوماتيكياً الى الموظف المختص للمتابعة مع العميل وتحديث معلومات التذكرة حتى يتم غلقها بحل مشكلة العميل,",
				]),
				'configuration_groups'  =>
				[
					// app id & app secret & demo & live && offline
					[
						'key'           	=> 'app',
						'name'          	=> localize(['App settings', 'إعدادت التطبيق']),
						'description'   	=> "",
						'configurations' 	=> [
							['text',  		'app_id',						null,		[
								'App ID',
								'الرقم التعريفي للتطبيق',
							]],
							['text',  		'app_secret',				null,		[
								'App secret',
								'كلمة السر للتطبيق',
							]],
							['boolean',		'demo',             true,  [
								'Demo version <p class=\"font-md\">Contains forms filled with data automatically | uses files of relatively large sizes</p>',
								'نسخة تجريبية <p class=\"font-md\">تحتوي على استمارات مليئة بالبيانات بشكل تلقائي | تستخدم ملفات بأحجام كبيرة نسبياً</p>',
							]],
							['boolean',		'offline',					false,  [
								'Offline version <p class=\"font-md\">Not associated with a domain name | will not work without a license</p>',
								'نسخة تعمل بدون إنترنت <p class=\"font-md\">غير مربوط بإسم نطاق | لن تعمل بدون ترخيص</p>',
							]],
							['boolean',		'live',							true,  	[
								'Software is uploaded to cPanel domain/sub-domain <p class=\"font-md\">Uses files of relatively small sizes</p>',
								'نسخة مربوطة بإسم نطاق رئيسي / فرعي cPanel <p class=\"font-md\">تستخدم ملفات بأحجام صغيرة نسبياً</p>',
							]],
						],
					],

					// organization
					[
						'key'           	=> 'organization',
						'name'          	=> localize(['Organization settings', 'إعدادات المؤسسة']),
						'description'   	=> "",
						'configurations' 	=> [
							[
								'number',
								'number_of_branches',
								1,
								[
									'Number of avilable branches',
									'عدد الفروع المتاحة',
								]
							],
							[
								'number',
								'number_of_points_of_sale',
								1,
								[
									'Number of available points of sale for each branch',
									'عدد نقاط البيع المتاحة لكل فرع',
								]
							],
						],
					],

					// modules
					[
						'key'           	=> 'modules',
						'name'          	=> localize(['Modules', 'الصلاحيات']),
						'description'   	=> "",
						'configurations' 	=> [
							['boolean',	'requirePOSLicense',                                        			false, [
								'Require POS license',
								'تأمين نقاط البيع بإستخدام الترخيص',
							]],
							['boolean',	'taxPayer',                                                 			true,  [
								'Taxes',
								'ضرائب',
							]],
							['boolean',	'feesPayer',                                                			false, [
								'Fees',
								'رسوم',
							]],
							['boolean',	'smsServiceUser',                                           			false,  [
								'SMS service (Malath)',
								'خدمة الرسائل النصية (ملاذ Malath)',
							]],
							['boolean',	'takeawayCashier',                                          			true,  [
								'Takeaway cashier system',
								'نظام بيع سفري',
							]],
							['boolean',	'dineInCashier',                                            			true,  [
								'Dine-in cashier system',
								'نظام بيع محلي',
							]],
							['boolean',	'deliveryCashier',                                          			true,  [
								'Delivery cashier system',
								'نظام بيع توصيل',
							]],
							['boolean',	'onlineCashier',                                            			true,  [
								'Online cashier system',
								'نظام بيع أونلاين',
							]],
							['boolean',	'manufacturer',                                             			true,  [
								'Manufacturing management',
								'إدارة التصنيع',
							]],
							['boolean',	'eventsLogger',                                             			true,  [
								'Event logs',
								'سجل الأحداث',
							]],
							['boolean',	'sizes',                                                    			false, [
								'Use product sizes',
								'التعامل بمقاسات المنتجات',
							]],
							['boolean',	'weights',                                                  			false, [
								'Use product weights',
								'التعامل بأوزان المنتجات',
							]],
							['boolean',	'lowestPrice',                                              			false, [
								'Use lowest selling price',
								'التعامل بأقل سعر بيع',
							]],
							['boolean',	'salesman',                                                 			true,  [
								'Sales settings',
								'إعدادات المبيعات',
							]],
							['boolean',	'accountant',                                               			true,  [
								'Full accounting guide',
								'الدليل المحاسبي كامل',
							]],
							['boolean',	'quotations',                                               			false, [
								'Quotations',
								'عروض أسعار',
							]],
							['boolean',	'barcodePrinting',                                          			false, [
								'Barcode printing',
								'طباعة الباركود',
							]],
							['boolean',	'shipping',                                                 			false, [
								'Shipping',
								'الشحن (طباعة ملطق الشحن)',
							]],
							['boolean',	'finances',                                                 			true,  [
								'Revenues & expenses',
								'الإيرادات والمصروفات',
							]],
							['boolean',	'inventory',                                                			true,  [
								'Inventory',
								'المخزون',
							]],
							['boolean',	'subscriptions',                                            			false, [
								'Subscriptions',
								'الإشتراكات',
							]],
							['boolean',	'printingManager',                                          			true,  [
								'Printing settings',
								'إعدادات الطباعة',
							]],
							['boolean',	'databaseAdminstrator',                                     			false, [
								'Database backup / restoration',
								'اخذ / استعادة النسخ الاحتياطية من قاعدة البيانات',
							]],
							['boolean',	'importAndExport',                                          			true,  [
								'Import & Export',
								'الإستيراد والتصدير',
							]],
							['boolean',	'shifts',                                                   			true,  [
								'Dealing with shift system',
								'التعامل بنظام الورديات',
							]],
							['boolean',	'disbursementAuthorizations',                               			true,  [
								'Dealing with warehouse permits system',
								'التعامل بنظام أذونات المخازن',
							]],
							['boolean',	'automaticItemCoding',                                      			false, [
								'Automatic item coding',
								'التكويد التلقائي للأصناف',
							]],
							['boolean',	'search',                                                   			true,  [
								'Search admin panel page (in development..)',
								'البحث في صفحات المسؤول - الأدمن (قيد التطوير)',
							]],
							['boolean',	'externalPrint',                                            			true,  [
								'External printings (A4 / Roll Invoices)',
								'الطباعة الخارجية (فواتير A4 / Roll)',
							]],
							['boolean',	'requireHigherLevelForCashierTabs',                         			true,  [
								'Require higher-level login for cashier tabs',
								'طلب تسجيل الدخول لموظف بصلاحية أعلى لفتح تبويبات الكاشير',
							]],
							['boolean',	'sellingWithoutStock',                                      			true,  [
								'Allow selling without stock',
								'السماح بالبيع على المكشوف (بدون رصيد)',
							]],
							['boolean',	'shiftSales',                                               			false, [
								'Shift sales',
								'مبيعات الوردية',
							]],
							['boolean',	'print_report.SalesAndPurchases/purchases_base_date',       			true,  [
								"Analysis of total period purchases at the level of days",
								"تحليل مشتريات إجمالي فترة على مستوى الأيام",
							]],
							['boolean',	'print_report.SalesAndPurchases/purchases_base_invoices',   			true,  [
								"Analyze total period purchases at the level of invoice numbers",
								"تحليل مشتريات إجمالي فترة على مستوى أرقام الفواتير",
							]],
							['boolean',	'print_report.SalesAndPurchases/sales_base_invoices', 						true,  [
								"Analyze total period sales at the level of invoice numbers",
								"تحليل مبيعات إجمالي فترة على مستوى أرقام الفواتير",
							]],
							['boolean',	'print_report.SalesAndPurchases/sales_base_on_customer', 					true,  [
								"Total sales period analysis at customer level",
								"تحليل مبيعات إجمالي فترة على مستوى العملاء",
							]],
							['boolean',	'print_report.SalesAndPurchases/sales_base_on_date', 							true,  [
								"Analysis of total period purchases at the level of days",
								"تحليل مبيعات إجمالي فترة على مستوى الأيام",
							]],
							['boolean',	'print_report.SalesAndPurchases/sales_base_on_supplier', 					true,  [
								"Total sales period analysis at the supplier level",
								"تحليل مشتريات إجمالي فترة على مستوى الموردين",
							]],
							['boolean',	'print_report.inventory/inventory', 															true,  [
								"Warehouse inventory",
								"جرد مخزن",
							]],
							['boolean',	'print_report.itemsMovement/demand_limit', 												true,  [
								"Items that have reached the minimum reorder",
								"الأصناف التي بلغت الحد الأدنى لإعادة الطلب",
							]],
							['boolean',	'print_report.itemsMovement/demand_limit_pos', 										true,  [
								"Order limit for point of sale",
								"حد الطلب لنقطة بيع",
							]],
							['boolean',	'print_report.itemsMovement/items_move', 													true,  [
								"Movement of items during a period on the store",
								"حركة الأصناف خلال فترة على مخزن",
							]],
							['boolean',	'print_report.profitability/base_on_sale_date', 									true,  [
								"Profitability at the level of days over a period of time",
								"الربحية على مستوى الأيام خلال فترة زمنية",
							]],
							['boolean',	'print_report.profitability/profitability_base_date', 						true,  [
								"Profitability at the level of items sold total during a period according to the last cost of the item",
								"الربحية على مستوى الأصناف المباعة إجمالي خلال فترة على حسب آخر تكلفة للصنف",
							]],
							['boolean',	'print_report.profitability/profitability_base_item', 						true,  [
								"Profitability based on brands",
								"الربحية بناءً على الأصناف",
							]],
							['boolean',	'print_report.profitability/profitability_base_on_sale_products', true,  [
								"Profitability at the level of items sold",
								"الربحية على مستوى الأصناف المباعة",
							]],
							['boolean',	'print_report.accounts/accounts_level',														true,  [
								"Trial Balance",
								"يزان المراجعة",
							]],
							['boolean',	'print_report.accounts/entaccount_statement', 										true,  [
								"Entity statement of account",
								"كشف حساب جهة",
							]],
							['boolean',	'print_report.accounts/entities_account_statement', 							true,  [
								"Entity statement of accounts",
								"كشف حساب جهات",
							]],
							['boolean',	'print_report.accounts/general_for_account_invoice', 							true,  [
								"Detailed account statement",
								"كشف حساب تفصيلي",
							]],
							['boolean',	'print_report.accounts/general_for_accounts', 										true,  [
								"General ledger account",
								"حساب الأستاذ",
							]],
							['boolean',	'print_report.accounts/simplified_accounts', 											true,  [
								"Accounts guide",
								"دليل الحسابات",
							]],
							['boolean',	'print_report.accounts/trial_balance', 														true,  [
								"Statement of account details",
								"كشف تفاصيل حساب",
							]],
							['boolean',	'print_report.shift/pro', 																				true,  [
								"Point of sale profitability",
								"بحية مبيعات لنقاط البيع",
							]],
							['boolean',	'print_report.shift/sales_system', 																true,  [
								"Sales analysis based on sales systems",
								"حليل المبيعات على مستوى أنظمة البيع",
							]],
							['boolean',	'print_report.shift/sales_systemA4', 															true,  [
								"Sales analysis based on sales systems A4",
								"حليل المبيعات على مستوى أنظمة البيع A4",
							]],
							['boolean',	'print_report.shift/total_sales_shift', 													true,  [
								"Total sales for shift",
								"جمالي مبيعات الوردية تحليل",
							]],
							['boolean',	'print_report.daily/daily', 																			true,  [
								"Daily report",
								"تقرير اليومية",
							]],
							['boolean',	'print_report.financials/finances', 															true,  [
								"Daily report",
								"الإيرادات / المصروفات",
							]],
							['boolean',	'print_report.financials/tax_declaration', 												true,  [
								"Daily report",
								"كشف شامل لضريبة المبيعات والمشتريات",
							]],
						],
					],

					// cashier settings
					[
						'key'           	=> 'cashier_settings',
						'name'          	=> localize(['Cashier settings', 'إعدادت الكاشير']),
						'description'   	=> "",
						'configurations' 	=> [
							[
								'boolean',
								'desktop_has_grid',
								true,
								[
									'desktop_has_grid',
									'desktop_has_grid'
								]
							],
							[
								'text',
								'desktop_grid_width',
								'66%',
								[
									'desktop_grid_width',
									'desktop_grid_width'
								]
							],
							[
								'text',
								'desktop_grid_gap',
								'1.875rem',
								[
									'desktop_grid_gap',
									'desktop_grid_gap'
								]
							],
							[
								'number',
								'desktop_rows',
								6,
								[
									'desktop_rows',
									'desktop_rows'
								]
							],
							[
								'number',
								'desktop_cols',
								7,
								[
									'desktop_cols',
									'desktop_cols'
								]
							],
							[
								'number',
								'desktop_main_categories_cols',
								1,
								[
									'desktop_main_categories_cols',
									'desktop_main_categories_cols'
								]
							],
							[
								'number',
								'desktop_sub_categories_cols',
								1,
								[
									'desktop_sub_categories_cols',
									'desktop_sub_categories_cols'
								]
							],
							[
								'number',
								'desktop_fields_in_row',
								2,
								[
									'desktop_fields_in_row',
									'desktop_fields_in_row'
								]
							],
							[
								'number',
								'desktop_delivery_items_in_row',
								6,
								[
									'desktop_delivery_items_in_row',
									'desktop_delivery_items_in_row'
								]
							],
							[
								'number',
								'desktop_dineIn_items_in_row',
								6,
								[
									'desktop_dineIn_items_in_row',
									'desktop_dineIn_items_in_row'
								]
							],
							[
								'boolean',
								'desktop_detailed_summary',
								false,
								[
									'desktop_detailed_summary',
									'desktop_detailed_summary'
								]
							],
							[
								'boolean',
								'desktop_detailed_items_table',
								false,
								[
									'desktop_detailed_items_table',
									'desktop_detailed_items_table'
								]
							],
							[
								'array',
								'desktop_headers',
								[
									'show_labels'       => true,
									'customer'          => true,
									'sms'				=> false,
									'delivery_datetime' => true,
									'warehouse'         => true,
									'search'            => true,
									'select_product'    => true,
									'add_by_barcode'    => true,
									'notes'             => false,
								],
								[
									'desktop_headers',
									'desktop_headers'
								]
							],
							[
								'boolean',
								'tablet_has_grid',
								true,
								[
									'tablet_has_grid',
									'tablet_has_grid'
								]
							],
							[
								'number',
								'tablet_rows',
								7,
								[
									'tablet_rows',
									'tablet_rows'
								]
							],
							[
								'number',
								'tablet_cols',
								7,
								[
									'tablet_cols',
									'tablet_cols'
								]
							],
							[
								'number',
								'tablet_main_categories_cols',
								1,
								[
									'tablet_main_categories_cols',
									'tablet_main_categories_cols'
								]
							],
							[
								'number',
								'tablet_sub_categories_cols',
								1,
								[
									'tablet_sub_categories_cols',
									'tablet_sub_categories_cols'
								]
							],
							[
								'boolean',
								'mobile_has_grid',
								true,
								[
									'mobile_has_grid',
									'mobile_has_grid'
								]
							],
							[
								'number',
								'mobile_rows',
								8,
								[
									'mobile_rows',
									'mobile_rows'
								]
							],
							[
								'number',
								'mobile_cols',
								4,
								[
									'mobile_cols',
									'mobile_cols'
								]
							],
							[
								'number',
								'mobile_main_categories_cols',
								1,
								[
									'mobile_main_categories_cols',
									'mobile_main_categories_cols'
								]
							],
							[
								'number',
								'mobile_sub_categories_cols',
								1,
								[
									'mobile_sub_categories_cols',
									'mobile_sub_categories_cols'
								]
							],
							[
								'text',
								'main_category_bg',
								'#6cb2eb',
								[
									'main_category_bg',
									'main_category_bg'
								]
							],
							[
								'text',
								'main_category_fg',
								'#ffffff',
								[
									'main_category_fg',
									'main_category_fg'
								]
							],
							[
								'text',
								'selected_main_category_bg',
								'#3127c4',
								[
									'selected_main_category_bg',
									'selected_main_category_bg'
								]
							],
							[
								'text',
								'selected_main_category_fg',
								'#ffffff',
								[
									'selected_main_category_fg',
									'selected_main_category_fg'
								]
							],
							[
								'text',
								'sub_category_bg',
								'#6c9dc6',
								[
									'sub_category_bg',
									'sub_category_bg'
								]
							],
							[
								'text',
								'sub_category_fg',
								'#ffffff',
								[
									'sub_category_fg',
									'sub_category_fg'
								]
							],
							[
								'text',
								'selected_sub_category_bg',
								'#272173',
								[
									'selected_sub_category_bg',
									'selected_sub_category_bg'
								]
							],
							[
								'text',
								'selected_sub_category_fg',
								'#ffffff',
								[
									'selected_sub_category_fg',
									'selected_sub_category_fg'
								]
							],
							[
								'text',
								'product_bg',
								'#3a73a1',
								[
									'product_bg',
									'product_bg'
								]
							],
							[
								'text',
								'product_overlay_bg',
								'#000000',
								[
									'product_overlay_bg',
									'product_overlay_bg'
								]
							],
							[
								'text',
								'product_overlay_fg',
								'#ffffff',
								[
									'product_overlay_fg',
									'product_overlay_fg'
								]
							],
							[
								'number',
								'product_overlay_transparency',
								0.5,
								[
									'product_overlay_transparency',
									'product_overlay_transparency'
								]
							],
							[
								'boolean',
								'show_language_switcher',
								true,
								[
									'show_language_switcher',
									'show_language_switcher'
								]
							],
							[
								'boolean',
								'must_print_in_ar',
								true,
								[
									'must_print_in_ar',
									'must_print_in_ar'
								]
							],
							[
								'boolean',
								'start_fullscreened',
								false,
								[
									'start_fullscreened',
									'start_fullscreened'
								]
							],
							[
								'boolean',
								'prevent_refresh',
								false,
								[
									'prevent_refresh',
									'prevent_refresh'
								]
							],
							[
								'text',
								'idle_img',
								'Cashier/DiningTables/idle.png',
								[
									'idle_img',
									'idle_img'
								]
							],
							[
								'text',
								'busy_img',
								'Cashier/DiningTables/busy.png',
								[
									'busy_img',
									'busy_img'
								]
							],
							[
								'text',
								'reserved_img',
								'Cashier/DiningTables/reserved.png',
								[
									'reserved_img',
									'reserved_img'
								]
							],
						],
					],
				],
			],

			// afaqstore (offline)
			[
				'model'									=> null,
				'online'								=> false,
				'name'                  => localize(['RGB (Offline)', 'RGB (أوفلاين)']),
				'description'           => localize([
					"The customer service and technical support program, provided by Afaq Al-Khaleej Est. for Information Technology, is a program that provides the organization with solutions to give its customers a distinguished support service with the ability to send text messages, pictures or videos related to the customer’s problem and its solution through technical support and customer service staff or one of the specialized supervisors. The process is done by opening the support ticket for the customer and then automatically transferring it to the concerned employee to follow up with the customer and update the ticket information until it is closed by solving the customer’s problem.",
					"برنامج خدمة العملاء والدعم الفني المقدم من شركة آفاق الخليج لتقنيات المعلومات هو برنامج يوفر للمؤسسة حلول لمنح عملاءها خدمة دعم متميزة بإمكانية إرسال الرسائل النصية او الصور او مقاطع الفيديو المتعلقة بمشكلة العميل وحلها عن طريق موظفي الدعم الفني وخدمة العملاء او احد المشرفين المختصين. تتم العملية بفتح تذكرة الدعم للعميل ومن ثم تحويلها اوتوماتيكياً الى الموظف المختص للمتابعة مع العميل وتحديث معلومات التذكرة حتى يتم غلقها بحل مشكلة العميل,",
				]),
				'configuration_groups'  => [],
			],
		];

		foreach ($apps as $app) {
			foreach ($app['configuration_groups'] as $group_key => $group) {
				foreach ($group['configurations'] as $config_key => $config) {
					$app['configuration_groups'][$group_key]['configurations'][$config_key] = [
						'datatype'		=> $config[0],
						'key'					=> $config[1],
						'default'			=> $config[2],
						'name' 				=> localize($config[3]),
						'description'	=> null,
					];
				}
			}

			$created_app = app(AppsController::class)->store(new AppRequest($app))->original;
			$created_app->image()->create(['src' => 'apps/' . str_replace(' ', '', $app['name']['en']) . '.png']);
		}
	}
}
