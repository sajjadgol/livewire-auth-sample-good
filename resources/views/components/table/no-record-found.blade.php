<x-table.row>
    <x-table.cell colspan="15">
        <div class="flex justify-center items-center space-x-2">
            <span class="font-medium text-cool-gray-400 text-xl">
                {{ empty(trim($slot)) ? __('component.No record found') : $slot }} 
            </span>
        </div>
    </x-table.cell>
</x-table.row>

