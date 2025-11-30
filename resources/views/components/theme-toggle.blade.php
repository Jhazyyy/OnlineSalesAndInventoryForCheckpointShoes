<!-- Theme Toggle Component -->
<div x-data="{
    isDark: false,
    
    init() {
        // Check for saved theme preference or default to light theme
        const savedTheme = localStorage.getItem('theme');
        
        // Always default to light theme if no saved preference exists
        if (savedTheme === 'dark') {
            this.isDark = true;
            document.documentElement.classList.add('dark');
        } else {
            // Default to light theme (ignore system preference)
            this.isDark = false;
            document.documentElement.classList.remove('dark');
            // Set light as default if no theme was previously saved
            if (!savedTheme) {
                localStorage.setItem('theme', 'light');
            }
        }
        
        // Save current theme state
        localStorage.setItem('theme', this.isDark ? 'dark' : 'light');
    },
    
    toggle() {
        this.isDark = !this.isDark;
        
        if (this.isDark) {
            document.documentElement.classList.add('dark');
            localStorage.setItem('theme', 'dark');
        } else {
            document.documentElement.classList.remove('dark');
            localStorage.setItem('theme', 'light');
        }
    }
}" class="relative">
    <button 
        @click="toggle()" 
        class="flex items-center justify-center w-7 h-7 rounded-full hover:bg-gray-300 dark:hover:bg-gray-600 transition-colors duration-200 focus:outline-none focus:ring-1"
        :title="isDark ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
    >
        <!-- Sun Icon (Light Mode) -->
        <svg x-show="!isDark" x-transition class="w-5 h-5 text-yellow-500" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path fill-rule="evenodd" d="M10 2a1 1 0 011 1v1a1 1 0 11-2 0V3a1 1 0 011-1zm4 8a4 4 0 11-8 0 4 4 0 018 0zm-.464 4.95l.707.707a1 1 0 001.414-1.414l-.707-.707a1 1 0 00-1.414 1.414zm2.12-10.607a1 1 0 010 1.414l-.706.707a1 1 0 11-1.414-1.414l.707-.707a1 1 0 011.414 0zM17 11a1 1 0 100-2h-1a1 1 0 100 2h1zm-7 4a1 1 0 011 1v1a1 1 0 11-2 0v-1a1 1 0 011-1zM5.05 6.464A1 1 0 106.465 5.05l-.708-.707a1 1 0 00-1.414 1.414l.707.707zm1.414 8.486l-.707.707a1 1 0 01-1.414-1.414l.707-.707a1 1 0 011.414 1.414zM4 11a1 1 0 100-2H3a1 1 0 000 2h1z" clip-rule="evenodd"></path>
        </svg>
        
        <!-- Moon Icon (Dark Mode) -->
        <svg x-show="isDark" x-transition class="w-5 h-5 text-blue-300" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
            <path d="M17.293 13.293A8 8 0 016.707 2.707a8.001 8.001 0 1010.586 10.586z"></path>
        </svg>
    </button>
</div>
