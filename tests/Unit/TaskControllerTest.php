<?php

namespace Tests\Unit;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use App\Models\User;
use App\Models\Task;
use Tests\TestCase;

class TaskControllerTest extends TestCase
{
    use DatabaseTransactions;

    /**
     * Test creating a task.
     *
     * @return void
     */
    public function testCreateTask(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user);

        $response = $this->post('/tasks', [
            'title' => 'Test Task',
            'date' => '2023-06-10',
            'time_spent' => 20,
            'comment' => 'Task Comment',
        ]);

        $response->assertRedirect('/tasks');
        $this->assertDatabaseHas('tasks', [
            'title' => 'Test Task',
            'user_id' => $user->id,
        ]);
    }

    /**
     * Test viewing a task.
     *
     * @return void
     */
    public function testIndex()
    {
        $user = User::factory()->create();

        $userTasks = Task::factory()->count(3)->create(['user_id' => $user->id]);

        $this->actingAs($user);

        $response = $this->get('/tasks');

        $response->assertStatus(200);

        foreach ($userTasks as $task) {
            $response->assertSee($task->title);
        }
    }

    /**
     * Test updating a task.
     *
     * @return void
     */
    public function testUpdateTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $updatedTitle = 'Updated Test Task';
        $updatedComment = 'Updated Task Comment';
        $updatedDate = '2023-05-17';
        $updatedTimeSpent = 12;

        $response = $this->actingAs($user)
            ->put('/tasks/'. $task->id, [
                'title' => $updatedTitle,
                'comment' => $updatedComment,
                'date' => $updatedDate,
                'time_spent' => $updatedTimeSpent,
            ]);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseHas('tasks', [
            'id' => $task->id,
            'title' => $updatedTitle,
            'comment' => $updatedComment,
            'date' => $updatedDate,
            'time_spent' => $updatedTimeSpent,
        ]);
    }

    /**
     * Test deleting a task.
     *
     * @return void
     */
    public function testDeleteTask()
    {
        $user = User::factory()->create();
        $task = Task::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($user)
            ->delete('/tasks/'. $task->id);

        $response->assertRedirect(route('tasks.index'));

        $this->assertDatabaseMissing('tasks', [
            'id' => $task->id,
        ]);
    }

}
