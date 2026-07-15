<?php
$directory = __DIR__ . '/app/Views';

$replacements = [
    'bg-indigo-50' => 'bg-teal-50',
    'bg-indigo-100' => 'bg-teal-100',
    'bg-indigo-500' => 'bg-teal-500',
    'bg-indigo-600' => 'bg-teal-600',
    'bg-indigo-700' => 'bg-teal-700',
    'bg-indigo-900' => 'bg-teal-900',
    'text-indigo-200' => 'text-teal-200',
    'text-indigo-500' => 'text-teal-500',
    'text-indigo-600' => 'text-teal-600',
    'text-indigo-700' => 'text-teal-700',
    'text-indigo-800' => 'text-teal-800',
    'text-indigo-950' => 'text-teal-950',
    'border-indigo-200' => 'border-teal-200',
    'border-indigo-500' => 'border-teal-500',
    'ring-indigo-200' => 'ring-teal-200',
    'ring-indigo-400' => 'ring-teal-400',
    'ring-indigo-500' => 'ring-teal-500',
    'focus:ring-indigo-500' => 'focus:ring-teal-500',
    'hover:bg-indigo-50' => 'hover:bg-teal-50',
    'hover:bg-indigo-100' => 'hover:bg-teal-100',
    'hover:bg-indigo-600' => 'hover:bg-teal-600',
    'hover:bg-indigo-700' => 'hover:bg-teal-700',
    'hover:text-indigo-600' => 'hover:text-teal-600',
    'hover:text-indigo-700' => 'hover:text-teal-700',
    'hover:text-indigo-800' => 'hover:text-teal-800',
    'from-indigo-500' => 'from-teal-500',
    'to-indigo-500' => 'to-teal-500',
    'shadow-indigo-100' => 'shadow-teal-100',
    'shadow-indigo-200' => 'shadow-teal-200',
    'shadow-indigo-500/20' => 'shadow-teal-500/20',
    'shadow-indigo-500/30' => 'shadow-teal-500/30',
    
    // Purple to emerald (for gradients)
    'to-purple-600' => 'to-emerald-500',
    'bg-purple-50' => 'bg-emerald-50',
    'text-purple-600' => 'text-emerald-600',
];

function processDirectory($dir, $replacements) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            // Simple str_replace is fine here because these classes are quite unique
            $newContent = str_replace(array_keys($replacements), array_values($replacements), $content);
            
            if ($content !== $newContent) {
                file_put_contents($file->getPathname(), $newContent);
            }
        }
    }
}

processDirectory($directory, $replacements);
echo "Replacement to teal complete.\n";
