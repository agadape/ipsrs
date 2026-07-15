<?php
$directory = __DIR__ . '/app/Views';

$replacements = [
    // Backgrounds
    'bg-white' => 'bg-[#121620]/60',
    'bg-gray-50' => 'bg-[#181C25]/80',
    'bg-gray-100' => 'bg-[#202532]',
    'bg-gray-200' => 'bg-[#2D3342]',
    
    // Borders
    'border-gray-100' => 'border-white/5',
    'border-gray-200' => 'border-white/10',
    'border-gray-300' => 'border-white/20',
    
    // Text
    'text-gray-800' => 'text-gray-100',
    'text-gray-900' => 'text-white',
    'text-gray-700' => 'text-gray-200',
    'text-gray-600' => 'text-gray-300',
    'text-gray-500' => 'text-gray-400',
    
    // Hover Backgrounds
    'hover:bg-gray-50' => 'hover:bg-white/5',
    'hover:bg-gray-100' => 'hover:bg-white/10',
    'hover:bg-gray-200' => 'hover:bg-white/15',

    // Specific Primary Colors (Indigo -> Cyber Accent)
    'text-indigo-600' => 'text-[#CCFF00]',
    'bg-indigo-600' => 'bg-[#CCFF00] text-black border-none', // Special case for primary buttons
    'hover:bg-indigo-700' => 'hover:bg-[#B3E600] text-black',
    'bg-indigo-50' => 'bg-[#CCFF00]/10',
    'bg-indigo-100' => 'bg-[#CCFF00]/20',
    'ring-indigo-400/50' => 'ring-[#CCFF00]/50',
    'focus:ring-indigo-500' => 'focus:ring-[#CCFF00]',
    
    // Cards & shadows
    'shadow-sm' => 'shadow-[0_4px_20px_rgba(0,0,0,0.5)]',
];

function processDirectory($dir, $replacements) {
    $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($dir));
    
    foreach ($iterator as $file) {
        if ($file->isFile() && $file->getExtension() === 'php') {
            $content = file_get_contents($file->getPathname());
            
            foreach ($replacements as $search => $replace) {
                // Regex boundary for tailwind classes inside class="" or generally
                $content = preg_replace('/(?<=\s|"|\')' . preg_quote($search, '/') . '(?=\s|"|\')/', $replace, $content);
            }
            
            file_put_contents($file->getPathname(), $content);
        }
    }
}

processDirectory($directory, $replacements);
echo "Replacement complete.\n";
