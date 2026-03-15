<?php

use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdvertsController;
use App\Http\Controllers\CookieController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DishesController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntroController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\OAuthController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\WalletController;
use Illuminate\Support\Facades\Route;

Route::middleware(['web'])->group(function () {
    Route::get('/admin_access', [AdminController::class, 'adminLogin'])
        ->name('admin.login');
    Route::post('/admin_access', [AdminController::class, 'handleAdminLogin'])
        ->name('admin.login.submit');
});

// Mollie webhook route - MOET buiten auth/csrf middleware staan
// Deze route wordt server-to-server aangeroepen door Mollie
Route::post('/mollie/webhook', [CustomerController::class, 'handleMollieWebhook'])
    ->name('mollie.webhook');

// Wallet payment webhooks voor transactiekosten en IBAN verificatie
Route::post('/mollie/webhook/wallet', [WalletController::class, 'handleMollieWalletWebhook'])
    ->name('mollie.webhook.wallet');

Route::middleware(['web', 'cookie.consent', 'website.status'])->group(function () {
    Route::get('/', [HomeController::class, 'index'])->name('home');
    Route::get('info', [HomeController::class, 'info'])->name('info');
    Route::get('contact', [HomeController::class, 'contact'])->name('contact');
    Route::post('contact-form', [HomeController::class, 'contactForm'])->name('contact.form');
    Route::get('success', [HomeController::class, 'contactSuccess'])->name('contact.success');
    Route::get('facts/customer', [HomeController::class, 'customerFacts'])->name('customer.facts');
    Route::get('facts/cook', [HomeController::class, 'cookFacts'])->name('cook.facts');
    Route::get('facts/cook-tips', [HomeController::class, 'cookTips'])->name('cook.tips');
    Route::get('terms-and-conditions', [HomeController::class, 'terms'])->name('terms.conditions');
    Route::get('privacy', [HomeController::class, 'privacy'])->name('privacy');
    Route::get('cookie', [HomeController::class, 'cookie'])->name('cookie');
    Route::get('search', [SearchController::class, 'searchByCoordinates'])->name('search.coordinates');
    Route::get('review/{orderUuid}/{clientUuid}', [ReviewController::class, 'getReviewView'])->name('review.order');
    Route::post('review/{orderUuid}', [ReviewController::class, 'submitReview'])->name('review.order.store');

    Route::get('/dac7/form/{uuid}/{token}', [AdminController::class, 'showDac7Form'])->name('dac7.form');
    Route::get('/dac7/email/{uuid}/{token}', [AdminController::class, 'showDac7EmailForm'])->name('dac7.email.form');
    Route::post('/dac7/email/submit/{uuid}/{token}', [AdminController::class, 'submitDac7EmailForm'])->name('dac7.email.submit');
    Route::post('/dac7/submit/{uuid}/{token}', [AdminController::class, 'submitDac7Form'])->name('dac7.submit');
    Route::get('/dac7/success', [AdminController::class, 'showDac7Success'])->name('dac7.success');

    Route::get('search/cooks', [SearchController::class, 'searchByCookName'])->name('search.cooks');
    Route::get('search/distance', [SearchController::class, 'searchCookByDistance'])->name('search.cooks.distance');
    Route::get('search/cooks/{uuid}/details', [SearchController::class, 'showCookDetails'])->name('search.cooks.detail');
    Route::get('search/cooks/{uuid}/details/reviews', [SearchController::class, 'showCookReviews'])->name('search.cooks.detail.review');
    Route::get('search/cooks/{cookUuid}/details/dish/{dishUuid}', [CustomerController::class, 'getDishDetailsViewByCook'])->name('search.cooks.detail.dish');
    Route::get('search/cooks/{cookUuid}/details/advert/{advertUuid}', [CustomerController::class, 'getAdvertDetailsViewByCook'])->name('search.cooks.detail.advert');
    Route::get('details/{uuid}', [CustomerController::class, 'getAdvertDetailsView'])->name('advert.details');
    Route::get('details/{uuid}/order', [CustomerController::class, 'getAdvertOrderView'])->name('advert.order');
    Route::post('details/{uuid}/order/submit', [CustomerController::class, 'submitCustomerOrder'])->name('advert.order.submit');

    Route::post('/dashboard/admin/dashboard/payouts/approve', [AdminController::class, 'aprovePayouts'])
        ->name('dashboard.admin.payouts.approve');

    Route::get('details/order/cancel', [CustomerController::class, 'cancelCustomOrder'])->name('advert.order.cancel');

    Route::get('details/order/complete', [CustomerController::class, 'completeCustomerOrder'])->name('advert.order.complete');
    Route::get('order/cancel', [OrderController::class, 'cancelCustomerOrder'])->name('customer.cancel.order');
    Route::post('order/cancel/{uuid}/{key}', [OrderController::class, 'submitCancelCustomerOrder'])->name('submit.customer.cancel.order');
    Route::get('/order/cancel/{uuid}', [OrderController::class, 'cancelCustomerOrder'])
        ->name('order.cancel.customer');

    Route::get('/mollie/authorize', [OAuthController::class, 'createProfile'])->name('mollie.authorize');
    Route::get('/mollie/callback', [OAuthController::class, 'callback'])->name('mollie.callback');

    Route::post('customer/cook/subscribe/{uuid}', [CustomerController::class, 'submitMailingList'])->name('customer.cook.subscribe');
    Route::get('/cooks/{cookUuid}/unsubscribe/{clientUuid}', [CustomerController::class, 'unsubscribeFromMailingList'])->name('mailinglist.unsubscribe');

    Route::post('/accept-cookies', [CookieController::class, 'saveCookieRights'])->name('accepted.cookies.response');

    Route::group(['prefix' => 'register', 'as' => 'register.'], function ($route) {
        $route->get('info', [RegisterController::class, 'registerInfo'])->name('info');
        $route->get('now', [RegisterController::class, 'registerNow'])->name('now');
        $route->post('submit', [RegisterController::class, 'register'])->name('submit')->middleware('limit.account.creation');
        $route->get('submitted', [RegisterController::class, 'registrationSubmitted'])->name('submitted');
        $route->post('verification', [RegisterController::class, 'resendVerificationEmail'])->name('verification');
    });

    Route::group(['prefix' => 'login', 'as' => 'login.'], function ($route) {
        $route->get('/', [LoginController::class, 'login'])->name('home');
        $route->post('submit', [LoginController::class, 'loginAsUser'])->name('submit');
        $route->get('forgot', [LoginController::class, 'forgotPassword'])->name('forgot');
        $route->group(['prefix' => 'forgot', 'as' => 'forgot.'], function ($route) {
            $route->post('submit', [LoginController::class, 'sendForgotPasswordEmail'])->name('submit');
            $route->get('reset', [LoginController::class, 'passwordReset'])->name('reset');
            $route->post('reset/submit', [LoginController::class, 'submitPasswordReset'])->name('reset.submit');
            $route->get('notification', [LoginController::class, 'notificationPoint'])->name('notification');
        });
    });

    Route::group([
        'prefix' => 'register',
        'as' => 'verification.',
    ], function ($route) {
        $route->get('verify', [RegisterController::class, 'verify'])->name('verify');
        $route->get('already-verified', [RegisterController::class, 'alreadyVerified'])->name('already.verified');
        $route->get('changed-verified', [RegisterController::class, 'changedVerified'])->name('changed-verified');

        $route->get('needed', [RegisterController::class, 'needVerification'])->name('notice');
        $route->get('information', [DashboardController::class, 'firstTimeUser'])->name('first')->middleware('auth', 'user.blocked', 'verified', 'user.status', 'prevent.completed.user');
        $route->post('information/submit', [DashboardController::class, 'postProfileAndCookInformation'])->name('information.submit')->middleware('auth', 'user.blocked', 'verified', 'user.status', 'prevent.completed.user');
        $route->get('location', [DashboardController::class, 'firstTimeUserLocation'])->name('location')->middleware('auth', 'user.blocked', 'verified', 'user.status', 'prevent.completed.user');
        $route->post('location/submit', [DashboardController::class, 'postLocation'])->name('location.submit')->middleware('auth', 'user.blocked', 'verified', 'user.status', 'prevent.completed.user');
        $route->get('banking', [DashboardController::class, 'verifyIban'])->name('banking')->middleware('auth', 'user.blocked', 'verified', 'user.status');
    });

    Route::get('/img/pasta.jpg', function () {
        $path = public_path('img/pasta.jpg');

        return response()->file($path);
    });
    Route::get('/img/kok.png', function () {
        $path = public_path('img/kok.png');

        return response()->file($path);
    });

    Route::get('/test-preparation-mail', function () {
        $advert = \App\Models\Advert::first();
        if (! $advert) {
            return 'Geen advertentie gevonden om te testen';
        }

        if ($advert->order->count() == 0) {
            return 'Deze advertentie heeft geen bestellingen om te testen';
        }

        $order = $advert->order->first();
        $original_state = $order->payment_state;
        $order->payment_state = \App\Models\Order::IN_PROCESS;
        $order->save();

        $mail = new \App\Mail\AdvertPreparationMail($advert, $advert->cook);

        $order->payment_state = $original_state;
        $order->save();

        return $mail->render();
    });

    Route::group([
        'prefix' => 'dashboard',
        'as' => 'dashboard.',
        'middleware' => [
            'auth',
            'user.blocked',
            'verified',
            'user.status',
        ],
    ], function ($route) {
        $route->group(['middleware' => 'role:cook'], function ($route) {
            $route->group([
                'prefix' => 'adverts',
                'as' => 'adverts.',
                'middleware' => 'role:cook',
                'controller' => AdvertsController::class,
            ], function ($route) {
                $route->get('active', 'activeAdverts')->name('active.home');
                $route->get('past', 'pastAdverts')->name('past.home');
                $route->get('create', 'createAdvert')->name('create');

                $route->get('create/dish/{uuid}', 'createAdvertWithDish')->name('createWithDish');

                $route->get('show/{uuid}', 'showAdvert')->name('show');
                $route->get('update/{uuid}', 'editAdvert')->name('update');
                $route->patch('update/{uuid}', 'updateAdvert')->name('update.store');
                $route->post('update/{uuid}/confirm', 'submitUpdateAdvert')->name('update.confirm');
                $route->get('publish/{uuid}', 'publishAdvert')->middleware('cook.publish')->name('publish');
                $route->post('store', 'storeAdvert')->name('store');
                $route->get('cancel/{uuid}', 'cancelAdvert')->name('cancel');
                $route->post('cancel/{uuid}', 'submitCancelAdvert')->name('cancel.store');
            });

            $route->group([
                'prefix' => 'dishes',
                'as' => 'dishes.',
                'middleware' => 'role:cook',
                'controller' => DishesController::class,
            ], function ($route) {
                $route->get('/', 'dishes')->name('new');
                $route->get('old', 'oldDishes')->name('old');
                $route->get('create', 'createNewDish')->name('create');
                $route->get('duplicate/{uuid}', 'duplicateDish')->name('duplicate');
                $route->post('save', 'storeNewDish')->name('store');
                $route->get('edit/{uuid}', 'editDish')->name('edit');
                $route->patch('update/{uuid}', 'updateDish')->name('update');
                $route->post('update/{uuid}/confirm', 'comfirmUpdateDish')->name('update.confirm');
                $route->get('show/{uuid}', 'showSingleDish')->name('show');
                $route->delete('destroy/{uuid}', 'destroyDish')->middleware(['middleware' => 'role:admin'])->name('destroy');
                $route->delete('delete/{uuid}/confirm', 'confirmDestroyDish')->middleware(['middleware' => 'role:admin'])->name('delete.confirm');
            });

            $route->group([
                'prefix' => 'orders',
                'as' => 'orders.',
                'controller' => OrderController::class,
            ], function ($route) {
                $route->get('/', 'orders')->name('home');
                $route->get('show/{uuid}', 'showOrder')->name('show');
                $route->get('cancel/{uuid}', 'cancel')->name('cancel');
                $route->post('cancel/{uuid}', 'cancelOrder')->name('cancel.store');

                $route->get('document/{uuid}/download', 'downloadOrderDocument')->name('document.download');
                $route->get('document/{uuid}/send', 'sendOrderDocument')->name('document.send');
            });

            $route->group([
                'prefix' => 'settings',
                'as' => 'settings.',
                'controller' => SettingController::class,
            ], function ($route) {
                $route->get('/', 'settings')->name('home');
                $route->post('profile/image', 'addProfileImage')->name('profile.image');
                $route->delete('profile/image', 'removeProfileImage')->name('profile.image.delete');
                $route->post('profile/description', 'updateProfileDescription')->name('profile.description.post');
                $route->get('profile/remove', 'removeCompleteAccount')->name('profile.remove');
                $route->delete('profile/delete', 'deleteUserAccount')->name('profile.delete');
                $route->get('profile/image/{uuid}', 'getProfileImage')->name('profile.image.get');

                $route->get('first/cookie', 'createFirstTimeData')->name('first.cookie');
                $route->get('details', 'showUserDetails')->name('details.home');
                $route->post('details', 'updateDetails')->name('details.update');
                $route->get('details/location', 'updateLocation')->name('update.location');
                $route->post('details/location/update', 'submitLocationUpdate')->name('update.location.submit');

                $route->get('toggle-edit', 'toggleEditMode')->name('toggle.edit');

                $route->get('reports', 'getCurrentSettings')->name('reports.home');
                $route->post('reports', 'createOrUpdateReports')->name('reports.update');

                $route->get('privacy', 'getPrivacySettings')->name('privacy.home');
                $route->post('privacy', 'updatePrivacySettings')->name('privacy.update');

                $route->get('password', 'showPasswordChange')->name('password.change');
                $route->post('password', 'updatePassword')->name('password.update');
            });

            $route->group([
                'prefix' => 'wallet',
                'as' => 'wallet.',
                'controller' => WalletController::class,
            ], function ($route) {
                $route->get('home', 'overview')->name('home');
                $route->post('payout', 'payoutToCook')->name('payout');
                $route->get('iban', 'iban')->name('iban');
                $route->post('iban/add', 'addIban')->name('iban.add');
                $route->post('iban/id', 'addIdCardToIban')->name('iban.id');
                $route->get('invoice/{payment}', 'downloadInvoice')->name('download.invoice');
                $route->get('iban/confirm', 'confirmIbanVerification')->name('iban.confirm');
                $route->get('iban/cancel', 'cancelIbanVerification')->name('iban.cancel');
                $route->get('transaction/payment', 'payTransactionCosts')->name('pay.transaction');
                $route->get('transaction/confirm', 'payTransactionCostsConfirm')->name('pay.transaction.confirm');
                $route->get('transaction/cancel', 'payTransactionCostsCancel')->name('pay.transaction.cancel');
            });
            $route->get('/mark-intro-as-completed', [IntroController::class, 'markAsCompleted'])->name('mark-intro-as-completed');
        });

        $route->group(['middleware' => 'role:admin'], function ($route) {
            $route->group([
                'prefix' => 'admin',
                'middleware' => ['auth', 'role:admin'],
                'as' => 'admin.',
                'controller' => AdminController::class,
            ], function ($route) {
                $route->get('accounts', 'getAccounts')->name('accounts');
                $route->get('accounts/{uuid}', 'showSingleAccount')->name('accounts.single');
                $route->patch('accounts/{uuid}/kvk', 'updateKvkDetails')->name('accounts.kvk.update');
                $route->get('accounts/login/{uuid}', 'loginAsUser')->name('accounts.login');
                $route->get('accounts/block/{uuid}', 'blockUser')->name('accounts.block');
                $route->get('accounts/unblock/{uuid}', 'unblockUser')->name('accounts.unblock');
                $route->get('accounts/delete/{uuid}', 'deleteUser')->name('accounts.delete');
                $route->get('accounts/restore/{uuid}', 'restoreUser')->name('accounts.restore');
                $route->get('dishes', 'getDishes')->name('dishes');
                $route->get('dishes/{uuid}', 'showSingleDish')->name('dishes.single');
                $route->get('dishes/{uuid}/edit', 'editSingleDish')->name('dishes.edit');
                $route->patch('dishes/{uuid}/update', 'updateDish')->name('dishes.update');
                $route->get('dac7', 'getUsersDac7')->name('dac7');
                $route->patch('dac7/{uuid}', 'updateDac7Information')->name('dac7.update');
                $route->post('dac7/{uuid}/reset', 'resetDac7Information')->name('dac7.reset');
                $route->get('dac7/{uuid}/download/{type}', 'downloadDac7Id')->name('dac7.download.id');
                $route->get('banking', 'getUsersBanking')->name('accounts.banking');

                $route->get('adverts', 'getAdverts')->name('adverts');
                $route->get('adverts/{uuid}', 'showSingleAdvert')->name('adverts.single');
                $route->get('adverts/{uuid}/update', 'updateAdvert')->name('adverts.update');

                $route->get('reviews', 'getReviews')->name('reviews');
                $route->get('reviews/{uuid}', 'showSingleReview')->name('reviews.single');
                $route->get('reviews/{uuid}/delete', 'deleteReview')->name('reviews.delete');

                $route->get('dashboard/accounts', 'getDashboard')->name('dashboard.accounts');
                $route->get('dashboard/dishes', 'getDashboardDishes')->name('dashboard.dishes');
                $route->get('dashboard/orders', 'getDashboardOrders')->name('dashboard.orders');
                $route->get('dashboard/revenue', 'getDashboardRevenue')->name('dashboard.revenue');

                $route->get('dashboard/payouts', 'getPayouts')->name('payouts');
                $route->post('dashboard/payouts/aprove', 'aprovePayouts')->name('payouts.aprove');

                $route->post('reveal-sensitive-data', 'revealSensitiveData')->name('reveal.sensitive.data');
                $route->get('audit-logs', 'getAuditLogs')->name('audit-logs');

                $route->get('dashboard/website/status', 'websiteStatus')->name('website.status');
                $route->post('dashboard/website/status/online', 'updateWebsiteStatusOnline')->name('update.website.status.online');
                $route->post('dashboard/website/status/offline', 'updateWebsiteStatusOffline')->name('update.website.status.offline');

                $route->get('settings', 'getSettings')->name('settings');
            });
        });
    });
    Route::get('logout', [LoginController::class, 'logout'])->name('logout');
});
