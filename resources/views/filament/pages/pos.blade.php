<x-filament-panels::page>
    

<div class="flex items-center gap-2" x-data="{ showTooltip: false }">
    <span class="h-3 w-3 rounded-full {{ $printerStatus === 'online' ? 'bg-green-500' : 'bg-red-500' }}"
          @mouseover="showTooltip = true"
          @mouseleave="showTooltip = false">
    </span>

   
    
    <div x-show="showTooltip" x-cloak
         class="absolute bg-gray-800 text-white text-xs rounded py-1 px-2 -mt-8 ml-4">
        {{ $printerStatus === 'online' ? 'Printer is connected and ready' : 'Printer is disconnected' }}
    </div>
</div>

    @livewire('pos')

    
</x-filament-panels::page>
