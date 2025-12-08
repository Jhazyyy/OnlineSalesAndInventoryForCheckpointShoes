@props([
    'route' => null,
    'href' => null,
    'routePattern' => null,
    'icon' => null,
    'title' => '',
    'isDropdown' => false,
    'showDropdown' => false,
    'size' => 'normal', // 'normal' or 'small'
    'activeClass' => 'bg-blue-50 dark:bg-blue-900 text-blue-700 dark:text-blue-300 border-r-2 border-blue-500',
    'baseClass' => null
])

@php
    // Determine if this navigation item is active
    $isActive = false;
    
    if ($routePattern) {
        // Handle multiple patterns separated by pipe (|)
        if (strpos($routePattern, '|') !== false) {
            $patterns = explode('|', $routePattern);
            $isActive = request()->routeIs($patterns);
        } else {
            $isActive = request()->routeIs($routePattern);
        }
    } elseif ($route) {
        $isActive = request()->routeIs($route);
    } elseif ($href) {
        $isActive = request()->url() === $href;
    }
    
    // Set default base classes based on size
    if (!$baseClass) {
        $baseClass = $size === 'small' 
            ? 'flex items-center px-4 py-2 text-sm text-gray-600 dark:text-gray-400 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200'
            : 'flex items-center px-4 py-2 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700 transition-colors duration-200';
    }
    
    // Build final classes
    $classes = $baseClass . ($isActive ? ' ' . $activeClass : '');
    
    // Determine the URL
    $url = null;
    if ($route) {
        $url = route($route);
    } elseif ($href) {
        $url = $href;
    }
@endphp

@if ($isDropdown)
    <div x-data="{ 
        storageKey: 'sidebar_dropdown_{{ str_replace('.', '_', $routePattern ?? 'default') }}',
        open: false,
        init() {
            // Initialize from localStorage or default to active state
            const stored = localStorage.getItem(this.storageKey);
            if (stored !== null) {
                this.open = stored === 'true';
            } else {
                this.open = {{ $isActive || $showDropdown ? 'true' : 'false' }};
            }
        },
        toggle() {
            this.open = !this.open;
            localStorage.setItem(this.storageKey, this.open);
        }
    }" class="relative">
        <button @click="toggle()"
            class="{{ $classes }} justify-between w-full">
            <div class="flex items-center">
                @if ($icon)
                    {!! $icon !!}
                @endif
                {{ $title }}
            </div>
            <svg class="w-4 h-4 transform transition-transform duration-200"
                :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M19 9l-7 7-7-7"></path>
            </svg>
        </button>
        
        <!-- Dropdown Content -->
        <div x-show="open" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 transform scale-95"
            x-transition:enter-end="opacity-100 transform scale-100"
            x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100 transform scale-100"
            x-transition:leave-end="opacity-0 transform scale-95"
            class="mt-2 ml-8 space-y-1">
            {{ $slot }}
        </div>
    </div>
@else
    <a href="{{ $url }}" class="{{ $classes }}">
        @if ($icon)
            {!! $icon !!}
        @endif
        {{ $title }}
    </a>
@endif
