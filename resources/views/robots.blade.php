@foreach(config('bad-bot.user-agents') as $bot)
User-Agent: {{ $bot }}
Disallow: /
@endforeach


