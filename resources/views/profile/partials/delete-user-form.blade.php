<section class="space-y-6">
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Delete Account') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <button type="button"
            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150"
            onclick="document.getElementById('confirm-user-deletion').classList.remove('hidden')">
        {{ __('Delete Account') }}
    </button>

    <div id="confirm-user-deletion"
         class="hidden fixed inset-0 z-50 flex items-center justify-center bg-black/50"
         style="display: none;">
        <div class="bg-white rounded-lg shadow-xl p-6 max-w-md w-full mx-4">
            <form method="post" action="{{ route('profile.destroy') }}">
                @csrf
                @method('delete')

                <h2 class="text-lg font-medium text-gray-900">
                    {{ __('Are you sure you want to delete your account?') }}
                </h2>

                <p class="mt-1 text-sm text-gray-600">
                    {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                </p>

                <div class="mt-6">
                    <label for="password" class="sr-only">{{ __('Password') }}</label>
                    <input id="password" name="password" type="password"
                           class="mt-1 block w-3/4 rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="{{ __('Password') }}" />

                    @if ($errors->userDeletion->get('password'))
                        <p class="mt-2 text-sm text-red-600">{{ implode(', ', $errors->userDeletion->get('password')) }}</p>
                    @endif
                </div>

                <div class="mt-6 flex justify-end gap-3">
                    <button type="button"
                            class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150"
                            onclick="document.getElementById('confirm-user-deletion').classList.add('hidden')">
                        {{ __('Cancel') }}
                    </button>

                    <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        {{ __('Delete Account') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>
