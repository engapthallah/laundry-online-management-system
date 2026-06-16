<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicServiceController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/services', [PublicServiceController::class, 'index'])->name('services.index');
Route::get('/about', function () {
    return view('public.about');
})->name('about');

Route::get('/reviews', [\App\Http\Controllers\PublicReviewController::class, 'index'])->name('reviews.public');
Route::get('/contact', [\App\Http\Controllers\PublicSupportController::class, 'create'])->name('contact.create');
Route::post('/contact', [\App\Http\Controllers\PublicSupportController::class, 'store'])->name('contact.store');

// Authenticated route that redirects based on role
Route::get('/dashboard', function () {
    $user = auth()->user();
    
    if ($user->isAdmin()) {
        return redirect()->route('admin.dashboard');
    }
    if ($user->isStaff()) {
        return redirect()->route('staff.dashboard');
    }
    if ($user->isDelivery()) {
        return redirect()->route('delivery.dashboard');
    }
    if ($user->isCustomer()) {
        return redirect()->route('customer.dashboard');
    }
    
    return redirect()->route('home');
})->middleware(['auth'])->name('dashboard');

// Admin routes
Route::prefix('admin')->middleware(['auth', 'admin'])->name('admin.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Admin\DashboardController::class, 'index'])->name('dashboard');
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class);
    Route::patch('users/{user}/toggle-status', [\App\Http\Controllers\Admin\UserController::class, 'toggleStatus'])->name('users.toggleStatus');
    Route::resource('services', \App\Http\Controllers\Admin\ServiceController::class);
    Route::patch('services/{service}/toggle-status', [\App\Http\Controllers\Admin\ServiceController::class, 'toggleStatus'])->name('services.toggleStatus');
    Route::resource('orders', \App\Http\Controllers\Admin\OrderController::class)->only(['index','show']);
    Route::patch('orders/{order}/status', [\App\Http\Controllers\Admin\OrderController::class, 'updateStatus'])->name('orders.updateStatus');
    Route::patch('orders/{order}/assign-staff', [\App\Http\Controllers\Admin\OrderController::class, 'assignStaff'])->name('orders.assignStaff');
    Route::resource('delivery', \App\Http\Controllers\Admin\DeliveryController::class);
    Route::patch('delivery/{delivery}/status', [\App\Http\Controllers\Admin\DeliveryController::class, 'updateStatus'])->name('delivery.updateStatus');
    Route::get('support/export', [\App\Http\Controllers\Admin\SupportController::class, 'export'])->name('support.export');
    Route::get('support', [\App\Http\Controllers\Admin\SupportController::class, 'index'])->name('support.index');
    Route::get('support/{support}', [\App\Http\Controllers\Admin\SupportController::class, 'show'])->name('support.show');
    Route::post('support/{support}/reply', [\App\Http\Controllers\Admin\SupportController::class, 'reply'])->name('support.reply');
    Route::patch('support/{support}/status', [\App\Http\Controllers\Admin\SupportController::class, 'updateStatus'])->name('support.updateStatus');
    
    // Admin Reviews
    Route::get('reviews', [\App\Http\Controllers\Admin\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'show'])->name('reviews.show');
    Route::delete('reviews/{review}', [\App\Http\Controllers\Admin\ReviewController::class, 'destroy'])->name('reviews.destroy');

    // Admin Payments
    Route::get('payments/export', [\App\Http\Controllers\Admin\PaymentController::class, 'exportCsv'])->name('payments.export');
    Route::get('payments', [\App\Http\Controllers\Admin\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [\App\Http\Controllers\Admin\PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/complete', [\App\Http\Controllers\Admin\PaymentController::class, 'markComplete'])->name('payments.complete');
    Route::post('payments/{payment}/refund', [\App\Http\Controllers\Admin\PaymentController::class, 'refund'])->name('payments.refund');
    Route::post('payments/{payment}/fail', [\App\Http\Controllers\Admin\PaymentController::class, 'markFailed'])->name('payments.fail');
    Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Admin\PaymentController::class, 'receipt'])->name('payments.receipt');

    // Admin Notifications
    // Admin Notifications
    Route::get('/notifications', [\App\Http\Controllers\Admin\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Admin\NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Admin\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    // Admin Analytics
    Route::get('/analytics', [\App\Http\Controllers\Admin\AnalyticsController::class, 'index'])->name('analytics.index');
    Route::get('/analytics/export/csv', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportCsv'])->name('analytics.export.csv');
    Route::get('/analytics/export/pdf', [\App\Http\Controllers\Admin\AnalyticsController::class, 'exportPdf'])->name('analytics.export.pdf'); // redirect to printable
    Route::get('/analytics/print', [\App\Http\Controllers\Admin\AnalyticsController::class, 'printable'])->name('analytics.printable');
    Route::get('/analytics/data/revenue', [\App\Http\Controllers\Admin\AnalyticsController::class, 'revenueData'])->name('analytics.revenue');
    Route::get('/analytics/data/orders', [\App\Http\Controllers\Admin\AnalyticsController::class, 'ordersData'])->name('analytics.orders');
});

// Staff routes
Route::prefix('staff')->middleware(['auth', 'staff'])->name('staff.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [\App\Http\Controllers\Staff\DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::get('/orders', [\App\Http\Controllers\Staff\OrderController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [\App\Http\Controllers\Staff\OrderController::class, 'show'])->name('orders.show');
    Route::patch('/orders/{order}/status', [\App\Http\Controllers\Staff\OrderController::class, 'updateStatus'])->name('orders.updateStatus');

    // Notifications
    Route::get('/notifications', [\App\Http\Controllers\Staff\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Staff\NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Staff\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Staff\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Staff\ProfileController::class, 'update'])->name('profile.update');
});

// Delivery route group
Route::middleware(['auth', 'delivery'])
    ->prefix('delivery')
    ->name('delivery.')
    ->group(function () {
        // Dashboard
        Route::get('/dashboard', [App\Http\Controllers\Delivery\DashboardController::class, 'index'])->name('dashboard');

        // Deliveries
        Route::get('/deliveries', [App\Http\Controllers\Delivery\DeliveryController::class, 'index'])->name('deliveries.index');
        Route::get('/deliveries/{assignment}', [App\Http\Controllers\Delivery\DeliveryController::class, 'show'])->name('deliveries.show');
        Route::patch('/deliveries/{assignment}/status', [App\Http\Controllers\Delivery\DeliveryController::class, 'updateStatus'])->name('deliveries.updateStatus');

        // Notifications
        Route::get('/notifications', [\App\Http\Controllers\Delivery\NotificationController::class, 'index'])->name('notifications.index');
        Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Delivery\NotificationController::class, 'markRead'])->name('notifications.markRead');
        Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Delivery\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');

        // Profile
        Route::get('/profile', [App\Http\Controllers\Delivery\ProfileController::class, 'edit'])->name('profile.edit');
        Route::patch('/profile', [App\Http\Controllers\Delivery\ProfileController::class, 'update'])->name('profile.update');
    });

