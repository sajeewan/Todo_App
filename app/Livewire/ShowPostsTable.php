<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Tasks;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Hash;
use Illuminate\Console\View\Components\Task;

class ShowPostsTable extends Component
{
    use WithPagination;
    
    public $taskId = null;
    public $showModal = false;
    public $search = '';
    public $sortField = 'title';
    public $sortDirection = 'asc';

    public $title ='';
    public $body = '';
    public $status = 'To-Do';

    protected $rules = [
        'title' => 'required|string',
        'body' => 'required|string',
        'status' => 'required',
    ];

    public function updatedSearch()
    {
        $this->resetPage();
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    public function openModal($taskId = null)
    {
        $this->resetInput();
        $this->taskId = $taskId;

        if ($taskId) {
            $task = Tasks::find($taskId);
            $this->title = $task->title;
            $this->body = $task->body;
            $this->status = $task->status;
            
        } else {
            $this->rules['title'] = 'required|string';
            $this->rules['body'] = 'required|string';
            $this->rules['status'] = 'required';
        }

        $this->showModal = true;
    }

    public function closeModal()
    {
        $this->showModal = false;
        $this->resetInput();
    }

    public function resetInput()
    {
        $this->title = '';
        $this->body = '';
        $this->status = '';
        $this->taskId = null;
        $this->resetErrorBag();
    }

    public function save()
    {
        $validatedData = $this->validate();

        if ($this->taskId) {
            $task = Tasks::find($this->taskId);
            $task->update([
                'title' => $this->title,
                'body' => $this->body,
                'status' => $this->status,
            ]);
        } else {
            Tasks::create([
                'title' => $this->title,
                'body' => $this->body,
                'status' => $this->status,
            ]);
        }

        $this->closeModal();
        $this->dispatch('notify', 'Task saved successfully!');
    }

    public function delete($taskId)
    {
        Tasks::find($taskId)->delete();
        $this->dispatch('notify', 'Task deleted successfully!');
    }

    public function render()
    {
        $tasks = Tasks::where('title', 'like', '%' . $this->search . '%')
            ->orWhere('body', 'like', '%' . $this->search . '%')
            ->orderBy($this->sortField, $this->sortDirection)
            ->paginate(10);
        return view('livewire.show-posts-table', [
            'tasks' => $tasks,
        ]);
    }
}
