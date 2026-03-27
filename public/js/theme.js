/**
 * Theme Management Script
 * Handles dark/light mode toggle and persistence
 */

(function() {
    'use strict';

    // Initialize theme on page load
    document.addEventListener('DOMContentLoaded', function() {
        initializeTheme();
    });

    /**
     * Initialize theme based on localStorage or system preference
     */
    function initializeTheme() {
        const savedTheme = localStorage.getItem('theme');
        const body = document.body;

        if (savedTheme) {
            applyTheme(savedTheme);
        } else {
            // Default to light theme
            applyTheme('light');
        }
    }

    /**
     * Apply theme to the document
     * @param {string} theme - 'light' or 'dark'
     */
    function applyTheme(theme) {
        const body = document.body;
        
        // Remove existing theme classes
        body.classList.remove('theme-light', 'theme-dark');
        
        // Add new theme class
        body.classList.add('theme-' + theme);
        
        // Update toggle button icon
        updateToggleIcon(theme);
        
        // Save to localStorage
        localStorage.setItem('theme', theme);
    }

    /**
     * Toggle between light and dark themes
     */
    function toggleTheme() {
        const currentTheme = localStorage.getItem('theme') || 'light';
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        
        applyTheme(newTheme);
    }

    /**
     * Update the toggle button icon based on current theme
     * @param {string} theme - Current theme
     */
    function updateToggleIcon(theme) {
        const lightIcon = document.getElementById('theme-icon-light');
        const darkIcon = document.getElementById('theme-icon-dark');

        if (lightIcon && darkIcon) {
            if (theme === 'dark') {
                lightIcon.classList.add('d-none');
                darkIcon.classList.remove('d-none');
            } else {
                lightIcon.classList.remove('d-none');
                darkIcon.classList.add('d-none');
            }
        }
    }

    // Make toggleTheme available globally
    window.toggleTheme = toggleTheme;
})();
