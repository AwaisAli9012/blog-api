<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  auth: Object,
  posts: Array,
  totalPosts: Number,
  totalComments: Number,
})

function deletePost(id) {
  if (confirm('Delete this post?')) {
    router.delete(`/posts/${id}`)
  }
}
</script>

<template>
  <div style="min-height:100vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:0">

    <!-- Navbar -->
    <nav style="background:rgba(255,255,255,0.1);backdrop-filter:blur(10px);padding:16px 32px;display:flex;justify-content:space-between;align-items:center;border-bottom:1px solid rgba(255,255,255,0.2)">
      <a href="/posts" style="color:white;font-size:1.2rem;font-weight:800;text-decoration:none">📝 My Blog</a>
      <div style="display:flex;align-items:center;gap:20px">
        <span style="color:rgba(255,255,255,0.9);font-size:0.9rem">Hi, {{ auth?.name }}</span>
        <a href="/posts" style="color:white;text-decoration:none;font-size:0.85rem;background:rgba(255,255,255,0.2);padding:6px 14px;border-radius:20px">Public Blog</a>
        <button @click="router.post('/logout')" style="background:white;color:#667eea;border:none;padding:6px 14px;border-radius:20px;font-size:0.85rem;font-weight:700;cursor:pointer">Logout</button>
      </div>
    </nav>

    <div style="max-width:860px;margin:0 auto;padding:40px 20px">

      <!-- Header -->
      <div style="margin-bottom:32px">
        <h1 style="color:white;font-size:2rem;font-weight:800;margin:0 0 6px">Your Dashboard</h1>
        <p style="color:rgba(255,255,255,0.75);margin:0;font-size:0.95rem">Manage your posts and track engagement</p>
      </div>

      <!-- Stats Cards -->
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;margin-bottom:36px">
        <div style="background:white;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(0,0,0,0.1)">
          <p style="margin:0 0 6px;color:#9ca3af;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em">Total Posts</p>
          <p style="margin:0;font-size:2.5rem;font-weight:800;color:#1a1a2e">{{ totalPosts }}</p>
        </div>
        <div style="background:white;border-radius:16px;padding:24px;box-shadow:0 4px 20px rgba(0,0,0,0.1)">
          <p style="margin:0 0 6px;color:#9ca3af;font-size:0.8rem;font-weight:600;text-transform:uppercase;letter-spacing:0.05em">Total Comments</p>
          <p style="margin:0;font-size:2.5rem;font-weight:800;color:#1a1a2e">{{ totalComments }}</p>
        </div>
      </div>

      <!-- Posts List -->
      <div style="background:white;border-radius:20px;padding:28px;box-shadow:0 4px 20px rgba(0,0,0,0.1)">
        <h2 style="margin:0 0 20px;font-size:1.1rem;font-weight:700;color:#1a1a2e">Your Posts</h2>

        <div v-if="posts.length === 0" style="text-align:center;padding:40px;color:#9ca3af">
          <p style="font-size:1.1rem;margin:0">No posts yet.</p>
          <a href="/posts" style="color:#667eea;font-weight:700;font-size:0.9rem">Create your first post →</a>
        </div>

        <div v-for="post in posts" :key="post.id"
          style="display:flex;justify-content:space-between;align-items:center;padding:16px;border-radius:12px;margin-bottom:10px;background:#f8f9ff;border:1px solid #eeefff;gap:12px">

          <div style="flex:1;min-width:0">
            <a :href="`/posts/${post.id}`"
              style="font-weight:700;color:#1a1a2e;text-decoration:none;font-size:0.95rem;display:block;white-space:nowrap;overflow:hidden;text-overflow:ellipsis">
              {{ post.title }}
            </a>
            <div style="display:flex;gap:12px;margin-top:4px;flex-wrap:wrap">
              <span style="font-size:0.75rem;color:#9ca3af">📅 {{ post.created_at }}</span>
              <span style="font-size:0.75rem;color:#667eea;font-weight:600">💬 {{ post.comment_count }} comment{{ post.comment_count !== 1 ? 's' : '' }}</span>
            </div>
          </div>

          <div style="display:flex;gap:8px;flex-shrink:0">
            <a :href="`/posts/${post.id}`"
              style="background:linear-gradient(135deg,#667eea,#764ba2);color:white;text-decoration:none;border:none;padding:6px 14px;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer">
              View
            </a>
            <button @click="deletePost(post.id)"
              style="background:#fee2e2;color:#ef4444;border:none;padding:6px 14px;border-radius:8px;font-size:0.8rem;font-weight:600;cursor:pointer">
              Delete
            </button>
          </div>

        </div>
      </div>

    </div>
  </div>
</template>
