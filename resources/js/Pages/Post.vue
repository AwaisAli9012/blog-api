<script setup>
import { router } from '@inertiajs/vue3'

defineProps({ post: Object, auth: Object })

function logout() { router.post('/logout') }
function remove(id) {
  if (confirm('Delete this post?')) router.delete(`/posts/${id}`)
}
</script>

<template>
  <div class="page">
    <nav class="nav">
      <a href="/posts" class="nav-brand">📝 My Blog</a>
      <div class="nav-links">
        <template v-if="auth">
          <span class="nav-greeting">Hi, {{ auth.name }}</span>
          <a href="/dashboard" class="nav-link">Dashboard</a>
          <button @click="logout" class="nav-btn">Logout</button>
        </template>
        <template v-else>
          <a href="/login" class="nav-link">Login</a>
          <a href="/register" class="nav-btn">Register</a>
        </template>
      </div>
    </nav>

    <div class="container">
      <a href="/posts" class="back-link">← Back to all posts</a>

      <div class="card">
        <h1 class="post-title">{{ post.title }}</h1>

        <div class="meta">
          <span class="meta-tag">✍️ {{ post.user ? post.user.name : 'Unknown' }}</span>
          <span class="meta-tag">📅 {{ new Date(post.created_at).toLocaleDateString() }}</span>
        </div>

        <div class="post-content">{{ post.content }}</div>

        <div v-if="auth && auth.id === post.user_id" class="delete-row">
          <button @click="remove(post.id)" class="delete-btn">🗑 Delete Post</button>
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped>
* { box-sizing: border-box; margin: 0; padding: 0; }
.page { min-height: 100vh; background: #0f172a; color: #f1f5f9; font-family: system-ui, sans-serif; }
.nav { background: #1e293b; border-bottom: 1px solid #334155; padding: 16px 28px; display: flex; justify-content: space-between; align-items: center; position: sticky; top: 0; z-index: 100; }
.nav-brand { color: #f1f5f9; font-size: 1.05rem; font-weight: 800; text-decoration: none; }
.nav-links { display: flex; align-items: center; gap: 16px; }
.nav-greeting { color: #64748b; font-size: 0.82rem; }
.nav-link { color: #94a3b8; text-decoration: none; font-size: 0.85rem; }
.nav-link:hover { color: #f1f5f9; }
.nav-btn { background: #6366f1; color: white; border: none; padding: 7px 16px; border-radius: 8px; font-size: 0.82rem; font-weight: 600; cursor: pointer; }
.container { max-width: 700px; margin: 0 auto; padding: 48px 20px; }
.back-link { color: #818cf8; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-block; margin-bottom: 24px; }
.back-link:hover { color: #a5b4fc; }
.card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 36px; }
.post-title { font-size: 1.8rem; font-weight: 800; color: #f1f5f9; line-height: 1.3; margin-bottom: 20px; }
.meta { display: flex; gap: 10px; flex-wrap: wrap; margin-bottom: 28px; padding-bottom: 24px; border-bottom: 1px solid #334155; }
.meta-tag { font-size: 0.8rem; color: #94a3b8; background: #0f172a; padding: 4px 12px; border-radius: 20px; border: 1px solid #334155; }
.post-content { color: #94a3b8; font-size: 1rem; line-height: 1.8; white-space: pre-wrap; }
.delete-row { margin-top: 36px; padding-top: 24px; border-top: 1px solid #334155; }
.delete-btn { background: transparent; color: #ef4444; border: 1px solid #ef4444; border-radius: 8px; padding: 8px 20px; cursor: pointer; font-weight: 600; font-size: 0.85rem; }
.delete-btn:hover { background: #ef4444; color: white; }
</style>
