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

        $todayEvents = [];
        foreach ($events as $event) {
            if (is_array($event) && isset($event["created_at"]) && str_starts_with($event["created_at"], $today)) {
                $todayEvents[] = $event;
            }
        }

        $this->info("Today events found: " . count($todayEvents));

        if (count($todayEvents) === 0) {
            $this->info("No activity today.");
            return;
        }

        $lines = ["## GitHub Activity for " . $today];
        foreach ($todayEvents as $event) {
            $lines[] = "- " . $event["type"] . " on " . $event["repo"]["name"] . " at " . substr($event["created_at"], 11, 5);
        }

        $title = "GitHub Activity - " . $today;
        $user = User::first();
        Post::create(["title" => $title, "content" => implode("
", $lines), "user_id" => $user->id]);
        $this->info("Post created: " . $title);
    }
}
