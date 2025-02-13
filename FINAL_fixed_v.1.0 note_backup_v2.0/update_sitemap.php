<?php
// Base URL of the website, without trailing slash.
$base_url = 'https://inote.pw';

// Path to the directory to save the notes in, without trailing slash.
$save_path = '_tmp';

// Maximum size of each sitemap file (in bytes)
$max_sitemap_size = 50 * 1024 * 1024; // 50MB


// Function to update sitemap.xml
function updateSitemap($urls, $base_url) {
    global $max_sitemap_size; // Declare $max_sitemap_size as global
    $sitemapPath = __DIR__ . '/sitemap.xml';
    $sitemapIndex = __DIR__ . '/sitemap_1.xml';


    $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
    $sitemapContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;

    $sitemapSize = strlen($sitemapContent);
    $sitemapCount = 0;
    $sitemapFiles = [];

    foreach ($urls as $url) {
        $urlEntry = '<url>' . PHP_EOL;
        $urlEntry .= '    <loc>' . htmlspecialchars($base_url . $url) . '</loc>' . PHP_EOL;
        $urlEntry .= '    <lastmod>' . date('Y-m-d\TH:i:sP') . '</lastmod>' . PHP_EOL;
        $urlEntry .= '    <changefreq>daily</changefreq>' . PHP_EOL;
        $urlEntry .= '    <priority>' . ($url === '/' ? '1.0' : '0.8') . '</priority>' . PHP_EOL;
        $urlEntry .= '</url>' . PHP_EOL;

        $newSize = $sitemapSize + strlen($urlEntry);
        if ($newSize > $max_sitemap_size) {
            // Create a new sitemap file
            $sitemapCount++;
            $newSitemapPath = __DIR__ . '/sitemap' . $sitemapCount . '.xml';
            file_put_contents($newSitemapPath, $sitemapContent . '</urlset>', LOCK_EX);
            $sitemapFiles[] = $newSitemapPath;
            
            // Start a new sitemap
            $sitemapContent = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
            $sitemapContent .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
            $sitemapSize = strlen($sitemapContent);
        }

        $sitemapContent .= $urlEntry;
        $sitemapSize = $newSize;
    }

    // Save the last sitemap file
    $sitemapCount++;
    $lastSitemapPath = __DIR__ . '/sitemap' . $sitemapCount . '.xml';
    file_put_contents($lastSitemapPath, $sitemapContent . '</urlset>', LOCK_EX);
    $sitemapFiles[] = $lastSitemapPath;

    // Create sitemap index if there are multiple sitemaps
    if ($sitemapCount > 1) {
        $sitemapIndexContent = '<?xml version="1.0" encoding="UTF-8"?>' . PHP_EOL;
        $sitemapIndexContent .= '<sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . PHP_EOL;
        foreach ($sitemapFiles as $sitemapFile) {
            $sitemapIndexContent .= '<sitemap>' . PHP_EOL;
            $sitemapIndexContent .= '    <loc>' . htmlspecialchars($base_url . '/' . basename($sitemapFile)) . '</loc>' . PHP_EOL;
            $sitemapIndexContent .= '    <lastmod>' . date('Y-m-d\TH:i:sP', filemtime($sitemapFile)) . '</lastmod>' . PHP_EOL;
            $sitemapIndexContent .= '</sitemap>' . PHP_EOL;
        }
        $sitemapIndexContent .= '</sitemapindex>';
        file_put_contents($sitemapIndex, $sitemapIndexContent, LOCK_EX);
        rename($lastSitemapPath, $sitemapPath); // Rename the last sitemap to sitemap.xml
    } else {
        rename($lastSitemapPath, $sitemapPath); // Rename the last sitemap to sitemap.xml
    }
}

// Get all note files from the save directory
$noteFiles = glob($save_path . '/*');

// Check if the directory is accessible before updating the sitemap
if (is_dir($save_path) && is_readable($save_path)) {
    updateSitemap($urls, $base_url);
    echo "Sitemap updated successfully.";
} else {
    // Handle the case where the directory is not accessible
    error_log("Error: Directory '$save_path' is not accessible for sitemap generation.");
    echo "Failed to update sitemap.";
}

?>