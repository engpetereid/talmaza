<x-app-layout>
    @section('header', Auth::user()->role == 'admin' ? 'لوحة التحكم' : 'الرئيسية')

    @if(Auth::user()->role == 'admin')
        <livewire:admin-dashboard />
    @else
        <livewire:leader-dashboard />
    @endif

</x-app-layout>
