<x-app-layout>
    <x-slot name="header">
        <h2 class="mb-0">{{ __('Profile') }}</h2>
    </x-slot>

    <div class="container py-4">
        @if(session('status') === 'profile-updated')
            <div id="profile-status" class="alert alert-success alert-dismissible fade show mb-4" role="alert">
                {{ __('Saved.') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
            <script>
                setTimeout(function() {
                    var el = document.getElementById('profile-status');
                    if (el) {
                        var bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                        bsAlert.close();
                    }
                }, 3000);
            </script>
        @endif

        <div class="row">
            <div class="col-md-8">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-md-8">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <hr class="my-4">

        <div class="row">
            <div class="col-md-8">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
</x-app-layout>
