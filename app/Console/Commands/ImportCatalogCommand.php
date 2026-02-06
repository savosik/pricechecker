<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class ImportCatalogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'catalog:import';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import catalog from external API';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Starting catalog import...');

        $apiUrl = config('catalog.api_url');

        if (!$apiUrl) {
            $this->error('Catalog API URL is not configured. Please set CATALOG_API_URL in .env or config/catalog.php');
            return Command::FAILURE;
        }

        try {
            $this->info("Fetching data from: {$apiUrl}");
            $response = Http::timeout(300)->get($apiUrl);

            if (!$response->successful()) {
                $this->error("Failed to fetch catalog data. Status: {$response->status()}");
                return Command::FAILURE;
            }

            $products = $response->json();

            if (!is_array($products)) {
                $this->error('Invalid response format. Expected array of products.');
                return Command::FAILURE;
            }

            // Validate that we have an array of products
            if (empty($products)) {
                $this->warn('No products found in the response.');
                return Command::SUCCESS;
            }

            // Check if first element is an array (array of products) or object
            $firstItem = reset($products);
            if (!is_array($firstItem)) {
                $this->error('Invalid response format. Expected array of product objects.');
                return Command::FAILURE;
            }

            $this->info("Found " . count($products) . " products to import");

            $bar = $this->output->createProgressBar(count($products));
            $bar->start();

            $imported = 0;
            $errors = 0;

            foreach ($products as $productData) {
                try {
                    $this->importProduct($productData);
                    $imported++;
                } catch (\Exception $e) {
                    $errors++;
                    $sku = $productData['sku'] ?? ($productData['code'] ?? 'unknown');
                    Log::error('Failed to import product', [
                        'sku' => $sku,
                        'name' => $productData['name'] ?? 'unknown',
                        'error' => $e->getMessage(),
                        'trace' => $e->getTraceAsString(),
                    ]);
                    if ($this->output->isVerbose()) {
                        $this->warn("Failed to import product SKU: {$sku} - {$e->getMessage()}");
                    }
                }
                $bar->advance();
            }

            $bar->finish();
            $this->newLine(2);

            $this->info("Import completed!");
            $this->info("Imported: {$imported}");
            if ($errors > 0) {
                $this->warn("Errors: {$errors}");
            }

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error("Error during import: " . $e->getMessage());
            Log::error('Catalog import failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }
    }

    /**
     * Import a single product
     */
    protected function importProduct(array $productData): void
    {
        // Validate required fields
        if (empty($productData['sku'])) {
            throw new \InvalidArgumentException('Product SKU is required');
        }

        if (empty($productData['name'])) {
            throw new \InvalidArgumentException('Product name is required');
        }

        // Get or create brand
        $brand = null;
        if (!empty($productData['brand_name'])) {
            $brand = Brand::firstOrCreate(
                ['name' => trim($productData['brand_name'])]
            );
        }

        // Get or create category hierarchy
        $category = null;
        if (!empty($productData['category_path'])) {
            $categoryPath = trim($productData['category_path']);
            if (!empty($categoryPath)) {
                $category = $this->getOrCreateCategoryPath($categoryPath);
            }
        }

        // Get or create product
        $product = Product::updateOrCreate(
            ['sku' => trim($productData['sku'])],
            [
                'name' => trim($productData['name']),
                'image_url' => !empty($productData['image_main']) ? trim($productData['image_main']) : null,
                'brand_id' => $brand?->id,
            ]
        );

        // Attach category to product
        if ($category) {
            $product->categories()->syncWithoutDetaching([$category->id]);
        }
    }

    /**
     * Get or create category hierarchy from path string
     * Example: "Секс-игрушки/Вибраторы/Нереалистичные вибраторы"
     */
    protected function getOrCreateCategoryPath(string $categoryPath): Category
    {
        $parts = explode('/', $categoryPath);
        $parts = array_map('trim', $parts);
        $parts = array_filter($parts);

        $parentId = null;
        $lastCategory = null;

        foreach ($parts as $categoryName) {
            $lastCategory = Category::firstOrCreate(
                [
                    'name' => $categoryName,
                    'parent_id' => $parentId,
                ]
            );

            $parentId = $lastCategory->id;
        }

        return $lastCategory;
    }
}

