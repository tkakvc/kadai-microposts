@if (count($microposts) > 0)
    <ul class="list-unstyled">
        @foreach ($microposts as $micropost)
            <li class="media">
                <img class="mr-2 rounded" src="{{ Gravatar::src($user->email, 50) }}" alt="">
                <div class="media-body">
                    <div>
                        {{ $micropost->content }}
                    </div>
                    <div>
                        <p>{!! link_to_route('favorites.favorite', 'Favorite', ['id' => $micropost->id]) !!}</p>
                    </div>
                </div>
            </li>
        @endforeach
    </ul>
    {{ $microposts->links('pagination::bootstrap-4') }}
@endif