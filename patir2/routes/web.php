<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Client\AuthController;
use App\Http\Controllers\Client\AddressController;
use App\Http\Controllers\Client\CartController;
use App\Http\Controllers\Client\ConsultationController;
use App\Http\Controllers\Client\CustomerServiceController;
use App\Http\Controllers\Client\HistoryController;
use App\Http\Controllers\Client\OrderController;
use App\Http\Controllers\Client\ProfileController;
use App\Http\Controllers\Client\PrescriptionController;
use App\Http\Controllers\Client\ReviewController;
use App\Http\Controllers\Doctor\ConsultationController as DoctorConsultationController;
use App\Http\Controllers\Doctor\ProfileController as DoctorProfileController;
use App\Http\Controllers\Admin\ProductController as AdminProductController;
use App\Http\Controllers\StoreController;
use Illuminate\Http\Request;

Route::redirect('/', '/login');

Route::get('/beranda', function (Request $request) {
    if ($request->user()?->role === 'admin') {
        return redirect()->route('admin.toko');
    }
    if (in_array($request->user()?->role, ['doctor', 'dokter'], true)) {
        return redirect()->route('doctor.consultations.index');
    }

    return view('beranda');
})->middleware(['auth', 'admin.interface'])->name('beranda');
Route::get('/toko', [StoreController::class, 'index'])->middleware(['auth', 'admin.interface'])->name('toko');
Route::get('/informasi-obat/{obat}', [StoreController::class, 'showMedicine'])->middleware(['auth', 'admin.interface'])->name('informasi-obat.show');

Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/toko', [AdminProductController::class, 'index'])->name('toko');
    Route::get('/obat/tambah', [AdminProductController::class, 'create'])->name('products.create');
    Route::post('/obat', [AdminProductController::class, 'store'])->name('products.store');
    Route::get('/obat/{product}/edit', [AdminProductController::class, 'edit'])->name('products.edit');
    Route::patch('/obat/{product}', [AdminProductController::class, 'update'])->name('products.update');
    Route::patch('/obat/{product}/stok', [AdminProductController::class, 'updateStock'])->name('products.stock');
    Route::delete('/obat/{product}', [AdminProductController::class, 'destroy'])->name('products.destroy');
});

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login', [AuthController::class, 'store'])->name('login.store');
    Route::get('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'registerClient'])->name('register.store');
});

