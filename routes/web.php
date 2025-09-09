<?php

use App\Http\Controllers\TableController;
use App\Models\Warehouse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OjolController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ShiftController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ExpenseController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\RegionalController;
use App\Http\Controllers\WhatsappController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\OrderTypeController;
use App\Http\Controllers\WarehouseController;
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\StockOpnameController;
use App\Http\Controllers\CloseCashierController;
use App\Http\Controllers\BusinessStockController;
use App\Http\Controllers\CustomMessageController;
use App\Http\Controllers\OjolWarehouseController;
use App\Http\Controllers\StockPurchaseController;
use App\Http\Controllers\CustomCategoryController;
use App\Http\Controllers\ExpenseCategoryController;
use App\Http\Controllers\ProductWarehouseController;
use App\Http\Controllers\Report\ProductOmzetController;
use App\Http\Controllers\Report\FinanceReportController;
use App\Http\Controllers\TableTransactionController;

// Fallback: redirect any unknown route to the admin login page
Route::fallback(function () {
    return redirect()->route('admin.auth.index');
});

// Maintenance helpers (consider securing/limiting in production)
Route::get('/get-storage', function () {
    Artisan::call('storage:link');
});
Route::get('/config-clear', function () {
    Artisan::call('config:clear');
});
Route::get('/optimize-clear', function () {
    Artisan::call('optimize:clear');
});

