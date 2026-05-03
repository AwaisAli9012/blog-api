<script setup>
import { router } from '@inertiajs/vue3'

defineProps({
    post: Object,
    auth: Object
})

function logout() {
    router.post('/logout')
}

function remove(id) {
    if (confirm('Are you sure you want to delete this post?')) {
        router.delete(`/posts/${id}`)
    }
}
</script>

<template>
  <div style="min-height:100vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:40px 16px;font-family:'Segoe UI',sans-serif">
    <div style="max-width:760px;margin:0 auto">

      <!-- Navbar -->
      <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:32px">
        <a href="/posts" style="color:white;font-size:1.2rem;font-weight:800;text-decoration:none">✍️ My Blog</a>
        <div style="display:flex;gap:12px">
          <template v-if="auth">
            <span style="color:rgba(255,255,255,0.9);font-size:0.9rem;align-self:center">Hi, {{ auth.name }}</span>
            <a href="/dashboard" style="color:white;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:600">Dashboard</a>
            <a href="#" style="color:white;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:600" @click.prevent="logout">Logout</a>
          </template>
          <template v-else>
            <a href="/login" style="color:white;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:600">Login</a>
            <a href="/register" style="color:white;background:rgba(255,255,255,0.2);padding:8px 16px;border-radius:8px;text-decoration:none;font-size:0.9rem;font-weight:600">Register</a>
          </template>
        </div>
      </div>

      <!-- Post Content -->
      <div style="background:white;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,0.15);padding:40px">

        <!-- Back link -->
        <a href="/posts" style="color:#667eea;font-size:0.9rem;font-weight:600;text-decoration:none;display:inline-block;margin-bottom:24px">← Back to all posts</a>

        <!-- Title -->
        <h1 style="font-size:2rem;font-weight:800;color:#1a1a2e;margin:0 0 16px;line-height:1.3">{{ post.title }}</h1>

        <!-- Meta -->
        <div style="display:flex;align-items:center;gap:12px;margin-bottom:32px;padding-bottom:24px;border-bottom:2px solid #f3f4f6">
          <span style="font-size:0.85rem;color:#a0aec0;background:#f7fafc;padding:4px 12px;border-radius:20px">
            ✍️ {{ post.user ? post.user.name : 'Unknown' }}
          </span>
          <span style="font-size:0.85rem;color:#a0aec0;background:#f7fafc;padding:4px 12px;border-radius:20px">
            📅 {{ new Date(post.created_at).toLocaleDateString() }}
          </span>
        </div>

        <!-- Content -->
        <div style="color:#374151;font-size:1.05rem;line-height:1.8;white-space:pre-wrap">{{ post.content }}</div>

        <!-- Delete button (only for logged in users) -->
        <div v-if="auth && auth.id === post.user_id" style="margin-top:40px;padding-top:24px;border-top:2px solid #f3f4f6">
          <button @click="remove(post.id)"
            style="background:#fff0f0;color:#ef4444;border:1px solid #fecaca;border-radius:8px;padding:8px 20px;cursor:pointer;font-weight:600;font-size:0.9rem">
            🗑 Delete Post
          </button>
        </div>

      </div>
    </div>
  </div>
</template>