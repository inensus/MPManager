<?php


namespace Tests\Feature;


use App\Jobs\TariffPricingComponentsCalculator;
use App\Models\Meter\MeterTariff;
use App\Models\TariffPricingComponent;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class TariffPricingCalculator extends TestCase
{

    use RefreshDatabase, WithFaker;

    /** @test */
    public function compontentPriceChangesTotalPrice()
    {
        $this->withoutExceptionHandling();
        factory(MeterTariff::class)->create();

        $tariff = MeterTariff::first();
        $tariffPrice = $tariff->total_price;


        $pricingCalculator = new TariffPricingComponentsCalculator($tariff,
            [
                [
                    'name' => 'Some component',
                    'price' => 100000,
                ],
                [
                    'name' => 'Some other component',
                    'price' => 100000
                ],
            ]);
        $pricingCalculator->handle();
        $updatedTariff = $tariff->fresh();
        echo $tariff->price . ' Tariff price\n';
        echo $tariff->total_price . ' Tariff price\n';
        $this->assertEquals($tariffPrice + 200000, $updatedTariff->total_price);

        $this->assertCount(2, TariffPricingComponent::all());
    }
}
