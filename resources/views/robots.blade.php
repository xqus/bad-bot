@foreach(config('bad-bot.deny-list') as $bot)
User-Agent: {{ $bot }}
Disallow: /
@endforeach


