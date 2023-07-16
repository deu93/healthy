<div>
  <div  x-data="{
    focused: {{ $parentComment ? 'true' : 'false' }},
    isEdit: {{ $commentModel ? 'true' : 'false' }},
    init() {
        if(this.isEdit || this.focused)
            this.$refs.input.focus()
        $wire.on('commentCreated', ()=> {
            this.focused = false;
        })
    }
  }" class="mb-6">

    <div class="mb-2 mt-5">
        <textarea x-ref="input" wire:model="comment" @click="focused=true" class="resize-none	block w-full rounded-md border-0 px-3.5 py-2 text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 placeholder:text-gray-400 focus:ring-2 focus:ring-inset focus:ring-indigo-600 sm:text-sm sm:leading-6" :rows="isEdit || focused ? '3' : '1'" placeholder="Leave a comment"> </textarea>
        @error('comment')
        <p class="text-red-500">{{ $message }}</p>
    @enderror
    </div>
    <div :class="isEdit || focused ? '' : 'hidden'">
        <button wire:click="createComment" type="button" class="rounded-md bg-indigo-600 px-3.5 py-2.5 text-center text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-600 mr-3">Submit</button>
        <button @click="focused = false; isEdit=false; $wire.emitUp('cancelEditing')" type="button" class="bg-red-500 hover:bg-red-600 text-white font-bold py-2 px-4 rounded-lg">Cancel</button>
    </div>
  </div>

</div>
