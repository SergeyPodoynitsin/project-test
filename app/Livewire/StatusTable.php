<?php

namespace App\Livewire;

use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\Builder;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ComponentColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use App\Models\Task;
use App\Models\Status;

class StatusTable extends DataTableComponent
{
    protected $model = Status::class;

    public Task $task;

    public function builder(): Builder
    {
        return Status::query()->where('task_id', $this->task->id)->orderBy('updated_at');
    }
    
    public function configure(): void
    {
        $this->setPrimaryKey('id');
    }

    public function columns(): array
    {
        
        return [
            Column::make("ID", "id")
                ->sortable(),
            Column::make("Статус", "status")
                ->sortable()
                ->searchable(),
            Column::make("создан", "created_at")
                ->sortable()
                ->searchable(),
            Column::make("изменен", "updated_at")
                ->sortable()
                ->searchable(),
        ];
    }
}
