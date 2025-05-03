<div>
  <!-- Notification -->
  <div x-data="{ show: false, message: '' }" x-show="show" x-init="@this.on('notify', (msg) => { message = msg; show = true; setTimeout(() => show = false, 3000); })" class="fixed px-4 py-2 text-white transition-opacity duration-300 bg-green-500 rounded-lg shadow-lg top-4 right-4" x-cloak>
    <span x-text="message"></span>
  </div>

  <!-- Header -->
  <div class="flex items-center justify-between mb-6">
    <div class="relative">
      <input type="text" wire:model.live.debounce.500ms="search" placeholder="Search users..." class="w-64 py-2 pl-10 pr-4 bg-white border border-gray-300 rounded-lg shadow-sm focus:ring-2 focus:ring-blue-500 focus:border-transparent">
      <svg class="absolute w-5 h-5 text-gray-400 transform -translate-y-1/2 left-3 top-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
      </svg>
    </div>
    <button wire:click="openModal" class="flex items-center gap-2 px-4 py-2 text-white transition bg-blue-600 rounded-lg shadow-md hover:bg-blue-700">
      <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
      </svg>
      Add User
    </button>
  </div>

  <!-- Table -->
  <div class="overflow-hidden bg-white rounded-lg shadow-lg">
    <table class="min-w-full divide-y divide-gray-200">
      <thead class="bg-gray-50">
        <tr>
          <th wire:click="sortBy('name')" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer">
            {{-- Name @if($sortField === 'name') <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span> @endif --}}
            name
          </th>
          <th wire:click="sortBy('email')" class="px-6 py-3 text-xs font-medium tracking-wider text-left text-gray-500 uppercase cursor-pointer">
            Email {{-- @if($sortField === 'email') <span>{!! $sortDirection === 'asc' ? '↑' : '↓' !!}</span> @endif --}}
          </th>
          <th class="px-6 py-3 text-xs font-medium tracking-wider text-right text-gray-500 uppercase">Actions</th>
        </tr>
      </thead>
      <tbody class="bg-white divide-y divide-gray-200">
        @forelse ($tasks as $task)
        <tr class="transition hover:bg-gray-50">
          <td class="px-6 py-4 text-sm font-medium text-gray-900 whitespace-nowrap">{{ $task->title }}</td>
          <td class="px-6 py-4 text-sm text-gray-500 whitespace-nowrap">{{ $task->body }}</td>
          <td class="px-6 py-4 text-sm font-medium text-right whitespace-nowrap">
            <button wire:click="openModal({{ $task->id }})" class="mr-4 text-blue-600 hover:text-blue-800">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15.828a2 2 0 01-2.828 0l-1.414-1.414a2 2 0 010-2.828L14.586 4.586z"></path>
              </svg>
            </button>
            <button wire:click="delete({{ $task->id }})" onclick="return confirm('Are you sure?')" class="text-red-600 hover:text-red-800">
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5-4h4M9 7v12m6-12v12"></path>
              </svg>
            </button>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="3" class="px-6 py-4 text-sm text-center text-gray-500">No tasks found.</td>
        </tr>
        @endforelse
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="px-6 py-4 bg-gray-50">
      {{ $tasks->links() }}
    </div>
  </div>

  <!-- Modal -->
  <div x-show="$wire.showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-600 bg-opacity-50" x-cloak>
    <div class="w-full max-w-md p-6 bg-white rounded-lg shadow-xl">
      <h2 class="mb-4 text-lg font-semibold text-gray-900">{{ $taskId ? 'Edit Task' : 'Add Task' }}</h2>
      <form wire:submit.prevent="save">
        <div class="space-y-4">
          <div>
            <label for="title" class="block text-sm font-medium text-gray-700">Title</label>
            <input type="text" wire:model="title" id="title" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter title">
            @error('title') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
          </div>

          <div>
            <label for="body" class="block text-sm font-medium text-gray-700">Body Content</label>
            <textarea wire:model="body" id="body" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm" placeholder="Enter body"></textarea>
            @error('body') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
          </div>

          <div>
            <label for="status" class="block text-sm font-medium text-gray-700">Status</label>
            <select wire:model="status" id="status" class="block w-full mt-1 border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm">
              <option value="">Select status</option>
              <option value="To-Do">To-Do</option>
              <option value="In Progress">In Progress</option>
              <option value="Done">Done</option>
            </select>
            @error('status') <span class="text-sm text-red-600">{{ $message }}</span> @enderror
          </div>
        </div>

        <div class="flex justify-end gap-3 mt-6">
          <button type="button" wire:click="closeModal" class="px-4 py-2 text-gray-700 transition bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
          <button type="submit" class="px-4 py-2 text-black transition bg-blue-600 rounded-lg hover:bg-blue-700">Save</button>
        </div>
      </form>
    </div>

  </div>