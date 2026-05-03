<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

defineProps({ posts: Array })

const title = ref('')
const content = ref('')

function submit() {
  router.post('/posts', { title: title.value, content: content.value }, {
    onSuccess: () => { title.value = ''; content.value = '' }
  })
}

function remove(id) {
  if (confirm('Are you sure?')) router.delete(`/posts/${id}`)
}
</script>

<template>
  <div style="min-height:100vh;background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);padding:40px 16px;font-family:'Segoe UI',sans-serif">
    <div style="max-width:760px;margin:0 auto">

      <!-- Header -->
      <div style="text-align:center;margin-bottom:40px">
        <h1 style="font-size:2.8rem;font-weight:800;color:white;margin:0 0 8px;text-shadow:0 2px 8px rgba(0,0,0,0.2)">✍️ My Blog</h1>
        <p style="color:rgba(255,255,255,0.8);font-size:1.1rem;margin:0">Share your thoughts with the world</p>
      </div>

      <!-- Posts List -->
      <div style="display:flex;flex-direction:column;gap:20px;margin-bottom:40px">
        <div v-for="post in posts" :key="post.id"
          style="background:white;border-radius:16px;box-shadow:0 8px 32px rgba(0,0,0,0.12);padding:24px;display:flex;justify-content:space-between;align-items:flex-start;transition:transform 0.2s"
          onmouseover="this.style.transform='translateY(-2px)'"
          onmouseout="this.style.transform='translateY(0)'">
          <div style="flex:1">
            <div style="display:flex;align-items:center;gap:10px;margin-bottom:8px">
              <div style="width:8px;height:8px;background:linear-gradient(135deg,#667eea,#764ba2);border-radius:50%"></div>
              <h2 style="font-size:1.2rem;font-weight:700;color:#1a1a2e;margin:0">{{ post.title }}</h2>
            </div>
            <p style="color:#6b7280;font-size:0.95rem;margin:0 0 12px;line-height:1.6">{{ post.content }}</p>
            <span style="font-size:0.75rem;color:#a0aec0;background:#f7fafc;padding:3px 10px;border-radius:20px">📅 Just now</span>
          </div>
          <button @click="remove(post.id)"
            style="margin-left:20px;background:#fff0f0;color:#ef4444;border:1px solid #fecaca;border-radius:8px;padding:6px 14px;cursor:pointer;font-weight:600;font-size:0.85rem;transition:background 0.2s"
            onmouseover="this.style.background='#fee2e2'"
            onmouseout="this.style.background='#fff0f0'">
            🗑 Delete
          </button>
        </div>

        <div v-if="posts.length === 0"
          style="background:rgba(255,255,255,0.15);border-radius:16px;padding:48px;text-align:center">
          <p style="color:white;font-size:1.1rem;margin:0">No posts yet. Be the first to write one! 🚀</p>
        </div>
      </div>

      <!-- Create Post Form -->
      <div style="background:white;border-radius:20px;box-shadow:0 8px 32px rgba(0,0,0,0.15);padding:32px">
        <h2 style="font-size:1.6rem;font-weight:800;color:#1a1a2e;margin:0 0 6px">Create New Post</h2>
        <p style="color:#9ca3af;margin:0 0 24px;font-size:0.9rem">Fill in the details below to publish your post</p>

        <form @submit.prevent="submit" style="display:flex;flex-direction:column;gap:16px">
          <div>
            <label style="display:block;font-size:0.85rem;font-weight:600;color:#374151;margin-bottom:6px">Post Title</label>
            <input v-model="title" placeholder="Enter an interesting title..."
              style="border:2px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:1rem;outline:none;width:100%;box-sizing:border-box;transition:border-color 0.2s;color:#1f2937"
              onfocus="this.style.borderColor='#667eea'"
              onblur="this.style.borderColor='#e5e7eb'"/>
          </div>
          <div>
            <label style="display:block;font-size:0.85rem;font-weight:600;color:#374151;margin-bottom:6px">Content</label>
            <textarea v-model="content" placeholder="Write your post content here..." rows="5"
              style="border:2px solid #e5e7eb;border-radius:10px;padding:12px 16px;font-size:1rem;outline:none;width:100%;box-sizing:border-box;resize:vertical;color:#1f2937;transition:border-color 0.2s"
              onfocus="this.style.borderColor='#667eea'"
              onblur="this.style.borderColor='#e5e7eb'"></textarea>
          </div>
          <button type="submit"
            style="background:linear-gradient(135deg,#667eea 0%,#764ba2 100%);color:white;border:none;border-radius:10px;padding:14px 32px;font-size:1rem;font-weight:700;cursor:pointer;align-self:flex-start;box-shadow:0 4px 15px rgba(102,126,234,0.4);transition:opacity 0.2s"
            onmouseover="this.style.opacity='0.9'"
            onmouseout="this.style.opacity='1'">
            🚀 Publish Post
          </button>
        </form>
      </div>

    </div>
  </div>
</template>