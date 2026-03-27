<?php

/**
 * Theme Helper Functions
 * Provides functions for theme-aware styling
 */
if (! function_exists('getTheme')) {
    function getTheme(): string
    {
        return session('theme', 'light');
    }
}

if (! function_exists('theme_navbar')) {
    function theme_navbar(): string
    {
        $theme = getTheme();

        return $theme === 'dark' ? 'dark' : 'light';
    }
}

if (! function_exists('theme_sidebar')) {
    function theme_sidebar(): string
    {
        $theme = getTheme();

        return $theme === 'dark' ? 'dark' : 'secondary';
    }
}

if (! function_exists('theme_sidebar_text')) {
    function theme_sidebar_text(): string
    {
        $theme = getTheme();

        return $theme === 'dark' ? 'light' : 'dark';
    }
}

if (! function_exists('theme_footer')) {
    function theme_footer(): string
    {
        $theme = getTheme();

        return $theme === 'dark' ? 'dark' : 'light';
    }
}

if (! function_exists('theme_footer_text')) {
    function theme_footer_text(): string
    {
        $theme = getTheme();

        return $theme === 'dark' ? 'light' : 'dark';
    }
}

if (! function_exists('isDarkMode')) {
    function isDarkMode(): bool
    {
        return getTheme() === 'dark';
    }
}