// Customer routes
Route::prefix('customer')->middleware(['auth', 'customer'])->name('customer.')->group(function () {
    Route::get('/dashboard', [\App\Http\Controllers\Customer\DashboardController::class, 'index'])->name('dashboard');

    // Orders
    Route::resource('orders', \App\Http\Controllers\Customer\OrderController::class)->only(['index', 'create', 'store', 'show']);
    Route::post('orders/{order}/cancel', [\App\Http\Controllers\Customer\OrderController::class, 'cancel'])->name('orders.cancel');

    // Payments
    Route::get('payments', [\App\Http\Controllers\Customer\PaymentController::class, 'index'])->name('payments.index');
    Route::get('payments/{payment}', [\App\Http\Controllers\Customer\PaymentController::class, 'show'])->name('payments.show');
    Route::post('payments/{payment}/confirm', [\App\Http\Controllers\Customer\PaymentController::class, 'confirm'])->name('payments.confirm');
    Route::post('payments/{payment}/retry', [\App\Http\Controllers\Customer\PaymentController::class, 'retry'])->name('payments.retry');
    Route::get('payments/{payment}/receipt', [\App\Http\Controllers\Customer\PaymentController::class, 'receipt'])->name('payments.receipt');

    // Reviews
    Route::get('reviews', [\App\Http\Controllers\Customer\ReviewController::class, 'index'])->name('reviews.index');
    Route::get('reviews/create/{order}', [\App\Http\Controllers\Customer\ReviewController::class, 'create'])->name('reviews.create');
    Route::post('reviews', [\App\Http\Controllers\Customer\ReviewController::class, 'store'])->name('reviews.store');

    // Support
    Route::resource('support', \App\Http\Controllers\Customer\SupportController::class)->only(['index', 'create', 'store', 'show']);

    // Profile
    Route::get('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [\App\Http\Controllers\Customer\ProfileController::class, 'update'])->name('profile.update');

    // Customer Notifications
    Route::get('/notifications', [\App\Http\Controllers\Customer\NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{notification}/read', [\App\Http\Controllers\Customer\NotificationController::class, 'markRead'])->name('notifications.markRead');
    Route::post('/notifications/mark-all-read', [\App\Http\Controllers\Customer\NotificationController::class, 'markAllRead'])->name('notifications.markAllRead');
});

// Shared Profile routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
