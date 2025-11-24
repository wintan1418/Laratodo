<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Task Details') }}
            </h2>
            <div class="flex gap-2">
                <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                    @csrf
                    @if($task->completed)
                        <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Mark Pending
                        </button>
                    @else
                        <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                            Mark Complete
                        </button>
                    @endif
                </form>
                <a href="{{ route('tasks.edit', $task) }}" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                    Edit
                </a>
                <form action="{{ route('tasks.destroy', $task) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-md text-sm font-medium">
                        Delete
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <div class="mb-6">
                        <h1 class="text-3xl font-semibold text-gray-900 dark:text-gray-100 mb-4">{{ $task->title }}</h1>
                        
                        <div class="mb-4 flex items-center gap-4">
                            <div>
                                <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Status: </span>
                                @if($task->completed)
                                    <span class="inline-flex rounded-full bg-green-100 px-3 py-1 text-sm font-semibold leading-5 text-green-800">
                                        Completed
                                    </span>
                                @else
                                    <span class="inline-flex rounded-full bg-yellow-100 px-3 py-1 text-sm font-semibold leading-5 text-yellow-800">
                                        Pending
                                    </span>
                                @endif
                            </div>
                            <form action="{{ route('tasks.toggle', $task) }}" method="POST" class="inline">
                                @csrf
                                @if($task->completed)
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-yellow-600 hover:bg-yellow-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500">
                                        Mark as Pending
                                    </button>
                                @else
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                                        Mark as Completed
                                    </button>
                                @endif
                            </form>
                        </div>

                        @if($task->description)
                            <div class="mb-6">
                                <h2 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</h2>
                                <p class="text-gray-900 dark:text-gray-100 whitespace-pre-wrap">{{ $task->description }}</p>
                            </div>
                        @endif

                        <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Created:</span>
                                    <span class="text-gray-900 dark:text-gray-100 ml-2">{{ $task->created_at->format('F d, Y \a\t g:i A') }}</span>
                                </div>
                                <div>
                                    <span class="text-gray-500 dark:text-gray-400">Last Updated:</span>
                                    <span class="text-gray-900 dark:text-gray-100 ml-2">{{ $task->updated_at->format('F d, Y \a\t g:i A') }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6">
                        <a href="{{ route('tasks.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-900 dark:hover:text-blue-300">
                            ← Back to all tasks
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
