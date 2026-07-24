<section class="border rounded p-4 bg-white">
    <header>
        <h2 class="h4">{{ __('Delete Account') }}</h2>
        <p class="text-muted">
            {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Before deleting your account, please download any data or information that you wish to retain.') }}
        </p>
    </header>

    <div class="mt-3">
        <x-danger-button type="button" data-bs-toggle="modal" data-bs-target="#confirm-user-deletion">
            {{ __('Delete Account') }}
        </x-danger-button>
    </div>

    <div class="modal fade" id="confirm-user-deletion" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="post" action="{{ route('profile.destroy') }}" class="p-4">
                    @csrf
                    @method('delete')

                    <h2 class="h5">{{ __('Are you sure you want to delete your account?') }}</h2>
                    <p class="text-muted">
                        {{ __('Once your account is deleted, all of its resources and data will be permanently deleted. Please enter your password to confirm you would like to permanently delete your account.') }}
                    </p>

                    <div class="mb-3">
                        <x-input-label for="password" value="{{ __('Password') }}" class="visually-hidden" />
                        <x-text-input id="password" name="password" type="password" class="form-control" placeholder="{{ __('Password') }}" />
                        <x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" />
                    </div>

                    <div class="d-flex justify-content-end gap-2 mt-3">
                        <x-secondary-button type="button" data-bs-dismiss="modal">
                            {{ __('Cancel') }}
                        </x-secondary-button>

                        <x-danger-button>
                            {{ __('Delete Account') }}
                        </x-danger-button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
