<?php

namespace App\Livewire;

use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ComponentColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use App\Models\Task;

class TaskTable extends DataTableComponent
{
    protected $model = Task::class;

    public function configure(): void
    {
        $this->setPrimaryKey('id')
            ->setLayout('components.layouts.app');
    }

    public function columns(): array
    {
        return [
            Column::make("Id", "id")
                ->sortable()
                ->searchable(),
            Column::make("Title", "title")
                ->sortable()
                ->searchable(),
            Column::make("Description", "description")
                ->sortable()
                ->searchable(),
        ];
    }
}