Route::group(['prefix' => 'admin'], function () {
    // Admin authentication (login/logout)
    Route::middleware(['web'])->group(function () {
        Route::get('login', [AuthController::class, 'index'])->name('admin.auth.index');
        Route::post('login', [AuthController::class, 'login'])->name('admin.auth.login');
        Route::post('logout', [AuthController::class, 'logout'])->name('admin.auth.logout');
    });

    // Authenticated: basic home/upload
    Route::group(['middleware' => 'auth:web'], function () {
        Route::post('upload-apk', [HomeController::class, 'uploadApk'])->name('uploadApk');
        Route::controller(HomeController::class)->group(function () {
            Route::get('home', 'home');
        });
    });

    // Main admin area
    Route::group(['middleware' => ['common', 'auth:web', 'active']], function () {

        // Dashboard & home
        Route::controller(HomeController::class)->group(function () {
            Route::get('/', 'index');
            Route::get('/dashboard', 'dashboard')->name('admin.dashboard');
            Route::get('my-transactions/{year}/{month}', 'myTransaction');
        });

        // WhatsApp
        Route::controller(WhatsappController::class)->name('whatsapp.')->group(function () {
            Route::get('/whatsapp', 'index')->name('index');
            Route::post('/whatsapp/store', 'store')->name('store');
            Route::get('/whatsapp/sessions', 'sessions')->name('sessions');
            Route::get('/whatsapp/session-details', 'sessionDetails')->name('sessionDetails');
            Route::get('/whatsapp/create-session', 'createSession')->name('createSession');
            Route::get('/whatsapp/logout', 'logout')->name('logout');
            Route::get('/whatsapp/check-connection', 'checkConnection')->name('checkConnection');
            Route::get('/whatsapp/check-number/{number}', 'checkNumber')->name('checkNumber');
            Route::post('/whatsapp/send-message/{number}/{message}', 'sendMessage')->name('sendMessage');
        });

        // Custom messages
        Route::controller(CustomMessageController::class)->group(function () {
            Route::resource('custom-message', CustomMessageController::class);
        });

        // Products & outlets
        Route::get('produk-outlet/sort/{category}', [ProductWarehouseController::class, 'sort'])->name('produk-outlet.sort');
        Route::put('produk-outlet/sort', [ProductWarehouseController::class, 'storeSort'])->name('produk-outlet.storeSort');
        Route::resource('produk-outlet', ProductWarehouseController::class);
        Route::resource('produk', ProductController::class)->except(['show']);
        Route::controller(ProductController::class)->group(function () {
            Route::post('products/product-data', 'productData');
            Route::get('products/gencode', 'generateCode');
            Route::get('products/search', 'search');
            Route::get('products/product_warehouse/{id}', 'productWarehouseData');
            Route::post('products/deletebyselection', 'deleteBySelection');

            Route::post('importproduct', 'importProduct')->name('product.import');
            Route::post('exportproduct', 'exportProduct')->name('product.export');
        });

        // Roles & permissions
        Route::resource('role', RoleController::class);
        Route::controller(RoleController::class)->group(function () {
            Route::get('role/permission/{id}', 'permission')->name('role.permission');
            Route::post('role/set_permission', 'setPermission')->name('role.setPermission');
        });

        // Business & units
        Route::resource('business', BusinessController::class);
        Route::resource('unit', UnitController::class);
        Route::controller(UnitController::class)->group(function () {
            Route::post('importunit', 'importUnit')->name('unit.import');
            Route::post('unit/deletebyselection', 'deleteBySelection');
            Route::get('unit/lims_unit_search', 'limsUnitSearch')->name('unit.search');
        });

        // Categories
        Route::resource('kategori', CategoryController::class);

        // Regional
        Route::resource('regional', RegionalController::class);

        // Warehouses (Outlets)
        Route::controller(WarehouseController::class)->group(function () {
            Route::post('importwarehouse', 'importWarehouse')->name('outlet.import');
            Route::post('warehouse/deletebyselection', 'deleteBySelection');
            Route::get('warehouse/lims_warehouse_search', 'limsWarehouseSearch')->name('warehouse.search');
            Route::get('warehouse/all', 'warehouseAll')->name('warehouse.all');
            Route::get('outlet/get-outlet-by-id/{id}', 'getOutletById')->name('outlet.getById');
            Route::put('outlet/renewal/{id}', 'renewal')->name('outlet.renewal');
        });
        Route::resource('outlet', WarehouseController::class)->except('show');

        // Order types
        Route::resource('tipe-pesanan', OrderTypeController::class);

        // Ingredients & stock
        Route::controller(IngredientController::class)->group(function () {
            Route::post('ingredient/import', 'import')->name('ingredient.import');
            Route::post('ingredient/deletebyselection', 'deleteBySelection');
            Route::post('ingredient/ingredient-data', 'ingredientData');
        });
        Route::resource('bahan-baku', IngredientController::class);
        Route::resource('stok', StockController::class);

        // Stock opname & cashier
        Route::resource('stock-opname', StockOpnameController::class);
        Route::resource('close-cashier', CloseCashierController::class);
        Route::put('stock-opname-detail/{id}', [StockOpnameController::class, 'updateDetail'])->name('updateDetailStockOpname');
        Route::resource('kategori_bahan_baku', KategoriBahanBakuController::class);

        // Stock purchases
        Route::resource('pembelian-stok', StockPurchaseController::class);

        // Shifts
        Route::controller(ShiftController::class)->group(function () {
            Route::post('shift/import', 'import')->name('shift.import');
            Route::post('shift/deletebyselection', 'deleteBySelection');
            Route::post('shift/shift-data', 'shiftData');
        });
        Route::resource('shift', ShiftController::class);

        // Reports
        Route::controller(ReportController::class)->group(function () {
            Route::prefix('report')->group(function () {
                Route::get('product_quantity_alert', 'productQuantityAlert')->name('report.qtyAlert');
                Route::get('daily-sale-objective', 'dailySaleObjective')->name('report.dailySaleObjective');
                Route::post('daily-sale-objective-data', 'dailySaleObjectiveData');
                Route::get('product-expiry', 'productExpiry')->name('report.productExpiry');
                Route::get('warehouse_stock', 'warehouseStock')->name('report.warehouseStock');
                Route::get('daily_sale/{year}/{month}', 'dailySale');
                // Daily Sales Reports with month/year filters
                Route::get('daily_sale_outlet', 'dailySaleOutlet')->name('admin.report.daily_sale_outlet');
                Route::get('daily_sale_outlet_pdf', 'dailySaleOutletPdf')->name('admin.report.daily_sale_outlet_pdf');
                Route::post('daily_sale/{year}/{month}', 'dailySaleByWarehouse')->name('report.dailySaleByWarehouse');
                Route::get('monthly_sale/{year}', 'monthlySale');
                Route::post('monthly_sale/{year}', 'monthlySaleByWarehouse')->name('report.monthlySaleByWarehouse');
                Route::get('daily_purchase/{year}/{month}', 'dailyPurchase');
                Route::post('daily_purchase/{year}/{month}', 'dailyPurchaseByWarehouse')->name('report.dailyPurchaseByWarehouse');
                Route::get('monthly_purchase/{year}', 'monthlyPurchase')->name('report.monthlyPurchase');
                Route::post('monthly_purchase/{year}', 'monthlyPurchaseByWarehouse')->name('report.monthlyPurchaseByWarehouse');
                Route::get('best_seller', 'bestSeller');
                Route::post('best_seller', 'bestSellerByWarehouse')->name('report.bestSellerByWarehouse');
                Route::post('profit_loss', 'profitLoss')->name('report.profitLoss');
                Route::get('product_report', 'productReport')->name('report.product');
                Route::get('difference_stock_report', 'differenceStockReport')->name('report.differenceStockReport');
                Route::get('get-warehouses-by-regional/{regional_id}', 'getWarehousesByRegional')->name('report.get_warehouses_by_regional');
                Route::get('remaining_stock_report', 'remainingStockReport')->name('report.remainingStockReport');
                Route::get('remaining_stock_report/print', 'remainingStockReportPrint')->name('report.remainingStockReportPrint');
                Route::get('list-transaksi', 'listTransaction')->name('report.listTransaction');
                Route::post('product_report_data', 'productReportData');
                Route::post('purchase', 'purchaseReport')->name('report.purchase');
                Route::post('sale_report', 'saleReport')->name('report.sale');
                Route::post('sale-report-chart', 'saleReportChart')->name('report.saleChart');
                Route::post('payment_report_by_date', 'paymentReportByDate')->name('report.paymentByDate');
                Route::post('warehouse_report', 'warehouseReport')->name('report.warehouse');
                Route::post('warehouse-sale-data', 'warehouseSaleData');
                Route::post('warehouse-purchase-data', 'warehousePurchaseData');
                Route::post('warehouse-expense-data', 'warehouseExpenseData');
                Route::post('warehouse-quotation-data', 'warehouseQuotationData');
                Route::post('warehouse-return-data', 'warehouseReturnData');
                Route::post('user_report', 'userReport')->name('report.user');
                Route::post('user-sale-data', 'userSaleData');
                Route::post('user-purchase-data', 'userPurchaseData');
                Route::post('user-expense-data', 'userExpenseData');
                Route::post('user-quotation-data', 'userQuotationData');
                Route::post('user-payment-data', 'userPaymentData');
                Route::post('user-transfer-data', 'userTransferData');
                Route::post('user-payroll-data', 'userPayrollData');
                Route::post('customer_report', 'customerReport')->name('report.customer');
                Route::post('customer-sale-data', 'customerSaleData');
                Route::post('customer-payment-data', 'customerPaymentData');
                Route::post('customer-quotation-data', 'customerQuotationData');
                Route::post('customer-return-data', 'customerReturnData');
                Route::post('customer-group', 'customerGroupReport')->name('report.customer_group');
                Route::post('customer-group-sale-data', 'customerGroupSaleData');
                Route::post('customer-group-payment-data', 'customerGroupPaymentData');
                Route::post('customer-group-quotation-data', 'customerGroupQuotationData');
                Route::post('customer-group-return-data', 'customerGroupReturnData');
                Route::post('supplier', 'supplierReport')->name('report.supplier');
                Route::post('supplier-purchase-data', 'supplierPurchaseData');
                Route::post('supplier-payment-data', 'supplierPaymentData');
                Route::post('supplier-return-data', 'supplierReturnData');
                Route::post('supplier-quotation-data', 'supplierQuotationData');
                Route::post('customer-due-report', 'customerDueReportByDate')->name('report.customerDueByDate');
                Route::post('customer-due-report-data', 'customerDueReportData');
                Route::post('supplier-due-report', 'supplierDueReportByDate')->name('report.supplierDueByDate');
                Route::post('supplier-due-report-data', 'supplierDueReportData');
            });
        });

        // Users
        Route::controller(UserController::class)->group(function () {
            Route::get('user/profile/{id}', 'profile')->name('user.profile');
            Route::put('user/update_profile/{id}', 'profileUpdate')->name('user.profileUpdate');
            Route::put('user/changepass/{id}', 'changePassword')->name('user.password');
            Route::get('user/genpass', 'generatePassword');
            Route::post('user/deletebyselection', 'deleteBySelection');
            Route::get('user/notification', 'notificationUsers')->name('user.notification');
            Route::get('user/all', 'allUsers')->name('user.all');
        });
        Route::resource('user', UserController::class);

        // Settings
        Route::controller(SettingController::class)->group(function () {
            Route::prefix('setting')->group(function () {
                Route::get('general_setting', 'generalSetting')->name('setting.general');
                Route::post('general_setting_store', 'generalSettingStore')->name('setting.generalStore');

                Route::get('reward-point-setting', 'rewardPointSetting')->name('setting.rewardPoint');
                Route::post('reward-point-setting_store', 'rewardPointSettingStore')->name('setting.rewardPointStore');

                Route::get('general_setting/change-theme/{theme}', 'changeTheme');
                Route::get('mail_setting', 'mailSetting')->name('setting.mail');
                Route::get('sms_setting', 'smsSetting')->name('setting.sms');
                Route::get('createsms', 'createSms')->name('setting.createSms');
                Route::post('sendsms', 'sendSms')->name('setting.sendSms');
                Route::get('hrm_setting', 'hrmSetting')->name('setting.hrm');
                Route::post('hrm_setting_store', 'hrmSettingStore')->name('setting.hrmStore');
                Route::post('mail_setting_store', 'mailSettingStore')->name('setting.mailStore');
                Route::post('sms_setting_store', 'smsSettingStore')->name('setting.smsStore');
                Route::get('pos_setting', 'posSetting')->name('setting.pos');
                Route::post('pos_setting_store', 'posSettingStore')->name('setting.posStore');
                Route::get('empty-database', 'emptyDatabase')->name('setting.emptyDatabase');
            });
            Route::get('backup', 'backup')->name('setting.backup');
        });

        // Expense categories
        Route::controller(ExpenseCategoryController::class)->group(function () {
            Route::get('expense_categories/gencode', 'generateCode');
            Route::post('expense_categories/import', 'import')->name('expense_category.import');
            Route::post('expense_categories/deletebyselection', 'deleteBySelection');
            Route::get('expense_categories/all', 'expenseCategoriesAll')->name('expense_category.all');;
        });
        Route::resource('nama-pengeluaran', ExpenseCategoryController::class);

        // Expenses
        Route::controller(ExpenseController::class)->group(function () {
            Route::post('expenses/expense-data', 'expenseData')->name('expenses.data');
            Route::post('expenses/deletebyselection', 'deleteBySelection');
        });
        Route::get('/expense/export/{warehouse_id}', [ExpenseController::class, 'export'])->name('expense.export');
        Route::resource('pengeluaran', ExpenseController::class);
        Route::get('/expenses/data', [ExpenseController::class, 'getExpenses'])->name('expenses.data');

        // Ojol
        Route::controller(OjolController::class)->name('ojol.')->group(function () {
            Route::get('ojol', 'index')->name('index');
            Route::get('ojol/create', 'create')->name('create');
            Route::post('ojol/create', 'store')->name('store');
            Route::get('ojol/edit/{ojol}', 'edit')->name('edit');
            Route::put('ojol/edit/{ojol}', 'update')->name('update');
            Route::delete('ojol/destroy/{ojol}', 'destroy')->name('destroy');
        });
        // Ojol Warehouse
        Route::controller(OjolWarehouseController::class)->name('ojol-warehouse.')->group(function () {
            Route::get('ojol_outlet', 'index')->name('index');
            Route::get('ojol_outlet/form/{ojol}', 'form')->name('form');
            Route::post('ojol_outlet/form/{ojol}', 'store')->name('store');
            Route::delete('ojol_outlet/destroy/{ojol}', 'destroy')->name('destroy');
        });

        // CloseCashier transaction details
        Route::get('close-cashier/transaction/{transaction}', [CloseCashierController::class, 'transactionDetails'])->name('close_cashier_transaction');

        // Business outlet stocks
        Route::controller(BusinessStockController::class)->name('business-stock.')->group(function () {
            Route::get('/business_stocks', 'index')->name('index');
        });

        // Custom category parent
        Route::controller(CustomCategoryController::class)->name('custom-category.')->group(function () {
            Route::get('/custom_categories', 'index')->name('index');
            Route::get('/custom_category/{category}', 'form')->name('form');
            Route::post('/custom_category/{category}', 'store')->name('store');
            Route::delete('/custom_category/{category}', 'destroy')->name('destroy');
        });

        // Warehouse max shifts count
        Route::get('/max_shifts', [WarehouseController::class, 'maxShiftPage'])->name('maxShiftPage');
        Route::put('/max_shifts', [WarehouseController::class, 'maxShiftUpdate'])->name('maxShiftUpdate');

        // Finance report
        Route::get('/finance-report', [FinanceReportController::class, 'financeReport'])->name('financeReport');
        Route::get('/get-warehouses-by-regional/{regional_id}', [App\Http\Controllers\Report\FinanceReportController::class, 'getWarehousesByRegional'])->name('getWarehousesByRegional');

        // Close cashier report regional filtering
        Route::get('close-cashier/get-warehouses-by-regional/{regional_id}', [CloseCashierController::class, 'getWarehousesByRegional'])->name('close_cashier.get_warehouses_by_regional');

        // Product omzet report
        Route::get('products-omzet-by-month', [ProductOmzetController::class, 'index'])->name('report.productsOmzetByMonth');
        Route::get('products-omzet-by-month-excel', [ProductOmzetController::class, 'productsOmzetByMonthExcel'])->name('report.productsOmzetByMonthExcel');

        // Outlet table management
        Route::resource('tables', TableController::class);
    });
});

Route::get('/menu/demo/{warehouse}', [TableTransactionController::class, 'demo']);
Route::get('/menu/demo-mobile/{warehouse}', [TableTransactionController::class, 'demoMobile']);
Route::get('/menu/info', function() {
    return view('backend.layout.menu-info');
});

Route::get('/table/{tableCode}', [TableTransactionController::class, 'qrMenuAccess']);
Route::get('/menu/{tableTransactionCode}', [TableTransactionController::class, 'getTableMenuPage'])->name('getTableMenuPage');
