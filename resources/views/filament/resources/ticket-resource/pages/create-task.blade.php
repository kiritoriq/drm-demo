<x-filament::page>
    <form wire:submit.prevent="createTask" class="mt-8 space-y-6 md:mt-12">
        {{ $this->taskForm }}
    </form>
</x-filament::page>
