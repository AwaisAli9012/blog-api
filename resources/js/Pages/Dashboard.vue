<script setup>
import { ref } from 'vue'
import { router } from '@inertiajs/vue3'

const props = defineProps({
  auth: Object,
  posts: Array,
  totalPosts: Number,
  totalComments: Number,
})

const showModal = ref(false)
const modalMessage = ref('')
const modalAction = ref(null)

function askConfirm(message, action) {
  modalMessage.value = message
  modalAction.value = action
  showModal.value = true
}
function confirmYes() {
  if (modalAction.value) modalAction.value()
  showModal.value = false
}
function confirmNo() { showModal.value = false }

function deletePost(id) {
  askConfirm('Delete this post? This cannot be undone.', () => router.delete(`/posts/${id}`))
}
</script>

<template>
  <div class="page">

    <div v-if="showModal" class="modal-overlay" @click.self="confirmNo">
      <div class="modal">
        <p class="modal-msg">{{ modalMessage }}</p>
        <div class="modal-actions">
          <button @click="confirmNo" class="modal-cancel">Cancel</button>
          <button @click="confirmYes" class="modal-confirm">Delete</button>
        </div>
      </div>
    </div>
    <nav class="nav">
      <a href="/posts" class="nav-brand">📝 My Blog</a>
      <div class="nav-links">
        <span class="nav-greeting">Hi, {{ auth?.name }}</span>
        <a href="/posts" class="nav-link">Public Blog</a>
        <a href="/posts/create" class="nav-btn">+ New Post</a>
        <button @click="router.post('/logout')" class="nav-btn">Logout</button>
      </div>
    </nav>

    <div class="container">
      <div>
        <h1 class="page-title">Dashboard</h1>
        <p class="page-sub">Manage your posts and track engagement</p>
      </div>

      <div class="stats-grid">
        <div class="stat-card">
          <p class="stat-label">TOTAL POSTS</p>
          <p class="stat-number">{{ totalPosts }}</p>
        </div>
        <div class="stat-card">
          <p class="stat-label">TOTAL COMMENTS</p>
          <p class="stat-number">{{ totalComments }}</p>
        </div>
      </div>

      <div>
        <p class="section-label">YOUR POSTS</p>
        <div class="card">
          <div v-if="posts.length === 0" class="empty">
            <p>No posts yet.</p>
            <a href="/posts/create" class="accent-link">Write your first post →</a>
          </div>
          <div v-for="(post, i) in posts" :key="post.id" class="post-row"
            :style="i < posts.length - 1 ? 'border-bottom: 1px solid #334155' : ''">
            <div style="flex:1;min-width:0">
              <a :href="`/posts/${post.id}`" class="post-title">{{ post.title }}</a>
              <div class="post-meta">
                <span>📅 {{ new Date(post.created_at).toLocaleDateString() }}</span>
                <span class="accent">💬 {{ post.comment_count }} comment{{ post.comment_count !== 1 ? 's' : '' }}</span>
              </div>
            </div>
            <div class="row-actions">
              <a :href="`/posts/${post.id}`" class="view-btn">View</a>
              <button @click="deletePost(post.id)" class="del-btn">Delete</button>
            </div>
          </div>
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
.container { max-width: 760px; margin: 0 auto; padding: 48px 20px; display: flex; flex-direction: column; gap: 36px; }
.page-title { font-size: 1.8rem; font-weight: 800; color: #f1f5f9; margin-bottom: 6px; }
.page-sub { color: #64748b; font-size: 0.9rem; }
.stats-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
.stat-card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 24px; }
.stat-label { font-size: 0.7rem; font-weight: 700; color: #475569; letter-spacing: 0.1em; margin-bottom: 10px; }
.stat-number { font-size: 2.8rem; font-weight: 800; color: #818cf8; }
.section-label { font-size: 0.72rem; font-weight: 700; color: #475569; letter-spacing: 0.1em; text-transform: uppercase; margin-bottom: 14px; }
.card { background: #1e293b; border: 1px solid #334155; border-radius: 16px; overflow: hidden; }
.empty { padding: 40px; text-align: center; color: #475569; }
.accent-link { color: #818cf8; text-decoration: none; font-weight: 600; font-size: 0.9rem; }
.post-row { display: flex; align-items: center; gap: 16px; padding: 18px 24px; }
.post-title { font-weight: 700; color: #e2e8f0; text-decoration: none; font-size: 0.95rem; display: block; margin-bottom: 6px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 400px; }
.post-title:hover { color: #818cf8; }
.post-meta { display: flex; gap: 14px; font-size: 0.75rem; color: #475569; }
.accent { color: #818cf8; font-weight: 600; }
.row-actions { display: flex; gap: 8px; flex-shrink: 0; }
.view-btn { background: #6366f1; color: white; text-decoration: none; padding: 6px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; }
.del-btn { background: transparent; color: #ef4444; border: 1px solid #3f1e1e; padding: 6px 14px; border-radius: 8px; font-size: 0.8rem; font-weight: 600; cursor: pointer; }
.del-btn:hover { background: #3f1e1e; }
.modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.7); display: flex; align-items: center; justify-content: center; z-index: 999; }
.modal { background: #1e293b; border: 1px solid #334155; border-radius: 16px; padding: 32px; max-width: 360px; width: 90%; text-align: center; }
.modal-msg { color: #f1f5f9; font-size: 1rem; font-weight: 600; margin-bottom: 24px; line-height: 1.5; }
.modal-actions { display: flex; gap: 12px; justify-content: center; }
.modal-cancel { background: #334155; color: #94a3b8; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; }
.modal-confirm { background: #ef4444; color: white; border: none; padding: 10px 24px; border-radius: 8px; font-size: 0.9rem; font-weight: 600; cursor: pointer; }
</style>
