<?php

namespace App\Providers;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Blade::directive('sortLink', function ($expression) {
            [$column, $label] = explode(',', $expression, 2);
            $column = trim($column, " '\"");
            $label = trim($label, " '\"");

            return "<?php
                \$_sort = request('sort');
                \$_dir = request('direction');
                if (\$_sort !== '$column') {
                    \$_url = request()->fullUrlWithQuery(['sort' => '$column', 'direction' => 'asc']);
                    \$_arrow = '';
                } elseif (\$_dir === 'asc') {
                    \$_url = request()->fullUrlWithQuery(['sort' => '$column', 'direction' => 'desc']);
                    \$_arrow = '<i class=\"fas fa-chevron-up text-xs ml-1.5\"></i>';
                } else {
                    \$_url = request()->fullUrlWithQuery(['sort' => null, 'direction' => null]);
                    \$_arrow = '<i class=\"fas fa-chevron-down text-xs ml-1.5\"></i>';
                }
                echo '<a href=\"' . e(\$_url) . '\" class=\"text-gray-600 hover:text-gray-900 inline-flex items-center\">' . e('$label') . ' ' . \$_arrow . '</a>';
            ?>";
        });
    }
}