Route::middleware(['auth', 'admin.interface'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/profil', function (Request $request) {
        if (in_array($request->user()->role, ['doctor', 'dokter'], true)) {
            return redirect()->route('doctor.profile');
        }

        return app(ProfileController::class)->show($request);
    })->name('profil');
    Route::patch('/profil', [ProfileController::class, 'update'])->name('profil.update');
    Route::get('/konsultasi', fn (Request $request) => match ($request->user()->role) {
        'admin' => redirect()->route('admin.toko'),
        'doctor', 'dokter' => redirect()->route('doctor.consultations.index'),
        default => view('konsultasi'),
    })->name('konsultasi');
    Route::get('/riwayat', [HistoryController::class, 'index'])->name('riwayat');
    Route::get('/riwayat/{order}', [HistoryController::class, 'showOrder'])->whereNumber('order')->name('riwayat.detail');
    Route::view('/customer-service', 'customer-service')->name('customer-service');
    Route::view('/customer-service/chat', 'customer-service-chat')->name('customer-service.chat');
    Route::get('/konsultasi/chat/{dokter}', function (string $dokter) {
        $doctors = [
            'jon-d-ojan' => ['name' => 'Dr. Jon D Ojan', 'image' => 'dr-jon.png'],
            'syarif' => ['name' => 'Ir. Dr. Syarif', 'image' => 'dr-syarif.png'],
            'fatir-d-h' => ['name' => 'Dr. Fatir D H', 'image' => 'dr-fatir.png'],
        ];

        abort_unless(isset($doctors[$dokter]), 404);

        return view('konsultasi.chat', ['doctor' => $doctors[$dokter]]);
    })->name('konsultasi.chat');
    Route::view('/checkout', 'checkout')->name('checkout');
    Route::view('/checkout/konfirmasi', 'checkout.konfirmasi')->name('checkout.confirmation');
    Route::get('/checkout/alamat', [AddressController::class, 'page'])->name('checkout.address');

    Route::prefix('client')->name('client.')->group(function () {
        Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
        Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
        Route::patch('/cart/{cartItem}', [CartController::class, 'update'])->name('cart.update');
        Route::delete('/cart/{cartItem}', [CartController::class, 'destroy'])->name('cart.destroy');
        Route::get('/prescriptions', [PrescriptionController::class, 'index'])->name('prescriptions.index');
        Route::post('/prescriptions', [PrescriptionController::class, 'store'])->name('prescriptions.store');
        Route::get('/prescriptions/{prescription}/file', [PrescriptionController::class, 'download'])->name('prescriptions.download');
        Route::delete('/prescriptions/{prescription}', [PrescriptionController::class, 'destroy'])->name('prescriptions.destroy');
        Route::get('/profile', [ProfileController::class, 'data'])->name('profile.show');
        Route::get('/addresses', [AddressController::class, 'index'])->name('addresses.index');
        Route::post('/addresses', [AddressController::class, 'store'])->name('addresses.store');
        Route::patch('/addresses/{address}', [AddressController::class, 'update'])->name('addresses.update');
        Route::patch('/addresses/{address}/select', [AddressController::class, 'select'])->name('addresses.select');
        Route::delete('/addresses/{address}', [AddressController::class, 'destroy'])->name('addresses.destroy');
        Route::get('/orders', [OrderController::class, 'index'])->name('orders.index');
        Route::post('/orders', [OrderController::class, 'store'])->name('orders.store');
        Route::get('/orders/{order}', [OrderController::class, 'show'])->name('orders.show');
        Route::patch('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
        Route::get('/consultations', [ConsultationController::class, 'index'])->name('consultations.index');
        Route::post('/consultations', [ConsultationController::class, 'store'])->name('consultations.store');
        Route::get('/consultations/{consultation}', [ConsultationController::class, 'show'])->name('consultations.show');
        Route::get('/consultation-messages/{message}/attachment', [ConsultationController::class, 'attachment'])->name('consultations.messages.attachment');
        Route::post('/consultations/{consultation}/messages', [ConsultationController::class, 'sendMessage'])->name('consultations.messages.store');
        Route::delete('/consultations/{consultation}', [ConsultationController::class, 'destroy'])->name('consultations.destroy');
        Route::get('/reviews', [ReviewController::class, 'index'])->name('reviews.index');
        Route::post('/reviews', [ReviewController::class, 'store'])->name('reviews.store');
        Route::patch('/reviews/{review}', [ReviewController::class, 'update'])->name('reviews.update');
        Route::delete('/reviews/{review}', [ReviewController::class, 'destroy'])->name('reviews.destroy');
        Route::get('/customer-service/messages', [CustomerServiceController::class, 'index'])->name('customer-service.index');
        Route::post('/customer-service/messages', [CustomerServiceController::class, 'store'])->name('customer-service.store');
        Route::delete('/customer-service/messages/{message}', [CustomerServiceController::class, 'destroy'])->name('customer-service.destroy');
    });
});

Route::prefix('dokter')->name('doctor.')->middleware(['auth', 'role:doctor'])->group(function () {
    Route::get('/profil', [DoctorProfileController::class, 'show'])->name('profile');
    Route::patch('/profil', [DoctorProfileController::class, 'update'])->name('profile.update');
    Route::get('/konsultasi', [DoctorConsultationController::class, 'index'])->name('consultations.index');
    Route::get('/konsultasi/{consultation}', [DoctorConsultationController::class, 'show'])->name('consultations.show');
    Route::post('/konsultasi/{consultation}/pesan', [DoctorConsultationController::class, 'reply'])->name('consultations.reply');
    Route::get('/pesan/{message}/lampiran', [DoctorConsultationController::class, 'attachment'])->name('consultations.attachment');
});
