<div id="notifications-root" class="relative">
    <button id="notifications-toggle" class="flex items-center justify-center w-6 h-6 rounded-full text-black dark:text-white hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 focus:outline-none focus:ring-1">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
            <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0M3.124 7.5A8.969 8.969 0 0 1 5.292 3m13.416 0a8.969 8.969 0 0 1 2.168 4.5" />
        </svg>
            <span id="notifications-badge" class="hidden absolute -top-1 -right-1 items-center justify-center px-1.5 py-0.5 text-xs font-semibold leading-none text-white bg-red-600 rounded-full">0</span>
    </button>

    <div id="notifications-panel" class="hidden origin-top-right absolute right-0 mt-2 w-96 rounded-md shadow-lg bg-white dark:bg-gray-800 ring-1 ring-black ring-opacity-5 focus:outline-none z-50">
        <div class="py-2 px-4 border-b dark:border-gray-700 flex items-center justify-between dark:text-white">
            <h3 class="text-lg font-semibold">Notifications</h3>
            <span id="notifications-count" class="text-sm text-gray-500">0 new</span>
        </div>

        <div id="notifications-list" class="max-h-80 overflow-y-auto">
            <div class="p-4 text-sm text-gray-500">No notifications</div>
        </div>
    </div>
</div>