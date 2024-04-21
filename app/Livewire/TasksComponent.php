<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Query\JoinClause;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Task;
use App\Models\Status;
use App\Enums\StatusEnum;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Enum;
use Barryvdh\Debugbar\Facades\Debugbar;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class TasksComponent extends Component
{
    
    use WithPagination;

    public $popUp = false;
    public $infoUp = false;
    public $setStatusUp = false;
    public $statusFilterUp = false;
    public $statusFilter = [];

    public Task $task;
    public $title;
    public $description;
    public $status;

    public $orderColumn = 'title';
    public $sortOrder = "asc";
    public $searchTerm ="";
    
    public $sortLink = '/images/sortLink.svg';            
    public $sortLinkUp = '/images/sortLinkUp.svg';
    public $sortLinkDown = '/images/sortLinkDown.svg';
    public $sortLinkStatus = '/images/sortLinkStatus.svg';
    
    public $sortIcon = array();

    protected $rules = [
        'title' => ['required','string','min:1'],
        'description' => ['required','string','min:3'],
        'status' => ['required'],
        //'status' => ['required', 'exists:statuses'],
    ];

    public function create()
    {
        $this->popUp = true;
        $this->task = new Task;
        $this->status = StatusEnum::cases()[0]->value;
    }

    public function read(Task $task)
    {
        $this->infoUp = true;
        $this->task = $task;
    }

    public function update(Task $task)
    {
        $this->popUp = true;
        $this->task = $task;
        $this->title = $task->title;
        $this->description = $task->description;
        $this->status = $task->status->status;
    }

    public function setStatus(Task $task)
    {
        $this->setStatusUp = true;
        $this->popUp = true;
        $this->task = $task;
    }

    public function close()
    {
        
        $this->reset([
            'popUp', 
            'infoUp', 
            'statusFilterUp', 
            'setStatusUp',
            'task',
            'title',
            'description',
            'status'
        ]);
        //$this->reset();
    }

    public function store_task()
    {        
        $this->validate();
        $this->task->fill(['title' => $this->title, 'description' => $this->description])->save();
        $this->store_status($this->task);
    }

    public function store_status(Task $task)
    {
        $this->title = $task->title;
        $this->description = $task->description;
        $this->validate();
        Status::create(['task_id' => $task->id, 'status' => $this->status])->save();        
        $this->close();
    }

    public function delete(Task $task)
    {
        $task->delete();
        $this->close();
    }

    public function sortOrderColumn($columnName)
    {
        
        $this->sortIcon = [
            'title'=>$this->sortLink,
            'description'=>$this->sortLink,
            'status'=>$this->sortLink
        ];
        
        if($this->orderColumn == $columnName)
        {
            if($this->sortOrder == "asc")
            {
                $this->sortOrder = "desc";
                $this->sortIcon[$columnName] = $this->sortLinkDown;
            }else{
                $this->sortOrder = "asc";
                $this->sortIcon[$columnName] = $this->sortLinkUp;
            }
        }else{
            $this->sortOrder = "asc";
            $this->sortIcon[$columnName] = $this->sortLinkUp;
        };
        
        $this->orderColumn = $columnName;

    }

    
    public function paginate($items, $perPage = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);
        return new LengthAwarePaginator($items->forPage($page, $perPage), $items->count(), $perPage, $page, $options);
    }


    public function statusFilterToggle(){
        $this->statusFilterUp ? $this->statusFilterUp = false : $this->statusFilterUp = true;
    }


    public function render()
    {
        
        $tasks = Task::select();
            //->orderBy($this->orderColumn, $this->sortOrder);
                
        if(!empty($this->searchTerm))
        {
            $searchTerm = '%'.$this->searchTerm.'%';            
            $tasks->whereAny(['title','description'],'LIKE',$searchTerm);
        }

        if(empty($this->sortIcon))
        {
            $this->sortIcon = [
                'title'=>$this->sortLinkUp,
                'description'=>$this->sortLink,
                'status'=>$this->sortLink
            ];
        }
  
        if(empty($this->statusFilter))
        {
            $this->statusFilter = array_column(StatusEnum::cases(), 'value');
        }


        $tasks_collection = $tasks->get();

        $filtered = $tasks_collection->filter(function ($value, int $key) {
            return in_array($value->status->status->value, $this->statusFilter);
        });

        
        if($this->orderColumn<>'status')
        {
            $this->sortOrder == "asc" ? $sorted = $filtered->sortBy([$this->orderColumn]) : $sorted = $filtered->sortByDesc([$this->orderColumn]);
        }else{
            $this->sortOrder == "asc" ? $sorted = $filtered->sortBy(['status.status.value']) : $sorted = $filtered->sortByDesc(['status.status.value']);
        }
               
        $tasks = $this->paginate($sorted, 10);

        return view('livewire.tasks-component',['tasks' => $tasks])->layout('components.layouts.app',['title' => 'Задачи']);
    }


}
