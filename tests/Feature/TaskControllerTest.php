<?php

namespace Tests\Feature;

use App\Models\Task;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use Symfony\Component\HttpFoundation\StreamedResponse;


class TaskControllerTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /**
     * Test generating a report in different formats.
     *
     * @return void
     */
    public function testGenerateReport()
    {
        $user = User::factory()->create();

        $startDate = '2023-01-01';
        $endDate = '2023-06-13';

        $formats = ['csv', 'pdf'];

        foreach ($formats as $format) {
            Task::factory(10)->create([
                'user_id' => $user->id,
                'date' => $this->faker->dateTimeBetween($startDate, $endDate)->format('Y-m-d'),
            ]);

            $response = $this->actingAs($user)
                ->get(route('tasks.report', [
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'format' => $format,
                ]));

            $response->assertSuccessful();

            switch ($format) {
                case 'csv':
                    $response->assertHeader('Content-Type', 'text/csv; charset=UTF-8');
                    break;
                case 'pdf':
                default:
                    $response->assertHeader('Content-Type', 'application/pdf');
                    break;
            }
        }
    }
}
