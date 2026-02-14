<?php

use App\Livewire\Forms\LoginForm;
use Illuminate\Support\Facades\Session;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component {
    public LoginForm $form;

    /**
     * Handle an incoming authentication request.
     */
    public function login(): void
    {
        $this->validate();

        $this->form->authenticate();

        Session::regenerate();

        $this->redirectIntended(default: route('dashboard', absolute: false), navigate: true);

    }

}; ?>

<div>
    <x-auth-session-status class="mb-4" :status="session('status')" />
    <div>
        @if ($errors->any())
            <p class="text-sm font-bold text-center text-red-600">
                رقم الهاتف أو كلمة المرور غير صحيحة
            </p>
        @endif
        <form wire:submit="login" class="space-y-6">

            <!-- Phone Number -->
            <div class="space-y-2">
                <label for="phone" class="block mr-1 text-sm font-black text-gray-700">رقم التليفون</label>
                <div class="relative">
                    <input wire:model="form.phone" id="phone" type="tel" name="phone" required autofocus
                        autocomplete="username" placeholder="01xxxxxxxxx"
                        class="w-full py-4 pl-4 pr-12 text-lg font-bold text-right text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 dir-ltr">
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z" />
                        </svg>
                    </div>
                </div>

                @error('form.phone')
                    <p
                        class="flex items-center gap-1 p-2 mt-1 text-sm font-bold text-red-600 border border-red-100 rounded-lg bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Password -->
            <div class="space-y-2" x-data="{ show: false }">
                <label for="password" class="block mr-1 text-sm font-black text-gray-700">كلمة المرور</label>
                <div class="relative">
                    <input wire:model="form.password" dir="rtl" id="password" :type="show ? 'text' : 'password'"
                        name="password" required autocomplete="current-password" placeholder="••••••"
                        class="w-full py-4 pl-12 pr-12 text-lg font-bold text-gray-900 placeholder-gray-400 transition-all border-2 border-gray-200 bg-gray-50 rounded-2xl focus:ring-indigo-500 focus:border-indigo-500">

                    <!-- Lock Icon -->
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 text-gray-400 pointer-events-none">
                        <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round">
                            <rect x="3" y="11" width="18" height="11" rx="2" ry="2" />
                            <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                        </svg>
                    </div>

                    <!-- Toggle Visibility Button -->
                    <button type="button" @click="show = !show"
                        class="absolute inset-y-0 left-0 flex items-center pl-4 text-gray-400 transition-colors hover:text-indigo-600 focus:outline-none">
                        <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                            viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z" />
                            <circle cx="12" cy="12" r="3" />
                        </svg>
                        <svg x-show="show" style="display: none;" xmlns="http://www.w3.org/2000/svg" width="24"
                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"
                            stroke-linecap="round" stroke-linejoin="round">
                            <path
                                d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24" />
                            <line x1="1" y1="1" x2="23" y2="23" />
                        </svg>
                    </button>
                </div>

                @error('form.password')
                    <p
                        class="flex items-center gap-1 p-2 mt-1 text-sm font-bold text-red-600 border border-red-100 rounded-lg bg-red-50">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none"
                            stroke="currentColor" stroke-width="2.5">
                            <circle cx="12" cy="12" r="10" />
                            <line x1="12" y1="8" x2="12" y2="12" />
                            <line x1="12" y1="16" x2="12.01" y2="16" />
                        </svg>
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="block">
                <label for="remember" class="inline-flex items-center cursor-pointer group">
                    <div class="relative flex items-center">
                        <input wire:model="form.remember" id="remember" type="checkbox"
                            class="w-6 h-6 text-indigo-600 transition-all border-2 border-gray-300 rounded-lg cursor-pointer peer focus:ring-indigo-500 checked:bg-indigo-600 checked:border-indigo-600"
                            name="remember">
                        <svg class="absolute w-4 h-4 text-white transition-opacity -translate-x-1/2 -translate-y-1/2 opacity-0 pointer-events-none left-1/2 top-1/2 peer-checked:opacity-100"
                            xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                            stroke-width="4" stroke-linecap="round" stroke-linejoin="round">
                            <polyline points="20 6 9 17 4 12" />
                        </svg>
                    </div>
                    <span
                        class="text-sm font-bold text-gray-600 transition-colors ms-3 group-hover:text-indigo-600">تذكر
                        دخولي</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                class="w-full bg-indigo-600 text-white text-xl font-black py-4 rounded-2xl shadow-xl shadow-indigo-200 hover:bg-indigo-700 hover:shadow-2xl transition-all transform active:scale-[0.98] flex items-center justify-center gap-3">
                <span wire:loading.remove>دخول</span>
                <span wire:loading>جاري الدخول...</span>
                <svg wire:loading.remove xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24"
                    fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round">
                    <line x1="5" y1="12" x2="19" y2="12" />
                    <polyline points="12 5 19 12 12 19" />
                </svg>
            </button>
        </form>
    </div>
    <p class="mt-8 text-xs font-bold text-center text-gray-400">جميع الحقوق محفوظة Peter Eid &copy; {{ date('Y') }}</p>
</div>