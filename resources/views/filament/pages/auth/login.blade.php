
<x-filament-panels::page.simple>
    @if (session('verified'))
        <div style="padding: 1rem; margin-bottom: 1rem; border-radius: 0.5rem; background-color: #dcfce7; color: #166534;">
            Email Anda telah berhasil diverifikasi! Silakan login.
        </div>
    @endif

    <x-filament-panels::form wire:submit="authenticate">
        {{ $this->form }}

        <x-filament-panels::form.actions
            :actions="$this->getFormActions()"
            :full-width="$this->hasFullWidthFormActions()"
        />
    </x-filament-panels::form>
</x-filament-panels::page.simple>