<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({ auth: Object })

const title = ref('')
const content = ref('')

function submit() {
  if (!title.value.trim() || !content.value.trim()) return
  router.post('/posts', { title: title.value, content: content.value })
}
</script>

<template>
  <div class="page">
    <nav class="nav">
      <a href="/posts" class="nav-brand">📝 My Blog</a>
      <div class="nav-links">
        <span class="nav-greeting">Hi, {{ auth?.name }}</span>
        <a href="/dashboard" class="nav-link">Dashboard</a>
        <button @click="router.post('/logout')" class="nav-btn">Logout</button>
      </div>
    </nav>

    <div class="container">
      <a href="/posts" class="back-link">← Back to Posts</a>

      <div class="card">
        <h1 class="page-title">New Post</h1>
        <p class="page-sub">Share something with the world</p>

        <div class="field-group">
          <label class="label">Title</label>
          <input v-model="title" placeholder="Give your post a title..." class="field" />
        </div>

        <div class="field-group">
          <label class="label">Content</label>
          <textarea v-model="content" placeholder="What's on your mind?" class="field textarea"></textarea>
        </div>

        <div class="actions">
          <a href="/posts" class="cancel-btn">Cancel</a>
          <button @click="submit" class="submit-btn">Publish →</button>
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
.container { max-width: 680px; margin: 0 auto; padding: 48px 20px; }
.back-link { color: #818cf8; text-decoration: none; font-size: 0.85rem; font-weight: 600; display: inline-block; margin-bottom: 24px; }
.back-link:hover { color: #a5b4fc; }
.card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 36px; }
.page-title { font-size: 1.6rem; font-weight: 800; color: #f1f5f9; margin-bottom: 6px; }
.page-sub { color: #64748b; font-size: 0.88rem; margin-bottom: 32px; }
.field-group { margin-bottom: 20px; }
.label { display: block; font-size: 0.78rem; font-weight: 700; color: #64748b; letter-spacing: 0.05em; text-transform: uppercase; margin-bottom: 8px; }
.field { width: 100%; background: #0f172a; border: 1px solid #334155; color: #f1f5f9; border-radius: 10px; padding: 12px 16px; font-size: 0.9rem; outline: none; font-family: inherit; }
.field:focus { border-color: #6366f1; }
.field::placeholder { color: #475569; }
.textarea { min-height: 200px; resize: vertical; line-height: 1.7; }
.actions { display: flex; justify-content: flex-end; gap: 12px; margin-top: 28px; }
.cancel-btn { color: #64748b; text-decoration: none; font-size: 0.88rem; font-weight: 600; padding: 10px 20px; border-radius: 8px; border: 1px solid #334155; }
.cancel-btn:hover { background: #334155; color: #f1f5f9; }
.submit-btn { background: #6366f1; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 700; cursor: pointer; }
.submit-btn:hover { background: #4f46e5; }
</style>
