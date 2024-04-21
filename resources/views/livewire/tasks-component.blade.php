<div >    
    <div class="border rounded-xl m-4 p-4 bg-slate-300">
        <table class="table w-full">      
            <thead>
                <tr class="bg-slate-200">
                    <th class="sort p-2 border-b w-2/12">
                        <div class="flex flex-row justify-center">
                            <a wire:click="sortOrderColumn('title')" class="cursor-pointer">Задача</a><img class="w-6" src="{{$sortIcon['title']}}">
                        </div>
                    </th>
                    <th class="sort p-2 border-b  w-7/12">
                        <div class="flex flex-row justify-center">
                            <a wire:click="sortOrderColumn('description')" class="cursor-pointer">Описание</a><img class="w-6" src="{{$sortIcon['description']}}">
                        </div>
                    </th>
                    <th class="sort p-2 border-b  w-1/12">
                        <div class="flex flex-row justify-center">
                            <a wire:click="sortOrderColumn('status')" class="cursor-pointer">Статус</a><img class="w-6" src="{{$sortIcon['status']}}"><a wire:click="statusFilterToggle()" class="cursor-pointer"><img class="w-6" src="{{$sortLinkStatus}}"></a>
                            @if ($statusFilterUp)
                                <div class="flex flex-col bg-white absolute z-10 justify-start items-start border p-2 m-2 w-40">
                                    @foreach (App\Enums\StatusEnum::cases() as $status)
                                        <label>
                                            <input wire:model.live="statusFilter" name="{{$status->value}}" value="{{$status->value}}" type="checkbox">
                                            {{$status->value}}
                                        </label>
                                    @endforeach
                                    <div class="flex flex-col bg-white z-10 justify-center items-center border hover:bg-grey-500 w-full p-1">
                                        <a wire:click="statusFilterToggle()" class="cursor-pointer">закрыть</a>
                                    </div>
                                </div>
                                
                            @endif
                        </div>
                    </th>
                    <th class="p-2 border-b  w-1/12">
                        <div class="flex flex-row justify-center">
                            <a class="text-blue-600 hover:text-blue-900 cursor-pointer" wire:click="create()">
                                + Новая задача
                            </a>
                        </div>
                    </th>
                    <th class="p-2 border-b  w-1/12">
                        <div class="flex flex-row">
                            <input type="text" wire:model.live="searchTerm" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Поиск"/>
                        </div>
                    </th>
                </tr>
            </thead>
            <tbody>
                @foreach ($tasks as $item)
                    <tr class="odd:bg-white even:bg-slate-100">
                        <td class="p-2 border-b font-semibold">
                            {{$item->title}}
                        </td>
                        <td class="p-2 border-b font-medium">
                            {{$item->description}}
                        </td>
                        <td class="p-2 border-b">
                            <div class="flex flex-row justify-center">
                                @if (!empty($item->status->status))
                                <a class="text-purple-500 font-medium hover:text-purple-900 cursor-pointer" wire:click="setStatus({{$item}})">
                                    {{$item->status->status}}
                                </a>
                                @endif
                            </div>
                        </td>
                        <td class="p-2 border-b">
                            <div class="flex flex-row justify-center">
                                <a class="text-green-600 hover:text-green-900 cursor-pointer" wire:click="read({{$item}})">История статусов</a>
                            </div>
                        </td>
                        <td class="p-2 border-b text-center">      
                            <div class="flex flex-row justify-center">
                                <a class="text-blue-600 hover:text-blue-900 cursor-pointer" wire:click="update({{$item}})">Изменить</a>
                                <a class="text-red-600 hover:text-red-900 cursor-pointer" wire:click="delete({{$item}})" wire:confirm="Удалить задачу?">Удалить</a>
                            </div>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <div class = "m-2 p-2">
            {{$tasks->links()}}
        </div>
    </div>


    
    @if ($popUp)        
    <div class="relative">
        <div class="bg-black fixed top-0 right-0 left-0 z-40 w-full h-full opacity-70"></div>
        <div id="update-modal" tabindex="-1" class="flex overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-50 justify-center items-center w-full md:inset-0 h-[calc(100%-1rem)] max-h-full">
            <div class="relative p-4 w-full max-w-2xl max-h-full">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{$task->exists ? 'Редактировать' : 'Добавить'}}
                        </h3>
                        <button wire:click="close()" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Закрыть</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    <form class="p-4 md:p-5" 
                        wire:submit = "
                            @if ($setStatusUp)
                                @if ($task->exists)
                                    store_status({{$task}})
                                @endif
                            @else
                                store_task()
                            @endif
                            ">
                        <div class="grid gap-4 mb-4 grid-cols-2">

                            <div class="col-span-2 @if($setStatusUp) hidden @endif">
                                <label for="task_title" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Название</label>
                                <input type="text" id="task_title" wire:model="title" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-600 focus:border-primary-600 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500" placeholder="Название задачи" />
                            </div>
                            @error('title')
                                <p class="text-red-500">{{$message}}</p>
                            @enderror

                            <div class="col-span-2  @if($setStatusUp) hidden @endif">
                                <label for="task_description" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Описание</label>
                                <textarea id="task_description" wire:model="description" rows="4" class="block p-2.5 w-full text-sm text-gray-900 bg-gray-50 rounded-lg border border-gray-300 focus:ring-blue-500 focus:border-blue-500 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" placeholder="Описание задачи"></textarea>                    
                            </div>
                            @error('description')
                                <p class="text-red-500">{{$message}}</p>
                            @enderror

                            <div class="col-span-2 sm:col-span-1">
                                <label for="task_status" class="block mb-2 text-sm font-medium text-gray-900 dark:text-white">Состояние</label>
                                <select id="task_status" wire:model.change="status" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5 dark:bg-gray-600 dark:border-gray-500 dark:placeholder-gray-400 dark:text-white dark:focus:ring-primary-500 dark:focus:border-primary-500">                               
                                    @foreach (App\Enums\StatusEnum::cases() as $status)
                                        <option value="{{$status->value}}">{{$status->value}}</option>
                                    @endforeach
                                </select>
                            </div>
                            @error('status')
                                <p class="text-red-500">{{$message}}</p>
                            @enderror
                        </div>
                        <button type="submit" class="text-white inline-flex items-center bg-blue-700 hover:bg-blue-800 focus:ring-4 focus:outline-none focus:ring-blue-300 font-medium rounded-lg text-sm px-5 py-2.5 text-center dark:bg-blue-600 dark:hover:bg-blue-700 dark:focus:ring-blue-800">
                            {{$task->exists ? 'Сохранить' : 'Добавить'}}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif



    @if ($infoUp)        
    <div class="relative">
        <div wire:click="close()" class="bg-black fixed top-0 right-0 left-0 z-40 w-full h-full opacity-70"></div>
        <div id="read-modal" tabindex="-1" class="flex overflow-y-auto overflow-x-hidden fixed top-0 right-0 left-0 z-40 justify-center items-center md:inset-0 h-[calc(100%-1rem)]">
            <div class="relative p-4 w-full max-h-full max-w-5xl z-50">
                <!-- Modal content -->
                <div class="relative bg-white rounded-lg shadow dark:bg-gray-700">
                    <!-- Modal header -->
                    <div class="flex items-center justify-between p-4 md:p-5 border-b rounded-t dark:border-gray-600">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{$task->exists ? 'Информация о задаче' : 'Не выбрана задача'}}
                        </h3>
                        <button wire:click="close()" type="button" class="text-gray-400 bg-transparent hover:bg-gray-200 hover:text-gray-900 rounded-lg text-sm w-8 h-8 ms-auto inline-flex justify-center items-center dark:hover:bg-gray-600 dark:hover:text-white" data-modal-toggle="crud-modal">
                            <svg class="w-3 h-3" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 14 14">
                                <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m1 1 6 6m0 0 6 6M7 7l6-6M7 7l-6 6"/>
                            </svg>
                            <span class="sr-only">Закрыть</span>
                        </button>
                    </div>
                    <!-- Modal body -->
                    @if($task->exists)
                        <table class="w-full">
                            <tr><td class="p-2 border-b border-r">ID</td><td colspan="2" class="p-2 border-b">{{$task->id}}</td></tr>
                            <tr><td class="p-2 border-b border-r">Название</td><td colspan="2" class="p-2 border-b">{{$task->title}}</td></tr>
                            <tr><td class="p-2 border-b border-r">Описание</td><td colspan="2" class="p-2 border-b">{{$task->description}}</td></tr>
                            <tr><td class="p-2 border-b border-r">Текущий статус</td><td colspan="2" class="p-2 border-b">{{$task->status->status}}</td></tr>
                            <tr><td class="p-2 font-medium" colspan="3">Информация об изменении статуса задачи:</td></tr>
                            <tr><td class="p-2 border-b border-r">ID</td><td class="p-2 border-b border-r">Статус</td><td class="p-2 border-b">Изменен</td></tr>
                            @foreach ($task->statuses as $task_status)
                                <tr><td class="p-2 border-b border-r">{{$task_status->id}}</td><td class="p-2 border-b border-r">{{$task_status->status}}</td><td class="p-2 border-b">{{$task_status->updated_at}}</td></tr>
                            @endforeach
                        </table>
                        <!--div class="p-2">                            
                            livewire('status-table',['task' => $task])
                        </div-->
                    @endif                    
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
