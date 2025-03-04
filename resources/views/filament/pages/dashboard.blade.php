<x-filament-panels::page>
    <x-filament-widgets::widgets
        :columns="[
            'default' => 3,
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            '2xl' => 3,
        ]"
        :widgets="$this->getHeaderWidgets()"
        class="filament-dashboard-widgets-container"
    />

    <x-filament-widgets::widgets
        :columns="[
            'default' => 3,
            'sm' => 1,
            'md' => 2,
            'lg' => 3,
            '2xl' => 3,
        ]"
        :widgets="$this->getWidgets()"
        class="filament-dashboard-widgets-container"
    />
</x-filament-panels::page> 