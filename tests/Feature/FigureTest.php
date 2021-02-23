<?php

namespace Tests\Feature;

use App\Models\Repositories\Contracts\Wallet\FigureRepository;
use Illuminate\Auth\GenericUser;
use Tests\Fakes\Repositories\FakeFigureRepository;
use Tests\TestCase;

class FigureTest extends TestCase
{
    public function test_cannot_get_figure_without_authorization()
    {
        $this->get('/api/v1/figure/1345');

        $this->assertResponseStatus(401);
    }

    public function test_cannot_get_figure_without_required_parameters()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/figure/1345');

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();
    }

    public function test_cannot_get_figure_without_right_parameter_types()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/figure/1345?startDate=random&endDate=string');

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();
    }

    public function test_cannot_get_figure_if_end_date_before_start_date()
    {
        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get('/api/v1/figure/1345?startDate=2018-02-02&endDate=2018-01-01');

        $this->assertResponseStatus(422);
        $this->assertResponseMatchesSwagger();
    }

    public function test_get_figure()
    {
        $account = '123456';
        $this->app->alias(FakeFigureRepository::class, FigureRepository::class);

        $authorized = factory(GenericUser::class)->make();
        $this->actingAs($authorized)->get(sprintf('/api/v1/figure/%d?startDate=2018-01-01&endDate=2018-02-02',
            $account));

        $this->assertResponseOk();
        $this->assertResponseMatchesSwagger();

        $json = $this->response->getData();
        $this->assertObjectHasAttribute('response', $json);

        $response = $json->response;
        $this->assertObjectHasAttribute('data', $response);
        $this->assertAttributeEquals('figure', 'type', $response->data);

        $this->assertResponseMatchesJsonSnapshot();
    }
}
