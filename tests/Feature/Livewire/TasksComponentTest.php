<?php

namespace Tests\Feature\Livewire;

use App\Livewire\TasksComponent;
use App\Models\Task;
use App\Models\Status;
use App\Enums\StatusEnum;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Livewire\Livewire;
use Tests\TestCase;

class TasksComponentTest extends TestCase
{

    protected $test_title = '';
    protected $test_description = '';
    protected $test_status = '';

    public function set_parameters()
    {
        $this->test_title = 'title_'.env('APP_KEY');
        $this->test_description = 'description_'.env('APP_KEY');
        $this->test_status = StatusEnum::cases()[0]->value;
    }

    public function get_test_task_count(){
        return Task::query()
            ->where('title', '=' ,$this->test_title)
            ->where('description', '=' ,$this->test_description)
            ->count();
    }

    public function get_test_task_last_id(){
        $task = Task::query()
            ->where('title', '=' ,$this->test_title)
            ->where('description', '=' ,$this->test_description)            
            ->orderBy('id')
            ->limit(1)
            ->get();
        return $task[0]->id;
    }

    public function create_test_task(){        
        Livewire::test(TasksComponent::class)
            ->call('create')
            ->set([
                'title' => $this->test_title,
                'description' => $this->test_description,
                'status' => $this->test_status
            ])
            ->call('store_task');
    }

    //---RENDER
    public function test_renders()
    {
        Livewire::test(TasksComponent::class)
            ->assertStatus(200);
    }

    //---CREATE
    public function test_create_task()
    {        
        $this->set_parameters();
        $count = $this->get_test_task_count();
        $this->create_test_task();
        $new_count = $this->get_test_task_count();
        $this->assertEquals(1+$count, $new_count);
    }

    //---UPDATE
    public function test_update_task()
    {        
        $count = $this->get_test_task_count();
        if($count < 1){
            $this->test_create_task();
            $count = $this->get_test_task_count();
        }

        $task_id = $this->get_test_task_last_id();
        $new_title = 'updated_'.$this->test_title;
        $new_description = 'updated_'.$this->test_description;
        $new_status = StatusEnum::cases()[1]->value;

        Livewire::test(TasksComponent::class)
            ->call('update', Task::find($task_id))
            ->set([
                'title' => $new_title,
                'description' => $new_description,
                'status' => $new_status
            ])
            ->call('store_task');

        $new_count = $this->get_test_task_count();

        $task = Task::find($task_id);

        $this->assertEquals($count-1, $new_count);
        $this->assertEquals($task->title, $new_title);
        $this->assertEquals($task->description, $new_description);
        $this->assertEquals($task->status->status->value, $new_status);

    }

    //---DELETE
    public function test_delete_task()
    {
        $count = $this->get_test_task_count();        
        if($count < 1){
            $this->test_create_task();
            $count = $this->get_test_task_count();
        }

        $task_id = $this->get_test_task_last_id();

        Livewire::test(TasksComponent::class)
            ->call('delete', Task::find($task_id));

        $this->assertEquals(empty(Task::find($task_id)), true);
    }

    //---STATUS
    public function test_set_status()
    {
        $count = $this->get_test_task_count();
        if($count < 1){
            $this->test_create_task();
            $count = $this->get_test_task_count();
        }

        $task_id = $this->get_test_task_last_id();

        $task = Task::find($task_id);

        $status_enum = StatusEnum::cases()[2];

        Livewire::test(TasksComponent::class)
            ->call('setStatus', $task)
            ->set(['status' => $status_enum->value])
            ->call('store_status', $task);

        $this->assertEquals($task->status->status->value, $status_enum->value);
                
    }

}
