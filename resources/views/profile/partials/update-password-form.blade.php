<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900">
            {{ __('Update Password') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600">
            {{ __('Ensure your account is using a long, random password to stay secure.') }}
        </p>
    </header>

    <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('put')

        <div>
            <label for="current_password" class="block text-sm font-medium text-gray-700">{{ __('Current Password') }}</label>
            <input id="current_password" name="current_password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="current-password" />
            @if ($errors->updatePassword->get('current_password'))
                <p class="mt-2 text-sm text-red-600">{{ implode(', ', $errors->updatePassword->get('current_password')) }}</p>
            @endif
        </div>

        <div>
            <label for="password" class="block text-sm font-medium text-gray-700">{{ __('New Password') }}</label>
            <input id="password" name="password" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password" />
            @if ($errors->updatePassword->get('password'))
                <p class="mt-2 text-sm text-red-600">{{ implode(', ', $errors->updatePassword->get('password')) }}</p>
            @endif
        </div>

        <div>
            <label for="password_confirmation" class="block text-sm font-medium text-gray-700">{{ __('Confirm Password') }}</label>
            <input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" autocomplete="new-password" />
            @if ($errors->updatePassword->get('password_confirmation'))
                <p class="mt-2 text-sm text-red-600">{{ implode(', ', $errors->updatePassword->get('password_confirmation')) }}</p>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                {{ __('Save') }}
            </button>

            @if (session('status') === 'password-updated')
                <p class="text-sm text-gray-600 saved-message">{{ __('Saved.') }}</p>
            @endif
        </div>
    </form>
</section>
