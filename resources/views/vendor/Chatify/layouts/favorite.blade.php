{{-- code for profile image show --}}
@php 
    use App\Models\Profile;
    $profile = Profile::where('user_id', $user->id)->first();    
@endphp


<div class="favorite-list-item">
    @if($user)
        @if ($profile->image)
            <div data-id="{{ $user->id }}" data-action="0" class="avatar av-m"
                style="background-image: url('{{ asset('upload/profile/' . $profile->image) }}');">
            </div>
        @else
            <div data-id="{{ $user->id }}" data-action="0" class="avatar av-m"
                style="background-image: url('images/profile/default-profile-image.jpg');">
            </div>
        @endif
        
        <p>
            {{-- {{ strlen($user->name) > 5 ? substr($user->name,0,6).'..' : $user->name }} --}}
            {{ strlen($user->first_name) > 5 ? trim(substr($user->first_name,0,6)).'..' : $user->first_name }}
            {{-- {{ strlen($user->last_name) > 5 ? trim(substr($user->last_name,0,6)).'..' : $user->last_name }} --}}

        </p>
    @endif
</div>
