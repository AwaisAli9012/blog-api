<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use App\Models\Post;
use App\Models\User;

class SyncGithubActivity extends Command
{
    protected $signature = 'github:sync';
    protected $description = 'Fetch GitHub activity and create a blog post';

    public function handle()
    {
        $username = env('GITHUB_USERNAME');
        $token = env('GITHUB_TOKEN');
        $today = now()->toDateString();

        $response = Http::withToken($token)
            ->get("https://api.github.com/users/{$username}/events");

        $events = $response->json();
        if (!is_array($events)) {
            $this->error('GitHub API error: ' . json_encode($events));
            return;
        }

        $todayEvents = array_filter($events, function($event) use ($today) {
            if (!is_array($event) || !isset($event['created_at'])) return false;
            return str_starts_with($event['created_at'], $today);
        });

        if (empty($todayEvents)) {
            $this->info('No activity today.');
            return;
        }

        $lines = ["## GitHub Activity for {$today}\n"];
        foreach ($todayEvents as $event) {
            $type = $event['type'];
            $repo = $event['repo']['name'];
            $time = substr($event['created_at'], 11, 5);
            $lines[] = "- **{$type}** on `{$repo}` at {$time}";
        }

        $content = implode("\n", $lines);
        $title = "GitHub Activity - {$today}";

        $user = User::first();
        Post::create([
            'title' => $title,
            'content' => $content,
            'user_id' => $user->id,
        ]);

        $this->info("Post created: {$title}");
    }
}
