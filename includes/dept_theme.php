<?php
// includes/dept_theme.php
function dept_theme($name) {
    $name = strtolower($name);

    $themes = [
        'cardio'    => ['icon' => '❤️', 'gradient' => 'from-rose-500 to-pink-600', 'bg' => 'bg-rose-500/10', 'text' => 'text-rose-400', 'ring' => 'ring-rose-500/30', 'blob' => 'bg-rose-400'],
        'neuro'     => ['icon' => '🧠', 'gradient' => 'from-purple-500 to-violet-600', 'bg' => 'bg-purple-500/10', 'text' => 'text-purple-400', 'ring' => 'ring-purple-500/30', 'blob' => 'bg-purple-400'],
        'ortho'     => ['icon' => '🦴', 'gradient' => 'from-orange-500 to-amber-600', 'bg' => 'bg-orange-500/10', 'text' => 'text-orange-400', 'ring' => 'ring-orange-500/30', 'blob' => 'bg-orange-400'],
        'pediatric' => ['icon' => '👶', 'gradient' => 'from-sky-500 to-cyan-600', 'bg' => 'bg-sky-500/10', 'text' => 'text-sky-400', 'ring' => 'ring-sky-500/30', 'blob' => 'bg-sky-400'],
        'dermat'    => ['icon' => '🧴', 'gradient' => 'from-emerald-500 to-teal-600', 'bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'ring' => 'ring-emerald-500/30', 'blob' => 'bg-emerald-400'],
        'general'   => ['icon' => '⚕️', 'gradient' => 'from-blue-500 to-indigo-600', 'bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'ring' => 'ring-blue-500/30', 'blob' => 'bg-blue-400'],
    ];

    foreach ($themes as $key => $theme) {
        if (str_contains($name, $key)) return $theme;
    }

    return ['icon' => '🏥', 'gradient' => 'from-blue-500 to-indigo-600', 'bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'ring' => 'ring-blue-500/30', 'blob' => 'bg-blue-400'];
}
?>