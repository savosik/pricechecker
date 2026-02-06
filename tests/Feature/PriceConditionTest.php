<?php

namespace Tests\Feature;

use App\Jobs\ParseProductPriceJob;
use App\Models\Marketplace;
use App\Models\PriceHistory;
use App\Models\Product;
use App\Services\PriceParser\PriceData;
use App\Services\PriceParser\PriceParserFactory;
use App\Services\PriceParser\PriceParserInterface;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Mockery;
use Tests\TestCase;

class PriceConditionTest extends TestCase
{
    use RefreshDatabase;

    private Marketplace $marketplace;
    private $parserMock;

    protected function setUp(): void
    {
        parent::setUp();

        $this->marketplace = Marketplace::create([
            'name' => 'Ozon',
            'code' => 'ozon'
        ]);

        $this->parserMock = Mockery::mock(PriceParserInterface::class);
        
        // Mock the factory to return our parser mock
        $this->mock('alias:' . PriceParserFactory::class, function ($mock) {
            $mock->shouldReceive('make')->andReturn($this->parserMock);
        });
    }

    /** @test */
    public function it_saves_price_when_no_conditions_exist()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'test-sku',
            'condition' => collect([])
        ]);

        $this->parserMock->shouldReceive('getPrice')
            ->once()
            ->andReturn(new PriceData(100.0, 200.0));

        ParseProductPriceJob::dispatchSync($product, $this->marketplace->id, 'http://example.com');

        $this->assertDatabaseHas('price_histories', [
            'product_id' => $product->id,
            'user_price' => 100.0,
            'base_price' => 200.0,
        ]);
    }

    /** @test */
    public function it_saves_price_when_conditions_are_for_different_marketplace()
    {
        $otherMarketplace = Marketplace::create(['name' => 'WB', 'code' => 'wb']);
        
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'test-sku',
            'condition' => collect([
                [
                    'marketplace_id' => $otherMarketplace->id,
                    'price_type' => 'user_price',
                    'operator' => 'lt',
                    'value' => 50
                ]
            ])
        ]);

        $this->parserMock->shouldReceive('getPrice')
            ->once()
            ->andReturn(new PriceData(100.0, 200.0));

        ParseProductPriceJob::dispatchSync($product, $this->marketplace->id, 'http://example.com');

        $this->assertDatabaseHas('price_histories', [
            'product_id' => $product->id,
            'user_price' => 100.0,
        ]);
    }

    /**
     * @test
     * @dataProvider conditionProvider
     */
    public function it_correctly_evaluates_conditions(string $priceType, string $operator, float $value, float $price, bool $shouldSave)
    {
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'test-sku',
            'condition' => collect([
                [
                    'marketplace_id' => $this->marketplace->id,
                    'price_type' => $priceType,
                    'operator' => $operator,
                    'value' => $value
                ]
            ])
        ]);

        $priceData = $priceType === 'user_price' 
            ? new PriceData($price, 1000.0) 
            : new PriceData(1000.0, $price);

        $this->parserMock->shouldReceive('getPrice')
            ->once()
            ->andReturn($priceData);

        ParseProductPriceJob::dispatchSync($product, $this->marketplace->id, 'http://example.com');

        if ($shouldSave) {
            $this->assertDatabaseHas('price_histories', ['product_id' => $product->id]);
        } else {
            $this->assertDatabaseMissing('price_histories', ['product_id' => $product->id]);
        }
    }

    public static function conditionProvider(): array
    {
        return [
            // Operator gt (>)
            'gt_met' => ['user_price', 'gt', 500, 600, true],
            'gt_not_met' => ['user_price', 'gt', 500, 400, false],
            'gt_equal' => ['user_price', 'gt', 500, 500, false],

            // Operator gte (>=)
            'gte_met' => ['user_price', 'gte', 500, 600, true],
            'gte_equal' => ['user_price', 'gte', 500, 500, true],
            'gte_not_met' => ['user_price', 'gte', 500, 400, false],

            // Operator lt (<)
            'lt_met' => ['base_price', 'lt', 500, 400, true],
            'lt_not_met' => ['base_price', 'lt', 500, 600, false],
            'lt_equal' => ['base_price', 'lt', 500, 500, false],

            // Operator lte (<=)
            'lte_met' => ['base_price', 'lte', 500, 400, true],
            'lte_equal' => ['base_price', 'lte', 500, 500, true],
            'lte_not_met' => ['base_price', 'lte', 500, 600, false],

            // Operator eq (=)
            'eq_met' => ['user_price', 'eq', 500, 500, true],
            'eq_not_met' => ['user_price', 'eq', 500, 500.1, false],
            
            // Symbolic operators
            'symbol_gt' => ['user_price', '>', 500, 600, true],
            'symbol_lt' => ['user_price', '<', 500, 400, true],
        ];
    }

    /** @test */
    public function it_uses_or_logic_for_multiple_conditions()
    {
        $product = Product::create([
            'name' => 'Test Product',
            'sku' => 'test-sku',
            'condition' => collect([
                [
                    'marketplace_id' => $this->marketplace->id,
                    'price_type' => 'user_price',
                    'operator' => 'lt',
                    'value' => 100
                ],
                [
                    'marketplace_id' => $this->marketplace->id,
                    'price_type' => 'base_price',
                    'operator' => 'gt',
                    'value' => 1000
                ]
            ])
        ]);

        // Case 1: First condition met
        $this->parserMock->shouldReceive('getPrice')
            ->once()
            ->andReturn(new PriceData(50.0, 500.0));
        ParseProductPriceJob::dispatchSync($product, $this->marketplace->id, 'http://example.com');
        $this->assertEquals(1, PriceHistory::count());

        // Case 2: Second condition met
        $this->parserMock->shouldReceive('getPrice')
            ->once()
            ->andReturn(new PriceData(150.0, 1500.0));
        ParseProductPriceJob::dispatchSync($product, $this->marketplace->id, 'http://example.com');
        $this->assertEquals(2, PriceHistory::count());

        // Case 3: None met
        $this->parserMock->shouldReceive('getPrice')
            ->once()
            ->andReturn(new PriceData(150.0, 500.0));
        ParseProductPriceJob::dispatchSync($product, $this->marketplace->id, 'http://example.com');
        $this->assertEquals(2, PriceHistory::count());
    }
}
